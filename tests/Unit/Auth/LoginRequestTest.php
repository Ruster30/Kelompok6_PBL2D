<?php

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

// ─── Authenticate ───────────────────────────────────────────────────────────

test("authenticate uses email field when login is an email", function () {
    $user = User::factory()->create([
        "email"    => "user@example.com",
        "password" => bcrypt("secret123"),
    ]);

    $request = LoginRequest::create("/login", "POST", [
        "login"    => "user@example.com",
        "password" => "secret123",
    ]);
    $request->setContainer(app());

    $request->authenticate();

    expect(auth()->check())->toBeTrue();
});

test("authenticate uses phone field when login is a phone number", function () {
    // LoginRequest sanitizes phone: removes non-digits, strips leading zero
    // Input "08123456789" -> sanitized "8123456789"
    $user = User::factory()->create([
        "phone"    => "8123456789",
        "password" => bcrypt("secret123"),
    ]);

    $request = LoginRequest::create("/login", "POST", [
        "login"    => "08123456789",
        "password" => "secret123",
    ]);
    $request->setContainer(app());

    $request->authenticate();

    expect(auth()->check())->toBeTrue();
});

test("authenticate sanitizes phone by stripping non-digits and leading zero", function () {
    $user = User::factory()->create([
        "phone"    => "8123456789",
        "password" => bcrypt("secret123"),
    ]);

    $request = LoginRequest::create("/login", "POST", [
        "login"    => "0812-3456-789",
        "password" => "secret123",
    ]);
    $request->setContainer(app());

    $request->authenticate();

    expect(auth()->check())->toBeTrue();
});

test("authenticate throws validation exception for wrong password", function () {
    User::factory()->create([
        "email"    => "user@example.com",
        "password" => bcrypt("secret123"),
    ]);

    $request = LoginRequest::create("/login", "POST", [
        "login"    => "user@example.com",
        "password" => "wrong-password",
    ]);
    $request->setContainer(app());

    $request->authenticate();
})->throws(ValidationException::class);

// ─── Rate Limiting ──────────────────────────────────────────────────────────

test("ensureIsNotRateLimited passes when under limit", function () {
    $request = LoginRequest::create("/login", "POST", [
        "login" => "test@example.com",
    ]);
    $request->server->set("REMOTE_ADDR", "127.0.0.1");
    $request->setContainer(app());

    // Should not throw
    $request->ensureIsNotRateLimited();
    expect(true)->toBeTrue();
});

test("ensureIsNotRateLimited throws after 5 failed attempts", function () {
    $request = LoginRequest::create("/login", "POST", [
        "login" => "rate@test.com",
    ]);
    $request->server->set("REMOTE_ADDR", "127.0.0.1");
    $request->setContainer(app());

    // Hit the rate limiter 5 times
    $key = $request->throttleKey();
    for ($i = 0; $i < 5; $i++) {
        RateLimiter::hit($key);
    }

    $request->ensureIsNotRateLimited();
})->throws(ValidationException::class);

test("throttleKey combines login and IP address", function () {
    $request = LoginRequest::create("/login", "POST", [
        "login" => "user@test.com",
    ]);
    $request->server->set("REMOTE_ADDR", "192.168.1.1");
    $request->setContainer(app());

    $key = $request->throttleKey();

    expect($key)->toContain("user@test.com");
    expect($key)->toContain("192.168.1.1");
});

// ─── Rules ──────────────────────────────────────────────────────────────────

test("login request requires login and password fields", function () {
    $request = LoginRequest::create("/login", "POST");
    $rules = $request->rules();
    expect($rules)->toHaveKey("login");
    expect($rules)->toHaveKey("password");
});

test("login request is always authorized", function () {
    $request = LoginRequest::create("/login", "POST");
    expect($request->authorize())->toBeTrue();
});

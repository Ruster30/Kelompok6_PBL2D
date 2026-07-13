<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

test("store sanitizes phone number by removing non-digits", function () {
    $request = new Request();
    $request->merge([
        "name"                  => "Test User",
        "email"                 => "test@example.com",
        "phone"                 => "0812-3456-7890",
        "password"              => "secret123",
        "password_confirmation" => "secret123",
    ]);

    $controller = app(RegisteredUserController::class);
    $controller->store($request);

    $this->assertDatabaseHas("users", [
        "email" => "test@example.com",
        "phone" => "081234567890",
    ]);
});

test("store creates user and redirects to login", function () {
    $request = new Request();
    $request->merge([
        "name"                  => "New User",
        "email"                 => "newuser@example.com",
        "phone"                 => "0876543210",
        "password"              => "secret123",
        "password_confirmation" => "secret123",
    ]);

    $controller = app(RegisteredUserController::class);
    $response = $controller->store($request);

    $this->assertDatabaseHas("users", [
        "email" => "newuser@example.com",
        "name"  => "New User",
    ]);

    expect($response->getTargetUrl())->toContain("login");
    expect(session("status"))->toContain("berhasil");
    expect(auth()->check())->toBeFalse();
});

test("store validates required fields", function () {
    $request = new Request();
    $request->merge([]);

    $controller = app(RegisteredUserController::class);

    expect(fn () => $controller->store($request))
        ->toThrow(\Illuminate\Validation\ValidationException::class);
});

test("store validates unique email and phone", function () {
    User::factory()->create([
        "email" => "existing@example.com",
        "phone" => "0811111111",
    ]);

    $request = new Request();
    $request->merge([
        "name"                  => "Another User",
        "email"                 => "existing@example.com",
        "phone"                 => "0822222222",
        "password"              => "secret123",
        "password_confirmation" => "secret123",
    ]);

    $controller = app(RegisteredUserController::class);

    expect(fn () => $controller->store($request))
        ->toThrow(\Illuminate\Validation\ValidationException::class);
});

test("store validates phone uniqueness", function () {
    User::factory()->create([
        "email" => "other@example.com",
        "phone" => "0811111111",
    ]);

    $request = new Request();
    $request->merge([
        "name"                  => "User Duplicate Phone",
        "email"                 => "unique@example.com",
        "phone"                 => "0811111111",
        "password"              => "secret123",
        "password_confirmation" => "secret123",
    ]);

    $controller = app(RegisteredUserController::class);

    expect(fn () => $controller->store($request))
        ->toThrow(\Illuminate\Validation\ValidationException::class);
});

test("create returns register view", function () {
    $controller = app(RegisteredUserController::class);
    $response = $controller->create();

    expect($response->name())->toBe("auth.register");
});

<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Mockery\MockInterface;

uses(RefreshDatabase::class);

test("redirect returns socialite redirect response", function () {
    $mock = Mockery::mock("alias:" . Socialite::class);
    $mock->shouldReceive("driver->with->redirect")->once()->andReturn(redirect("https://accounts.google.com"));

    $controller = app(GoogleController::class);
    $response = $controller->redirect();

    expect($response->getTargetUrl())->toStartWith("https://accounts.google.com");
});

test("callback creates new user when no existing user is found", function () {
    $abstractUser = Mockery::mock("Laravel\Socialite\Contracts\User");
    $abstractUser->shouldReceive("getId")->andReturn("google-id-123");
    $abstractUser->shouldReceive("getName")->andReturn("John Doe");
    $abstractUser->shouldReceive("getEmail")->andReturn("john@example.com");
    $abstractUser->shouldReceive("getAvatar")->andReturn("https://avatar.url/photo.jpg");

    $mock = Mockery::mock("alias:" . Socialite::class);
    $mock->shouldReceive("driver->user")->once()->andReturn($abstractUser);

    $controller = app(GoogleController::class);
    $controller->callback();

    $this->assertDatabaseHas("users", [
        "email"     => "john@example.com",
        "name"      => "John Doe",
        "google_id" => "google-id-123",
        "role"      => "client",
    ]);

    expect(auth()->check())->toBeTrue();
});

test("callback links existing user by google_id", function () {
    $user = User::factory()->create([
        "email"     => "existing@example.com",
        "google_id" => "existing-google-id",
        "role"      => "client",
    ]);

    $abstractUser = Mockery::mock("Laravel\Socialite\Contracts\User");
    $abstractUser->shouldReceive("getId")->andReturn("existing-google-id");
    $abstractUser->shouldReceive("getName")->andReturn("Existing User");
    $abstractUser->shouldReceive("getEmail")->andReturn("existing@example.com");
    $abstractUser->shouldReceive("getAvatar")->andReturn("https://avatar.url/photo.jpg");

    $mock = Mockery::mock("alias:" . Socialite::class);
    $mock->shouldReceive("driver->user")->once()->andReturn($abstractUser);

    $controller = app(GoogleController::class);
    $controller->callback();

    expect(auth()->user()->id)->toBe($user->id);
});

test("callback links existing user by email when no google_id match", function () {
    $user = User::factory()->create([
        "email"     => "registered@example.com",
        "google_id" => null,
        "role"      => "client",
    ]);

    $abstractUser = Mockery::mock("Laravel\Socialite\Contracts\User");
    $abstractUser->shouldReceive("getId")->andReturn("new-google-id");
    $abstractUser->shouldReceive("getName")->andReturn("Registered User");
    $abstractUser->shouldReceive("getEmail")->andReturn("registered@example.com");
    $abstractUser->shouldReceive("getAvatar")->andReturn("https://avatar.url/photo.jpg");

    $mock = Mockery::mock("alias:" . Socialite::class);
    $mock->shouldReceive("driver->user")->once()->andReturn($abstractUser);

    $controller = app(GoogleController::class);
    $controller->callback();

    // Assert google_id was updated
    expect($user->fresh()->google_id)->toBe("new-google-id");
    expect(auth()->user()->id)->toBe($user->id);
});

test("callback failure redirects to login with error", function () {
    $mock = Mockery::mock("alias:" . Socialite::class);
    $mock->shouldReceive("driver->user")->once()->andThrow(new \Exception("Google API error"));

    $controller = app(GoogleController::class);
    $response = $controller->callback();

    expect($response->getTargetUrl())->toContain("login");
    expect(session("status"))->toContain("gagal");
    expect(auth()->check())->toBeFalse();
});

test("callback marks email as verified for new Google users", function () {
    $abstractUser = Mockery::mock("Laravel\Socialite\Contracts\User");
    $abstractUser->shouldReceive("getId")->andReturn("google-id-456");
    $abstractUser->shouldReceive("getName")->andReturn("Jane Doe");
    $abstractUser->shouldReceive("getEmail")->andReturn("jane@example.com");
    $abstractUser->shouldReceive("getAvatar")->andReturn("https://avatar.url/jane.jpg");

    $mock = Mockery::mock("alias:" . Socialite::class);
    $mock->shouldReceive("driver->user")->once()->andReturn($abstractUser);

    $controller = app(GoogleController::class);
    $controller->callback();

    $this->assertDatabaseHas("users", [
        "email"             => "jane@example.com",
        "email_verified_at" => now(),
    ]);
});

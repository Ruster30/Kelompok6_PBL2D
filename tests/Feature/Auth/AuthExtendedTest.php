<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test("users can login with phone number", function () {
    // LoginRequest sanitizes "08123456789" -> removes non-digits -> strips leading zero -> "8123456789"
    $user = User::factory()->create([
        "phone"    => "8123456789",
        "password" => bcrypt("password"),
    ]);

    $response = $this->post("/login", [
        "login"    => "08123456789",
        "password" => "password",
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route("dashboard", absolute: false));
});

test("users can login with formatted phone number", function () {
    // LoginRequest sanitizes "0812-3456-789" -> removes non-digits -> "08123456789" -> strips leading zero -> "8123456789"
    $user = User::factory()->create([
        "phone"    => "8123456789",
        "password" => bcrypt("password"),
    ]);

    $response = $this->post("/login", [
        "login"    => "0812-3456-789",
        "password" => "password",
    ]);

    $this->assertAuthenticated();
});

test("login with unknown phone fails", function () {
    $response = $this->post("/login", [
        "login"    => "089999999999",
        "password" => "password",
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors("login");
});

test("login with remember me sets remember token", function () {
    $user = User::factory()->create(["password" => bcrypt("password")]);

    $response = $this->post("/login", [
        "login"    => $user->email,
        "password" => "password",
        "remember" => "on",
    ]);

    $this->assertAuthenticated();
    expect($user->fresh()->remember_token)->not()->toBeNull();
});

test("registration sanitizes phone number with formatting", function () {
    $response = $this->post("/register", [
        "name"                  => "Test User",
        "email"                 => "test@register.test",
        "phone"                 => "+62 812-3456-7890",
        "password"              => "password",
        "password_confirmation" => "password",
    ]);

    $response->assertRedirect(route("login"));
    $this->assertDatabaseHas("users", [
        "email" => "test@register.test",
        "phone" => "6281234567890",
    ]);
});

test("logout clears session and redirects", function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->post("/logout");

    $this->assertGuest();
    $response->assertRedirect("/");
});

test("login requires both fields", function () {
    $response = $this->post("/login", []);

    $response->assertSessionHasErrors(["login", "password"]);
});

test("login page is accessible to guests", function () {
    $response = $this->get("/login");

    $response->assertOk();
});

test("login page redirects authenticated users", function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get("/login");

    $response->assertRedirect(route("dashboard", absolute: false));
});

<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

// ─── Forgot Password ────────────────────────────────────────────────────────

test("forgot-password page is accessible", function () {
    $response = $this->get("/forgot-password");

    $response->assertOk();
});

test("forgot-password rejects invalid email", function () {
    $response = $this->post("/forgot-password", [
        "email" => "not-an-email",
    ]);

    $response->assertSessionHasErrors("email");
});

test("forgot-password requires email field", function () {
    $response = $this->post("/forgot-password", []);

    $response->assertSessionHasErrors("email");
});

// ─── Password Reset ─────────────────────────────────────────────────────────

test("reset-password page requires token", function () {
    $response = $this->get("/reset-password/some-token");

    $response->assertOk();
});

test("reset-password validates all required fields", function () {
    $response = $this->post("/reset-password", []);

    $response->assertSessionHasErrors(["token", "email", "password"]);
});

test("reset-password fails with invalid token", function () {
    $user = User::factory()->create(["email" => "user@test.com"]);

    $response = $this->post("/reset-password", [
        "token"                 => "invalid-token",
        "email"                 => "user@test.com",
        "password"              => "new-password",
        "password_confirmation" => "new-password",
    ]);

    $response->assertSessionHasErrors("email");
});

// ─── Email Verification ─────────────────────────────────────────────────────

test("verified user is redirected from verification notice", function () {
    $user = User::factory()->create(["email_verified_at" => now()]);

    $response = $this->actingAs($user)->get("/verify-email");

    $response->assertRedirect(route("dashboard", absolute: false));
});

test("unverified user sees verification notice", function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get("/verify-email");

    $response->assertOk();
});

test("verification notification can be resent", function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->post("/email/verification-notification");

    $response->assertRedirect();
    $response->assertSessionHas("status", "verification-link-sent");
});

// ─── Confirm Password ───────────────────────────────────────────────────────

test("confirm-password page is accessible when authenticated", function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get("/confirm-password");

    $response->assertOk();
});

test("confirm-password requires authentication", function () {
    $response = $this->get("/confirm-password");

    $response->assertRedirect(route("login"));
});

// ─── Password Update via Web ────────────────────────────────────────────────

test("password update requires current password", function () {
    $user = User::factory()->create(["password" => bcrypt("correct-horse")]);

    $response = $this
        ->actingAs($user)
        ->from("/profile")
        ->put("/password", [
            "current_password"      => "wrong-password",
            "password"              => "new-password",
            "password_confirmation" => "new-password",
        ]);

    $response->assertSessionHasErrorsIn("updatePassword", "current_password");
});

<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

// ─── AuthenticatedSessionController ─────────────────────────────────────────

test("destroy logs out user and invalidates session", function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $request = Request::create("/logout", "POST");
    $request->setLaravelSession(app("session")->driver());
    $request->session()->start();

    $controller = app(AuthenticatedSessionController::class);
    $response = $controller->destroy($request);

    expect(auth()->check())->toBeFalse();
    expect($response->getTargetUrl())->toBe(url("/"));
});

test("create returns login view", function () {
    $controller = app(AuthenticatedSessionController::class);
    $response = $controller->create();

    expect($response->name())->toBe("auth.login");
});

// ─── PasswordController ─────────────────────────────────────────────────────

test("password update changes password", function () {
    $user = User::factory()->create([
        "password" => bcrypt("current-password"),
    ]);
    $this->actingAs($user);

    $request = Request::create("/password", "PUT", [
        "current_password"      => "current-password",
        "password"              => "new-password",
        "password_confirmation" => "new-password",
    ]);
    $request->setUserResolver(fn () => $user);

    $controller = app(PasswordController::class);
    $response = $controller->update($request);

    expect(Hash::check("new-password", $user->fresh()->password))->toBeTrue();
    expect($response->getSession()->get("status"))->toBe("password-updated");
});

test("password update validates current password", function () {
    $user = User::factory()->create([
        "password" => bcrypt("actual-password"),
    ]);
    $this->actingAs($user);

    $request = Request::create("/password", "PUT", [
        "current_password"      => "wrong-password",
        "password"              => "new-password",
        "password_confirmation" => "new-password",
    ]);
    $request->setUserResolver(fn () => $user);

    $controller = app(PasswordController::class);

    expect(fn () => $controller->update($request))
        ->toThrow(\Illuminate\Validation\ValidationException::class);
});

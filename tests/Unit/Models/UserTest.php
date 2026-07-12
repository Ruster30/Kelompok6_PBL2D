<?php

use App\Models\User;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ─── Role Helpers ───────────────────────────────────────────

test('admin role helper returns true for admin', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    expect($admin->isAdmin())->toBeTrue();
    expect($admin->isClient())->toBeFalse();
    expect($admin->isVendor())->toBeFalse();
});

test('client role helper returns true for client', function () {
    $client = User::factory()->create(['role' => 'client']);

    expect($client->isClient())->toBeTrue();
    expect($client->isAdmin())->toBeFalse();
    expect($client->isVendor())->toBeFalse();
});

test('vendor role helper returns true for vendor', function () {
    $vendor = User::factory()->create(['role' => 'vendor']);

    expect($vendor->isVendor())->toBeTrue();
    expect($vendor->isAdmin())->toBeFalse();
    expect($vendor->isClient())->toBeFalse();
});

// ─── Initials Accessor ──────────────────────────────────────

test('initials accessor returns first letter of first two words', function () {
    $user = User::factory()->create(['name' => 'Ahmad Rizki']);

    expect($user->initials)->toBe('AR');
});

test('initials accessor handles single word names', function () {
    $user = User::factory()->create(['name' => 'Bambang']);

    expect($user->initials)->toBe('BA');
});

test('initials accessor handles three word names', function () {
    $user = User::factory()->create(['name' => 'Muhammad Rizki Fadhillah']);

    expect($user->initials)->toBe('MR');
});

// ─── Avatar URL Accessor ────────────────────────────────────

test('avatar url returns ui-avatars fallback when no avatar uploaded', function () {
    $user = User::factory()->create([
        'name'   => 'Test User',
        'avatar' => null,
    ]);

    expect($user->avatar_url)->toContain('ui-avatars.com');
    expect($user->avatar_url)->toContain(urlencode('Test User'));
});

test('avatar url returns storage url when avatar is set', function () {
    $user = User::factory()->create([
        'avatar' => 'avatars/photo.jpg',
    ]);

    expect($user->avatar_url)->toContain('storage/avatars/photo.jpg');
});

// ─── Active Client Check ────────────────────────────────────

test('is_active_client returns true when last_active_at is within 30 days', function () {
    $user = User::factory()->create([
        'last_active_at' => now()->subDays(5),
    ]);

    expect($user->is_active_client)->toBeTrue();
});

test('is_active_client returns false when last_active_at is older than 30 days', function () {
    $user = User::factory()->create([
        'last_active_at' => now()->subDays(60),
    ]);

    expect($user->is_active_client)->toBeFalse();
});

test('is_active_client falls back to updated_at when last_active_at is null', function () {
    $user = User::factory()->create([
        'last_active_at' => null,
        'updated_at'     => now()->subDays(10),
    ]);

    expect($user->is_active_client)->toBeTrue();
});

// ─── Unread Notification Count ──────────────────────────────

test('unread_notif_count returns correct count', function () {
    $user = User::factory()->create();

    Notification::create(['user_id' => $user->id, 'judul' => 'Notif 1', 'pesan' => 'Test', 'tipe' => 'info', 'dibaca' => false]);
    Notification::create(['user_id' => $user->id, 'judul' => 'Notif 2', 'pesan' => 'Test', 'tipe' => 'info', 'dibaca' => false]);
    Notification::create(['user_id' => $user->id, 'judul' => 'Notif 3', 'pesan' => 'Test', 'tipe' => 'info', 'dibaca' => false]);
    Notification::create(['user_id' => $user->id, 'judul' => 'Notif 4', 'pesan' => 'Test', 'tipe' => 'info', 'dibaca' => true]);
    Notification::create(['user_id' => $user->id, 'judul' => 'Notif 5', 'pesan' => 'Test', 'tipe' => 'info', 'dibaca' => true]);

    expect($user->unread_notif_count)->toBe(3);
});

// ─── Total Event Attribute ──────────────────────────────────

test('total_event returns correct count of client events', function () {
    $client = User::factory()->create(['role' => 'client']);

    \App\Models\Event::factory()->count(3)->create(['client_id' => $client->id]);
    \App\Models\Event::factory()->count(2)->create(); // events milik client lain

    expect($client->total_event)->toBe(3);
});

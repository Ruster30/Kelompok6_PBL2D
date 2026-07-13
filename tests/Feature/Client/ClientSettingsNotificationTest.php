<?php

use App\Models\User;
use App\Models\Event;
use App\Models\Notification;

//
// WHITEBOX TEST: Client Settings, Notifications, Timeline
// File: app/Http/Controllers/Client/ClientController.php
// File: app/Services/ClientService.php
//

// ========== SETTINGS ==========

test('settings page loads with user data', function () {
    $client = User::factory()->create([
        'role' => 'client',
        'name' => 'Budi Santoso',
        'email' => 'budi@example.com',
    ]);

    $this->actingAs($client);
    $response = $this->get(route('client.settings'));
    $response->assertOk();
    $response->assertSee('Budi Santoso');
    $response->assertSee('budi@example.com');
});

test('client can update profile', function () {
    $client = User::factory()->create([
        'role' => 'client',
        'name' => 'Old Name',
        'email' => 'old@example.com',
    ]);

    $this->actingAs($client);
    $response = $this->put(route('client.settings.profile'), [
        'name' => 'New Name',
        'email' => 'new@example.com',
        'phone' => '08123456789',
    ]);
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $client->refresh();
    expect($client->name)->toBe('New Name');
    expect($client->email)->toBe('new@example.com');
    expect($client->phone)->toBe('08123456789');
});

test('profile update validates unique email', function () {
    $client = User::factory()->create(['role' => 'client', 'email' => 'client@test.com']);
    User::factory()->create(['role' => 'admin', 'email' => 'exists@test.com']);

    $this->actingAs($client);
    $response = $this->put(route('client.settings.profile'), [
        'name' => 'Test',
        'email' => 'exists@test.com',
    ]);
    $response->assertSessionHasErrors(['email']);
});

test('client can update password with correct current password', function () {
    $client = User::factory()->create([
        'role' => 'client',
        'password' => bcrypt('current_password'),
    ]);

    $this->actingAs($client);
    $response = $this->put(route('client.settings.password'), [
        'current_password' => 'current_password',
        'password' => 'new_password_123',
        'password_confirmation' => 'new_password_123',
    ]);
    $response->assertRedirect();
    $response->assertSessionHas('success');
});

test('password update fails with wrong current password', function () {
    $client = User::factory()->create([
        'role' => 'client',
        'password' => bcrypt('correct_password'),
    ]);

    $this->actingAs($client);
    $response = $this->put(route('client.settings.password'), [
        'current_password' => 'wrong_password',
        'password' => 'new_password_123',
        'password_confirmation' => 'new_password_123',
    ]);
    $response->assertSessionHasErrors(['current_password']);
});

test('password update requires min 8 characters', function () {
    $client = User::factory()->create([
        'role' => 'client',
        'password' => bcrypt('current_password'),
    ]);

    $this->actingAs($client);
    $response = $this->put(route('client.settings.password'), [
        'current_password' => 'current_password',
        'password' => 'short',
        'password_confirmation' => 'short',
    ]);
    $response->assertSessionHasErrors(['password']);
});

// ========== NOTIFICATIONS ==========

test('notifications page shows notifications', function () {
    $client = User::factory()->create(['role' => 'client']);
    Notification::factory()
        ->count(5)
        ->create(['user_id' => $client->id]);

    $this->actingAs($client);
    $response = $this->get(route('client.notifications'));
    $response->assertOk();
});

test('notifications page auto-marks as read', function () {
    $client = User::factory()->create(['role' => 'client']);
    Notification::factory()
        ->count(3)
        ->unread()
        ->create(['user_id' => $client->id]);

    expect(Notification::where('user_id', $client->id)->where('dibaca', false)->count())->toBe(3);

    $this->actingAs($client);
    $this->get(route('client.notifications'));

    expect(Notification::where('user_id', $client->id)->where('dibaca', false)->count())->toBe(0);
});

test('mark all notifications as read', function () {
    $client = User::factory()->create(['role' => 'client']);
    Notification::factory()
        ->count(5)
        ->unread()
        ->create(['user_id' => $client->id]);

    $this->actingAs($client);
    $response = $this->post(route('client.notif.read'));
    $response->assertRedirect();

    expect(Notification::where('user_id', $client->id)->where('dibaca', false)->count())->toBe(0);
});

test('notifications page shows empty state', function () {
    $client = User::factory()->create(['role' => 'client']);

    $this->actingAs($client);
    $response = $this->get(route('client.notifications'));
    $response->assertOk();
    $response->assertSee('Belum Ada Notifikasi');
});

// ========== TIMELINE ==========

test('timeline page loads with event selector', function () {
    $client = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);

    Event::factory()->count(3)->withClient($client)->withPic($admin)->create();

    $this->actingAs($client);
    $response = $this->get(route('client.timeline'));
    $response->assertOk();
});

test('timeline page shows empty state when no events', function () {
    $client = User::factory()->create(['role' => 'client']);

    $this->actingAs($client);
    $response = $this->get(route('client.timeline'));
    $response->assertOk();
    $response->assertSee('Belum Ada Event');
});

test('timeline shows specific event when id provided', function () {
    $client = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);
    $event = Event::factory()
        ->withClient($client)
        ->withPic($admin)
        ->create(['nama_event' => 'My Test Event']);

    $this->actingAs($client);
    $response = $this->get(route('client.timeline.show', $event->id));
    $response->assertOk();
});

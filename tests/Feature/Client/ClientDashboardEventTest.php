<?php

use App\Models\User;
use App\Models\Event;
use App\Models\Proposal;
use App\Models\Notification;

//
// WHITEBOX TEST: Client Dashboard & Events
// File: app/Http/Controllers/Client/ClientController.php
// File: app/Services/ClientService.php
//

test('dashboard displays client data correctly', function () {
    $client = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);

    Event::factory()
        ->withClient($client)
        ->withPic($admin)
        ->status('berjalan')
        ->create();

    Event::factory()
        ->withClient($client)
        ->withPic($admin)
        ->status('menunggu')
        ->create();

    Event::factory()
        ->withClient($client)
        ->withPic($admin)
        ->status('selesai')
        ->create();

    $this->actingAs($client);
    $response = $this->get(route('client.dashboard'));
    $response->assertOk();
    $response->assertViewHas('eventBerjalan', 1);
    $response->assertViewHas('eventMenunggu', 1);
    $response->assertSee($client->name);
});

test('dashboard shows empty state when client has no events', function () {
    $client = User::factory()->create(['role' => 'client']);

    $this->actingAs($client);
    $response = $this->get(route('client.dashboard'));
    $response->assertOk();
    $response->assertSee('Belum ada pengajuan');
});

test('events page paginates client events', function () {
    $client = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);

    Event::factory()
        ->count(12)
        ->withClient($client)
        ->withPic($admin)
        ->create();

    $this->actingAs($client);
    $response = $this->get(route('client.events'));
    $response->assertOk();
    $response->assertViewHas('events');
});

test('events page shows empty state', function () {
    $client = User::factory()->create(['role' => 'client']);

    $this->actingAs($client);
    $response = $this->get(route('client.events'));
    $response->assertOk();
    $response->assertSee('Belum Ada Event');
});

test('event create page loads successfully', function () {
    $client = User::factory()->create(['role' => 'client']);

    $this->actingAs($client);
    $response = $this->get(route('client.event.create'));
    $response->assertOk();
    $response->assertSee('Ajukan Event Baru');
});

test('event store with valid data creates event and notifies admin', function () {
    $client = User::factory()->create(['role' => 'client', 'name' => 'Test Client']);
    $admin1 = User::factory()->create(['role' => 'admin']);
    $admin2 = User::factory()->create(['role' => 'admin']);

    $eventData = [
        'nama_event' => 'Konferensi Tahunan 2026',
        'jenis_event' => 'Konferensi',
        'tanggal_event' => now()->addMonths(2)->format('Y-m-d'),
        'lokasi_event' => 'Jakarta Convention Center',
        'jumlah_tamu' => 500,
        'rentang_anggaran' => 'Rp 100 Juta - Rp 250 Juta',
        'detail_kebutuhan' => 'Membutuhkan sound system dan catering',
    ];

    $this->actingAs($client);
    $response = $this->post(route('client.event.store'), $eventData);
    $response->assertRedirect(route('client.dashboard'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('events', [
        'nama_event' => 'Konferensi Tahunan 2026',
        'client_id' => $client->id,
        'status_event' => 'menunggu',
    ]);

    $this->assertDatabaseHas('notifications', [
        'user_id' => $admin1->id,
        'judul' => 'Request Event Baru',
    ]);
    $this->assertDatabaseHas('notifications', [
        'user_id' => $admin2->id,
        'judul' => 'Request Event Baru',
    ]);
});

test('event store validates required fields', function () {
    $client = User::factory()->create(['role' => 'client']);

    $this->actingAs($client);
    $response = $this->post(route('client.event.store'), []);
    $response->assertSessionHasErrors(['nama_event', 'jenis_event', 'tanggal_event', 'lokasi_event']);
});

test('event store rejects past dates', function () {
    $client = User::factory()->create(['role' => 'client']);

    $this->actingAs($client);
    $response = $this->post(route('client.event.store'), [
        'nama_event' => 'Test Event',
        'jenis_event' => 'Konferensi',
        'tanggal_event' => now()->subDay()->format('Y-m-d'),
        'lokasi_event' => 'Jakarta',
        'jumlah_tamu' => 100,
    ]);
    $response->assertSessionHasErrors(['tanggal_event']);
});

test('event store rejects zero guests', function () {
    $client = User::factory()->create(['role' => 'client']);

    $this->actingAs($client);
    $response = $this->post(route('client.event.store'), [
        'nama_event' => 'Test Event',
        'jenis_event' => 'Konferensi',
        'tanggal_event' => now()->addMonth()->format('Y-m-d'),
        'lokasi_event' => 'Jakarta',
        'jumlah_tamu' => 0,
    ]);
    $response->assertSessionHasErrors(['jumlah_tamu']);
});

test('events page shows feedback button for completed events', function () {
    $client = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);

    $event = Event::factory()
        ->withClient($client)
        ->withPic($admin)
        ->status('selesai')
        ->create();

    $this->actingAs($client);
    $response = $this->get(route('client.events'));
    $response->assertOk();
    $response->assertSee('Beri Feedback');
});

test('dashboard shows notification count in sidebar', function () {
    $client = User::factory()->create(['role' => 'client']);

    Notification::factory()
        ->count(3)
        ->unread()
        ->create(['user_id' => $client->id]);

    $this->actingAs($client);
    $response = $this->get(route('client.dashboard'));
    $response->assertOk();
    $response->assertSee('3');
});

<?php

use App\Models\User;
use App\Models\Event;
use App\Models\Proposal;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;

//
// WHITEBOX TEST: Client Middleware & Access Control
//

test('client routes redirect to login when unauthenticated', function () {
    $routes = [
        route('client.dashboard'),
        route('client.events'),
        route('client.event.create'),
        route('client.invoices'),
        route('client.proposals'),
        route('client.settings'),
        route('client.notifications'),
    ];

    foreach ($routes as $route) {
        $response = $this->get($route);
        $response->assertRedirect(route('login'));
    }
});

test('client routes return 403 for non-client roles', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $vendor = User::factory()->create(['role' => 'vendor']);

    $routes = [
        route('client.dashboard'),
        route('client.events'),
        route('client.event.create'),
        route('client.invoices'),
    ];

    foreach ([$admin, $vendor] as $user) {
        foreach ($routes as $route) {
            $response = $this->actingAs($user)->get($route);
            $response->assertForbidden();
        }
    }
});

test('client routes return 200 for client role', function () {
    $client = User::factory()->create(['role' => 'client']);
    $response = $this->actingAs($client)->get(route('client.dashboard'));
    $response->assertOk();
});

test('client gets 404 when accessing other clients event timeline', function () {
    $clientA = User::factory()->create(['role' => 'client']);
    $clientB = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);
    $eventB = Event::factory()->withClient($clientB)->withPic($admin)->create();

    $this->actingAs($clientA);
    $response = $this->get(route('client.timeline.show', $eventB->id));
    // myEvent() uses findOrFail which throws ModelNotFoundException (404)
    $response->assertStatus(404);
});

test('client gets 404 when accessing other clients proposals', function () {
    $clientA = User::factory()->create(['role' => 'client']);
    $clientB = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);
    $eventB = Event::factory()->withClient($clientB)->withPic($admin)->create();
    $proposalB = Proposal::factory()->create(['event_id' => $eventB->id]);

    $this->actingAs($clientA);
    $response = $this->get(route('client.proposals.show', $proposalB->id));
    $response->assertStatus(404);
});

test('client gets 403 when accessing documents from other clients', function () {
    Storage::fake('public');
    $clientA = User::factory()->create(['role' => 'client']);
    $clientB = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);
    $eventB = Event::factory()->withClient($clientB)->withPic($admin)->create();
    $doc = Document::factory()->create([
        'event_id' => $eventB->id,
        'file_path' => 'documents/test.pdf',
    ]);
    Storage::disk('public')->put('documents/test.pdf', 'fake content');

    $this->actingAs($clientA);
    $response = $this->get(route('client.proposals.document.preview', $doc->id));
    $response->assertStatus(403);
});

test('document preview returns 404 when file missing on disk', function () {
    Storage::fake('public');
    $client = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);
    $event = Event::factory()->withClient($client)->withPic($admin)->create();
    $doc = Document::factory()->create([
        'event_id' => $event->id,
        'file_path' => 'documents/missing.pdf',
    ]);

    $this->actingAs($client);
    $response = $this->get(route('client.proposals.document.preview', $doc->id));
    $response->assertStatus(404);
});

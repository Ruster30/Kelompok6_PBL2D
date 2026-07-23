<?php

use App\Models\User;
use App\Models\Event;
use App\Models\Proposal;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Negotiation;
use App\Models\Document;
use App\Services\ClientService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

//
// WHITEBOX TEST: ClientService Unit Tests
// File: app/Services/ClientService.php
//

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->client = User::factory()->create(['role' => 'client', 'name' => 'Test Client']);
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->service = app(ClientService::class);
    $this->actingAs($this->client);
});

// ========== PRIVATE HELPER: uid() ==========

test('uid returns authenticated user id', function () {
    $reflection = new ReflectionClass($this->service);
    $method = $reflection->getMethod('uid');
    $method->setAccessible(true);

    $result = $method->invoke($this->service);
    expect($result)->toBe((int) $this->client->id);
});

// ========== DASHBOARD ==========

test('getDashboardData returns correct stats', function () {
    Event::factory()
        ->count(3)
        ->sequence(
            ['status_event' => 'berjalan'],
            ['status_event' => 'berjalan'],
            ['status_event' => 'menunggu'],
        )
        ->withClient($this->client)
        ->withPic($this->admin)
        ->create();

    $data = $this->service->getDashboardData();

    expect($data)->toHaveKey('eventBerjalan', 2);
    expect($data)->toHaveKey('eventMenunggu', 1);
    expect($data)->toHaveKey('recentEvents');
    expect($data)->toHaveKey('unreadCount');
    expect($data['recentEvents'])->toHaveCount(3);
});

// ========== EVENTS ==========

test('getEventsData returns paginated events', function () {
    Event::factory()
        ->count(5)
        ->withClient($this->client)
        ->withPic($this->admin)
        ->create();

    $data = $this->service->getEventsData();

    expect($data)->toHaveKey('events');
    expect($data['events'])->toHaveCount(5);
});

// ========== EVENT CREATE ==========

test('createEvent creates event with correct data', function () {
    $data = [
        'nama_event' => 'New Event',
        'jenis_event' => 'Konferensi',
        'tanggal_event' => now()->addMonths(2)->format('Y-m-d'),
        'lokasi_event' => 'Jakarta',
        'jumlah_tamu' => 500,
    ];

    $event = $this->service->createEvent($data);

    expect($event)->toBeInstanceOf(Event::class);
    expect($event->client_id)->toBe($this->client->id);
    expect($event->status_event)->toBe('menunggu');
    expect($event->nama_event)->toBe('New Event');
});

test('createEvent notifies all admins', function () {
    User::factory()->count(3)->create(['role' => 'admin']);

    $this->service->createEvent([
        'nama_event' => 'Test Event',
        'jenis_event' => 'Seminar',
        'tanggal_event' => now()->addMonth()->format('Y-m-d'),
        'lokasi_event' => 'Bandung',
        'jumlah_tamu' => 200,
    ]);

    // 1 admin from beforeEach + 3 new = 4
    expect(Notification::where('judul', 'Request Event Baru')->count())->toBe(4);
});

// ========== TIMELINE ==========

test('getTimelineData returns first event when no id given', function () {
    $eventA = Event::factory()
        ->withClient($this->client)
        ->withPic($this->admin)
        ->create(['nama_event' => 'Alpha']);
    $eventB = Event::factory()
        ->withClient($this->client)
        ->withPic($this->admin)
        ->create(['nama_event' => 'Beta']);

    $data = $this->service->getTimelineData(null);

    expect($data)->toHaveKey('selectedEvent');
    expect($data['selectedEvent']->id)->toBe($eventA->id);
});

test('getTimelineData calculates progress correctly', function () {
    $event = Event::factory()
        ->withClient($this->client)
        ->withPic($this->admin)
        ->create();
    $data = $this->service->getTimelineData($event->id);

    expect($data['progress'])->toBe(0);
    expect($data['totalTask'])->toBe(0);
    expect($data['doneTask'])->toBe(0);
});

// ========== INVOICES ==========

test('getInvoicesData returns only client invoices', function () {
    $event = Event::factory()->withClient($this->client)->withPic($this->admin)->create();
    Invoice::factory()->count(2)->create(['event_id' => $event->id]);

    $otherClient = User::factory()->create(['role' => 'client']);
    $otherEvent = Event::factory()->withClient($otherClient)->withPic($this->admin)->create();
    Invoice::factory()->count(3)->create(['event_id' => $otherEvent->id]);

    $data = $this->service->getInvoicesData();

    expect($data['invoices'])->toHaveCount(2);
});

// ========== PROPOSALS ==========

test('getProposalsData returns latest proposal per event', function () {
    $event = Event::factory()->withClient($this->client)->withPic($this->admin)->create();

    Proposal::factory()->create([
        'event_id' => $event->id,
        'versi' => 1,
        'created_at' => now()->subDays(5),
    ]);
    Proposal::factory()->create([
        'event_id' => $event->id,
        'versi' => 2,
        'created_at' => now(),
    ]);

    $data = $this->service->getProposalsData('penawaran', null, null);

    expect($data['latestProposals'])->toHaveCount(1);
});

test('getProposalShowData throws 404 for other client proposal', function () {
    $otherClient = User::factory()->create(['role' => 'client']);
    $otherEvent = Event::factory()->withClient($otherClient)->withPic($this->admin)->create();
    $proposal = Proposal::factory()->create(['event_id' => $otherEvent->id]);

    expect(fn () => $this->service->getProposalShowData($proposal->id))
        ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
});

// ========== PROPOSAL ACCEPTANCE ==========

test('terimaProposal updates proposal status and notifies admins', function () {
    $event = Event::factory()->withClient($this->client)->withPic($this->admin)->create();
    $proposal = Proposal::factory()->create([
        'event_id' => $event->id,
        'status' => 'menunggu_konfirmasi',
    ]);

    $this->service->terimaProposal($proposal->id);

    $this->assertDatabaseHas('proposals', [
        'id' => $proposal->id,
        'status' => 'diterima',
    ]);

    // Exactly 1 notification should be sent to the admin
    $notif = Notification::where('judul', 'Penawaran Diterima')->first();
    expect($notif)->not->toBeNull();
    expect($notif->user_id)->toBe($this->admin->id);
});

// ========== NEGOTIATION ==========

test('submitNegosiasi creates negotiation record', function () {
    $event = Event::factory()->withClient($this->client)->withPic($this->admin)->create();
    $proposal = Proposal::factory()->create([
        'event_id' => $event->id,
        'status' => 'menunggu_konfirmasi',
    ]);

    $this->service->submitNegosiasi($proposal->id, [
        'pesan' => 'Request discount',
        'budget_diinginkan' => '150.000.000',
        'catatan_tambahan' => 'Please consider',
    ]);

    $this->assertDatabaseHas('negotiations', [
        'event_id' => $event->id,
        'user_id' => $this->client->id,
        'pesan' => 'Request discount',
        'budget_diinginkan' => 150000000,
    ]);

    $this->assertDatabaseHas('proposals', [
        'id' => $proposal->id,
        'status' => 'negosiasi',
    ]);
});

test('submitNegosiasi with empty budget stores null', function () {
    $event = Event::factory()->withClient($this->client)->withPic($this->admin)->create();
    $proposal = Proposal::factory()->create([
        'event_id' => $event->id,
        'status' => 'menunggu_konfirmasi',
    ]);

    $this->service->submitNegosiasi($proposal->id, [
        'pesan' => 'Just a message',
        'budget_diinginkan' => '',
        'catatan_tambahan' => null,
    ]);

    $this->assertDatabaseHas('negotiations', [
        'pesan' => 'Just a message',
        'budget_diinginkan' => null,
    ]);
});

// ========== NEGOTIATION FINALIZATION ==========

test('terimaSetelahNegosiasi accepts proposal after negotiation', function () {
    $event = Event::factory()->withClient($this->client)->withPic($this->admin)->create();
    $proposal = Proposal::factory()->create([
        'event_id' => $event->id,
        'status' => 'direvisi',
    ]);
    Negotiation::create([
        'event_id' => $event->id,
        'user_id' => $this->client->id,
        'pesan' => 'Final offer',
    ]);

    $this->service->terimaSetelahNegosiasi($proposal->id);

    $this->assertDatabaseHas('proposals', [
        'id' => $proposal->id,
        'status' => 'diterima',
    ]);
});

// ========== DOCUMENT ACCESS ==========

test('verifyDocumentAccess passes for own document', function () {
    Storage::fake('public');
    $event = Event::factory()->withClient($this->client)->withPic($this->admin)->create();
    $doc = Document::factory()->create([
        'event_id' => $event->id,
        'file_path' => 'documents/test.pdf',
    ]);
    Storage::disk('public')->put('documents/test.pdf', 'content');

    $this->service->verifyDocumentAccess($doc);
    expect(true)->toBeTrue();
});

test('verifyDocumentAccess throws 403 for other client document', function () {
    Storage::fake('public');
    $otherClient = User::factory()->create(['role' => 'client']);
    $otherEvent = Event::factory()->withClient($otherClient)->withPic($this->admin)->create();
    $doc = Document::factory()->create([
        'event_id' => $otherEvent->id,
        'file_path' => 'documents/test.pdf',
    ]);
    Storage::disk('public')->put('documents/test.pdf', 'content');

    try {
        $this->service->verifyDocumentAccess($doc);
        $this->fail('Expected HttpException with status 403');
    } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
        expect($e->getStatusCode())->toBe(403);
    }
});

// ========== SETTINGS ==========

test('updateProfile updates user data', function () {
    $this->service->updateProfile([
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);

    $this->client->refresh();
    expect($this->client->name)->toBe('Updated Name');
    expect($this->client->email)->toBe('updated@example.com');
});

test('updatePassword hashes new password', function () {
    $this->service->updatePassword('new_secret_123');

    $this->client->refresh();
    expect(password_verify('new_secret_123', $this->client->password))->toBeTrue();
});

// ========== NOTIFICATIONS ==========

test('getNotificationsData marks all as read', function () {
    Notification::factory()->count(3)->unread()->create(['user_id' => $this->client->id]);

    $data = $this->service->getNotificationsData();

    expect($data)->toHaveKey('notifications');
    expect(Notification::where('user_id', $this->client->id)->where('dibaca', false)->count())->toBe(0);
});

test('markAllNotificationsRead marks all unread as read', function () {
    Notification::factory()->count(5)->unread()->create(['user_id' => $this->client->id]);
    Notification::factory()->count(2)->read()->create(['user_id' => $this->client->id]);

    $this->service->markAllNotificationsRead();

    expect(Notification::where('user_id', $this->client->id)->where('dibaca', false)->count())->toBe(0);
});

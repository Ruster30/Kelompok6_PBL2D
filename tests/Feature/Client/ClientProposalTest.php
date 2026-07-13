<?php

use App\Models\User;
use App\Models\Event;
use App\Models\Proposal;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Negotiation;
use App\Models\Notification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

//
// WHITEBOX TEST: Client Proposals & Negotiation Flow
// File: app/Http/Controllers/Client/ClientController.php
// File: app/Services/ClientService.php
// Routes: /client/proposals/...
//

beforeEach(function () {
    Storage::fake('public');
});

test('proposals page shows penawaran tab by default', function () {
    $client = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);
    $event = Event::factory()->withClient($client)->withPic($admin)->create();
    Proposal::factory()->create(['event_id' => $event->id]);

    $this->actingAs($client);
    $response = $this->get(route('client.proposals'));
    $response->assertOk();
});

test('proposals page shows document tabs', function () {
    $client = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);
    $event = Event::factory()->withClient($client)->withPic($admin)->create();

    $tabs = ['penawaran', 'proposal', 'rab', 'kontrak', 'laporan', 'kwitansi'];
    foreach ($tabs as $tab) {
        $response = $this->actingAs($client)->get(route('client.proposals', $tab));
        $response->assertOk();
    }
});

test('proposal show redirects to latest active version', function () {
    $client = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);
    $event = Event::factory()->withClient($client)->withPic($admin)->create();

    $oldProposal = Proposal::factory()->create([
        'event_id' => $event->id,
        'versi' => 1,
        'is_active' => false,
    ]);
    $newProposal = Proposal::factory()->create([
        'event_id' => $event->id,
        'versi' => 2,
        'is_active' => true,
    ]);

    $this->actingAs($client);
    $response = $this->get(route('client.proposals.show', $oldProposal->id));
    $response->assertRedirect(route('client.proposals.show', $newProposal->id));
});

test('client can accept proposal', function () {
    $client = User::factory()->create(['role' => 'client', 'name' => 'Client Tester']);
    $admin = User::factory()->create(['role' => 'admin']);
    $event = Event::factory()->withClient($client)->withPic($admin)->create();
    $proposal = Proposal::factory()->create([
        'event_id' => $event->id,
        'status' => 'menunggu_konfirmasi',
    ]);

    $this->actingAs($client);
    $response = $this->post(route('client.proposals.terima', $proposal->id));
    $response->assertRedirect(route('client.proposals.show', $proposal->id));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('proposals', [
        'id' => $proposal->id,
        'status' => 'diterima',
    ]);

    $this->assertDatabaseHas('notifications', [
        'user_id' => $admin->id,
        'judul' => 'Penawaran Diterima',
    ]);
});

test('client can submit negotiation', function () {
    $client = User::factory()->create(['role' => 'client', 'name' => 'Negotiator']);
    $admin = User::factory()->create(['role' => 'admin']);
    $event = Event::factory()->withClient($client)->withPic($admin)->create();
    $proposal = Proposal::factory()->create([
        'event_id' => $event->id,
        'status' => 'menunggu_konfirmasi',
    ]);

    $this->actingAs($client);
    $response = $this->post(route('client.proposals.negosiasi', $proposal->id), [
        'pesan' => 'Kami ingin diskon 20%',
        'budget_diinginkan' => '150.000.000',
        'catatan_tambahan' => 'Mohon dipertimbangkan',
    ]);
    $response->assertRedirect(route('client.proposals.show', $proposal->id));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('negotiations', [
        'event_id' => $event->id,
        'user_id' => $client->id,
        'pesan' => 'Kami ingin diskon 20%',
        'budget_diinginkan' => 150000000,
    ]);

    $this->assertDatabaseHas('proposals', [
        'id' => $proposal->id,
        'status' => 'negosiasi',
    ]);
});

test('negotiation budget is sanitized', function () {
    $client = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);
    $event = Event::factory()->withClient($client)->withPic($admin)->create();
    $proposal = Proposal::factory()->create([
        'event_id' => $event->id,
        'status' => 'menunggu_konfirmasi',
    ]);

    $this->actingAs($client);
    $response = $this->post(route('client.proposals.negosiasi', $proposal->id), [
        'pesan' => 'Test negotiation',
        'budget_diinginkan' => 'Rp 200.000.000',
        'catatan_tambahan' => null,
    ]);
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('negotiations', [
        'budget_diinginkan' => 200000000,
    ]);
});

test('negotiation form validates required pesan', function () {
    $client = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);
    $event = Event::factory()->withClient($client)->withPic($admin)->create();
    $proposal = Proposal::factory()->create([
        'event_id' => $event->id,
        'status' => 'menunggu_konfirmasi',
    ]);

    $this->actingAs($client);
    $response = $this->post(route('client.proposals.negosiasi', $proposal->id), [
        'pesan' => '',
    ]);
    $response->assertSessionHasErrors(['pesan']);
});

test('client cannot negotiate on already accepted proposal', function () {
    $client = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);
    $event = Event::factory()->withClient($client)->withPic($admin)->create();
    $proposal = Proposal::factory()->accepted()->create([
        'event_id' => $event->id,
    ]);

    $this->actingAs($client);
    $response = $this->get(route('client.proposals.negosiasi.form', $proposal->id));
    $response->assertRedirect(route('client.proposals.show', $proposal->id));
    $response->assertSessionHas('error', 'Penawaran ini tidak lagi dapat dinegosiasikan.');
});

test('client can accept after negotiation', function () {
    $client = User::factory()->create(['role' => 'client', 'name' => 'Finalizer']);
    $admin = User::factory()->create(['role' => 'admin']);
    $event = Event::factory()->withClient($client)->withPic($admin)->create();
    $proposal = Proposal::factory()->create([
        'event_id' => $event->id,
        'status' => 'direvisi',
    ]);

    $this->actingAs($client);
    $response = $this->post(route('client.proposals.terima-setelah-negosiasi', $proposal->id));
    $response->assertRedirect(route('client.proposals.show', $proposal->id));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('proposals', [
        'id' => $proposal->id,
        'status' => 'diterima',
    ]);
});

test('negosiasi form loads for proposals with menunggu_konfirmasi status', function () {
    $client = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);
    $event = Event::factory()->withClient($client)->withPic($admin)->create();
    $proposal = Proposal::factory()->create([
        'event_id' => $event->id,
        'status' => 'menunggu_konfirmasi',
    ]);

    $this->actingAs($client);
    $response = $this->get(route('client.proposals.negosiasi.form', $proposal->id));
    $response->assertOk();
    $response->assertSee('Ajukan Negosiasi');
});

<?php

use App\Models\Event;
use App\Models\Negotiation;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * White-box tests for the CORRECTED business workflow (Phase 11I.9C).
 *
 *   Proposal    = Surat Penawaran (single versioning entity, Proposal.versi)
 *   Negotiation = Form Negosiasi (pure client change-request record)
 *
 * There is deliberately NO separate "Surat Negosiasi" entity.
 */

beforeEach(function () {
    Storage::fake('public');
});

// ─── TEST A: Direct acceptance ────────────────────────────────────────────
test('direct acceptance accepts proposal and creates no negotiation', function () {
    $admin  = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'client']);
    $event  = Event::factory()->withClient($client)->withPic($admin)->create();
    $proposal = Proposal::factory()->create([
        'event_id'  => $event->id,
        'status'    => 'menunggu_konfirmasi',
        'versi'     => 1,
        'is_active' => true,
    ]);

    $this->actingAs($client);
    $this->post(route('client.proposals.terima', $proposal->id))
        ->assertRedirect(route('client.proposals.show', $proposal->id));

    $this->assertDatabaseHas('proposals', ['id' => $proposal->id, 'status' => 'diterima']);
    $this->assertDatabaseCount('negotiations', 0);
});

// ─── TEST B: Negotiation request ──────────────────────────────────────────
test('client negotiation request creates negotiation and marks proposal negosiasi', function () {
    $admin  = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'client']);
    $event  = Event::factory()->withClient($client)->withPic($admin)->create();
    $proposal = Proposal::factory()->create([
        'event_id'  => $event->id,
        'status'    => 'menunggu_konfirmasi',
        'versi'     => 1,
        'is_active' => true,
    ]);

    $this->actingAs($client);
    $this->post(route('client.proposals.negosiasi', $proposal->id), [
        'pesan'             => 'Mohon diskon 10%',
        'budget_diinginkan' => '150.000.000',
    ]);

    $this->assertDatabaseHas('negotiations', [
        'event_id' => $event->id,
        'user_id'  => $client->id,
        'pesan'    => 'Mohon diskon 10%',
    ]);
    $this->assertDatabaseHas('proposals', ['id' => $proposal->id, 'status' => 'negosiasi']);
});

// ─── TEST C: Revision ─────────────────────────────────────────────────────
test('admin revision creates proposal v2 and rotates active version', function () {
    $admin  = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'client']);
    $event  = Event::factory()->withClient($client)->withPic($admin)->create();
    Proposal::factory()->create([
        'event_id'  => $event->id,
        'status'    => 'negosiasi',
        'versi'     => 1,
        'is_active' => true,
    ]);

    $this->actingAs($admin);
    $this->post(route('admin.requests.kirim-revisi-penawaran', $event->id))
        ->assertRedirect();

    $this->assertDatabaseCount('proposals', 2);
    $v1 = Proposal::where('event_id', $event->id)->where('versi', 1)->first();
    $v2 = Proposal::where('event_id', $event->id)->where('versi', 2)->first();

    expect($v1->is_active)->toBe(0);
    expect($v2->is_active)->toBe(1);
    expect($v2->versi)->toBe(2);
    expect($v2->status)->toBe('direvisi');
});

// ─── TEST D: Accept after negotiation ─────────────────────────────────────
test('client accepts revised proposal after negotiation', function () {
    $admin  = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'client']);
    $event  = Event::factory()->withClient($client)->withPic($admin)->create();
    Proposal::factory()->create([
        'event_id'  => $event->id,
        'status'    => 'negosiasi',
        'versi'     => 1,
        'is_active' => true,
    ]);

    $this->actingAs($admin);
    $this->post(route('admin.requests.kirim-revisi-penawaran', $event->id));
    $v2 = Proposal::where('event_id', $event->id)->where('versi', 2)->first();

    $this->actingAs($client);
    $this->post(route('client.proposals.terima-setelah-negosiasi', $v2->id))
        ->assertRedirect();

    $this->assertDatabaseHas('proposals', ['id' => $v2->id, 'status' => 'diterima']);
});

// ─── TEST E: Multiple negotiations → v1, v2, v3 ───────────────────────────
test('multiple negotiations produce proposal versions 1, 2, 3', function () {
    $admin  = User::factory()->create(['role' => 'admin']);
    $client = User::factory()->create(['role' => 'client']);
    $event  = Event::factory()->withClient($client)->withPic($admin)->create();
    Proposal::factory()->create([
        'event_id'  => $event->id,
        'status'    => 'menunggu_konfirmasi',
        'versi'     => 1,
        'is_active' => true,
    ]);

    // Negotiation #1 → revision → v2
    $this->actingAs($client);
    $this->post(route('client.proposals.negosiasi',
        Proposal::where('event_id', $event->id)->where('versi', 1)->first()->id),
        ['pesan' => 'Nego 1', 'budget_diinginkan' => '1']);
    $this->actingAs($admin);
    $this->post(route('admin.requests.kirim-revisi-penawaran', $event->id));

    // Negotiation #2 → revision → v3
    $this->actingAs($client);
    $latest = Proposal::where('event_id', $event->id)->orderByDesc('versi')->first();
    $this->post(route('client.proposals.negosiasi', $latest->id),
        ['pesan' => 'Nego 2', 'budget_diinginkan' => '2']);
    $this->actingAs($admin);
    $this->post(route('admin.requests.kirim-revisi-penawaran', $event->id));

    $this->assertDatabaseCount('proposals', 3);
    expect(Proposal::where('event_id', $event->id)->where('versi', 1)->exists())->toBeTrue();
    expect(Proposal::where('event_id', $event->id)->where('versi', 2)->exists())->toBeTrue();
    expect(Proposal::where('event_id', $event->id)->where('versi', 3)->exists())->toBeTrue();

    // Only the latest version is active — single versioning system.
    expect(Proposal::where('event_id', $event->id)->where('is_active', true)->count())->toBe(1);
    expect(Proposal::where('event_id', $event->id)->where('versi', 3)->first()->is_active)->toBe(1);
});

// ─── TEST F: No SuratNegosiasi entity ─────────────────────────────────────
test('surat negosiasi entity is fully removed', function () {
    expect(class_exists(\App\Models\SuratNegosiasi::class, false))->toBeFalse();
    expect(Route::has('client.proposals.terima-surat-negosiasi'))->toBeFalse();
    expect(Route::has('admin.requests.surat-negosiasi.create'))->toBeFalse();
    expect(Route::has('admin.requests.surat-negosiasi.store'))->toBeFalse();
    expect(Route::has('admin.requests.surat-negosiasi.show'))->toBeFalse();
    expect(Schema::hasTable('surat_negotosiasis'))->toBeFalse();
});

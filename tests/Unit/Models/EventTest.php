<?php

use App\Models\User;
use App\Models\Event;
use App\Models\Timeline;
use App\Models\Proposal;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Negotiation;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ─── Relationships ──────────────────────────────────────────

test('event belongs to client and pic admin', function () {
    $client = User::factory()->create(['role' => 'client']);
    $admin  = User::factory()->create(['role' => 'admin']);
    $event  = Event::factory()->create([
        'client_id'    => $client->id,
        'pic_admin_id' => $admin->id,
    ]);

    expect($event->client->id)->toBe($client->id);
    expect($event->picAdmin->id)->toBe($admin->id);
});

test('event has many proposals', function () {
    $event = Event::factory()->create();
    Proposal::factory()->count(3)->create(['event_id' => $event->id]);

    expect($event->proposals)->toHaveCount(3);
});

test('event has many timelines', function () {
    $event = Event::factory()->create();
    Timeline::create([
        'event_id'         => $event->id,
        'nama_kegiatan'    => 'Kick Off',
        'tanggal_kegiatan' => now(),
        'status_kegiatan'  => 'belum_mulai',
    ]);

    expect($event->timelines)->toHaveCount(1);
});

// ─── Computed Attributes ────────────────────────────────────

test('progress returns 0 when no timelines exist', function () {
    $event = Event::factory()->create();

    expect($event->progress)->toBe(0);
});

test('progress calculates correctly based on completed timelines', function () {
    $event = Event::factory()->create();

    Timeline::create(['event_id' => $event->id, 'nama_kegiatan' => 'A', 'tanggal_kegiatan' => now(), 'status_kegiatan' => 'selesai']);
    Timeline::create(['event_id' => $event->id, 'nama_kegiatan' => 'B', 'tanggal_kegiatan' => now(), 'status_kegiatan' => 'selesai']);
    Timeline::create(['event_id' => $event->id, 'nama_kegiatan' => 'C', 'tanggal_kegiatan' => now(), 'status_kegiatan' => 'belum_mulai']);
    Timeline::create(['event_id' => $event->id, 'nama_kegiatan' => 'D', 'tanggal_kegiatan' => now(), 'status_kegiatan' => 'berjalan']);

    // 2 selesai dari 4 total = 50%
    expect($event->progress)->toBe(50);
});

test('status_label returns Indonesian labels', function () {
    $event = Event::factory()->create(['status_event' => 'menunggu']);
    expect($event->status_label)->toBe('Menunggu');

    $event->update(['status_event' => 'diproses']);
    expect($event->fresh()->status_label)->toBe('Diproses');

    $event->update(['status_event' => 'berjalan']);
    expect($event->fresh()->status_label)->toBe('Berjalan');

    $event->update(['status_event' => 'selesai']);
    expect($event->fresh()->status_label)->toBe('Selesai');

    $event->update(['status_event' => 'dibatalkan']);
    expect($event->fresh()->status_label)->toBe('Dibatalkan');
});

test('badge_class returns correct CSS class for each status', function () {
    $event = Event::factory()->create(['status_event' => 'berjalan']);
    expect($event->badge_class)->toBe('badge-aktif');

    $event->update(['status_event' => 'diproses']);
    expect($event->fresh()->badge_class)->toBe('badge-mendatang');

    $event->update(['status_event' => 'menunggu']);
    expect($event->fresh()->badge_class)->toBe('badge-pending');

    $event->update(['status_event' => 'selesai']);
    expect($event->fresh()->badge_class)->toBe('badge-selesai');

    $event->update(['status_event' => 'dibatalkan']);
    expect($event->fresh()->badge_class)->toBe('badge-ditolak');
});

// ─── Total Invoice ──────────────────────────────────────────

test('total_invoice returns first invoice total only', function () {
    $event = Event::factory()->create();

    Invoice::create(['event_id' => $event->id, 'nomor_invoice' => 'INV-001', 'total_invoice' => 50000000, 'status_invoice' => 'belum_bayar', 'tanggal_invoice' => now()]);
    Invoice::create(['event_id' => $event->id, 'nomor_invoice' => 'INV-002', 'total_invoice' => 25000000, 'status_invoice' => 'belum_bayar', 'tanggal_invoice' => now()]);

    // Harusnya 50jt, bukan 75jt
    expect($event->total_invoice)->toBe(50000000.0);
});

test('total_invoice returns 0 when no invoices', function () {
    $event = Event::factory()->create();

    expect($event->total_invoice)->toBe(0.0);
});

// ─── Total RAB ──────────────────────────────────────────────

test('total_rab returns sum of all rab subtotals', function () {
    $event = Event::factory()->create();
    \App\Models\Rab::factory()->count(3)->create([
        'event_id'       => $event->id,
        'subtotal_biaya' => 1000000,
    ]);

    expect($event->total_rab)->toBe(3000000.0);
});

// ─── Latest Proposal ────────────────────────────────────────

test('latest_proposal returns most recent proposal version', function () {
    $event = Event::factory()->create();
    Proposal::factory()->version(1)->create(['event_id' => $event->id, 'created_at' => now()->subDays(5)]);
    Proposal::factory()->version(2)->create(['event_id' => $event->id, 'created_at' => now()->subDays(2)]);
    Proposal::factory()->version(3)->create(['event_id' => $event->id, 'created_at' => now()]);

    expect($event->latestProposal->versi)->toBe(3);
});

test('active_proposal returns only active proposal', function () {
    $event = Event::factory()->create();
    Proposal::factory()->create(['event_id' => $event->id, 'is_active' => false]);
    $active = Proposal::factory()->create(['event_id' => $event->id, 'is_active' => true]);

    expect($event->activeProposal->id)->toBe($active->id);
});

// ─── Negotiation Check ──────────────────────────────────────

test('has_negotiation returns true when negotiations exist', function () {
    $event = Event::factory()->create();
    Negotiation::create(['event_id' => $event->id, 'user_id' => User::factory()->create()->id, 'pesan' => 'Test negosiasi']);

    expect($event->has_negotiation)->toBeTrue();
});

test('has_negotiation returns false when no negotiations exist', function () {
    $event = Event::factory()->create();

    expect($event->has_negotiation)->toBeFalse();
});

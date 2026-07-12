<?php

use App\Models\Event;
use App\Models\Timeline;
use App\Models\Negotiation;
use App\Models\User;
use App\Services\TimelineAutoFill;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ─── Proposal Diterima Langsung ─────────────────────────────

test('proposalDiterima creates 4 timeline stages', function () {
    $event = Event::factory()->create(['status_event' => 'menunggu']);

    TimelineAutoFill::proposalDiterima($event);

    expect(Timeline::where('event_id', $event->id)->count())->toBe(4);
});

test('proposalDiterima sets event status to diproses', function () {
    $event = Event::factory()->create(['status_event' => 'menunggu']);

    TimelineAutoFill::proposalDiterima($event);

    expect($event->fresh()->status_event)->toBe('diproses');
});

test('proposalDiterima does not change status if already diproses', function () {
    $event = Event::factory()->create(['status_event' => 'berjalan']);

    TimelineAutoFill::proposalDiterima($event);

    expect($event->fresh()->status_event)->toBe('berjalan');
});

test('proposalDiterima includes standard stage names', function () {
    $event = Event::factory()->create();

    TimelineAutoFill::proposalDiterima($event);

    $names = Timeline::where('event_id', $event->id)->pluck('nama_kegiatan')->toArray();
    expect($names)->toContain('Kick Off Meeting');
    expect($names)->toContain('Persiapan Event');
    expect($names)->toContain('Hari Pelaksanaan');
    expect($names)->toContain('Evaluasi Event');
});

// ─── Negosiasi Selesai ──────────────────────────────────────

test('negosiasiSelesai creates timeline stages', function () {
    $event = Event::factory()->create(['status_event' => 'menunggu']);
    $negosiasi = Negotiation::create([
        'event_id'          => $event->id,
        'user_id'           => User::factory()->create()->id,
        'pesan'             => 'Setuju dengan revisi',
        'budget_diinginkan' => 15000000,
    ]);

    TimelineAutoFill::negosiasiSelesai($event, $negosiasi);

    expect(Timeline::where('event_id', $event->id)->count())->toBe(4);
});

test('negosiasiSelesai sets event status to diproses', function () {
    $event = Event::factory()->create(['status_event' => 'menunggu']);
    $negosiasi = Negotiation::create([
        'event_id' => $event->id,
        'user_id'  => User::factory()->create()->id,
        'pesan'    => 'Setuju dengan revisi',
    ]);

    TimelineAutoFill::negosiasiSelesai($event, $negosiasi);

    expect($event->fresh()->status_event)->toBe('diproses');
});

// ─── Idempotent: tidak duplikat ─────────────────────────────

test('calling proposalDiterima twice does not duplicate timelines', function () {
    $event = Event::factory()->create();

    TimelineAutoFill::proposalDiterima($event);
    TimelineAutoFill::proposalDiterima($event); // panggil lagi

    expect(Timeline::where('event_id', $event->id)->count())->toBe(4);
});

test('calling both proposalDiterima and negosiasiSelesai only creates one set', function () {
    $event = Event::factory()->create();

    TimelineAutoFill::proposalDiterima($event);

    $negosiasi = Negotiation::create([
        'event_id' => $event->id,
        'user_id'  => User::factory()->create()->id,
        'pesan'    => 'Test',
    ]);
    TimelineAutoFill::negosiasiSelesai($event, $negosiasi);

    // Harus tetap 4, bukan 8
    expect(Timeline::where('event_id', $event->id)->count())->toBe(4);
});

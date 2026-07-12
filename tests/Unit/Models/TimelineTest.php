<?php

use App\Models\Event;
use App\Models\Timeline;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ─── Status Helpers ─────────────────────────────────────────

test('isDone returns true when status is selesai', function () {
    $event  = Event::factory()->create();
    $timeline = Timeline::create([
        'event_id'         => $event->id,
        'nama_kegiatan'    => 'Test',
        'tanggal_kegiatan' => now(),
        'status_kegiatan'  => 'selesai',
    ]);

    expect($timeline->isDone())->toBeTrue();
    expect($timeline->isBerjalan())->toBeFalse();
});

test('isBerjalan returns true when status is berjalan', function () {
    $event  = Event::factory()->create();
    $timeline = Timeline::create([
        'event_id'         => $event->id,
        'nama_kegiatan'    => 'Test',
        'tanggal_kegiatan' => now(),
        'status_kegiatan'  => 'berjalan',
    ]);

    expect($timeline->isBerjalan())->toBeTrue();
    expect($timeline->isDone())->toBeFalse();
});

// ─── Badge & Label ──────────────────────────────────────────

test('badge_class returns correct class for each status', function () {
    $event = Event::factory()->create();

    $t = Timeline::create(['event_id' => $event->id, 'nama_kegiatan' => 'A', 'tanggal_kegiatan' => now(), 'status_kegiatan' => 'selesai']);
    expect($t->badge_class)->toBe('badge-aktif');

    $t = Timeline::create(['event_id' => $event->id, 'nama_kegiatan' => 'B', 'tanggal_kegiatan' => now(), 'status_kegiatan' => 'berjalan']);
    expect($t->badge_class)->toBe('badge-mendatang');

    $t = Timeline::create(['event_id' => $event->id, 'nama_kegiatan' => 'C', 'tanggal_kegiatan' => now(), 'status_kegiatan' => 'belum_mulai']);
    expect($t->badge_class)->toBe('badge-pending');
});

test('status_label returns Indonesian labels', function () {
    $event = Event::factory()->create();

    $t = Timeline::create(['event_id' => $event->id, 'nama_kegiatan' => 'A', 'tanggal_kegiatan' => now(), 'status_kegiatan' => 'belum_mulai']);
    expect($t->status_label)->toBe('Belum Mulai');

    $t = Timeline::create(['event_id' => $event->id, 'nama_kegiatan' => 'B', 'tanggal_kegiatan' => now(), 'status_kegiatan' => 'berjalan']);
    expect($t->status_label)->toBe('Berjalan');

    $t = Timeline::create(['event_id' => $event->id, 'nama_kegiatan' => 'C', 'tanggal_kegiatan' => now(), 'status_kegiatan' => 'selesai']);
    expect($t->status_label)->toBe('Selesai');
});

// ─── Relationship ───────────────────────────────────────────

test('timeline belongs to event', function () {
    $event  = Event::factory()->create();
    $timeline = Timeline::create([
        'event_id'         => $event->id,
        'nama_kegiatan'    => 'Test',
        'tanggal_kegiatan' => now(),
        'status_kegiatan'  => 'belum_mulai',
    ]);

    expect($timeline->event->id)->toBe($event->id);
});

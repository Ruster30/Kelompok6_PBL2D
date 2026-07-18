<?php

use App\Models\User;
use App\Models\Vendor;
use App\Models\Event;
use App\Models\EventVendor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// ─── Relationships ──────────────────────────────────────────

test('vendor belongs to user', function () {
    $vendor = Vendor::factory()->withAccount()->create();

    expect($vendor->user)->toBeInstanceOf(User::class);
});

test('vendor can have null user', function () {
    $vendor = Vendor::factory()->create(['user_id' => null]);

    expect($vendor->user)->toBeNull();
});

test('vendor has many events through event_vendor', function () {
    $vendor = Vendor::factory()->create();
    $event  = Event::factory()->create();

    EventVendor::create([
        'event_id'  => $event->id,
        'vendor_id' => $vendor->id,
        'jadwal_vendor' => now(),
        'status_vendor' => 'ditugaskan',
    ]);

    expect($vendor->events)->toHaveCount(1);
    expect($vendor->events->first()->id)->toBe($event->id);
});

test('vendor has many event_vendors', function () {
    $vendor = Vendor::factory()->create();
    EventVendor::create(['event_id' => Event::factory()->create()->id, 'vendor_id' => $vendor->id]);
    EventVendor::create(['event_id' => Event::factory()->create()->id, 'vendor_id' => $vendor->id]);

    expect($vendor->eventVendors)->toHaveCount(2);
});

test('vendor has many rabs', function () {
    $vendor = Vendor::factory()->create();
    $event  = Event::factory()->create();
    \App\Models\Rab::factory()->count(3)->create([
        'event_id'  => $event->id,
        'vendor_id' => $vendor->id,
    ]);

    expect($vendor->rabs)->toHaveCount(3);
});

// ─── Accessors ──────────────────────────────────────────────

test('total_nilai returns sum of all rab subtotals', function () {
    $vendor = Vendor::factory()->create();
    $event  = Event::factory()->create();

    \App\Models\Rab::factory()->create(['event_id' => $event->id, 'vendor_id' => $vendor->id, 'subtotal_biaya' => 500000]);
    \App\Models\Rab::factory()->create(['event_id' => $event->id, 'vendor_id' => $vendor->id, 'subtotal_biaya' => 1500000]);

    expect($vendor->total_nilai)->toBe(2000000.0);
});

test('total_nilai returns 0 when no rabs exist', function () {
    $vendor = Vendor::factory()->create();

    expect($vendor->total_nilai)->toBe(0.0);
});

test('jumlah_event_aktif counts events with diproses or berjalan status', function () {
    $vendor = Vendor::factory()->create();

    // Event dengan status berjalan
    $event1 = Event::factory()->status('berjalan')->create();
    EventVendor::create(['event_id' => $event1->id, 'vendor_id' => $vendor->id, 'status_vendor' => 'ditugaskan']);

    // Event dengan status diproses
    $event2 = Event::factory()->status('diproses')->create();
    EventVendor::create(['event_id' => $event2->id, 'vendor_id' => $vendor->id, 'status_vendor' => 'dikerjakan']);

    // Event selesai — TIDAK dihitung
    $event3 = Event::factory()->status('selesai')->create();
    EventVendor::create(['event_id' => $event3->id, 'vendor_id' => $vendor->id, 'status_vendor' => 'selesai']);

    expect($vendor->jumlah_event_aktif)->toBe(2);
});

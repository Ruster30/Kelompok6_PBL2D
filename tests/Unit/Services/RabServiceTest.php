<?php

use App\Models\Event;
use App\Models\Rab;
use App\Models\RabAdditionalDetail;
use App\Models\Vendor;
use App\Services\RabService;
use App\Repositories\RabRepository;
use App\Repositories\RabAdditionalDetailRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Helper untuk mendapatkan RabService dengan repository real
function createRabService(): RabService
{
    return new RabService(
        app(RabRepository::class),
        app(RabAdditionalDetailRepository::class)
    );
}

// ─── Create RAB Item ────────────────────────────────────────

test('createRabItem calculates subtotal correctly', function () {
    $event  = Event::factory()->create();
    $service = createRabService();

    $rab = $service->createRabItem([
        'event_id'       => $event->id,
        'nama_biaya'     => 'Sewa Meja',
        'kategori_biaya' => 'Dekorasi',
        'jumlah_item'    => 10,
        'harga_satuan'   => 50000,
    ]);

    expect($rab)->toBeInstanceOf(Rab::class);
    expect((int) $rab->subtotal_biaya)->toBe(500000); // 10 * 50000
    expect($rab->event_id)->toBe($event->id);
});

// ─── Update RAB Item ────────────────────────────────────────

test('updateRabItem recalculates subtotal', function () {
    $rab     = Rab::factory()->create(['jumlah_item' => 5, 'harga_satuan' => 10000, 'subtotal_biaya' => 50000]);
    $service = createRabService();

    $updated = $service->updateRabItem($rab, [
        'event_id'       => $rab->event_id,
        'nama_biaya'     => $rab->nama_biaya,
        'kategori_biaya' => $rab->kategori_biaya,
        'jumlah_item'    => 3,
        'harga_satuan'   => 20000,
    ]);

    expect((int) $updated->subtotal_biaya)->toBe(60000); // 3 * 20000
});

// ─── Delete RAB Item ────────────────────────────────────────

test('deleteRabItem removes the record', function () {
    $rab     = Rab::factory()->create();
    $service = createRabService();

    $service->deleteRabItem($rab);

    expect(Rab::find($rab->id))->toBeNull();
});

// ─── Get Rab Data ───────────────────────────────────────────

test('getRabData returns events and vendors lists', function () {
    Event::factory()->count(2)->create();
    Vendor::factory()->count(3)->create();
    $service = createRabService();

    $data = $service->getRabData(null);

    expect($data)->toHaveKeys(['events', 'vendors', 'selectedEvent', 'rabItems', 'additionalDetail']);
    expect($data['events'])->toHaveCount(2);
    expect($data['vendors'])->toHaveCount(3);
});

// ─── Total Dibayar Klien ────────────────────────────────────

test('getTotalDibayarKlien returns subtotal vendor when no additional details', function () {
    $event = Event::factory()->create();
    Rab::factory()->count(2)->create(['event_id' => $event->id, 'subtotal_biaya' => 1000000]);
    $service = createRabService();

    $total = $service->getTotalDibayarKlien($event->id);

    expect($total)->toBe(2000000.0);
});

test('getTotalDibayarKlien includes fee EO percentage', function () {
    $event = Event::factory()->create();
    Rab::factory()->create(['event_id' => $event->id, 'subtotal_biaya' => 10000000]);

    // Fee EO 10%, tanpa PPN & PPh
    RabAdditionalDetail::create([
        'event_id'     => $event->id,
        'fee_enabled'  => true,
        'fee_percent'  => 10,
        'ppn_enabled'  => false,
        'ppn_percent'  => 0,
        'pph_enabled'  => false,
        'pph_percent'  => 0,
    ]);

    $service = createRabService();
    $total   = $service->getTotalDibayarKlien($event->id);

    // DPP = 10.000.000 + 10% = 11.000.000
    expect($total)->toBe(11000000.0);
});

test('getTotalDibayarKlien calculates DPP + PPN + PPh correctly', function () {
    $event = Event::factory()->create();

    Rab::factory()->create([
        'event_id' => $event->id,
        'subtotal_biaya' => 10000000,
    ]);

    // Fee EO 10%, PPN 11%, PPh 2%
    RabAdditionalDetail::create([
        'event_id'     => $event->id,
        'fee_enabled'  => true,
        'fee_percent'  => 10,
        'ppn_enabled'  => true,
        'ppn_percent'  => 11,
        'pph_enabled'  => true,
        'pph_percent'  => 2,
    ]);

    $service = createRabService();
    $total   = $service->getTotalDibayarKlien($event->id);

    // Total RAB = 10.000.000
    // Fee EO = 10% = 1.000.000
    // Subtotal = 11.000.000
    // PPN = 11.000.000 × 11% = 1.210.000
    // PPh = 11.000.000 × 2%  = 220.000
    // Grandtotal = 11.000.000 + 1.210.000 + 220.000
    //            = 12.430.000
    expect($total)->toBe(12430000.0);
});
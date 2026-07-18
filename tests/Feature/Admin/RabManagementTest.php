<?php

use App\Models\User;
use App\Models\Event;
use App\Models\Rab;
use App\Models\Vendor;
use App\Services\RabService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
});

// ─── Test RabService via Controller ─────────────────────────

test('admin can access RAB page', function () {
    Event::factory()->create();

    $response = $this->actingAs($this->admin)->get(route('admin.rab.index'));

    $response->assertOk();
});

test('admin can create RAB item via service', function () {
    $event = Event::factory()->create();
    $service = app(RabService::class);

    $rab = $service->createRabItem([
        'event_id'       => $event->id,
        'nama_biaya'     => 'Sewa Kursi',
        'kategori_biaya' => 'Dekorasi',
        'jumlah_item'    => 100,
        'satuan'         => 'unit',
        'harga_satuan'   => 15000,
    ]);

    expect(Rab::where('event_id', $event->id)->count())->toBe(1);
    expect((int) $rab->subtotal_biaya)->toBe(1500000); // 100 * 15000
});

test('admin can delete RAB item via service', function () {
    $rab = Rab::factory()->create();
    $service = app(RabService::class);

    $service->deleteRabItem($rab);

    expect(Rab::find($rab->id))->toBeNull();
});

// ─── RAB Additional Details ─────────────────────────────────

test('admin can save additional details via service', function () {
    $event   = Event::factory()->create();
    $service = app(RabService::class);

    $service->saveAdditionalDetails($event->id, [
        'fee_enabled' => true,
        'fee_percent' => 10,
        'ppn_enabled' => true,
        'ppn_percent' => 11,
        'pph_enabled' => false,
        'pph_percent' => 0,
    ]);

    $this->assertDatabaseHas('rab_additional_details', [
        'event_id'    => $event->id,
        'fee_percent' => 10,
        'ppn_percent' => 11,
    ]);
});

test('saveAdditionalDetails updates existing record instead of duplicating', function () {
    $event   = Event::factory()->create();
    $service = app(RabService::class);

    $service->saveAdditionalDetails($event->id, ['fee_enabled' => true, 'fee_percent' => 5, 'ppn_enabled' => false, 'ppn_percent' => 0, 'pph_enabled' => false, 'pph_percent' => 0]);
    $service->saveAdditionalDetails($event->id, ['fee_enabled' => true, 'fee_percent' => 15, 'ppn_enabled' => false, 'ppn_percent' => 0, 'pph_enabled' => false, 'pph_percent' => 0]);

    expect(\App\Models\RabAdditionalDetail::where('event_id', $event->id)->count())->toBe(1);
    expect(\App\Models\RabAdditionalDetail::where('event_id', $event->id)->first()->fee_percent)->toBe('15.00');
});

<?php

use App\Models\Event;
use App\Models\EventVendor;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use App\Models\Vendor;
use App\Services\EventVendorService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = app(EventVendorService::class);
});

test("createAssignment creates event_vendor record", function () {
    $vendor = Vendor::factory()->create();
    $event  = Event::factory()->create();

    $result = $this->service->createAssignment([
        "event_id"      => $event->id,
        "vendor_id"     => $vendor->id,
        "jadwal_vendor" => "2026-08-15",
        "status_vendor" => "ditugaskan",
        "harga_vendor"  => 5000000,
        "deskripsi"     => "Test assignment",
    ]);

    expect($result)->toBeInstanceOf(EventVendor::class);
    $this->assertDatabaseHas("event_vendor", [
        "event_id"      => $event->id,
        "vendor_id"     => $vendor->id,
        "status_vendor" => "ditugaskan",
    ]);
});

test("createAssignment auto-creates a related task", function () {
    $vendor = Vendor::factory()->create();
    $event  = Event::factory()->create(["nama_event" => "Festival Musik"]);

    $this->service->createAssignment([
        "event_id"      => $event->id,
        "vendor_id"     => $vendor->id,
        "jadwal_vendor" => "2026-08-20",
        "status_vendor" => "dikerjakan",
        "prioritas"     => "tinggi",
    ]);

    $this->assertDatabaseHas("tasks", [
        "event_id"  => $event->id,
        "vendor_id" => $vendor->id,
        "status"    => "dikerjakan",
        "prioritas" => "tinggi",
    ]);
});

test("createAssignment sends notification to vendor with user account", function () {
    $user   = User::factory()->create(["role" => "vendor"]);
    $vendor = Vendor::factory()->create(["user_id" => $user->id]);
    $event  = Event::factory()->create(["nama_event" => "Wedding Expo"]);

    $this->service->createAssignment([
        "event_id"      => $event->id,
        "vendor_id"     => $vendor->id,
        "jadwal_vendor" => "2026-09-01",
        "status_vendor" => "ditugaskan",
    ]);

    $this->assertDatabaseHas("notifications", [
        "user_id" => $user->id,
        "judul"   => "Penugasan Baru",
        "tipe"    => "event",
    ]);
});

test("createAssignment does not send notification when vendor has no user", function () {
    $vendor = Vendor::factory()->create(["user_id" => null]);
    $event  = Event::factory()->create();

    $this->service->createAssignment([
        "event_id"      => $event->id,
        "vendor_id"     => $vendor->id,
        "status_vendor" => "ditugaskan",
    ]);

    $this->assertDatabaseCount("notifications", 0);
});

test("createAssignment uses custom task name when provided", function () {
    $vendor = Vendor::factory()->create();
    $event  = Event::factory()->create(["nama_event" => "Festival"]);

    $this->service->createAssignment([
        "event_id"      => $event->id,
        "vendor_id"     => $vendor->id,
        "nama_tugas"    => "Dekorasi Panggung",
        "status_vendor" => "ditugaskan",
    ]);

    $this->assertDatabaseHas("tasks", [
        "event_id"   => $event->id,
        "vendor_id"  => $vendor->id,
        "nama_tugas" => "Dekorasi Panggung",
    ]);
});

test("updateAssignment updates event_vendor record", function () {
    $ev = EventVendor::factory()->create(["status_vendor" => "ditugaskan"]);

    $this->service->updateAssignment($ev, [
        "event_id"      => $ev->event_id,
        "vendor_id"     => $ev->vendor_id,
        "status_vendor" => "selesai",
    ]);

    expect($ev->fresh()->status_vendor)->toBe("selesai");
});

test("updateAssignment sends notification about update", function () {
    $user   = User::factory()->create(["role" => "vendor"]);
    $vendor = Vendor::factory()->create(["user_id" => $user->id]);
    $event  = Event::factory()->create(["nama_event" => "Expo"]);
    $ev     = EventVendor::factory()->create([
        "event_id"  => $event->id,
        "vendor_id" => $vendor->id,
    ]);

    $this->service->updateAssignment($ev, [
        "event_id"      => $event->id,
        "vendor_id"     => $vendor->id,
        "status_vendor" => "dikerjakan",
        "jadwal_vendor" => "2026-10-01",
    ]);

    $this->assertDatabaseHas("notifications", [
        "user_id" => $user->id,
        "judul"   => "Penugasan Diperbarui",
    ]);
});

test("deleteAssignment removes event_vendor and related tasks", function () {
    $vendor = Vendor::factory()->create();
    $event  = Event::factory()->create();
    $ev     = EventVendor::factory()->create([
        "event_id"  => $event->id,
        "vendor_id" => $vendor->id,
    ]);

    Task::factory()->create([
        "event_id"   => $event->id,
        "vendor_id"  => $vendor->id,
        "nama_tugas" => "Penugasan: Test Event",
    ]);

    $this->service->deleteAssignment($ev);

    $this->assertDatabaseMissing("event_vendor", ["id" => $ev->id]);
    $this->assertDatabaseMissing("tasks", [
        "event_id"  => $event->id,
        "vendor_id" => $vendor->id,
    ]);
});

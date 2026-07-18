<?php

use App\Models\Event;
use App\Models\EventVendor;
use App\Models\Task;
use App\Models\Vendor;
use App\Repositories\EventVendorRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->repository = app(EventVendorRepository::class);
});

test("paginateWithFilters returns paginated results", function () {
    $event = Event::factory()->create();
    $vendor = Vendor::factory()->create();
    EventVendor::factory()->count(15)->create([
        "event_id"  => $event->id,
        "vendor_id" => $vendor->id,
    ]);

    $result = $this->repository->paginateWithFilters(null, null);

    expect($result->total())->toBe(15);
    expect($result->perPage())->toBe(10);
});

test("paginateWithFilters filters by search on event name", function () {
    $event1 = Event::factory()->create(["nama_event" => "Festival Musik"]);
    $event2 = Event::factory()->create(["nama_event" => "Workshop Kopi"]);
    $vendor = Vendor::factory()->create();

    EventVendor::factory()->create(["event_id" => $event1->id, "vendor_id" => $vendor->id]);
    EventVendor::factory()->create(["event_id" => $event2->id, "vendor_id" => $vendor->id]);

    $result = $this->repository->paginateWithFilters("Musik", null);

    expect($result->total())->toBe(1);
});

test("paginateWithFilters filters by status", function () {
    $event = Event::factory()->create();
    $vendor = Vendor::factory()->create();

    EventVendor::factory()->create([
        "event_id"      => $event->id,
        "vendor_id"     => $vendor->id,
        "status_vendor" => "ditugaskan",
    ]);
    EventVendor::factory()->create([
        "event_id"      => $event->id,
        "vendor_id"     => $vendor->id,
        "status_vendor" => "selesai",
    ]);

    $result = $this->repository->paginateWithFilters(null, "selesai");

    expect($result->total())->toBe(1);
});

test("getAllEvents returns all events ordered by name", function () {
    Event::factory()->create(["nama_event" => "Z Event"]);
    Event::factory()->create(["nama_event" => "A Event"]);

    $events = $this->repository->getAllEvents();

    expect($events->first()->nama_event)->toBe("A Event");
    expect($events->count())->toBe(2);
});

test("getAllVendors returns all vendors ordered by name", function () {
    Vendor::factory()->create(["nama_vendor" => "Z Vendor"]);
    Vendor::factory()->create(["nama_vendor" => "A Vendor"]);

    $vendors = $this->repository->getAllVendors();

    expect($vendors->first()->nama_vendor)->toBe("A Vendor");
    expect($vendors->count())->toBe(2);
});

test("create stores event_vendor", function () {
    $event = Event::factory()->create();
    $vendor = Vendor::factory()->create();

    $ev = $this->repository->create([
        "event_id"      => $event->id,
        "vendor_id"     => $vendor->id,
        "status_vendor" => "ditugaskan",
    ]);

    expect($ev)->toBeInstanceOf(EventVendor::class);
    $this->assertDatabaseHas("event_vendor", ["event_id" => $event->id, "vendor_id" => $vendor->id]);
});

test("update modifies event_vendor", function () {
    $ev = EventVendor::factory()->create(["status_vendor" => "ditugaskan"]);

    $updated = $this->repository->update($ev, ["status_vendor" => "selesai"]);

    expect($updated->status_vendor)->toBe("selesai");
});

test("delete removes event_vendor", function () {
    $ev = EventVendor::factory()->create();

    $this->repository->delete($ev);

    $this->assertDatabaseMissing("event_vendor", ["id" => $ev->id]);
});

test("deleteRelatedTasks removes tasks with Penugasan prefix", function () {
    $event  = Event::factory()->create();
    $vendor = Vendor::factory()->create();

    Task::factory()->create([
        "event_id"   => $event->id,
        "vendor_id"  => $vendor->id,
        "nama_tugas" => "Penugasan: Event Wedding",
    ]);
    Task::factory()->create([
        "event_id"   => $event->id,
        "vendor_id"  => $vendor->id,
        "nama_tugas" => "Dekorasi Panggung",
    ]);

    $this->repository->deleteRelatedTasks($event->id, $vendor->id);

    $this->assertDatabaseMissing("tasks", ["nama_tugas" => "Penugasan: Event Wedding"]);
    $this->assertDatabaseHas("tasks", ["nama_tugas" => "Dekorasi Panggung"]);
});

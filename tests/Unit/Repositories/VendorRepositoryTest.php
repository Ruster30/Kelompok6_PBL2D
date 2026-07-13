<?php

use App\Models\Vendor;
use App\Repositories\VendorRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->repository = app(VendorRepository::class);
});

test("paginateWithFilters returns paginated vendors", function () {
    Vendor::factory()->count(15)->create();

    $result = $this->repository->paginateWithFilters(null);

    expect($result->total())->toBe(15);
    expect($result->perPage())->toBe(10);
});

test("paginateWithFilters searches by nama_vendor", function () {
    Vendor::factory()->create(["nama_vendor" => "Cahaya Catering"]);
    Vendor::factory()->create(["nama_vendor" => "Berlian Dekorasi"]);
    Vendor::factory()->create(["nama_vendor" => "Mawar Catering"]);

    $result = $this->repository->paginateWithFilters("Catering");

    expect($result->total())->toBe(2);
});

test("paginateWithFilters returns all when search is empty", function () {
    Vendor::factory()->count(3)->create();

    $result = $this->repository->paginateWithFilters("");

    expect($result->total())->toBe(3);
});

test("countTotal returns total vendor count", function () {
    Vendor::factory()->count(7)->create();

    expect($this->repository->countTotal())->toBe(7);
});

test("countActive counts vendors with user_id", function () {
    Vendor::factory()->count(3)->create(["user_id" => null]);
    Vendor::factory()->count(4)->withAccount()->create();

    expect($this->repository->countActive())->toBe(4);
});

test("countBusy counts vendors with active event assignments", function () {
    $vendor1 = Vendor::factory()->create();
    $vendor2 = Vendor::factory()->create();
    $vendor3 = Vendor::factory()->create();

    \App\Models\EventVendor::factory()->count(2)->create([
        "vendor_id"     => $vendor1->id,
        "status_vendor" => "ditugaskan",
    ]);

    \App\Models\EventVendor::factory()->create([
        "vendor_id"     => $vendor2->id,
        "status_vendor" => "selesai",
    ]);

    \App\Models\EventVendor::factory()->create([
        "vendor_id"     => $vendor3->id,
        "status_vendor" => "dikerjakan",
    ]);

    expect($this->repository->countBusy())->toBe(2);
});

test("create stores new vendor", function () {
    $vendor = $this->repository->create([
        "nama_vendor"  => "Test Vendor",
        "jenis_vendor" => "Katering",
        "email"        => "test@vendor.com",
    ]);

    expect($vendor)->toBeInstanceOf(Vendor::class);
    expect($vendor->nama_vendor)->toBe("Test Vendor");
    $this->assertDatabaseHas("vendors", ["email" => "test@vendor.com"]);
});

test("update modifies vendor and returns fresh instance", function () {
    $vendor = Vendor::factory()->create(["nama_vendor" => "Old"]);

    $updated = $this->repository->update($vendor, ["nama_vendor" => "Updated"]);

    expect($updated->nama_vendor)->toBe("Updated");
    $this->assertDatabaseHas("vendors", ["id" => $vendor->id, "nama_vendor" => "Updated"]);
});

test("delete removes vendor", function () {
    $vendor = Vendor::factory()->create();

    $this->repository->delete($vendor);

    $this->assertDatabaseMissing("vendors", ["id" => $vendor->id]);
});

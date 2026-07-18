<?php

use App\Models\User;
use App\Models\Vendor;
use App\Services\AdminVendorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = app(AdminVendorService::class);
});

test("createVendor creates vendor without user account when no password", function () {
    $this->service->createVendor([
        "nama_vendor"  => "Cahaya Catering",
        "jenis_vendor" => "Katering",
        "email"        => "halo@catering.test",
        "alamat"       => "Padang",
        "deskripsi"    => "Catering enak",
    ]);

    $this->assertDatabaseHas("vendors", [
        "nama_vendor"  => "Cahaya Catering",
        "email"        => "halo@catering.test",
        "user_id"      => null,
    ]);
    $this->assertDatabaseMissing("users", ["email" => "halo@catering.test"]);
});

test("createVendor creates vendor with user account when password is provided", function () {
    $this->service->createVendor([
        "nama_vendor"  => "Cahaya Catering",
        "jenis_vendor" => "Katering",
        "email"        => "akun@catering.test",
        "alamat"       => "Padang",
        "password"     => "rahasia123",
    ]);

    $this->assertDatabaseHas("vendors", [
        "email"       => "akun@catering.test",
        "user_id"     => User::where("email", "akun@catering.test")->first()->id,
    ]);
    $this->assertDatabaseHas("users", [
        "email" => "akun@catering.test",
        "role"  => "vendor",
    ]);
});

test("createVendor throws when creating vendor with existing email and password", function () {
    User::factory()->create(["email" => "exist@test.com", "role" => "client"]);

    expect(fn () => $this->service->createVendor([
        "nama_vendor"  => "Test Vendor",
        "jenis_vendor" => "Katering",
        "email"        => "exist@test.com",
        "password"     => "rahasia123",
    ]))->toThrow(ValidationException::class);
});

test("createVendor allows duplicate email without password", function () {
    User::factory()->create(["email" => "exist@test.com", "role" => "client"]);

    $this->service->createVendor([
        "nama_vendor"  => "Test Vendor",
        "email"        => "exist@test.com",
    ]);

    $this->assertDatabaseHas("vendors", [
        "email"       => "exist@test.com",
        "user_id"     => null,
    ]);
});

test("updateVendor updates vendor basic info", function () {
    $vendor = Vendor::factory()->create(["nama_vendor" => "Old Name"]);

    $this->service->updateVendor($vendor, [
        "nama_vendor"  => "New Name",
        "jenis_vendor" => "Dekorasi",
        "email"        => "new@email.test",
        "alamat"       => "Jakarta",
        "deskripsi"    => "Updated description",
    ]);

    $this->assertDatabaseHas("vendors", [
        "id"           => $vendor->id,
        "nama_vendor"  => "New Name",
        "jenis_vendor" => "Dekorasi",
    ]);
});

test("updateVendor creates user account when vendor had none and password is given", function () {
    $vendor = Vendor::factory()->create(["user_id" => null]);

    $this->service->updateVendor($vendor, [
        "nama_vendor"  => $vendor->nama_vendor,
        "email"        => "newaccount@test.com",
        "password"     => "newpass123",
    ]);

    $this->assertDatabaseHas("users", [
        "email" => "newaccount@test.com",
        "role"  => "vendor",
    ]);
    expect($vendor->fresh()->user_id)->not()->toBeNull();
});

test("updateVendor syncs user email when vendor has existing account", function () {
    $user = User::factory()->create([
        "email" => "old@test.com",
        "role"  => "vendor",
    ]);
    $vendor = Vendor::factory()->create([
        "user_id" => $user->id,
        "email"   => "old@test.com",
    ]);

    $this->service->updateVendor($vendor, [
        "nama_vendor"  => $vendor->nama_vendor,
        "email"        => "updated@test.com",
    ]);

    expect($user->fresh()->email)->toBe("updated@test.com");
});

test("updateVendor throws when syncing to an email that already exists", function () {
    User::factory()->create(["email" => "taken@test.com", "role" => "client"]);
    $user = User::factory()->create(["email" => "vendor@test.com", "role" => "vendor"]);
    $vendor = Vendor::factory()->create([
        "user_id" => $user->id,
        "email"   => "vendor@test.com",
    ]);

    expect(fn () => $this->service->updateVendor($vendor, [
        "nama_vendor"  => "Test",
        "email"        => "taken@test.com",
    ]))->toThrow(ValidationException::class);
});

test("updateVendor updates user password when provided", function () {
    $user = User::factory()->create([
        "password" => bcrypt("old-password"),
        "role"     => "vendor",
    ]);
    $vendor = Vendor::factory()->create(["user_id" => $user->id]);

    $this->service->updateVendor($vendor, [
        "nama_vendor"  => $vendor->nama_vendor,
        "email"        => $vendor->email,
        "password"     => "new-secret-456",
    ]);

    expect(Hash::check("new-secret-456", $user->fresh()->password))->toBeTrue();
});

test("deleteVendor deletes vendor from database", function () {
    $vendor = Vendor::factory()->create();

    $this->service->deleteVendor($vendor);

    $this->assertDatabaseMissing("vendors", ["id" => $vendor->id]);
});

test("getIndexData returns vendors and counts", function () {
    Vendor::factory()->count(5)->create();
    Vendor::factory()->withAccount()->create();

    $data = $this->service->getIndexData(null);

    expect($data)->toHaveKeys(["vendors", "totalVendors", "activeVendors", "busyVendors"]);
    expect($data["totalVendors"])->toBe(6);
    expect($data["activeVendors"])->toBe(1);
});

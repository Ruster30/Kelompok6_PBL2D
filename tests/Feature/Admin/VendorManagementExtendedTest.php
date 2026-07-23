<?php

use App\Models\User;
use App\Models\Vendor;
use App\Models\Event;
use App\Models\EventVendor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(["role" => "admin"]);
});

// ─── Update Vendor with account sync ────────────────────────────────────────

test("admin can update vendor and create user account when vendor had none", function () {
    $vendor = Vendor::factory()->create(["user_id" => null]);

    $response = $this->actingAs($this->admin)->put(route("admin.vendors.update", $vendor), [
        "nama_vendor"  => "Updated Vendor",
        "jenis_vendor" => "Katering",
        "email"        => "newlogin@vendor.test",
        "password"     => "rahasia123",
    ]);

    $response->assertRedirect(route("admin.vendors.index"));
    $this->assertDatabaseHas("users", [
        "email" => "newlogin@vendor.test",
        "role"  => "vendor",
    ]);
    expect($vendor->fresh()->user_id)->not()->toBeNull();
});

test("admin can update vendor with existing account and sync email", function () {
    $user   = User::factory()->create(["email" => "old@vendor.test", "role" => "vendor"]);
    $vendor = Vendor::factory()->create([
        "user_id" => $user->id,
        "email"   => "old@vendor.test",
    ]);

    $response = $this->actingAs($this->admin)->put(route("admin.vendors.update", $vendor), [
        "nama_vendor"  => "Updated Vendor",
        "email"        => "new@vendor.test",
    ]);

    $response->assertRedirect(route("admin.vendors.index"));
    expect($user->fresh()->email)->toBe("new@vendor.test");
});

test("admin cannot update vendor with email conflict", function () {
    User::factory()->create(["email" => "taken@test.com", "role" => "client"]);
    $user   = User::factory()->create(["email" => "vendor@test.com", "role" => "vendor"]);
    $vendor = Vendor::factory()->create(["user_id" => $user->id, "email" => "vendor@test.com"]);

    $response = $this->actingAs($this->admin)->put(route("admin.vendors.update", $vendor), [
        "nama_vendor" => "Test Vendor",
        "email"       => "taken@test.com",
    ]);

    $response->assertSessionHasErrors("email");
});

// ─── Vendor search ──────────────────────────────────────────────────────────

test("vendor list can be filtered by search", function () {
    Vendor::factory()->create(["nama_vendor" => "Cahaya Catering"]);
    Vendor::factory()->create(["nama_vendor" => "Mawar Catering"]);
    Vendor::factory()->create(["nama_vendor" => "Berlian Dekorasi"]);

    $response = $this->actingAs($this->admin)->get(route("admin.vendors.index", ["search" => "Catering"]));

    $response->assertOk();
    $response->assertSee("Cahaya Catering");
    $response->assertSee("Mawar Catering");
    $response->assertDontSee("Berlian Dekorasi");
});

// ─── EventVendor Feature Tests ──────────────────────────────────────────────

test("admin can view event-vendor list", function () {
    EventVendor::factory()->count(3)->create();

    $response = $this->actingAs($this->admin)->get(route("admin.event-vendors.index"));

    $response->assertOk();
    $response->assertViewHas("eventVendors");
});

test("admin can create event-vendor assignment", function () {
    $event  = Event::factory()->create();
    $vendor = Vendor::factory()->create();

    $response = $this->actingAs($this->admin)->post(route("admin.event-vendors.store"), [
        "event_id"      => $event->id,
        "vendor_id"     => $vendor->id,
        "status_vendor" => "ditugaskan",
        "jadwal_vendor" => "2026-08-15",
        "harga_vendor"  => 5000000,
    ]);

    $response->assertRedirect(route("admin.event-vendors.index"));
    $this->assertDatabaseHas("event_vendor", [
        "event_id"      => $event->id,
        "vendor_id"     => $vendor->id,
        "status_vendor" => "ditugaskan",
    ]);
});

test("admin can update event-vendor assignment", function () {
    $ev = EventVendor::factory()->create(["status_vendor" => "ditugaskan"]);

    $response = $this->actingAs($this->admin)->put(route("admin.event-vendors.update", $ev), [
        "event_id"      => $ev->event_id,
        "vendor_id"     => $ev->vendor_id,
        "status_vendor" => "selesai",
    ]);

    $response->assertRedirect(route("admin.event-vendors.index"));
    expect($ev->fresh()->status_vendor)->toBe("selesai");
});

test("admin can delete event-vendor assignment", function () {
    $ev = EventVendor::factory()->create();

    $response = $this->actingAs($this->admin)->delete(route("admin.event-vendors.destroy", $ev));

    $response->assertRedirect(route("admin.event-vendors.index"));
    $this->assertDatabaseMissing("event_vendor", ["id" => $ev->id]);
});

// ─── EventVendor validation ─────────────────────────────────────────────────

test("event-vendor store validates required fields", function () {
    $response = $this->actingAs($this->admin)->post(route("admin.event-vendors.store"), []);

    $response->assertSessionHasErrors(["event_id", "vendor_id", "status_vendor"]);
});

test("event-vendor store validates status_vendor values", function () {
    $event  = Event::factory()->create();
    $vendor = Vendor::factory()->create();

    $response = $this->actingAs($this->admin)->post(route("admin.event-vendors.store"), [
        "event_id"      => $event->id,
        "vendor_id"     => $vendor->id,
        "status_vendor" => "invalid_status",
    ]);

    $response->assertSessionHasErrors("status_vendor");
});

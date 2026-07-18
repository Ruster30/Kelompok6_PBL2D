<?php

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
});

// ─── List Vendors ───────────────────────────────────────────

test('admin can view vendor list', function () {
    Vendor::factory()->count(3)->create();

    $response = $this->actingAs($this->admin)->get(route('admin.vendors.index'));

    $response->assertOk();
    $response->assertViewHas('vendors');
});

test('non-admin cannot view vendor list', function () {
    $client = User::factory()->create(['role' => 'client']);

    $response = $this->actingAs($client)->get(route('admin.vendors.index'));

    $response->assertForbidden();
});

// ─── Create Vendor ──────────────────────────────────────────

test('admin can create vendor without login account', function () {
    $response = $this->actingAs($this->admin)->post(route('admin.vendors.store'), [
        'nama_vendor'  => 'Cahaya Catering',
        'jenis_vendor' => 'Katering',
        'email'        => 'halo@cahayacatering.test',
        'alamat'       => 'Padang',
    ]);

    $response->assertRedirect(route('admin.vendors.index'));
    $this->assertDatabaseHas('vendors', [
        'nama_vendor' => 'Cahaya Catering',
        'email'       => 'halo@cahayacatering.test',
        'user_id'     => null,
    ]);
    $this->assertDatabaseMissing('users', ['email' => 'halo@cahayacatering.test']);
});

test('admin can create vendor with login account', function () {
    $response = $this->actingAs($this->admin)->post(route('admin.vendors.store'), [
        'nama_vendor'  => 'Cahaya Catering',
        'jenis_vendor' => 'Katering',
        'email'        => 'akun@cahayacatering.test',
        'alamat'       => 'Padang',
        'password'     => 'rahasia123',
    ]);

    $response->assertRedirect(route('admin.vendors.index'));
    $this->assertDatabaseHas('vendors', ['email' => 'akun@cahayacatering.test']);
    $this->assertDatabaseHas('users', [
        'email' => 'akun@cahayacatering.test',
        'role'  => 'vendor',
    ]);
});

test('admin cannot create vendor with duplicate email for account', function () {
    User::factory()->create(['email' => 'exist@test.com', 'role' => 'client']);

    $response = $this->actingAs($this->admin)->post(route('admin.vendors.store'), [
        'nama_vendor'  => 'Test Vendor',
        'jenis_vendor' => 'Katering',
        'email'        => 'exist@test.com',
        'password'     => 'rahasia123',
    ]);

    $response->assertSessionHasErrors('email');
});

// ─── Update Vendor ──────────────────────────────────────────

test('admin can update vendor', function () {
    $vendor = Vendor::factory()->create(['nama_vendor' => 'Old Name']);

    $response = $this->actingAs($this->admin)->put(route('admin.vendors.update', $vendor), [
        'nama_vendor'  => 'New Name',
        'jenis_vendor' => 'Dekorasi',
        'email'        => 'new@email.test',
        'alamat'       => 'Jakarta',
    ]);

    $response->assertRedirect(route('admin.vendors.index'));
    $this->assertDatabaseHas('vendors', [
        'id'          => $vendor->id,
        'nama_vendor' => 'New Name',
    ]);
});

// ─── Delete Vendor ──────────────────────────────────────────

test('admin can delete vendor', function () {
    $vendor = Vendor::factory()->create();

    $response = $this->actingAs($this->admin)->delete(route('admin.vendors.destroy', $vendor));

    $response->assertRedirect(route('admin.vendors.index'));
    $this->assertDatabaseMissing('vendors', ['id' => $vendor->id]);
});

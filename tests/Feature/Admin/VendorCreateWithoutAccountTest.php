<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorCreateWithoutAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_vendor_with_contact_email_without_login_account(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.vendors.store'), [
            'nama_vendor' => 'Cahaya Catering',
            'jenis_vendor' => 'Katering',
            'email' => 'halo@cahayacatering.test',
            'alamat' => 'Padang',
        ]);

        $response->assertRedirect(route('admin.vendors.index'));
        $this->assertDatabaseHas('vendors', [
            'nama_vendor' => 'Cahaya Catering',
            'email' => 'halo@cahayacatering.test',
            'user_id' => null,
        ]);
        $this->assertDatabaseMissing('users', ['email' => 'halo@cahayacatering.test']);
    }
}

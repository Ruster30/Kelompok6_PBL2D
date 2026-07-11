<?php

use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '081234567890',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    // Pastikan user berhasil dibuat
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'phone' => '081234567890',
    ]);

    // Setelah registrasi, user belum login
    $this->assertGuest();

    // Harus diarahkan ke halaman login
    $response->assertRedirect(route('login'));
});
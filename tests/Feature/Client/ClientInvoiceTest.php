<?php

use App\Models\User;
use App\Models\Event;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

//
// WHITEBOX TEST: Client Invoices & Payments
// File: app/Http/Controllers/Client/ClientController.php
// File: app/Services/ClientService.php
// Routes: /client/invoices/...
//

beforeEach(function () {
    Storage::fake('public');
});

test('invoices page loads with summary data', function () {
    $client = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($client);
    $response = $this->get(route('client.invoices'));
    $response->assertOk();
    $response->assertSee('Tagihan');
});

test('invoices shows empty state', function () {
    $client = User::factory()->create(['role' => 'client']);

    $this->actingAs($client);
    $response = $this->get(route('client.invoices'));
    $response->assertOk();
    $response->assertSee('Belum ada tagihan');
});

test('client can upload payment proof', function () {
    $client = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);
    $event = Event::factory()->withClient($client)->withPic($admin)->create();

    $invoice = Invoice::factory()->create([
        'event_id' => $event->id,
        'total_invoice' => 50000000,
        'status_invoice' => 'belum_bayar',
    ]);

    $file = UploadedFile::fake()->image('bukti.jpg', 200, 200);

    $this->actingAs($client);
    $response = $this->post(route('client.invoices.bayar', $invoice->id), [
        'nominal' => 50000000,
        'bukti_pembayaran' => $file,
    ]);
    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('payments', [
        'invoice_id' => $invoice->id,
        'nominal' => 50000000,
        'status_pembayaran' => 'menunggu',
    ]);

    Storage::disk('public')->assertExists('payments/' . $file->hashName());
});

test('client cannot pay already lunas invoice', function () {
    $client = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);
    $event = Event::factory()->withClient($client)->withPic($admin)->create();

    $invoice = Invoice::factory()->create([
        'event_id' => $event->id,
        'total_invoice' => 50000000,
        'status_invoice' => 'lunas',
    ]);

    $file = UploadedFile::fake()->image('bukti.jpg');

    $this->actingAs($client);
    $response = $this->post(route('client.invoices.bayar', $invoice->id), [
        'nominal' => 50000000,
        'bukti_pembayaran' => $file,
    ]);
    $response->assertRedirect();
    $response->assertSessionHas('error', 'Invoice ini sudah lunas.');
});

test('client cannot pay invoice in menunggu_verifikasi status', function () {
    $client = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);
    $event = Event::factory()->withClient($client)->withPic($admin)->create();

    $invoice = Invoice::factory()->create([
        'event_id' => $event->id,
        'status_invoice' => 'menunggu_verifikasi',
    ]);

    $file = UploadedFile::fake()->image('bukti.jpg');

    $this->actingAs($client);
    $response = $this->post(route('client.invoices.bayar', $invoice->id), [
        'nominal' => 50000000,
        'bukti_pembayaran' => $file,
    ]);
    $response->assertRedirect();
    $response->assertSessionHas('error', 'Bukti pembayaran sebelumnya masih menunggu verifikasi admin.');
});

test('payment upload validates file type', function () {
    $client = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);
    $event = Event::factory()->withClient($client)->withPic($admin)->create();
    $invoice = Invoice::factory()->create([
        'event_id' => $event->id,
        'status_invoice' => 'belum_bayar',
    ]);

    $file = UploadedFile::fake()->create('document.txt', 100);

    $this->actingAs($client);
    $response = $this->post(route('client.invoices.bayar', $invoice->id), [
        'nominal' => 100000,
        'bukti_pembayaran' => $file,
    ]);
    $response->assertSessionHasErrors(['bukti_pembayaran']);
});

test('payment upload validates file size (max 5MB)', function () {
    $client = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);
    $event = Event::factory()->withClient($client)->withPic($admin)->create();
    $invoice = Invoice::factory()->create([
        'event_id' => $event->id,
        'status_invoice' => 'belum_bayar',
    ]);

    $file = UploadedFile::fake()->create('bukti.pdf', 6000);

    $this->actingAs($client);
    $response = $this->post(route('client.invoices.bayar', $invoice->id), [
        'nominal' => 100000,
        'bukti_pembayaran' => $file,
    ]);
    $response->assertSessionHasErrors(['bukti_pembayaran']);
});

test('client cannot pay invoice belonging to another client', function () {
    $clientA = User::factory()->create(['role' => 'client']);
    $clientB = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);

    $eventB = Event::factory()->withClient($clientB)->withPic($admin)->create();
    $invoiceB = Invoice::factory()->create([
        'event_id' => $eventB->id,
        'status_invoice' => 'belum_bayar',
    ]);

    $file = UploadedFile::fake()->image('bukti.jpg');

    $this->actingAs($clientA);
    $response = $this->post(route('client.invoices.bayar', $invoiceB->id), [
        'nominal' => 50000000,
        'bukti_pembayaran' => $file,
    ]);
    $response->assertStatus(404);
});

test('payment upload validates nominal field', function () {
    $client = User::factory()->create(['role' => 'client']);
    $admin = User::factory()->create(['role' => 'admin']);
    $event = Event::factory()->withClient($client)->withPic($admin)->create();
    $invoice = Invoice::factory()->create([
        'event_id' => $event->id,
        'status_invoice' => 'belum_bayar',
    ]);

    $file = UploadedFile::fake()->image('bukti.jpg');

    $this->actingAs($client);
    $response = $this->post(route('client.invoices.bayar', $invoice->id), [
        'nominal' => 0,
        'bukti_pembayaran' => $file,
    ]);
    $response->assertSessionHasErrors(['nominal']);
});

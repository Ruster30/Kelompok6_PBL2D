<?php

use App\Models\User;
use App\Models\Event;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentScheme;
use App\Models\Rab;
use App\Services\AdminPaymentService;
use App\Services\ClientService;
use App\Services\PaymentSchemeService;
use App\Services\RabService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

//
// COMPREHENSIVE PAYMENT FLOW TESTS
//
// Menguji seluruh skenario pembayaran:
// 1. Full Payment (Langsung Lunas)
// 2. DP + Pelunasan
//
// Urutan: Generate Invoice → Bayar → Verifikasi → Kirim Kwitansi → Generate Invoice Pelunasan
//

beforeEach(function () {
    Storage::fake('public');
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->client = User::factory()->create(['role' => 'client']);

    $this->rabService = app(RabService::class);
    $this->paymentSchemeService = app(PaymentSchemeService::class);
    $this->adminPaymentService = app(AdminPaymentService::class);
    $this->clientService = app(ClientService::class);
});

// ── Helper ──────────────────────────────────────────────────

function createRabWithTotal(Event $event, float $total): void
{
    Rab::factory()->create([
        'event_id' => $event->id,
        'jumlah_item' => 1,
        'harga_satuan' => $total,
        'subtotal_biaya' => $total,
    ]);
}

function getFirstPayment(Invoice $invoice): Payment
{
    return Payment::where('invoice_id', $invoice->id)->first();
}

// ══════════════════════════════════════════════════════════════
// TEST 1: FULL PAYMENT FLOW
// ══════════════════════════════════════════════════════════════

describe('Full Payment Flow', function () {

    test('1.1 Full Payment: Generate Invoice', function () {
        $event = Event::factory()->withClient($this->client)->withPic($this->admin)->create();
        createRabWithTotal($event, 10000000);

        // Set payment scheme to full_payment
        $this->paymentSchemeService->saveScheme($event->id, [
            'jenis_pembayaran' => 'full_payment',
        ]);

        $invoices = Invoice::where('event_id', $event->id)->get();
        expect($invoices)->toHaveCount(1);
        expect($invoices[0]->total_invoice)->toEqual(10000000.0);
        expect($invoices[0]->status_invoice)->toEqual('belum_bayar');
    });

    test('1.2 Full Payment: Client Bayar', function () {
        $event = Event::factory()->withClient($this->client)->withPic($this->admin)->create();
        createRabWithTotal($event, 10000000);

        $this->paymentSchemeService->saveScheme($event->id, [
            'jenis_pembayaran' => 'full_payment',
        ]);

        $invoice = Invoice::where('event_id', $event->id)->first();
        $file = UploadedFile::fake()->image('bukti.jpg');

        $this->actingAs($this->client);
        $response = $this->post(route('client.invoices.bayar', $invoice->id), [
            'nominal' => 10000000,
            'bukti_pembayaran' => $file,
        ]);
        $response->assertSessionHas('success');
        $response->assertRedirect();

        // Verifikasi payment record
        $payment = getFirstPayment($invoice);
        expect($payment->jenis_pembayaran)->toEqual('pelunasan');
        expect($payment->status_pembayaran)->toEqual('menunggu');
        expect($payment->nominal)->toEqual(10000000);

        // Invoice status berubah
        $invoice->refresh();
        expect($invoice->status_invoice)->toEqual('menunggu_verifikasi');
    });

    test('1.3 Full Payment: Admin Verifikasi', function () {
        $event = Event::factory()->withClient($this->client)->withPic($this->admin)->create();
        createRabWithTotal($event, 10000000);

        $this->paymentSchemeService->saveScheme($event->id, [
            'jenis_pembayaran' => 'full_payment',
        ]);

        $this->actingAs($this->client);
        $invoice = Invoice::where('event_id', $event->id)->first();
        $file = UploadedFile::fake()->image('bukti.jpg');
        $this->post(route('client.invoices.bayar', $invoice->id), [
            'nominal' => 10000000,
            'bukti_pembayaran' => $file,
        ]);

        // Admin verifikasi
        $payment = getFirstPayment($invoice);
        $this->adminPaymentService->verifyPayment($payment, 'diverifikasi');

        // Cek status setelah verifikasi
        $invoice->refresh();
        $event->refresh();

        expect($invoice->status_invoice)->toEqual('lunas');
        expect($event->status_event)->toEqual('selesai');
        expect($event->status_pembayaran)->toEqual('lunas');

        $payment->refresh();
        expect($payment->status_pembayaran)->toEqual('diverifikasi');
    });

});

// ══════════════════════════════════════════════════════════════
// TEST 2: DP + PELUNASAN FLOW
// ══════════════════════════════════════════════════════════════

describe('DP + Pelunasan Flow', function () {

    test('2.1 DP + Pelunasan: Generate Invoice DP', function () {
        $event = Event::factory()->withClient($this->client)->withPic($this->admin)->create();
        createRabWithTotal($event, 50000000);

        // Set payment scheme to dp_dan_pelunasan (DP 30%)
        $this->paymentSchemeService->saveScheme($event->id, [
            'jenis_pembayaran' => 'dp_dan_pelunasan',
            'mode_dp' => 'persentase',
            'persentase_dp' => 30,
        ]);

        $invoices = Invoice::where('event_id', $event->id)->get();
        expect($invoices)->toHaveCount(1);
        expect($invoices[0]->total_invoice)->toEqual(15000000.0); // 30% dari 50jt
        expect($invoices[0]->status_invoice)->toEqual('belum_bayar');
    });

    test('2.2 DP + Pelunasan: Client Bayar DP', function () {
        $event = Event::factory()->withClient($this->client)->withPic($this->admin)->create();
        createRabWithTotal($event, 50000000);

        $this->paymentSchemeService->saveScheme($event->id, [
            'jenis_pembayaran' => 'dp_dan_pelunasan',
            'mode_dp' => 'persentase',
            'persentase_dp' => 30,
        ]);

        $invoice = Invoice::where('event_id', $event->id)->first();
        $file = UploadedFile::fake()->image('bukti_dp.jpg');

        $this->actingAs($this->client);
        $response = $this->post(route('client.invoices.bayar', $invoice->id), [
            'nominal' => 15000000,
            'bukti_pembayaran' => $file,
        ]);
        $response->assertSessionHas('success');

        // ⭐ INI YANG DIUJI: apakah DP terdeteksi sebagai 'dp'?
        $payment = getFirstPayment($invoice);
        expect($payment->jenis_pembayaran)->toEqual('dp');
        expect($payment->status_pembayaran)->toEqual('menunggu');
        expect($payment->nominal)->toEqual(15000000);

        $invoice->refresh();
        expect($invoice->status_invoice)->toEqual('menunggu_verifikasi');
    });

    test('2.3 DP + Pelunasan: Admin Verifikasi DP', function () {
        $event = Event::factory()->withClient($this->client)->withPic($this->admin)->create();
        createRabWithTotal($event, 50000000);

        $this->paymentSchemeService->saveScheme($event->id, [
            'jenis_pembayaran' => 'dp_dan_pelunasan',
            'mode_dp' => 'persentase',
            'persentase_dp' => 30,
        ]);

        $this->actingAs($this->client);
        $invoice = Invoice::where('event_id', $event->id)->first();
        $file = UploadedFile::fake()->image('bukti_dp.jpg');
        $this->post(route('client.invoices.bayar', $invoice->id), [
            'nominal' => 15000000,
            'bukti_pembayaran' => $file,
        ]);

        // Admin verifikasi DP
        $payment = getFirstPayment($invoice);
        $this->adminPaymentService->verifyPayment($payment, 'diverifikasi');

        $invoice->refresh();
        $event->refresh();

        expect($invoice->status_invoice)->toEqual('dp_lunas');
        expect($event->status_event)->not->toEqual('selesai'); // Event belum selesai
        expect($event->status_pembayaran)->toEqual('belum_lunas'); // Masih DP

        $payment->refresh();
        expect($payment->status_pembayaran)->toEqual('diverifikasi');
        expect($payment->jenis_pembayaran)->toEqual('dp');
    });

    test('2.4 DP + Pelunasan: Admin Generate Invoice Pelunasan', function () {
        $event = Event::factory()->withClient($this->client)->withPic($this->admin)->create();
        createRabWithTotal($event, 50000000);

        $this->paymentSchemeService->saveScheme($event->id, [
            'jenis_pembayaran' => 'dp_dan_pelunasan',
            'mode_dp' => 'persentase',
            'persentase_dp' => 30,
        ]);

        // Client bayar DP
        $this->actingAs($this->client);
        $invoiceDP = Invoice::where('event_id', $event->id)->first();
        $this->post(route('client.invoices.bayar', $invoiceDP->id), [
            'nominal' => 15000000,
            'bukti_pembayaran' => UploadedFile::fake()->image('bukti_dp.jpg'),
        ]);

        // Admin verifikasi DP
        $paymentDP = getFirstPayment($invoiceDP);
        $this->adminPaymentService->verifyPayment($paymentDP, 'diverifikasi');

        // Admin generate invoice pelunasan
        $canSend = $this->adminPaymentService->canSendPelunasan($paymentDP);
        expect($canSend)->toBeNull(); // Harusnya bisa dikirim

        $this->adminPaymentService->sendPelunasan($paymentDP);

        // Cek invoice pelunasan terbuat
        $invoices = Invoice::where('event_id', $event->id)->orderBy('id')->get();
        expect($invoices)->toHaveCount(2);

        $invoicePelunasan = $invoices->last();
        expect($invoicePelunasan->total_invoice)->toEqual(35000000.0); // 70% dari 50jt (sisa)
        expect($invoicePelunasan->status_invoice)->toEqual('belum_bayar');
    });

    test('2.5 DP + Pelunasan: Client Bayar Pelunasan', function () {
        $event = Event::factory()->withClient($this->client)->withPic($this->admin)->create();
        createRabWithTotal($event, 50000000);

        $this->paymentSchemeService->saveScheme($event->id, [
            'jenis_pembayaran' => 'dp_dan_pelunasan',
            'mode_dp' => 'persentase',
            'persentase_dp' => 30,
        ]);

        // Flow: Bayar DP → Verifikasi DP → Generate Pelunasan
        $this->actingAs($this->client);
        $invoiceDP = Invoice::where('event_id', $event->id)->first();
        $this->post(route('client.invoices.bayar', $invoiceDP->id), [
            'nominal' => 15000000,
            'bukti_pembayaran' => UploadedFile::fake()->image('bukti_dp.jpg'),
        ]);
        $paymentDP = getFirstPayment($invoiceDP);
        $this->adminPaymentService->verifyPayment($paymentDP, 'diverifikasi');
        $this->adminPaymentService->sendPelunasan($paymentDP);

        // Client bayar pelunasan
        $invoicePelunasan = Invoice::where('event_id', $event->id)->orderBy('id', 'desc')->first();
        $file = UploadedFile::fake()->image('bukti_pelunasan.jpg');

        $response = $this->post(route('client.invoices.bayar', $invoicePelunasan->id), [
            'nominal' => 35000000,
            'bukti_pembayaran' => $file,
        ]);
        $response->assertSessionHas('success');

        $paymentPelunasan = Payment::where('invoice_id', $invoicePelunasan->id)->first();
        expect($paymentPelunasan->jenis_pembayaran)->toEqual('pelunasan');
        expect($paymentPelunasan->status_pembayaran)->toEqual('menunggu');
        expect($paymentPelunasan->nominal)->toEqual(35000000);

        $invoicePelunasan->refresh();
        expect($invoicePelunasan->status_invoice)->toEqual('menunggu_verifikasi');
    });

    test('2.6 DP + Pelunasan: Admin Verifikasi Pelunasan', function () {
        $event = Event::factory()->withClient($this->client)->withPic($this->admin)->create();
        createRabWithTotal($event, 50000000);

        $this->paymentSchemeService->saveScheme($event->id, [
            'jenis_pembayaran' => 'dp_dan_pelunasan',
            'mode_dp' => 'persentase',
            'persentase_dp' => 30,
        ]);

        // Flow lengkap sampai bayar pelunasan
        $this->actingAs($this->client);
        $invoiceDP = Invoice::where('event_id', $event->id)->first();
        $this->post(route('client.invoices.bayar', $invoiceDP->id), [
            'nominal' => 15000000,
            'bukti_pembayaran' => UploadedFile::fake()->image('bukti_dp.jpg'),
        ]);
        $paymentDP = getFirstPayment($invoiceDP);
        $this->adminPaymentService->verifyPayment($paymentDP, 'diverifikasi');
        $this->adminPaymentService->sendPelunasan($paymentDP);

        $invoicePelunasan = Invoice::where('event_id', $event->id)->orderBy('id', 'desc')->first();
        $this->post(route('client.invoices.bayar', $invoicePelunasan->id), [
            'nominal' => 35000000,
            'bukti_pembayaran' => UploadedFile::fake()->image('bukti_pelunasan.jpg'),
        ]);

        // Admin verifikasi pelunasan
        $paymentPelunasan = Payment::where('invoice_id', $invoicePelunasan->id)->first();
        $this->adminPaymentService->verifyPayment($paymentPelunasan, 'diverifikasi');

        $invoicePelunasan->refresh();
        $event->refresh();

        expect($invoicePelunasan->status_invoice)->toEqual('lunas');
        expect($event->status_event)->toEqual('selesai');
        expect($event->status_pembayaran)->toEqual('lunas');

        // Invoice DP tetap dp_lunas
        $invoiceDP->refresh();
        expect($invoiceDP->status_invoice)->toEqual('dp_lunas');
    });

    test('2.7 DP + Pelunasan: canSendPelunasan mencegah duplikasi', function () {
        $event = Event::factory()->withClient($this->client)->withPic($this->admin)->create();
        createRabWithTotal($event, 50000000);

        $this->paymentSchemeService->saveScheme($event->id, [
            'jenis_pembayaran' => 'dp_dan_pelunasan',
            'mode_dp' => 'persentase',
            'persentase_dp' => 30,
        ]);

        // Flow sampai generate pelunasan
        $this->actingAs($this->client);
        $invoiceDP = Invoice::where('event_id', $event->id)->first();
        $this->post(route('client.invoices.bayar', $invoiceDP->id), [
            'nominal' => 15000000,
            'bukti_pembayaran' => UploadedFile::fake()->image('bukti_dp.jpg'),
        ]);
        $paymentDP = getFirstPayment($invoiceDP);
        $this->adminPaymentService->verifyPayment($paymentDP, 'diverifikasi');
        $this->adminPaymentService->sendPelunasan($paymentDP);

        // Coba kirim pelunasan lagi — harusnya ditolak
        $canSend = $this->adminPaymentService->canSendPelunasan($paymentDP);
        expect($canSend)->not->toBeNull();
        expect($canSend)->toContain('Invoice pelunasan sudah ada');
    });

});

// ══════════════════════════════════════════════════════════════
// TEST 3: REGRESI — Full Payment Tidak Terpengaruh
// ══════════════════════════════════════════════════════════════

describe('Regresi: Full Payment tetap berjalan normal', function () {

    test('3.1 Full Payment: jenis_pembayaran tetap pelunasan', function () {
        $event = Event::factory()->withClient($this->client)->withPic($this->admin)->create();
        createRabWithTotal($event, 25000000);

        $this->paymentSchemeService->saveScheme($event->id, [
            'jenis_pembayaran' => 'full_payment',
        ]);

        $this->actingAs($this->client);
        $invoice = Invoice::where('event_id', $event->id)->first();
        $this->post(route('client.invoices.bayar', $invoice->id), [
            'nominal' => 25000000,
            'bukti_pembayaran' => UploadedFile::fake()->image('bukti.jpg'),
        ]);

        $payment = getFirstPayment($invoice);
        expect($payment->jenis_pembayaran)->toEqual('pelunasan');
    });

    test('3.2 Full Payment: status_pembayaran terupdate lunas', function () {
        $event = Event::factory()->withClient($this->client)->withPic($this->admin)->create();
        createRabWithTotal($event, 25000000);

        $this->paymentSchemeService->saveScheme($event->id, [
            'jenis_pembayaran' => 'full_payment',
        ]);

        $this->actingAs($this->client);
        $invoice = Invoice::where('event_id', $event->id)->first();
        $this->post(route('client.invoices.bayar', $invoice->id), [
            'nominal' => 25000000,
            'bukti_pembayaran' => UploadedFile::fake()->image('bukti.jpg'),
        ]);

        $payment = getFirstPayment($invoice);
        $this->adminPaymentService->verifyPayment($payment, 'diverifikasi');

        $event->refresh();
        expect($event->status_pembayaran)->toEqual('lunas');
    });

});


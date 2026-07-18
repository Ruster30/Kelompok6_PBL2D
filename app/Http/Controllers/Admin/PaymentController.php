<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VerifyPaymentRequest;
use App\Models\Payment;
use App\Services\AdminPaymentService;
use App\Services\DocumentBuilderService;

class PaymentController extends Controller
{
    public function __construct(
        private AdminPaymentService $paymentService,
        private DocumentBuilderService $documentBuilderService,
    ) {}

    public function index()
    {
        return view('admin.payments.index', $this->paymentService->getIndexData());
    }

    public function show(Payment $payment)
    {
        return view('admin.payments.show', $this->paymentService->getShowData($payment));
    }

    public function verify(VerifyPaymentRequest $request, Payment $payment)
    {
        $this->paymentService->verifyPayment($payment, $request->status_pembayaran);

        $label = $request->status_pembayaran === 'diverifikasi' ? 'diterima' : 'ditolak';

        return back()->with('success', 'Pembayaran berhasil ' . $label . '.');
    }

    public function sendPelunasan(Payment $payment)
    {
        $error = $this->paymentService->canSendPelunasan($payment);

        if ($error) {
            return back()->with('error', $error);
        }

        $this->paymentService->sendPelunasan($payment);

        return back()->with('success', 'Invoice Pelunasan berhasil dibuat dan dikirim ke Client.');
    }

    /**
     * Kirim Kwitansi ke Client secara manual.
     * Generate kwitansi PDF, simpan ke storage & documents, kirim notifikasi ke client.
     */
    public function sendKwitansi(Payment $payment)
    {
        $payment->load("invoice.event");

        if (!$payment->invoice || !$payment->invoice->event) {
            return back()->with('error', 'Data pembayaran tidak valid.');
        }

        $event = $payment->invoice->event;

        // Generate & simpan kwitansi
        $this->documentBuilderService->generateAndSaveKwitansi($event);

        // Kirim notifikasi ke client
        if ($event->client) {
            \App\Models\Notification::create([
                'user_id' => $event->client_id,
                'judul'   => 'Kwitansi Tersedia',
                'pesan'   => 'Kwitansi untuk event "' . $event->nama_event . '" telah diterbitkan. Silakan lihat di menu Dokumen - Kwitansi.',
                'tipe'    => 'info',
                'dibaca'  => false,
            ]);
        }

        return back()->with('success', 'Kwitansi berhasil dibuat dan dikirim ke Client.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VerifyPaymentRequest;
use App\Models\Payment;
use App\Services\AdminPaymentService;

class PaymentController extends Controller
{
    public function __construct(
        private AdminPaymentService $paymentService,
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
}

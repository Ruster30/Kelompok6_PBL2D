<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['invoice.event'])
            ->latest()
            ->paginate(15);

        return view('admin.payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        $payment->load('invoice.event');
        return view('admin.payments.show', compact('payment'));
    }

    public function verify(Request $request, Payment $payment)
    {
        $request->validate([
            'status_pembayaran' => 'required|in:diverifikasi,ditolak',
        ]);

        $payment->update(['status_pembayaran' => $request->status_pembayaran]);

        // Jika diverifikasi & jenis_pembayaran = pelunasan, update invoice jadi lunas
        if ($request->status_pembayaran === 'diverifikasi' && $payment->jenis_pembayaran === 'pelunasan') {
            $payment->invoice?->update(['status_invoice' => 'lunas']);
        }

        $label = $request->status_pembayaran === 'diverifikasi' ? 'diverifikasi' : 'ditolak';
        return back()->with('success', "Pembayaran berhasil {$label}.");
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        DB::transaction(function () use ($request, $payment) {
            $payment->update(['status_pembayaran' => $request->status_pembayaran]);

            if ($request->status_pembayaran === 'diverifikasi') {
                if ($payment->jenis_pembayaran === 'dp') {
                    $payment->invoice?->update(['status_invoice' => 'dp_lunas']);
                } else {
                    $payment->invoice?->update(['status_invoice' => 'lunas']);
                    $payment->invoice?->event?->update(['status_event' => 'selesai']);
                }
                return;
            }

            $payment->invoice?->update(['status_invoice' => 'ditolak']);
        });

        $label = $request->status_pembayaran === 'diverifikasi' ? 'diterima' : 'ditolak';
        return back()->with('success', "Pembayaran berhasil {$label}.");
    }

    public function sendPelunasan(Payment $payment)
    {
        $payment->load('invoice.event');
        $invoice = $payment->invoice;
        $event = $invoice->event;

        if ($payment->jenis_pembayaran !== 'dp' || $payment->status_pembayaran !== 'diverifikasi') {
            return back()->with('error', 'Hanya pembayaran DP yang sudah diverifikasi yang dapat dibuatkan invoice pelunasan.');
        }

        if ($event->invoices()->count() > 1) {
            return back()->with('error', 'Invoice pelunasan sudah pernah dibuat untuk event ini.');
        }

        $sisaPembayaran = max(0, $invoice->total_invoice - $payment->nominal);

        DB::transaction(function () use ($event, $sisaPembayaran) {
            $newInvoice = \App\Models\Invoice::create([
                'event_id' => $event->id,
                'nomor_invoice' => sprintf('INV-%s-%03d', now()->format('Ymd'), \App\Models\Invoice::whereDate('created_at', today())->count() + 1),
                'total_invoice' => $sisaPembayaran,
                'status_invoice' => 'belum_bayar',
                'tanggal_invoice' => now()->toDateString(),
            ]);

            $documentBuilder = new \App\Services\DocumentBuilderService();
            // This will generate the invoice using the newly created one because it is the latest
            $documentBuilder->sendToClient($event, 'invoice');
        });

        return back()->with('success', 'Invoice Pelunasan berhasil dibuat dan dikirim ke Client.');
    }
}

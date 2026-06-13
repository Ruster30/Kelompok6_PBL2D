<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        return view('admin.payments.index', [
            'payments' => Payment::latest()->paginate(15),
        ]);
    }

    public function show(Payment $payment)
    {
        return view('admin.payments.show', compact('payment'));
    }

    public function verify(Request $request, Payment $payment)
    {
        $request->validate(['status' => 'required|in:lunas,ditolak']);
        $payment->update(['status' => $request->status]);
        $label = $request->status === 'lunas' ? 'diverifikasi' : 'ditolak';
        return back()->with('success', "Pembayaran berhasil {$label}.");
    }
}

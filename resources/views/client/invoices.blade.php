{{-- resources/views/client/invoices.blade.php --}}
@extends('layouts.client')
@section('title', 'Anggaran & Faktur')
@section('page-title', 'Anggaran & Faktur')

@section('content')

<div class="page-header">
    <h1 style="font-size:26px; font-weight:800; color:var(--dark); margin-bottom:4px;">Tagihan & Pembayaran</h1>
    <p style="color:var(--text-muted);">Kelola tagihan dan unggah bukti pembayaran Anda</p>
</div>

<div class="invoice-table-wrap">
    <table class="invoice-table">
        <thead>
            <tr>
                <th>No. Tagihan</th>
                <th>Event</th>
                <th>Tanggal</th>
                <th>Total</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="6">
                    <div class="invoice-empty">
                        <i class="bi bi-receipt"></i>
                        Belum ada tagihan
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

@endsection
@extends('layouts.admin')

@section('title', 'Pembayaran')
@section('page-title', 'Pembayaran')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Verifikasi Pembayaran</h1>
        <p>Kelola dan verifikasi bukti pembayaran dari klien</p>
    </div>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Tagihan</th>
                <th>Klien ID</th>
                <th>Jumlah</th>
                <th>Status</th>
                <th>Bukti</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
            <tr>
                <td>{{ \Carbon\Carbon::parse($payment->created_at)->format('d M Y') }}</td>
                <td style="font-weight:500;">{{ $payment->invoice_number ?? 'INV-' . str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $payment->client_id }}</td>
                <td style="font-weight:600;">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                <td>
                    @php
                        $map = ['pending'=>'badge-pending','lunas'=>'badge-active','ditolak'=>'badge-cancel','dp'=>'badge-done'];
                        $cls = $map[strtolower($payment->status)] ?? 'badge-pending';
                    @endphp
                    <span class="badge {{ $cls }}">{{ ucfirst($payment->status) }}</span>
                </td>
                <td>
                    @if($payment->proof_file)
                    <a href="{{ asset('storage/'.$payment->proof_file) }}" target="_blank" class="btn btn-outline btn-sm">
                        <i class="fas fa-image"></i> Lihat
                    </a>
                    @else
                    <span style="color:#94a3b8; font-size:13px;">-</span>
                    @endif
                </td>
                <td>
                    <div class="action-btns">
                        @if($payment->status === 'pending')
                        <form action="{{ route('admin.payments.verify', $payment->id) }}" method="POST" style="display:inline;">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="lunas">
                            <button type="submit" class="btn btn-primary btn-sm" title="Verifikasi">
                                <i class="fas fa-check"></i> Verifikasi
                            </button>
                        </form>
                        <form action="{{ route('admin.payments.verify', $payment->id) }}" method="POST" style="display:inline;"
                              onsubmit="return confirm('Tolak pembayaran ini?')">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="ditolak">
                            <button type="submit" class="action-btn danger" title="Tolak">
                                <i class="fas fa-times" style="font-size:12px;"></i>
                            </button>
                        </form>
                        @else
                        <a href="{{ route('admin.payments.show', $payment->id) }}" class="action-btn" title="Detail">
                            <i class="fas fa-eye" style="font-size:12px;"></i>
                        </a>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr class="empty-row"><td colspan="7">Belum ada data pembayaran</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($payments->hasPages())
    <div style="padding:16px 24px; border-top:1px solid #f1f5f9;">
        {{ $payments->links() }}
    </div>
    @endif
</div>
@endsection

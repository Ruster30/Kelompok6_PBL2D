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
                <th>Invoice</th>
                <th>Event</th>
                <th>Jenis</th>
                <th>Nominal</th>
                <th>Status</th>
                <th>Bukti</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
            <tr>
                <td>{{ \Carbon\Carbon::parse($payment->tanggal_pembayaran)->format('d M Y') }}</td>
                <td style="font-weight:500;">{{ $payment->invoice->nomor_invoice ?? '-' }}</td>
                <td>{{ $payment->invoice->event->nama_event ?? '-' }}</td>
                <td>
                    <span class="badge {{ $payment->jenis_pembayaran === 'dp' ? 'badge-pending' : 'badge-done' }}">
                        {{ $payment->jenis_pembayaran === 'dp' ? 'DP' : 'Pelunasan' }}
                    </span>
                </td>
                <td style="font-weight:600;">Rp {{ number_format($payment->nominal, 0, ',', '.') }}</td>
                <td>
                    @php
                        $map = ['menunggu'=>'badge-pending','diverifikasi'=>'badge-active','ditolak'=>'badge-cancel'];
                        $cls = $map[$payment->status_pembayaran] ?? 'badge-pending';
                        $labels = ['menunggu'=>'Menunggu Verifikasi','diverifikasi'=>'Verified','ditolak'=>'Ditolak'];
                    @endphp
                    <span class="badge {{ $cls }}">{{ $labels[$payment->status_pembayaran] }}</span>
                </td>
                <td>
                    @if($payment->bukti_pembayaran)
                    <a href="{{ asset('storage/' . $payment->bukti_pembayaran) }}" target="_blank" class="btn btn-outline btn-sm">
                        <i class="fas fa-image"></i> Lihat
                    </a>
                    @else
                    <span style="color:#94a3b8; font-size:13px;">-</span>
                    @endif
                </td>
                <td>
                    @if($payment->status_pembayaran === 'menunggu')
                    <div class="action-btns">
                        <form action="{{ route('admin.payments.verify', $payment->id) }}" method="POST" style="display:inline;">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status_pembayaran" value="diverifikasi">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-check"></i> Terima
                            </button>
                        </form>
                        <form action="{{ route('admin.payments.verify', $payment->id) }}" method="POST" style="display:inline;"
                              onsubmit="return confirm('Tolak pembayaran ini?')">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status_pembayaran" value="ditolak">
                            <button type="submit" class="action-btn danger" title="Tolak">
                                <i class="fas fa-times" style="font-size:12px;"></i>
                            </button>
                        </form>
                    </div>
                    @else
                    <div class="action-btns">
                        <a href="{{ route('admin.payments.show', $payment->id) }}" class="action-btn" title="Detail">
                            <i class="fas fa-eye" style="font-size:12px;"></i>
                        </a>
                        @if($payment->status_pembayaran === 'diverifikasi' && $payment->jenis_pembayaran === 'dp' && $payment->invoice->event->invoices()->count() == 1)
                        <form action="{{ route('admin.payments.sendPelunasan', $payment->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-outline btn-sm" style="margin-left:5px;" title="Kirim Invoice Pelunasan">
                                <i class="fas fa-file-invoice"></i> Kirim Pelunasan
                            </button>
                        </form>
                        @endif
                    </div>
                    @endif
                </td>
            </tr>
            @empty
            <tr class="empty-row"><td colspan="8">Belum ada data pembayaran</td></tr>
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

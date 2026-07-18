@extends('layouts.admin')

@section('title', 'Pembayaran')
@section('page-title', 'Pembayaran')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Verifikasi Pembayaran</h1>
        <p>Kelola dan verifikasi bukti pembayaran dari klien</p>
    </div>
    <div style="display:flex; align-items:center; gap:10px;">
        <a href="{{ route('admin.payments.index', array_merge(request()->query(), ['export' => 'pdf'])) }}" class="btn btn-outline" style="padding:8px 16px;">
            <i class="fas fa-download"></i> Export
        </a>
    </div>
</div>

@php
    $totalPayments = $payments->total();
    $totalVerified = $payments->filter(fn($p) => $p->status_pembayaran === 'diverifikasi')->count();
    $totalPending = $payments->filter(fn($p) => $p->status_pembayaran === 'menunggu')->count();
    $totalRejected = $payments->filter(fn($p) => $p->status_pembayaran === 'ditolak')->count();
    $totalNominal = $payments->sum('nominal');
@endphp

{{-- Stats Cards --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-top"><i class="fas fa-credit-card stat-icon"></i><span class="stat-badge">Total</span></div>
        <div class="stat-value">{{ $totalPayments }}</div>
        <div class="stat-label">Total Pembayaran</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-top"><i class="fas fa-check-circle stat-icon"></i><span class="stat-badge green">Verified</span></div>
        <div class="stat-value" style="color:#16a34a;">{{ $totalVerified }}</div>
        <div class="stat-label">Diverifikasi</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-top"><i class="fas fa-clock stat-icon"></i><span class="stat-badge blue">Pending</span></div>
        <div class="stat-value" style="color:#f59e0b;">{{ $totalPending }}</div>
        <div class="stat-label">Menunggu Verifikasi</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-top"><i class="fas fa-times-circle stat-icon"></i><span class="stat-badge red">Ditolak</span></div>
        <div class="stat-value" style="color:#dc2626;">{{ $totalRejected }}</div>
        <div class="stat-label">Ditolak</div>
    </div>
</div>

<div class="card">
    <div style="padding:16px 24px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between;">
        <span style="font-size:15px;font-weight:700;color:#0f172a;">Daftar Pembayaran</span>
        <span style="font-size:12px;color:#94a3b8;">Total: {{ $totalPayments }} transaksi</span>
    </div>
    <div class="table-responsive-wrap card-view-mobile"><table>
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
                <td data-label="Tanggal">{{ \Carbon\Carbon::parse($payment->tanggal_pembayaran)->format('d M Y') }}</td>
                <td data-label="Invoice" style="font-weight:500;">{{ $payment->invoice->nomor_invoice ?? '-' }}</td>
                <td data-label="Event">{{ $payment->invoice->event->nama_event ?? '-' }}</td>
                <td data-label="Jenis"><span class="badge {{ $payment->jenis_pembayaran === 'dp' ? 'badge-pending' : 'badge-done' }}">
                        {{ $payment->jenis_pembayaran === 'dp' ? 'DP' : 'Pelunasan' }}
                    </span>
                </td>
                <td data-label="Nominal" style="font-weight:600;">Rp {{ number_format($payment->nominal, 0, ',', '.') }}</td>
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
                    {{-- Status Menunggu: Tombol Terima/Tolak --}}
                    <div class="action-btns">
                        <form action="{{ route('admin.payments.verify', $payment->id) }}" method="POST" style="display:inline;"
                              onsubmit="return swalApprove(this, 'Terima Pembayaran?', 'Pembayaran akan diverifikasi.')">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status_pembayaran" value="diverifikasi">
                            <button type="submit" class="action-btn" title="Terima">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                        <form action="{{ route('admin.payments.verify', $payment->id) }}" method="POST" style="display:inline;"
                              onsubmit="return swalReject(this, 'Tolak Pembayaran?', 'Pembayaran akan ditolak.')">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status_pembayaran" value="ditolak">
                            <button type="submit" class="action-btn danger" title="Tolak">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                    </div>
                    @elseif($payment->status_pembayaran === 'diverifikasi')
                    {{-- Status Diverifikasi: 3 aksi (Kirim Kwitansi, Kirim Pelunasan, atau badge Selesai) --}}
                    <div class="action-btns">
                        {{-- Tombol Kirim Kwitansi --}}
                        <form action="{{ route('admin.payments.sendKwitansi', $payment->id) }}" method="POST" style="display:inline;"
                              onsubmit="return swalSend(this, 'Kirim Kwitansi?', 'Kwitansi akan dibuat dan dikirim ke client.')">
                            @csrf
                            <button type="submit" class="action-btn" title="Kirim Kwitansi">
                                <i class="fas fa-file-invoice"></i>
                            </button>
                        </form>

                        @if($payment->jenis_pembayaran === 'dp')
                            @if($payment->invoice->event->invoices()->whereIn('status_invoice', ['belum_bayar', 'terkirim', 'draft'])->count() == 0)
                            <form action="{{ route('admin.payments.sendPelunasan', $payment->id) }}" method="POST" style="display:inline;"
                                  onsubmit="return swalSend(this, 'Kirim Invoice Pelunasan?', 'Invoice pelunasan akan dikirim ke client.')">
                                @csrf
                                <button type="submit" class="action-btn" title="Kirim Pelunasan" style="color:#f59e0b;">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </button>
                            </form>
                            @else
                            <span class="action-btn" title="Pelunasan Terkirim" style="cursor:default;color:#16a34a;">
                                <i class="fas fa-check-circle"></i>
                            </span>
                            @endif
                        @else
                            <span class="action-btn" title="Pembayaran Selesai" style="cursor:default;color:#16a34a;">
                                <i class="fas fa-check-circle"></i>
                            </span>
                        @endif
                    </div>
                    @else
                    {{-- Status Ditolak atau lainnya: Tidak ada aksi --}}
                    <span style="color:#94a3b8;font-size:13px;">-</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr class="empty-row"><td colspan="8">
                    <div class="empty-state" style="padding:40px 20px;">
                        <div class="empty-state-icon"><i class="bi bi-credit-card" style="font-size:40px;"></i></div>
                        <h3 class="empty-state-title">Belum ada data pembayaran</h3>
                        <p class="empty-state-text">Pembayaran akan muncul setelah klien melakukan pembayaran.</p>
                    </div>
                </td></tr>
            @endforelse
        </tbody>
    </table></div>

    @if($payments->hasPages())
    <div style="padding:16px 24px; border-top:1px solid #f1f5f9;">
        {{ $payments->links() }}
    </div>
    @endif
</div>
@endsection







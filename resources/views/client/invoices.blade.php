@extends('layouts.client')
@section('title','Anggaran & Faktur')
@section('page-title','Anggaran & Faktur')

@section('content')

<div class="page-header">
    <h1 style="font-size:26px;font-weight:800;color:var(--dark);margin-bottom:4px;">Tagihan & Pembayaran</h1>
    <p style="color:var(--text-muted);">Kelola tagihan dan unggah bukti pembayaran Anda.</p>
</div>

{{-- Ringkasan keuangan --}}
<div class="stat-cards" style="margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-file-earmark-text"></i></div>
        <div class="stat-info">
            <div class="stat-number" style="font-size:20px;">
                Rp {{ number_format($totalInvoice,0,',','.') }}
            </div>
            <div class="stat-label">Total Invoice</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(34,197,94,.1);">
            <i class="bi bi-check-circle" style="color:#16a34a;"></i>
        </div>
        <div class="stat-info">
            <div class="stat-number" style="font-size:20px;">
                Rp {{ number_format($totalDibayar,0,',','.') }}
            </div>
            <div class="stat-label">Sudah Dibayar</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(239,68,68,.1);">
            <i class="bi bi-exclamation-circle" style="color:#dc2626;"></i>
        </div>
        <div class="stat-info">
            <div class="stat-number" style="font-size:20px;">
                Rp {{ number_format($sisaTagihan,0,',','.') }}
            </div>
            <div class="stat-label">Sisa Tagihan</div>
        </div>
    </div>
</div>

{{-- Tabel Invoice --}}
<div style="margin-bottom:28px;">
    <h3 style="font-size:17px;font-weight:700;color:var(--dark);margin-bottom:14px;">Daftar Invoice</h3>
    <div class="invoice-table-wrap">
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>No. Invoice</th>
                    <th>Event</th>
                    <th>Tanggal</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr>
                    <td style="font-weight:700;">{{ $inv->nomor_invoice }}</td>
                    <td>{{ $inv->event->nama_event ?? '-' }}</td>
                    <td>{{ $inv->tanggal_invoice->format('j M Y') }}</td>
                    <td style="font-weight:700;">{{ $inv->formatted_total }}</td>
                    <td>
                        <span class="badge {{ $inv->badge_class }}">
                            {{ ucfirst($inv->status_invoice) }}
                        </span>
                    </td>
                    <td>
                        @if($inv->status_invoice !== 'lunas')
                        <button onclick="openPayModal({{ $inv->event_id }})"
                                class="btn btn-accent btn-sm">
                            <i class="bi bi-credit-card"></i> Bayar
                        </button>
                        @else
                        <span style="color:var(--text-muted);font-size:12px;">Lunas</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="invoice-empty">
                            <i class="bi bi-receipt"></i>
                            Belum ada tagihan
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($invoices->hasPages())
    <div style="margin-top:16px;">{{ $invoices->links() }}</div>
    @endif
</div>

{{-- Riwayat Pembayaran --}}
<div>
    <h3 style="font-size:17px;font-weight:700;color:var(--dark);margin-bottom:14px;">
        Riwayat Pembayaran
    </h3>
    <div class="invoice-table-wrap">
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Jenis</th>
                    <th>Nominal</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Bukti</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $pay)
                <tr>
                    <td>{{ $pay->event->nama_event ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $pay->jenis_pembayaran === 'dp' ? 'badge-mendatang' : 'badge-aktif' }}">
                            {{ strtoupper($pay->jenis_pembayaran) }}
                        </span>
                    </td>
                    <td style="font-weight:700;">{{ $pay->formatted_nominal }}</td>
                    <td>{{ $pay->tanggal_pembayaran->format('j M Y') }}</td>
                    <td>
                        <span class="badge {{ $pay->badge_class }}">
                            {{ ucfirst($pay->status_pembayaran) }}
                        </span>
                    </td>
                    <td>
                        @if($pay->bukti_url)
                        <a href="{{ $pay->bukti_url }}" target="_blank"
                           class="btn btn-ghost-accent btn-sm">
                            <i class="bi bi-eye"></i> Lihat
                        </a>
                        @else
                        <span style="color:var(--text-light);font-size:12px;">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="invoice-empty">
                            <i class="bi bi-wallet2"></i>
                            Belum ada riwayat pembayaran
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Upload Bukti Bayar --}}
<div id="payModal"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);
            z-index:999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:14px;padding:28px;
                width:100%;max-width:440px;margin:20px;box-shadow:var(--shadow-lg);">
        <h5 style="font-weight:700;color:var(--dark);margin-bottom:4px;">
            Upload Bukti Pembayaran
        </h5>
        <p style="font-size:13px;color:var(--text-muted);margin-bottom:20px;">
            Upload bukti transfer (JPG, PNG, atau PDF, maks 5MB)
        </p>
        <form id="payForm" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Jenis Pembayaran</label>
                <select name="jenis_pembayaran" class="form-control" required>
                    <option value="dp">DP (Down Payment)</option>
                    <option value="pelunasan">Pelunasan</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Nominal (Rp)</label>
                <input type="number" name="nominal" class="form-control"
                       placeholder="mis. 25000000" min="1000" required>
            </div>
            <div class="form-group">
                <label class="form-label">File Bukti Pembayaran</label>
                <input type="file" name="bukti_pembayaran" class="form-control"
                       accept=".jpg,.jpeg,.png,.pdf" required>
            </div>
            <div style="display:flex;gap:10px;margin-top:20px;">
                <button type="button" onclick="closePayModal()"
                        class="btn btn-outline" style="flex:1;">Batal</button>
                <button type="submit" class="btn btn-accent" style="flex:1;">
                    <i class="bi bi-upload"></i> Upload
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openPayModal(eventId) {
    document.getElementById('payForm').action = '/client/invoices/' + eventId + '/bayar';
    document.getElementById('payModal').style.display = 'flex';
}
function closePayModal() {
    document.getElementById('payModal').style.display = 'none';
}
document.getElementById('payModal').addEventListener('click', function(e) {
    if (e.target === this) closePayModal();
});
</script>
@endpush
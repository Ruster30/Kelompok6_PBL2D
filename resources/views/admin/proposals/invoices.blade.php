@extends('layouts.admin')

@section('title', 'Dokumen')
@section('page-title', 'Dokumen')

@section('content')
<div class="page-header" style="margin-bottom:16px;">
    <div class="page-header-left">
        <h1>Dokumen</h1>
    </div>
</div>

<div class="tabs">
    <a href="{{ route('admin.proposals.index') }}" class="tab-link">Dokumen Umum</a>
    <a href="{{ route('admin.proposals.invoices') }}" class="tab-link active">Invoice &amp; Kwitansi</a>
    <a href="{{ route('admin.document_builder.index') }}" class="tab-link">Document Builder</a>
</div>

<div class="tab-content">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
        <h2 style="font-size:17px; font-weight:700; color:#0f172a;">Daftar Kwitansi</h2>
        <button class="btn btn-primary" onclick="document.getElementById('invoiceModal').classList.add('show')">
            <i class="fas fa-plus"></i> Buat Kwitansi Baru
        </button>
    </div>

    <table>
        <thead>
            <tr>
                <th>No Kwitansi</th>
                <th>Event</th>
                <th>Klien</th>
                <th>Jumlah</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
            <tr>
                <td style="font-weight:500;">{{ $invoice->nomor_invoice }}</td>
                <td>{{ $invoice->event->nama_event ?? '-' }}</td>
                <td>{{ $invoice->event->client->name ?? '-' }}</td>
                <td style="font-weight:600;">Rp {{ number_format($invoice->total_invoice, 0, ',', '.') }}</td>
                <td>
                    @php
                        $map = ['draft'=>'badge-gray','terkirim'=>'badge-pending','belum_bayar'=>'badge-pending','menunggu_verifikasi'=>'badge-pending','lunas'=>'badge-active','ditolak'=>'badge-cancel'];
                        $cls = $map[$invoice->status_invoice] ?? 'badge-gray';
                    @endphp
                    <span class="badge {{ $cls }}">{{ $invoice->status_label }}</span>
                </td>
                <td>
                    <div class="action-btns">
                        <a href="{{ route('admin.proposals.invoices.print', $invoice->id) }}" target="_blank" class="action-btn" title="Cetak">
                            <i class="fas fa-print" style="font-size:12px;"></i>
                        </a>
                        <button class="action-btn" title="Edit" onclick='editInvoice({{ json_encode($invoice) }})'>
                            <i class="fas fa-edit" style="font-size:12px;"></i>
                        </button>
                        <form action="{{ route('admin.proposals.invoices.destroy', $invoice->id) }}" method="POST" style="display:inline;"
                              onsubmit="return confirm('Hapus kwitansi ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-btn danger" title="Hapus">
                                <i class="fas fa-trash" style="font-size:12px;"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr class="empty-row"><td colspan="6">Belum ada kwitansi.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modal --}}
<div id="invoiceModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <span id="invoiceModalTitle">Buat Kwitansi Baru</span>
            <button class="modal-close" onclick="document.getElementById('invoiceModal').classList.remove('show')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="invoiceForm" action="{{ route('admin.proposals.storeInvoice') }}" method="POST">
            @csrf
            <input type="hidden" name="_method" id="invoiceFormMethod" value="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nomor Invoice *</label>
                    <input type="text" name="nomor_invoice" id="nomor_invoice" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Event *</label>
                    <select name="event_id" id="event_id" class="form-input" required>
                        <option value="">-- Pilih Event --</option>
                        @foreach($events as $event)
                        <option value="{{ $event->id }}">{{ $event->nama_event }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Total Invoice (Rp) *</label>
                    <input type="number" name="total_invoice" id="total_invoice" class="form-input" required min="0">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Tanggal Invoice *</label>
                        <input type="date" name="tanggal_invoice" id="tanggal_invoice" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status_invoice" id="status_invoice" class="form-input">
                            <option value="belum_bayar">Belum Bayar</option>
                            <option value="menunggu_verifikasi">Menunggu Verifikasi</option>
                            <option value="lunas">Lunas</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('invoiceModal').classList.remove('show')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function editInvoice(invoice) {
    document.getElementById('invoiceModalTitle').innerText = 'Edit Kwitansi';
    document.getElementById('nomor_invoice').value = invoice.nomor_invoice;
    document.getElementById('event_id').value = invoice.event_id;
    document.getElementById('total_invoice').value = invoice.total_invoice;
    document.getElementById('tanggal_invoice').value = invoice.tanggal_invoice;
    document.getElementById('status_invoice').value = invoice.status_invoice;
    document.getElementById('invoiceForm').action = '{{ url("admin/proposals/invoices") }}/' + invoice.id;
    document.getElementById('invoiceFormMethod').value = 'PUT';
    document.getElementById('invoiceModal').classList.add('show');
}
</script>
@endpush

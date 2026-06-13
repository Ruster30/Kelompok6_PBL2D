@extends('layouts.admin')

@section('title', 'Pusat Dokumentasi')
@section('page-title', 'Pusat Dokumentasi')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Pusat Dokumentasi</h1>
        <p>Kelola dan verifikasi dokumentasi event dari vendor.</p>
    </div>
</div>

<div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
    <div class="plain-stat">
        <div class="plain-stat-label">Total Dokumen</div>
        <div class="plain-stat-value">{{ $totalDocs }}</div>
    </div>
    <div class="plain-stat">
        <div class="plain-stat-label">Menunggu Verifikasi</div>
        <div class="plain-stat-value">{{ $pendingDocs }}</div>
    </div>
    <div class="plain-stat" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <div class="plain-stat-label">Arsip Disetujui</div>
            <div class="plain-stat-value">{{ $approvedDocs }}</div>
        </div>
        <i class="fas fa-archive" style="font-size:24px; color:#14b8a6;"></i>
    </div>
</div>

<div class="card">
    <div class="card-header" style="border-bottom:none; padding-bottom:14px;">
        <div class="toolbar" style="margin-bottom:0; flex:1;">
            <div class="search-wrap">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari berdasarkan event, vendor, atau file..." value="{{ request('search') }}">
            </div>
            <select class="select-filter" id="statusFilter">
                <option value="">Semua Status</option>
                <option value="menunggu" {{ request('status')=='menunggu' ? 'selected' : '' }}>Menunggu</option>
                <option value="disetujui" {{ request('status')=='disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="ditolak" {{ request('status')=='ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>File</th>
                <th>Event</th>
                <th>Tugas</th>
                <th>Vendor</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($documentations as $doc)
            <tr>
                <td style="font-weight:500;">
                    <i class="fas fa-file-image" style="color:#14b8a6; margin-right:8px;"></i>
                    {{ basename($doc->file_dokumentasi) }}
                </td>
                <td>{{ $doc->event->nama_event ?? '-' }}</td>
                <td>{{ $doc->deskripsi ?? '-' }}</td>
                <td>{{ $doc->vendor->nama_vendor ?? '-' }}</td>
                <td>
                    @php
                        $map = ['menunggu'=>'badge-pending','disetujui'=>'badge-active','ditolak'=>'badge-cancel'];
                        $cls = $map[$doc->status ?? 'menunggu'] ?? 'badge-pending';
                    @endphp
                    <span class="badge {{ $cls }}">{{ ucfirst($doc->status ?? 'menunggu') }}</span>
                </td>
                <td>
                    <div class="action-btns">
                        <a href="{{ asset('storage/' . $doc->file_dokumentasi) }}" target="_blank" class="action-btn" title="Lihat">
                            <i class="fas fa-eye" style="font-size:12px;"></i>
                        </a>
                        @if(($doc->status ?? 'menunggu') === 'menunggu')
                        <form action="{{ route('admin.documentation.approve', $doc->id) }}" method="POST" style="display:inline;">
                            @csrf @method('PATCH')
                            <button type="submit" class="action-btn" title="Setujui" style="color:#16a34a; border-color:#16a34a;">
                                <i class="fas fa-check" style="font-size:12px;"></i>
                            </button>
                        </form>
                        <form action="{{ route('admin.documentation.reject', $doc->id) }}" method="POST" style="display:inline;">
                            @csrf @method('PATCH')
                            <button type="submit" class="action-btn danger" title="Tolak">
                                <i class="fas fa-times" style="font-size:12px;"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr class="empty-row"><td colspan="6">Belum ada dokumentasi.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($documentations->hasPages())
    <div style="padding:16px 24px; border-top:1px solid #f1f5f9;">
        {{ $documentations->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.getElementById('searchInput').addEventListener('input', debounce(filterTable, 300));
document.getElementById('statusFilter').addEventListener('change', filterTable);
function filterTable() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    window.location.href = `{{ route('admin.documentations.index') }}?search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}`;
}
function debounce(fn, delay) {
    let t; return function(...args) { clearTimeout(t); t = setTimeout(() => fn.apply(this, args), delay); };
}
</script>
@endpush
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

<div class="doc-grid-3" style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px;">
    <div class="plain-stat">
        <div class="plain-stat-label">Total Dokumentasi</div>
        <div class="plain-stat-value">{{ $totalDocs }}</div>
    </div>
    <div class="plain-stat">
        <div class="plain-stat-label">File Menunggu Verifikasi</div>
        <div class="plain-stat-value">{{ $pendingDocs }}</div>
    </div>
    <div class="plain-stat" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <div class="plain-stat-label">File Disetujui</div>
            <div class="plain-stat-value">{{ $approvedDocs }}</div>
        </div>
        <i class="fas fa-archive" style="font-size:24px; color:#14b8a6;"></i>
    </div>
</div>

<div class="toolbar">
    <div class="search-wrap">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Cari berdasarkan event atau judul..." value="{{ request('search') }}">
    </div>
    <select class="select-filter" id="statusFilter">
        <option value="">Semua Status</option>
        <option value="menunggu" {{ request('status')=='menunggu' ? 'selected' : '' }}>Menunggu</option>
        <option value="disetujui" {{ request('status')=='disetujui' ? 'selected' : '' }}>Disetujui</option>
        <option value="ditolak" {{ request('status')=='ditolak' ? 'selected' : '' }}>Ditolak</option>
    </select>
</div>

@forelse($documentations as $doc)
<div class="card" style="margin-bottom:16px;">
    {{-- Header dokumentasi --}}
    <div class="card-header">
        <div>
            <span class="card-title">{{ $doc->judul }}</span>
            <div style="font-size:13px; color:#64748b; margin-top:2px;">
                <i class="fas fa-calendar-alt" style="margin-right:4px;"></i>
                Event: <strong>{{ $doc->event->nama_event ?? '-' }}</strong>
            </div>
            @if($doc->deskripsi)
            <div style="font-size:13px; color:#94a3b8; margin-top:2px;">{{ $doc->deskripsi }}</div>
            @endif
        </div>
        <div style="display:flex; gap:8px; align-items:center;">
            <span style="font-size:13px; color:#64748b;">{{ $doc->files->count() }} file</span>
        </div>
    </div>

    {{-- Daftar file dalam dokumentasi ini --}}
    @if($doc->files->isEmpty())
    <div style="padding:20px 24px; color:#94a3b8; font-size:14px; text-align:center;">
        Belum ada file yang diunggah untuk dokumentasi ini.
    </div>
    @else
    <table>
        <thead>
            <tr>
                <th>File</th>
                <th>Tipe</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($doc->files as $file)
            <tr>
                <td>
                    <div style="display:flex; align-items:center; gap:10px;">
                        @if($file->tipe_file === 'foto')
                            <i class="fas fa-image" style="color:#14b8a6; font-size:16px;"></i>
                        @else
                            <i class="fas fa-video" style="color:#6366f1; font-size:16px;"></i>
                        @endif
                        <span style="font-weight:500; font-size:14px;">{{ basename($file->file_path) }}</span>
                    </div>
                </td>
                <td>
                    <span class="badge {{ $file->tipe_file === 'foto' ? 'badge-active' : 'badge-done' }}">
                        {{ ucfirst($file->tipe_file) }}
                    </span>
                </td>
                <td>
                    @php
                        $sMap = ['menunggu'=>'badge-pending','disetujui'=>'badge-active','ditolak'=>'badge-cancel'];
                        $sCls = $sMap[$file->status] ?? 'badge-pending';
                        $sLabels = ['menunggu'=>'Menunggu','disetujui'=>'Disetujui','ditolak'=>'Ditolak'];
                    @endphp
                    <span class="badge {{ $sCls }}">{{ $sLabels[$file->status] }}</span>
                </td>
                <td>
                    <div class="action-btns">
                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                           class="action-btn" title="Lihat File">
                            <i class="fas fa-eye" style="font-size:12px;"></i>
                        </a>
                        @if($file->status === 'menunggu')
                        <form action="{{ route('admin.documentation.approve-file', $file->id) }}" method="POST" style="display:inline;"
                              onsubmit="return swalApprove(this, 'Setujui File?', 'File dokumentasi akan disetujui dan vendor akan diberitahu.')">
                            @csrf @method('PATCH')
                            <button type="submit" class="action-btn" title="Setujui"
                                    style="color:#16a34a; border-color:#16a34a;">
                                <i class="fas fa-check" style="font-size:12px;"></i>
                            </button>
                        </form>
                        <form action="{{ route('admin.documentation.reject-file', $file->id) }}" method="POST" style="display:inline;"
                              onsubmit="return swalReject(this, 'Tolak File?', 'File dokumentasi akan ditolak dan vendor akan diberitahu.')">
                            @csrf @method('PATCH')
                            <button type="submit" class="action-btn danger" title="Tolak">
                                <i class="fas fa-times" style="font-size:12px;"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@empty
<div class="card">
    <div class="empty-state">
        <i class="fas fa-folder-open"></i>
        <h3>Belum ada dokumentasi.</h3>
        <p>Dokumentasi diunggah oleh vendor melalui portal vendor.</p>
    </div>
</div>
@endforelse

@if($documentations->hasPages())
<div style="margin-top:16px;">{{ $documentations->links() }}</div>
@endif
@endsection

@push('scripts')
<script>
document.getElementById('searchInput').addEventListener('input', debounce(filterTable, 300));
document.getElementById('statusFilter').addEventListener('change', filterTable);
function filterTable() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    window.location.href = `{{ route('admin.documentation.index') }}?search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}`;
}
function debounce(fn, delay) {
    let t; return function(...args) { clearTimeout(t); t = setTimeout(() => fn.apply(this, args), delay); };
}
</script>
@endpush


@extends('layouts.admin')

@section('title', 'Proposal & Dokumen')
@section('page-title', 'Proposal & Dokumen')

@section('content')
<div class="page-header" style="margin-bottom:16px;">
    <div class="page-header-left">
        <h1>Proposal &amp; Dokumen</h1>
    </div>
</div>

<div class="tabs">
    <a href="{{ route('admin.proposals.index') }}" class="tab-link active">Dokumen Umum</a>
    <a href="{{ route('admin.proposals.invoices') }}" class="tab-link">Invoice &amp; Kwitansi</a>
    <a href="{{ route('admin.proposals.builder') }}" class="tab-link">Proposal Builder</a>
</div>

<div class="tab-content">
    <form action="{{ route('admin.proposals.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
        @csrf
        <label for="fileInput" class="upload-zone">
            <i class="fas fa-cloud-upload-alt"></i>
            <h3>Klik untuk upload atau seret file</h3>
            <p>SVG, PNG, JPG, PDF, DOCX atau XLSX (maks. 20MB)</p>
            <input type="file" name="file" id="fileInput" style="display:none;"
                   accept=".svg,.png,.jpg,.jpeg,.pdf,.docx,.xlsx" onchange="document.getElementById('uploadForm').submit()">
        </label>
        <div class="form-group" style="margin-top:12px;">
            <select name="event_id" class="form-input" style="max-width:300px;" onchange="this.form.querySelector('#fileInput')">
                <option value="">-- Tidak terkait event --</option>
                @foreach($events as $event)
                <option value="{{ $event->id }}">{{ $event->nama_event }}</option>
                @endforeach
            </select>
        </div>
    </form>

    <div class="toolbar" style="margin-top:24px;">
        <div class="search-wrap">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Cari dokumen..." value="{{ request('search') }}">
        </div>
        <select class="select-filter" id="typeFilter">
            <option value="">Filter Tipe</option>
            <option value="proposal" {{ request('type')=='proposal' ? 'selected' : '' }}>Proposal</option>
            <option value="kontrak" {{ request('type')=='kontrak' ? 'selected' : '' }}>Kontrak</option>
            <option value="lainnya" {{ request('type')=='lainnya' ? 'selected' : '' }}>Lainnya</option>
        </select>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama File</th>
                <th>Tipe</th>
                <th>Diunggah Oleh</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($documents as $doc)
            <tr>
                <td style="font-weight:500;">
                    <i class="fas fa-file-pdf" style="color:#f43f5e; margin-right:8px;"></i>
                    {{ $doc->nama_file }}
                </td>
                <td><span class="badge badge-blue" style="background:#dbeafe; color:#1e40af;">{{ ucfirst($doc->tipe) }}</span></td>
                <td>{{ $doc->user->name ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($doc->created_at)->format('d M Y') }}</td>
                <td>
                    <div class="action-btns">
                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="action-btn" title="Lihat">
                            <i class="fas fa-eye" style="font-size:12px;"></i>
                        </a>
                        <a href="{{ asset('storage/' . $doc->file_path) }}" download class="action-btn" title="Download">
                            <i class="fas fa-download" style="font-size:12px;"></i>
                        </a>
                        <form action="{{ route('admin.proposals.destroy', $doc->id) }}" method="POST" style="display:inline;"
                              onsubmit="return confirm('Hapus dokumen ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-btn danger" title="Hapus">
                                <i class="fas fa-trash" style="font-size:12px;"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr class="empty-row"><td colspan="5">Belum ada dokumen.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('searchInput').addEventListener('input', debounce(filterTable, 300));
document.getElementById('typeFilter').addEventListener('change', filterTable);
function filterTable() {
    const search = document.getElementById('searchInput').value;
    const type = document.getElementById('typeFilter').value;
    window.location.href = `{{ route('admin.proposals.index') }}?search=${encodeURIComponent(search)}&type=${encodeURIComponent(type)}`;
}
function debounce(fn, delay) {
    let t; return function(...args) { clearTimeout(t); t = setTimeout(() => fn.apply(this, args), delay); };
}
</script>
@endpush
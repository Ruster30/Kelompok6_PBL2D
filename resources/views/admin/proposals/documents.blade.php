@extends('layouts.admin')

@section('title', 'Proposal & Dokumen')
@section('page-title', 'Proposal & Dokumen')

@section('content')
<div class="page-header" style="margin-bottom:16px;">
    <div class="page-header-left">
        <h1>Proposal &amp; Dokumen</h1>
    </div>
</div>

{{-- Tab navigasi --}}
<div class="tabs">
    <a href="{{ route('admin.proposals.index') }}" class="tab-link active">Dokumen Umum</a>
    <a href="{{ route('admin.document_builder.index') }}" class="tab-link">Document Builder</a>
</div>

<div class="tab-content">
    {{-- Upload Zone --}}
    <form action="{{ route('admin.proposals.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
        @csrf

        {{-- Baris 1: Event + Jenis Dokumen --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
            <div>
                <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:4px;">
                    Event <span style="color:#9ca3af; font-weight:400;">(opsional)</span>
                </label>
                <select name="event_id" class="form-input">
                    <option value="">-- Tidak terkait event --</option>
                    @foreach($events as $event)
                    <option value="{{ $event->id }}">{{ $event->nama_event }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:4px;">
                    Jenis Dokumen <span style="color:#ef4444;">*</span>
                </label>
                <select name="tipe" id="tipeUpload" class="form-input" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="proposal">Proposal</option>
                    <option value="kontrak">Kontrak</option>
                    <option value="invoice">Invoice</option>
                    <option value="rab">RAB</option>
                    <option value="laporan">Laporan Akhir</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>
        </div>

        {{-- Drop Zone --}}
        <label for="fileInput" class="upload-zone">
            <i class="bi bi-cloud-upload" style="font-size:2rem;"></i>
            <h3>Klik untuk upload atau seret file ke sini</h3>
            <p>SVG, PNG, JPG, PDF, DOCX atau XLSX (maks. 100MB)</p>
            <input type="file" name="file" id="fileInput" style="display:none;"
                   accept=".svg,.png,.jpg,.jpeg,.pdf,.docx,.xlsx"
                   onchange="submitUpload()">
        </label>
    </form>

    {{-- Toolbar --}}
    <div class="toolbar" style="margin-top:24px;">
        <div class="search-wrap">
            <i class="bi bi-search"></i>
            <input type="text" id="searchInput" placeholder="Cari dokumen..." value="{{ request('search') }}">
        </div>
        <select class="select-filter" id="typeFilter">
            <option value="">Semua</option>
            <option value="proposal" {{ request('type') == 'proposal' ? 'selected' : '' }}>Proposal</option>
            <option value="kontrak"  {{ request('type') == 'kontrak'  ? 'selected' : '' }}>Kontrak</option>
            <option value="invoice"  {{ request('type') == 'invoice'  ? 'selected' : '' }}>Invoice</option>
            <option value="rab"      {{ request('type') == 'rab'      ? 'selected' : '' }}>RAB</option>
            <option value="laporan"  {{ request('type') == 'laporan'  ? 'selected' : '' }}>Laporan Akhir</option>
            <option value="lainnya"  {{ request('type') == 'lainnya'  ? 'selected' : '' }}>Lainnya</option>
        </select>
    </div>

    {{-- Tabel Dokumen --}}
    <table style="margin-top:16px;">
        <thead>
            <tr>
                <th>Nama File</th>
                <th>Jenis</th>
                <th>Event</th>
                <th>Diunggah Oleh</th>
                <th>Tanggal</th>
                <th style="text-align:center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($documents as $doc)
            <tr>
                <td style="font-weight:500;">
                    @php
                        $ext = strtolower(pathinfo($doc->nama_file, PATHINFO_EXTENSION));
                        $icon = match(true) {
                            in_array($ext, ['pdf'])        => 'bi-file-earmark-pdf',
                            in_array($ext, ['docx','doc']) => 'bi-file-earmark-word',
                            in_array($ext, ['xlsx','xls']) => 'bi-file-earmark-excel',
                            in_array($ext, ['png','jpg','jpeg','svg']) => 'bi-file-earmark-image',
                            default => 'bi-file-earmark',
                        };
                        $iconColor = match(true) {
                            $ext === 'pdf'  => '#f43f5e',
                            in_array($ext, ['docx','doc']) => '#2563eb',
                            in_array($ext, ['xlsx','xls']) => '#16a34a',
                            default => '#6b7280',
                        };
                    @endphp
                    <i class="bi {{ $icon }}" style="color:{{ $iconColor }}; margin-right:8px;"></i>
                    {{ $doc->nama_file }}
                </td>
                <td>
                    @php
                        $badgeColor = match($doc->tipe) {
                            'proposal' => ['bg'=>'#EEF2FF','text'=>'#4338CA'],
                            'kontrak'  => ['bg'=>'#DBEAFE','text'=>'#1D4ED8'],
                            'invoice'  => ['bg'=>'#FEF3C7','text'=>'#B45309'],
                            'rab'      => ['bg'=>'#DCFCE7','text'=>'#15803D'],
                            'laporan'  => ['bg'=>'#F3E8FF','text'=>'#7E22CE'],
                            default    => ['bg'=>'#F3F4F6','text'=>'#4B5563'],
                        };
                    @endphp
                    <span class="badge" style="background:{{ $badgeColor['bg'] }}; color:{{ $badgeColor['text'] }}; padding:3px 10px; border-radius:12px; font-size:12px; font-weight:500;">
                        {{ $doc->tipe_label }}
                    </span>
                </td>
                <td style="color:#6b7280; font-size:13px;">{{ $doc->event->nama_event ?? '-' }}</td>
                <td>{{ $doc->user->name ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($doc->created_at)->format('d M Y') }}</td>
                <td>
                    <div class="action-btns" style="justify-content:center; gap:6px;">

                        {{-- 👁 Lihat (Preview) --}}
                        <a href="{{ route('admin.proposals.preview', $doc->id) }}"
                           target="_blank"
                           class="action-btn"
                           title="Lihat / Preview">
                            <i class="bi bi-eye"></i>
                        </a>

                        {{-- ⬇ Download --}}
                        <a href="{{ route('admin.proposals.download', $doc->id) }}"
                           class="action-btn"
                           title="Download">
                            <i class="bi bi-download"></i>
                        </a>

                        {{-- 📤 Kirim ke Client --}}
                        <button type="button"
                                class="action-btn"
                                title="Kirim ke Client"
                                onclick="bukaModalKirim({{ $doc->id }}, '{{ addslashes($doc->nama_file) }}')">
                            <i class="bi bi-send"></i>
                        </button>

                        {{-- 🗑 Hapus --}}
                        <form action="{{ route('admin.proposals.destroy', $doc->id) }}"
                              method="POST"
                              style="display:inline;"
                              onsubmit="return confirm('Hapus dokumen \"{{ addslashes($doc->nama_file) }}\"?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn danger" title="Hapus">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>

                    </div>
                </td>
            </tr>
            @empty
            <tr class="empty-row">
                <td colspan="6" style="text-align:center; color:#9ca3af; padding:32px;">
                    <i class="bi bi-folder2-open" style="font-size:2rem; display:block; margin-bottom:8px;"></i>
                    Belum ada dokumen. Upload file di atas untuk memulai.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    @if($documents->hasPages())
    <div style="margin-top:16px;">
        {{ $documents->links() }}
    </div>
    @endif
</div>

{{-- ─── Modal Kirim ke Client ─────────────────────────── --}}
<div id="modalKirim"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; padding:28px 32px; width:100%; max-width:460px; box-shadow:0 20px 60px rgba(0,0,0,.2);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h3 style="margin:0; font-size:1.1rem; font-weight:600;">
                <i class="bi bi-send" style="color:#6366f1; margin-right:8px;"></i>
                Kirim Dokumen ke Client
            </h3>
            <button onclick="tutupModalKirim()"
                    style="background:none; border:none; font-size:1.4rem; cursor:pointer; color:#9ca3af; line-height:1;">&times;</button>
        </div>

        <p id="modalDocName" style="font-size:.9rem; color:#6b7280; margin-bottom:20px; padding:10px 14px; background:#f9fafb; border-radius:8px; border:1px solid #e5e7eb;">
            <i class="bi bi-file-earmark-pdf" style="color:#f43f5e;"></i>
            <span></span>
        </p>

        <form id="formKirim" method="POST">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:.85rem; font-weight:600; margin-bottom:6px; color:#374151;">
                    Pilih Client Tujuan <span style="color:#ef4444;">*</span>
                </label>
                <select name="client_id"
                        required
                        style="width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:.9rem; outline:none;">
                    <option value="">-- Pilih client --</option>
                    @foreach($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }} ({{ $client->email }})</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block; font-size:.85rem; font-weight:600; margin-bottom:6px; color:#374151;">
                    Pesan Tambahan <span style="color:#9ca3af;">(opsional)</span>
                </label>
                <textarea name="pesan"
                          rows="3"
                          placeholder="Contoh: Dokumen ini untuk ditinjau sebelum penandatanganan..."
                          style="width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:.9rem; resize:vertical; outline:none; box-sizing:border-box;"></textarea>
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button"
                        onclick="tutupModalKirim()"
                        style="padding:9px 20px; border:1px solid #d1d5db; background:#fff; border-radius:8px; cursor:pointer; font-size:.9rem; color:#374151;">
                    Batal
                </button>
                <button type="submit"
                        style="padding:9px 20px; background:#6366f1; color:#fff; border:none; border-radius:8px; cursor:pointer; font-size:.9rem; font-weight:600; display:flex; align-items:center; gap:6px;">
                    <i class="bi bi-send-fill"></i> Kirim
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ─── Validasi & Submit upload ──────────────────────────
function submitUpload() {
    const tipe = document.getElementById('tipeUpload').value;
    if (!tipe) {
        alert('Harap pilih Jenis Dokumen sebelum mengunggah file.');
        document.getElementById('fileInput').value = '';
        return;
    }
    document.getElementById('uploadForm').submit();
}

// ─── Filter pencarian ──────────────────────────
document.getElementById('searchInput').addEventListener('input', debounce(filterTable, 350));
document.getElementById('typeFilter').addEventListener('change', filterTable);

function filterTable() {
    const search = document.getElementById('searchInput').value;
    const type   = document.getElementById('typeFilter').value;
    window.location.href = `{{ route('admin.proposals.index') }}?search=${encodeURIComponent(search)}&type=${encodeURIComponent(type)}`;
}

function debounce(fn, delay) {
    let t;
    return function (...args) { clearTimeout(t); t = setTimeout(() => fn.apply(this, args), delay); };
}

// ─── Modal Kirim ke Client ─────────────────────
function bukaModalKirim(docId, docName) {
    document.getElementById('formKirim').action = `/admin/proposals/${docId}/send`;
    document.querySelector('#modalDocName span').textContent = docName;
    const modal = document.getElementById('modalKirim');
    modal.style.display = 'flex';
    modal.querySelector('select[name="client_id"]').value = '';
    modal.querySelector('textarea[name="pesan"]').value   = '';
}

function tutupModalKirim() {
    document.getElementById('modalKirim').style.display = 'none';
}

document.getElementById('modalKirim').addEventListener('click', function (e) {
    if (e.target === this) tutupModalKirim();
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') tutupModalKirim();
});
</script>
@endpush
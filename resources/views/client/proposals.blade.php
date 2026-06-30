@extends('layouts.client')
@section('title','Dokumen')
@section('page-title','Dokumen')

@section('content')

<div class="page-header" style="margin-bottom:16px;">
    <div class="page-header-left">
        <h1>Dokumen</h1>
        <p>Dokumen resmi yang dikirim oleh tim kami untuk event Anda.</p>
    </div>
</div>

@if(session('success'))
<div class="alert-banner" style="background:#d1fae5;border-color:#6ee7b7;color:#065f46;">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
</div>
@endif

{{-- ── Tab navigasi (style identik Landing Page CMS) ── --}}
<div class="tabs">
    <a href="{{ route('client.proposals', 'penawaran') }}"
       class="tab-link {{ $activeTab === 'penawaran' ? 'active' : '' }}">Surat Penawaran</a>
    <a href="{{ route('client.proposals', 'proposal') }}"
       class="tab-link {{ $activeTab === 'proposal' ? 'active' : '' }}">Proposal</a>
    <a href="{{ route('client.proposals', 'rab') }}"
       class="tab-link {{ $activeTab === 'rab' ? 'active' : '' }}">RAB</a>
    <a href="{{ route('client.proposals', 'kontrak') }}"
       class="tab-link {{ $activeTab === 'kontrak' ? 'active' : '' }}">Surat Kontrak</a>
    <a href="{{ route('client.proposals', 'laporan') }}"
       class="tab-link {{ $activeTab === 'laporan' ? 'active' : '' }}">Laporan Akhir</a>
</div>

<div class="tab-content">

@if($activeTab === 'penawaran')
{{-- ═══════════════════════════════════════════════════════════
     TAB: SURAT PENAWARAN — logika & tampilan LAMA, tidak diubah.
     Tetap menampilkan proposal terbaru per event + tombol
     "Lihat Surat Penawaran" → alur Terima/Negosiasi yang sudah ada.
═══════════════════════════════════════════════════════════ --}}

@forelse($latestProposals as $proposal)
<div class="penawaran-card" style="margin-bottom:16px;">
    <div class="penawaran-hdr">
        <div class="penawaran-icon">
            <i class="bi bi-file-earmark-text-fill"></i>
        </div>
        <div style="flex:1;">
            <div class="penawaran-name">{{ $proposal->event->nama_event }}</div>
            <div class="penawaran-type">{{ $proposal->event->jenis_event }}</div>
        </div>
        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;">
            <span class="badge {{ $proposal->badge_class }}">
                <i class="bi bi-{{ $proposal->status==='diterima' ? 'check-circle-fill' : ($proposal->status==='ditolak' ? 'x-circle-fill' : 'clock') }}"
                   style="margin-right:4px;"></i>
                {{ $proposal->status_label }}
            </span>
            @if($proposal->versi > 1)
            <span style="font-size:11px;background:#f1f5f9;color:#64748b;padding:2px 8px;border-radius:999px;font-weight:600;">
                Revisi v{{ $proposal->versi }}
            </span>
            @endif
        </div>
    </div>

    <div class="penawaran-rows">
        <div class="penawaran-row">
            <span class="penawaran-row-label">Tanggal Event</span>
            <span class="penawaran-row-value">
                {{ \Carbon\Carbon::parse($proposal->event->tanggal_event)->isoFormat('D MMMM Y') }}
            </span>
        </div>
        <div class="penawaran-row">
            <span class="penawaran-row-label">Lokasi</span>
            <span class="penawaran-row-value">{{ $proposal->event->lokasi_event }}</span>
        </div>
        @if($proposal->event->rentang_anggaran)
        <div class="penawaran-row">
            <span class="penawaran-row-label">Anggaran Ditawarkan</span>
            <span class="penawaran-row-value" style="font-weight:700;color:var(--accent);">
                {{ $proposal->event->rentang_anggaran }}
            </span>
        </div>
        @endif
        <div class="penawaran-row">
            <span class="penawaran-row-label">No. Surat</span>
            <span class="penawaran-row-value">{{ $proposal->nomor_proposal ?? '-' }}</span>
        </div>
        <div class="penawaran-row">
            <span class="penawaran-row-label">Tanggal Proposal</span>
            <span class="penawaran-row-value">
                {{ $proposal->tanggal_proposal ? \Carbon\Carbon::parse($proposal->tanggal_proposal)->isoFormat('D MMMM Y') : '-' }}
            </span>
        </div>
    </div>

    <div class="penawaran-cta">
        <a href="{{ route('client.proposals.show', $proposal->id) }}" class="btn-penawaran">
            Lihat Surat Penawaran <i class="bi bi-arrow-right"></i>
        </a>
    </div>
</div>
@empty
<div class="empty-state">
    <i class="bi bi-file-earmark-x"></i>
    <h3>Belum Ada Surat Penawaran</h3>
    <p>Tim kami akan mengirimkan surat penawaran setelah pengajuan event Anda diterima dan diproses.</p>
    <a href="{{ route('client.event.create') }}" class="btn btn-accent" style="margin-top:16px;">
        Ajukan Event Baru
    </a>
</div>
@endforelse

@else
{{-- ═══════════════════════════════════════════════════════════
     TAB: PROPOSAL / RAB / SURAT KONTRAK / LAPORAN AKHIR
     Grid card ala Landing Page CMS, data dari tabel `documents`.
═══════════════════════════════════════════════════════════ --}}

{{-- Toolbar: Search + Filter Event --}}
<form method="GET" action="{{ route('client.proposals', $activeTab) }}" class="toolbar">
    <div class="search-wrap">
        <i class="bi bi-search"></i>
        <input type="text" name="search" placeholder="Cari dokumen..." value="{{ request('search') }}"
               onchange="this.form.submit()">
    </div>
    <select name="event_id" class="select-filter" onchange="this.form.submit()">
        <option value="">Semua Event</option>
        @foreach($events as $event)
        <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
            {{ $event->nama_event }}
        </option>
        @endforeach
    </select>
</form>

<div class="cms-grid">
    @forelse($documents as $doc)
    @php
        $ext = strtolower(pathinfo($doc->nama_file, PATHINFO_EXTENSION));
        $icon = match(true) {
            $ext === 'pdf'                              => 'bi-file-earmark-pdf',
            in_array($ext, ['docx','doc'])               => 'bi-file-earmark-word',
            in_array($ext, ['xlsx','xls'])               => 'bi-file-earmark-excel',
            in_array($ext, ['png','jpg','jpeg','svg'])   => 'bi-file-earmark-image',
            default                                      => 'bi-file-earmark',
        };
        $iconColor = match(true) {
            $ext === 'pdf' => '#f43f5e',
            in_array($ext, ['docx','doc']) => '#2563eb',
            in_array($ext, ['xlsx','xls']) => '#16a34a',
            default => '#14b8a6',
        };
    @endphp
    <div class="cms-card">
        <div class="cms-icon-circle" style="color:{{ $iconColor }}; background:{{ $iconColor }}1a;">
            <i class="bi {{ $icon }}"></i>
        </div>
        <h3 style="overflow-wrap:anywhere;">{{ $doc->nama_file }}</h3>
        <p>
            {{ $doc->event->nama_event ?? 'Tidak terkait event' }}<br>
            <span style="color:#94a3b8;">{{ \Carbon\Carbon::parse($doc->created_at)->format('d M Y') }}</span>
        </p>

        <div style="margin-top:10px; display:flex; gap:8px; align-items:center;">
            <span class="badge {{ $doc->tipe_badge_class }}">{{ $doc->tipe_label }}</span>
        </div>

        {{-- Aksi: HANYA Lihat & Download — sama persis dengan Admin → Proposal & Dokumen --}}
        <div class="action-btns" style="margin-top:14px;">
            <a href="{{ route('client.proposals.document.preview', $doc->id) }}"
               target="_blank"
               class="action-btn"
               title="Lihat / Preview">
                <i class="bi bi-eye"></i>
            </a>
            <a href="{{ route('client.proposals.document.download', $doc->id) }}"
               class="action-btn"
               title="Download">
                <i class="bi bi-download"></i>
            </a>
        </div>
    </div>
    @empty
    <div class="empty-state" style="grid-column:1/-1;">
        <i class="bi bi-folder2-open"></i>
        <h3>Belum ada dokumen</h3>
        <p>
            @switch($activeTab)
                @case('proposal') Proposal akan tampil di sini setelah dikirim oleh tim kami. @break
                @case('rab')      RAB akan tampil di sini setelah dikirim oleh tim kami. @break
                @case('kontrak')  Surat kontrak akan tampil di sini setelah dikirim oleh tim kami. @break
                @case('laporan')  Laporan akhir akan tampil di sini setelah event Anda selesai. @break
            @endswitch
        </p>
    </div>
    @endforelse
</div>

@if($documents->hasPages())
<div style="margin-top:20px;">
    {{ $documents->links() }}
</div>
@endif

@endif
</div>{{-- /tab-content --}}

@endsection

@push('styles')
<style>
/*
  Style berikut direplikasi dari layouts/admin.blade.php (Landing Page CMS)
  agar tab Proposal/RAB/Kontrak/Laporan tampil konsisten dengan desain admin.
  Class .penawaran-* (tab Surat Penawaran) TIDAK disentuh — sudah ada di client.css.
*/
.tabs {
    display: flex; gap: 0; border-bottom: 1px solid var(--border, #e2e8f0);
    background: white; border-radius: 12px 12px 0 0; padding: 0 8px;
}
.tab-link {
    padding: 16px 20px; font-size: 14px; font-weight: 500; color: #64748b;
    text-decoration: none; border-bottom: 2px solid transparent; transition: all 0.2s;
}
.tab-link:hover { color: #0f172a; }
.tab-link.active { color: #0f9488; border-bottom-color: var(--accent, #14b8a6); font-weight: 600; }
.tab-content {
    background: white; border-radius: 0 0 12px 12px; padding: 24px;
    border: 1px solid var(--border, #e2e8f0); border-top: none;
}

.toolbar { display: flex; gap: 12px; margin-bottom: 20px; align-items: center; }
.search-wrap { flex: 1; position: relative; }
.search-wrap input {
    width: 100%; padding: 9px 16px 9px 38px; border: 1px solid var(--border, #e2e8f0);
    border-radius: 8px; font-size: 14px; color: #334155; outline: none; background: white;
}
.search-wrap input:focus { border-color: var(--accent, #14b8a6); box-shadow: 0 0 0 3px #ccfbf180; }
.search-wrap i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; }
.select-filter {
    padding: 9px 14px; border: 1px solid var(--border, #e2e8f0); border-radius: 8px; font-size: 14px;
    color: #334155; background: white; outline: none; cursor: pointer;
}
.select-filter:focus { border-color: var(--accent, #14b8a6); }

.cms-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }
.cms-card { border: 1px solid var(--border, #e2e8f0); border-radius: 10px; padding: 18px; position: relative; }
.cms-icon-circle {
    width: 44px; height: 44px; border-radius: 10px; background: #f0fdf9; color: var(--accent, #14b8a6);
    display: flex; align-items: center; justify-content: center; font-size: 18px; margin-bottom: 10px;
}
.cms-card h3 { font-size: 15px; font-weight: 600; color: #0f172a; margin-bottom: 6px; }
.cms-card p { font-size: 13px; color: #64748b; line-height: 1.5; }

.badge {
    display:inline-flex; align-items:center; justify-content:center;
    padding:5px 12px; border-radius:999px; font-size:12px; font-weight:600;
    white-space:nowrap;
}
.badge-mendatang { background:#ede9fe; color:#5b21b6; }
.badge-aktif     { background:#dbeafe; color:#1e40af; }
.badge-selesai   { background:#fef3c7; color:#92400e; }
.badge-pending   { background:#dcfce7; color:#166534; }
.badge-purple    { background:#f3e8ff; color:#7e22ce; }

.action-btns { display: flex; gap: 6px; }
.action-btn {
    width: 32px; height: 32px; border-radius: 6px; border: 1px solid var(--border, #e2e8f0);
    display: flex; align-items: center; justify-content: center; cursor: pointer; background: white;
    transition: all 0.15s; color: #64748b; text-decoration:none;
}
.action-btn:hover { border-color: var(--accent, #14b8a6); color: var(--accent, #14b8a6); }

.empty-state {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    padding: 60px 20px; text-align: center; gap: 10px;
}
.empty-state i { font-size: 40px; color: #cbd5e1; }
.empty-state h3 { font-size: 16px; font-weight: 600; color: #475569; }
.empty-state p { font-size: 13px; color: #94a3b8; max-width:320px; }

.alert-banner {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 18px; border-radius: 8px; font-size: 13px;
    margin-bottom: 20px; border: 1px solid;
}

@media (max-width: 1024px) {
    .cms-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 640px) {
    .cms-grid { grid-template-columns: 1fr; }
    .toolbar { flex-direction: column; align-items: stretch; }
    .tabs { overflow-x: auto; white-space: nowrap; }
}
</style>
@endpush
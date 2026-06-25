@extends('layouts.admin')

@section('title', 'Timeline')
@section('page-title', 'Timeline')

@push('styles')
<style>
    .timeline-shell { padding: 36px 28px; }
    .timeline-list { max-width: 900px; margin: 0 auto; }
    .timeline-item { display:grid; grid-template-columns:64px minmax(0, 1fr); min-height:170px; }
    .timeline-rail { position:relative; display:flex; justify-content:center; }
    .timeline-rail::after { content:""; position:absolute; top:38px; bottom:-2px; width:2px; background:#e2e8f0; }
    .timeline-item:last-child .timeline-rail::after { display:none; }
    .timeline-marker { position:relative; z-index:1; margin-top:18px; width:30px; height:30px; border:5px solid #fff; border-radius:50%; background:#f1f5f9; box-shadow:0 0 0 1px #e2e8f0; }
    .timeline-marker.is-running { background:#ccfbf1; box-shadow:0 0 0 1px #99f6e4; }
    .timeline-marker.is-done { background:#bbf7d0; box-shadow:0 0 0 1px #86efac; }
    .timeline-card { position:relative; margin:0 0 36px; padding:24px; border:1px solid #e2e8f0; border-radius:14px; background:#fff; box-shadow:0 1px 2px rgb(15 23 42 / 3%); }
    .timeline-card:hover { border-color:#99f6e4; }
    .timeline-card-top { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; }
    .timeline-title { font-size:17px; font-weight:700; color:#334155; }
    .timeline-date { display:inline-flex; margin-left:8px; padding:4px 9px; border-radius:7px; background:#f1f5f9; color:#64748b; font-size:12px; font-weight:600; vertical-align:middle; }
    .timeline-description { margin:12px 0 20px; color:#64748b; font-size:14px; line-height:1.6; }
    .timeline-meta { display:flex; align-items:center; flex-wrap:wrap; gap:12px; color:#64748b; font-size:13px; }
    .timeline-owner { display:inline-flex; align-items:center; gap:8px; }
    .timeline-owner-badge { width:28px; height:28px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; background:#e2e8f0; color:#64748b; font-size:10px; font-weight:700; }
    .timeline-deadline { display:inline-flex; gap:5px; padding:6px 10px; border-radius:6px; background:#fff1f2; color:#ef4444; font-size:12px; font-weight:600; }
    .timeline-actions { display:flex; gap:6px; }
    @media (max-width: 640px) { .timeline-shell { padding:20px 14px; } .timeline-item { grid-template-columns:42px minmax(0,1fr); } .timeline-card { padding:18px; } .timeline-card-top { flex-direction:column; } }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Timeline Event</h1>
        <p>Kelola jadwal dan progress event.</p>
    </div>
    <div style="display:flex; align-items:center; gap:12px;">
        <select class="select-filter" id="eventSelect" onchange="changeEvent(this.value)" style="min-width:220px;">
            <option value="">Belum ada event</option>
            @foreach($events as $event)
            <option value="{{ $event->id }}" @selected($selectedEvent && $selectedEvent->id === $event->id)>
                {{ $event->nama_event }}
            </option>
            @endforeach
        </select>
        <button class="btn btn-primary" @disabled(!$selectedEvent) onclick="openAddTimeline()">
            <i class="fas fa-plus"></i> Tambah
        </button>
    </div>
</div>

@if(!$selectedEvent)
<div class="card">
    <div class="empty-state">
        <i class="fas fa-calendar"></i>
        <h3>Belum ada event.</h3>
        <p>Buat event terlebih dahulu untuk menambahkan timeline.</p>
    </div>
</div>
@else
<div class="card timeline-shell">
    <div class="timeline-list">
        @forelse($timelines as $item)
        @php
            $markerClass = $item->status_kegiatan === 'selesai' ? 'is-done' : ($item->status_kegiatan === 'berjalan' ? 'is-running' : '');
            $initials = strtoupper(substr(preg_replace('/\s+/', '', $item->penanggung_jawab ?: 'Tim'), 0, 2));
        @endphp
        <div class="timeline-item">
            <div class="timeline-rail"><span class="timeline-marker {{ $markerClass }}"></span></div>
            <article class="timeline-card">
                <div class="timeline-card-top">
                    <div>
                        <span class="timeline-title">{{ $item->nama_kegiatan }}</span>
                        <span class="timeline-date">{{ $item->tanggal_kegiatan->translatedFormat('d M Y') }}</span>
                    </div>
                    <div class="timeline-actions">
                        <button type="button" class="action-btn" title="Edit" onclick='editTimeline(@json($item))'>
                            <i class="fas fa-edit" style="font-size:12px;"></i>
                        </button>
                        <form action="{{ route('admin.timeline.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus kegiatan ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-btn danger" title="Hapus"><i class="fas fa-trash" style="font-size:12px;"></i></button>
                        </form>
                    </div>
                </div>
                <p class="timeline-description">{{ $item->deskripsi ?: 'Belum ada deskripsi kegiatan.' }}</p>
                <div class="timeline-meta">
                    <span class="timeline-owner">
                        <span class="timeline-owner-badge">{{ $initials }}</span>
                        {{ $item->penanggung_jawab ?: 'Belum ditugaskan' }}
                    </span>
                    <span class="timeline-deadline">
                        Deadline: {{ $item->deadline?->translatedFormat('d M Y') ?? '-' }}
                    </span>
                    <span class="badge {{ $item->status_kegiatan === 'selesai' ? 'badge-active' : ($item->status_kegiatan === 'berjalan' ? 'badge-done' : 'badge-pending') }}">
                        {{ $item->status_label }}
                    </span>
                </div>
            </article>
        </div>
        @empty
        <div class="empty-state">
            <i class="fas fa-stream"></i>
            <h3>Timeline masih kosong.</h3>
            <p>Tambahkan tahapan kerja untuk event {{ $selectedEvent->nama_event }}.</p>
        </div>
        @endforelse
    </div>
</div>

<div id="addTimelineModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <span id="modalTitle">Tambah Timeline</span>
            <button type="button" class="modal-close" onclick="closeTimelineModal()"><i class="fas fa-times"></i></button>
        </div>
        <form id="timelineForm" action="{{ route('admin.timeline.store') }}" method="POST">
            @csrf
            <input type="hidden" name="event_id" value="{{ $selectedEvent->id }}">
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Kegiatan *</label>
                    <input type="text" name="nama_kegiatan" id="nama_kegiatan" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-input" rows="3" placeholder="Ringkasan kegiatan yang akan dikerjakan"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Penanggung Jawab / Tim</label>
                    <input type="text" name="penanggung_jawab" id="penanggung_jawab" class="form-input" placeholder="Contoh: Tim ALPHA">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Tanggal Kegiatan *</label>
                        <input type="date" name="tanggal_kegiatan" id="tanggal_kegiatan" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Deadline</label>
                        <input type="date" name="deadline" id="deadline" class="form-input">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status_kegiatan" id="status_kegiatan" class="form-input">
                        <option value="belum_mulai">Belum Mulai</option>
                        <option value="berjalan">Berjalan</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeTimelineModal()">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function changeEvent(id) {
    window.location.href = '{{ route("admin.timeline.index") }}' + (id ? '?event_id=' + id : '');
}

function openAddTimeline() {
    const form = document.getElementById('timelineForm');
    form.reset();
    document.getElementById('modalTitle').innerText = 'Tambah Timeline';
    form.action = '{{ route("admin.timeline.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('addTimelineModal').classList.add('show');
}

function editTimeline(item) {
    document.getElementById('modalTitle').innerText = 'Edit Timeline';
    document.getElementById('nama_kegiatan').value = item.nama_kegiatan;
    document.getElementById('deskripsi').value = item.deskripsi ?? '';
    document.getElementById('penanggung_jawab').value = item.penanggung_jawab ?? '';
    document.getElementById('tanggal_kegiatan').value = item.tanggal_kegiatan;
    document.getElementById('deadline').value = item.deadline ?? '';
    document.getElementById('status_kegiatan').value = item.status_kegiatan;
    document.getElementById('timelineForm').action = '{{ url("admin/timeline") }}/' + item.id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('addTimelineModal').classList.add('show');
}

function closeTimelineModal() {
    document.getElementById('addTimelineModal').classList.remove('show');
}
</script>
@endpush

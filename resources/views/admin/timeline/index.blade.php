@extends('layouts.admin')

@section('title', 'Timeline')
@section('page-title', 'Timeline')

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
            <option value="{{ $event->id }}" {{ $selectedEvent && $selectedEvent->id == $event->id ? 'selected' : '' }}>
                {{ $event->nama_event }}
            </option>
            @endforeach
        </select>
        <button class="btn btn-primary" {{ !$selectedEvent ? 'disabled' : '' }}
                onclick="openAddTimeline()">
            <i class="fas fa-plus"></i> Tambah
        </button>
    </div>
</div>

@if(!$selectedEvent)
<div class="card">
    <div class="empty-state">
        <i class="fas fa-calendar"></i>
        <h3>Pilih event untuk melihat timeline.</h3>
        <p>Silakan pilih event dari dropdown di atas.</p>
    </div>
</div>
@else
<div class="card">
    <div class="card-header">
        <span class="card-title">Timeline — {{ $selectedEvent->nama_event }}</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nama Kegiatan</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($timelines as $i => $item)
            <tr>
                <td style="color:#94a3b8;">{{ $i + 1 }}</td>
                <td style="font-weight:500;">{{ $item->nama_kegiatan }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal_kegiatan)->format('d M Y') }}</td>
                <td>
                    @php
                        $map = ['belum_mulai'=>'badge-pending','berjalan'=>'badge-active','selesai'=>'badge-done'];
                        $cls = $map[$item->status_kegiatan] ?? 'badge-pending';
                        $labels = ['belum_mulai'=>'Belum Mulai','berjalan'=>'Berjalan','selesai'=>'Selesai'];
                    @endphp
                    <span class="badge {{ $cls }}">{{ $labels[$item->status_kegiatan] ?? $item->status_kegiatan }}</span>
                </td>
                <td>
                    <div class="action-btns">
                        <button class="action-btn" title="Edit"
                                onclick='editTimeline({{ $item->id }}, {{ json_encode($item->nama_kegiatan) }}, {{ json_encode(\Carbon\Carbon::parse($item->tanggal_kegiatan)->format("Y-m-d")) }}, {{ json_encode($item->status_kegiatan) }})'>
                            <i class="fas fa-edit" style="font-size:12px;"></i>
                        </button>
                        <form action="{{ route('admin.timeline.destroy', $item->id) }}" method="POST" style="display:inline;"
                              onsubmit="return confirm('Hapus kegiatan ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-btn danger" title="Hapus">
                                <i class="fas fa-trash" style="font-size:12px;"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr class="empty-row"><td colspan="5">Belum ada timeline untuk event ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Add/Edit Modal --}}
<div id="addTimelineModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <span id="modalTitle">Tambah Timeline</span>
            <button class="modal-close" onclick="closeTimelineModal()">
                <i class="fas fa-times"></i>
            </button>
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
                    <label class="form-label">Tanggal Kegiatan *</label>
                    <input type="date" name="tanggal_kegiatan" id="tanggal_kegiatan" class="form-input" required>
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
    document.getElementById('modalTitle').innerText = 'Tambah Timeline';
    document.getElementById('timelineForm').reset();
    document.getElementById('timelineForm').action = '{{ route("admin.timeline.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('addTimelineModal').classList.add('show');
}

function editTimeline(id, nama, tanggal, status) {
    document.getElementById('modalTitle').innerText = 'Edit Timeline';
    document.getElementById('nama_kegiatan').value = nama;
    document.getElementById('tanggal_kegiatan').value = tanggal;
    document.getElementById('status_kegiatan').value = status;
    document.getElementById('timelineForm').action = '{{ url("admin/timeline") }}/' + id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('addTimelineModal').classList.add('show');
}

function closeTimelineModal() {
    document.getElementById('addTimelineModal').classList.remove('show');
}
</script>
@endpush

@extends('layouts.admin')

@section('title', 'Tugas & Tim')
@section('page-title', 'Tugas & Tim')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Tugas &amp; Tim</h1>
        <p>Kelola penugasan operasional ke vendor dan tim internal.</p>
    </div>
    <button class="btn btn-primary" {{ $events->isEmpty() ? 'disabled' : '' }}
            onclick="document.getElementById('addTaskModal').classList.add('show')">
        <i class="fas fa-plus"></i> Buat Tugas
    </button>
</div>

@if($events->isEmpty())
<div class="alert-banner">
    <i class="fas fa-exclamation-triangle"></i>
    Belum ada event. Tugas harus terhubung ke event — buat event terlebih dahulu di Kelola Event.
</div>
@endif

<div class="card">
    <div class="card-header" style="border-bottom:none; padding-bottom:14px;">
        <div class="toolbar" style="margin-bottom:0; flex:1;">
            <div class="search-wrap">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari tugas atau event..." value="{{ request('search') }}">
            </div>
            <select class="select-filter" id="statusFilter">
                <option value="">Semua Status</option>
                <option value="ditugaskan" {{ request('status')=='ditugaskan' ? 'selected' : '' }}>Ditugaskan</option>
                <option value="dikerjakan" {{ request('status')=='dikerjakan' ? 'selected' : '' }}>Dikerjakan</option>
                <option value="selesai" {{ request('status')=='selesai' ? 'selected' : '' }}>Selesai</option>
            </select>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tugas</th>
                <th>Event</th>
                <th>Ditugaskan Ke</th>
                <th>Prioritas</th>
                <th>Deadline</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $task)
            <tr>
                <td style="font-weight:500;">{{ $task->nama_tugas }}</td>
                <td>{{ $task->event->nama_event ?? '-' }}</td>
                <td>{{ $task->vendor->nama_vendor ?? $task->ditugaskan_ke ?? '-' }}</td>
                <td>
                    @php
                        $pMap = ['rendah'=>'badge-gray','sedang'=>'badge-pending','tinggi'=>'badge-cancel'];
                        $pCls = $pMap[$task->prioritas] ?? 'badge-gray';
                    @endphp
                    <span class="badge {{ $pCls }}">{{ ucfirst($task->prioritas) }}</span>
                </td>
                <td>{{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('d M Y') : '-' }}</td>
                <td>
                    @php
                        $sMap = ['ditugaskan'=>'badge-pending','dikerjakan'=>'badge-active','selesai'=>'badge-done'];
                        $sCls = $sMap[$task->status] ?? 'badge-pending';
                        $sLabels = ['ditugaskan'=>'Ditugaskan','dikerjakan'=>'Dikerjakan','selesai'=>'Selesai'];
                    @endphp
                    <span class="badge {{ $sCls }}">{{ $sLabels[$task->status] ?? $task->status }}</span>
                </td>
                <td>
                    <div class="action-btns">
                        <button class="action-btn" title="Edit"
                                onclick='editTask({{ $task->id }}, {{ json_encode($task) }})'>
                            <i class="fas fa-edit" style="font-size:12px;"></i>
                        </button>
                        <form action="{{ route('admin.tasks.destroy', $task->id) }}" method="POST" style="display:inline;"
                              onsubmit="return confirm('Hapus tugas ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-btn danger" title="Hapus">
                                <i class="fas fa-trash" style="font-size:12px;"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr class="empty-row"><td colspan="7">Belum ada tugas.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($tasks->hasPages())
    <div style="padding:16px 24px; border-top:1px solid #f1f5f9;">
        {{ $tasks->links() }}
    </div>
    @endif
</div>

{{-- Add/Edit Task Modal --}}
<div id="addTaskModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <span id="taskModalTitle">Buat Tugas</span>
            <button class="modal-close" onclick="document.getElementById('addTaskModal').classList.remove('show')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="taskForm" action="{{ route('admin.tasks.store') }}" method="POST">
            @csrf
            <input type="hidden" name="_method" id="taskFormMethod" value="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Tugas *</label>
                    <input type="text" name="nama_tugas" id="nama_tugas" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Event *</label>
                    <select name="event_id" id="task_event_id" class="form-input" required>
                        <option value="">-- Pilih Event --</option>
                        @foreach($events as $event)
                        <option value="{{ $event->id }}">{{ $event->nama_event }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Ditugaskan ke (Vendor)</label>
                    <select name="vendor_id" id="task_vendor_id" class="form-input">
                        <option value="">-- Pilih Vendor --</option>
                        @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}">{{ $vendor->nama_vendor }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Prioritas</label>
                        <select name="prioritas" id="prioritas" class="form-input">
                            <option value="rendah">Rendah</option>
                            <option value="sedang" selected>Sedang</option>
                            <option value="tinggi">Tinggi</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Deadline</label>
                        <input type="date" name="deadline" id="deadline" class="form-input">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" id="task_status" class="form-input">
                        <option value="ditugaskan">Ditugaskan</option>
                        <option value="dikerjakan">Dikerjakan</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-input" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('addTaskModal').classList.remove('show')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('searchInput').addEventListener('input', debounce(filterTable, 300));
document.getElementById('statusFilter').addEventListener('change', filterTable);

function filterTable() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    window.location.href = `{{ route('admin.tasks.index') }}?search=${encodeURIComponent(search)}&status=${encodeURIComponent(status)}`;
}
function debounce(fn, delay) {
    let t; return function(...args) { clearTimeout(t); t = setTimeout(() => fn.apply(this, args), delay); };
}

function editTask(id, task) {
    document.getElementById('taskModalTitle').innerText = 'Edit Tugas';
    document.getElementById('nama_tugas').value = task.nama_tugas;
    document.getElementById('task_event_id').value = task.event_id ?? '';
    document.getElementById('task_vendor_id').value = task.vendor_id ?? '';
    document.getElementById('prioritas').value = task.prioritas ?? 'sedang';
    document.getElementById('deadline').value = task.deadline ?? '';
    document.getElementById('task_status').value = task.status ?? 'ditugaskan';
    document.getElementById('deskripsi').value = task.deskripsi ?? '';
    document.getElementById('taskForm').action = '{{ url("admin/tasks") }}/' + id;
    document.getElementById('taskFormMethod').value = 'PUT';
    document.getElementById('addTaskModal').classList.add('show');
}
</script>
@endpush
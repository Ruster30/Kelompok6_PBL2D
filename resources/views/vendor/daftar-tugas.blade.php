@extends('vendor.layouts.app')

@section('title', 'Daftar Tugas')
@section('page-title', 'Daftar Tugas')

@section('content')

<div class="section-card">

    <!-- HEADER -->
    <div class="mb-4">
        <h2 style="font-size:18px; font-weight:600; color:#1a2332; margin:0 0 4px;">Daftar Tugas</h2>
        <p style="font-size:13px; color:#8a9bb0; margin:0;">Perbarui progres, unggah dokumentasi, dan kelola semua tugas Anda.</p>
    </div>

    <!-- TABLE -->
    <div class="table-responsive">
        <table class="table custom-table mb-0">
            <thead>
                <tr>
                    <th>Tugas</th>
                    <th>Event</th>
                    <th>Deadline</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tugas ?? [] as $t)
                    <tr>
                        <td>
                            <div class="fw-semibold" style="color:#1a2332;">{{ $t->nama }}</div>
                            <div style="font-size:12px; color:#94a3b8; margin-top:2px;">{{ $t->deskripsi }}</div>
                        </td>
                        <td style="font-size:13px; color:#64748b;">{{ $t->event->nama_event ?? '-' }}</td>
                        <td style="font-size:13px; color:#64748b;">
                            @if($t->deadline)
                                {{ \Carbon\Carbon::parse($t->deadline)->format('j/n/Y') }}
                            @else -
                            @endif
                        </td>
                        <td>
                            $iconColor = $st === 'selesai' ? 'icon-selesai' : 'icon-pending';
                            @php $st = strtolower($t->status ?? 'pending'); @endphp
                            <span style="display:inline-flex; align-items:center; gap:6px;">
                                <i class="bi bi-exclamation-circle" class="status-icon {{ $iconColor }}"></i>
                                <span class="badge-status badge-{{ $st === 'selesai' ? 'selesai-t' : ($st === 'terlambat' ? 'terlambat' : 'pending') }}">
                                    {{ ucfirst($t->status) }}
                                </span>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <button type="button"
                                    class="action-btn action-btn-teal"
                                    data-bs-toggle="modal"
                                    data-bs-target="#updateModal"
                                    data-tugas-id="{{ $t->id }}"
                                    data-tugas-nama="{{ $t->nama }}">
                                    <i class="bi bi-pencil-square"></i> Update
                                </button>
                                <button type="button"
                                    class="action-btn action-btn-gray"
                                    data-bs-toggle="modal"
                                    data-bs-target="#dokumentasiModal"
                                    data-tugas-id="{{ $t->id }}"
                                    data-tugas-nama="{{ $t->nama }}">
                                    <i class="bi bi-upload"></i> Dokumentasi
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    {{-- Demo static row --}}
                    <tr>
                        <td>
                            <div class="fw-semibold" style="color:#1a2332;">katering</div>
                            <div style="font-size:12px; color:#94a3b8; margin-top:2px;">menyediakan nasi goreng</div>
                        </td>
                        <td style="font-size:13px; color:#64748b;">Konser Feast</td>
                        <td style="font-size:13px; color:#64748b;">13/6/2026</td>
                        <td>
                            <span style="display:inline-flex; align-items:center; gap:6px;">
                                <i class="bi bi-exclamation-circle" style="color:#8a9bb0; font-size:14px;"></i>
                                <span class="badge-status badge-pending">Pending</span>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <button type="button"
                                    class="action-btn action-btn-teal"
                                    data-bs-toggle="modal"
                                    data-bs-target="#updateModal"
                                    data-tugas-id="1"
                                    data-tugas-nama="katering">
                                    <i class="bi bi-pencil-square"></i> Update
                                </button>
                                <button type="button"
                                    class="action-btn action-btn-gray"
                                    data-bs-toggle="modal"
                                    data-bs-target="#dokumentasiModal"
                                    data-tugas-id="1"
                                    data-tugas-nama="katering">
                                    <i class="bi bi-upload"></i> Dokumentasi
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


<!-- ===== MODAL: UNGGAH DOKUMENTASI ===== -->
<div class="modal fade" id="dokumentasiModal" tabindex="-1" aria-labelledby="dokumentasiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
        <div class="modal-content" style="padding:24px 0 20px;">

            <!-- Header -->
            <div class="modal-header-custom">
                <div class="modal-icon">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <h5 id="dokumentasiModalLabel">Unggah Dokumentasi</h5>
            </div>
            <p class="modal-subtext" id="modalTugasNama">katering</p>

            <hr style="margin: 16px 0 20px; border-color:#f1f5f9;">

            <form action="{{ route('vendor.dokumentasi.store') }}" method="POST" id="dokumentasiForm">
                @csrf
                <input type="hidden" name="tugas_id" id="dokTugasId">

                <div style="padding: 0 24px;">

                    <!-- Nama File -->
                    <div class="mb-3">
                        <label class="form-label-custom">
                            Nama File <span class="req">*</span>
                        </label>
                        <input
                            type="text"
                            name="nama_file"
                            class="form-control-custom"
                            placeholder="contoh: foto-pemasangan-banner.jpg"
                            required
                        >
                        <div class="form-hint">Format didukung: JPG, PNG, PDF, DOCX, MP4</div>
                    </div>

                    <!-- URL File -->
                    <div class="mb-3">
                        <label class="form-label-custom">
                            URL File <span class="req">*</span>
                        </label>
                        <input
                            type="url"
                            name="url_file"
                            class="form-control-custom"
                            placeholder="https://drive.google.com/..."
                            required
                        >
                    </div>

                    <!-- Catatan -->
                    <div class="mb-4">
                        <label class="form-label-custom">Catatan (Opsional)</label>
                        <textarea
                            name="catatan"
                            class="form-control-custom"
                            rows="3"
                            style="resize:vertical;"
                        ></textarea>
                    </div>

                </div>

                <!-- Footer -->
                <div style="padding: 0 24px; display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" class="action-btn action-btn-gray" style="padding:9px 20px; font-size:14px;" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn-teal">
                        Unggah
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


<!-- ===== MODAL: UPDATE STATUS ===== -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:460px;">
        <div class="modal-content" style="padding:24px 0 20px;">

            <!-- Header -->
            <div class="modal-header-custom">
                <div class="modal-icon">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <h5 id="updateModalLabel">Update Progres Tugas</h5>
            </div>
            <p class="modal-subtext" id="updateTugasNama">katering</p>

            <hr style="margin: 16px 0 20px; border-color:#f1f5f9;">

            <form action="{{ route('vendor.tugas.update') }}" method="POST" id="updateForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="tugas_id" id="updateTugasId">

                <div style="padding: 0 24px;">

                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label-custom">
                            Status <span class="req">*</span>
                        </label>
                        <select name="status" class="form-control-custom" style="cursor:pointer;" required>
                            <option value="pending">Pending</option>
                            <option value="on_progress">On Progress</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>

                    <!-- Catatan -->
                    <div class="mb-4">
                        <label class="form-label-custom">Catatan Progres (Opsional)</label>
                        <textarea
                            name="catatan"
                            class="form-control-custom"
                            rows="3"
                            placeholder="Tambahkan catatan mengenai perkembangan tugas ini..."
                            style="resize:vertical;"
                        ></textarea>
                    </div>

                </div>

                <!-- Footer -->
                <div style="padding: 0 24px; display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" class="action-btn action-btn-gray" style="padding:9px 20px; font-size:14px;" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn-teal">
                        Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Inject tugas data into dokumentasi modal
const dokumentasiModal = document.getElementById('dokumentasiModal');
dokumentasiModal.addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    document.getElementById('dokTugasId').value = btn.getAttribute('data-tugas-id');
    document.getElementById('modalTugasNama').textContent = btn.getAttribute('data-tugas-nama');
});

// Inject tugas data into update modal
const updateModal = document.getElementById('updateModal');
updateModal.addEventListener('show.bs.modal', function(e) {
    const btn = e.relatedTarget;
    document.getElementById('updateTugasId').value = btn.getAttribute('data-tugas-id');
    document.getElementById('updateTugasNama').textContent = btn.getAttribute('data-tugas-nama');
});
</script>
@endpush

@extends('layouts.vendor')

@section('title', 'Daftar Tugas')
@section('page-title', 'Daftar Tugas')
@section('breadcrumbs')
    <a href="{{ route('vendor.ringkasan') }}">Dashboard</a><span class="separator">/</span><span>Daftar Tugas</span>
@endsection

@section('content')

<div class="section-card">

    <div class="mb-4">
        <h2 class="section-card-title">
            Daftar Tugas
        </h2>

        <p class="text-muted mb-0" style="font-size: 14px;">
            Perbarui progres, tambahkan catatan, dan unggah dokumentasi tugas Anda.
        </p>
    </div>

    <div class="table-responsive">

        <table class="table card-view-mobile custom-table align-middle mb-0">

            <thead>
                <tr>
                    <th>TUGAS</th>
                    <th>EVENT</th>
                    <th>PRIORITAS</th>
                    <th>DEADLINE</th>
                    <th>PROGRES</th>
                    <th>STATUS</th>
                    <th>AKSI</th>
                </tr>
            </thead>

            <tbody>

            @forelse($tugas as $t)

                <tr>

                    <td>
                        <div class="fw-semibold">
                            {{ $t->nama_tugas }}
                        </div>

                        <small class="text-muted">
                            {{ $t->deskripsi }}
                        </small>
                    </td>

                    <td>
                        {{ $t->event->nama_event ?? '-' }}
                    </td>

                    <td>

                        @if($t->prioritas == 'tinggi')
                            <span class="badge bg-danger">
                                Tinggi
                            </span>

                        @elseif($t->prioritas == 'sedang')
                            <span class="badge bg-warning text-dark">
                                Sedang
                            </span>

                        @else
                            <span class="badge bg-secondary">
                                Rendah
                            </span>
                        @endif

                    </td>

                    <td>
                        {{ $t->deadline ? \Carbon\Carbon::parse($t->deadline)->format('d/m/Y') : '-' }}
                    </td>

                    <td>

                        @if($t->status == 'selesai')

                            <div class="progress" style="height:8px;">
                                <div class="progress-bar bg-success"
                                     style="width:100%">
                                </div>
                            </div>

                         

                        @elseif($t->status == 'dikerjakan')

                            <div class="progress" style="height:8px;">
                                <div class="progress-bar bg-primary"
                                     style="width:50%">
                                </div>
                            </div>

                       

                        @else

                            <div class="progress" style="height:8px;">
                                <div class="progress-bar bg-secondary"
                                     style="width:0%">
                                </div>
                            </div>

            

                        @endif

                    </td>

                    <td>

                        @if($t->status == 'selesai')
                            <span class="badge bg-success">
                                Selesai
                            </span>

                        @elseif($t->status == 'dikerjakan')
                            <span class="badge bg-primary">
                                Dikerjakan
                            </span>

                        @else
                            <span class="badge bg-warning text-dark">
                                Ditugaskan
                            </span>
                        @endif

                    </td>

                    <td style="white-space: nowrap;">
                        <div class="d-flex gap-2">
                            <button
                                class="action-btn action-btn-teal"
                                data-bs-toggle="modal"
                                data-bs-target="#updateModal"
                                data-tugas-id="{{ $t->id }}"
                                data-tugas-nama="{{ $t->nama_tugas }}"
                            >
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button
                                class="action-btn action-btn-gray"
                                data-bs-toggle="modal"
                                data-bs-target="#documentationModal"
                                data-tugas-id="{{ $t->id }}"
                                data-tugas-nama="{{ $t->nama_tugas }}"
                            >
                                <i class="bi bi-camera"></i> 
                            </button>
                        </div>
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="7" class="text-center py-5">

                        <div class="text-muted">
                            Belum ada tugas yang diberikan
                        </div>

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

</div>

{{-- MODAL UPDATE STATUS --}}

<div class="modal fade"
     id="updateModal"
     tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                action="{{ route('vendor.tugas.update') }}"
                method="POST"
            >

                @csrf
                @method('PUT')

                <div class="modal-header">

                    <h5 class="modal-title">
                        Update Status Tugas
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <input
                        type="hidden"
                        name="tugas_id"
                        id="updateTugasId"
                    >

                    <div class="mb-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select"
                        >
                            <option value="ditugaskan">
                                Ditugaskan
                            </option>

                            <option value="dikerjakan">
                                Dikerjakan
                            </option>

                            <option value="selesai">
                                Selesai
                            </option>
                        </select>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Simpan
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

{{-- MODAL UPLOAD DOKUMENTASI --}}
<div class="modal fade"
     id="documentationModal"
     tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <form
                action="{{ route('vendor.dokumentasi.store') }}"
                method="POST"
                enctype="multipart/form-data"
            >

                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">
                        Upload Dokumentasi
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body">
                    <input
                        type="hidden"
                        name="tugas_id"
                        id="documentationTugasId"
                    >

                    <div class="mb-3">
                        <label class="form-label">
                            Judul Dokumentasi
                        </label>
                        <input
                            type="text"
                            name="judul"
                            id="documentationTitle"
                            class="form-control"
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            Catatan
                        </label>
                        <textarea
                            name="catatan"
                            class="form-control"
                            rows="3"
                        ></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            File Foto/Video <span class="text-muted">(Dapat memilih lebih dari 1 file)</span>
                        </label>
                        <input
                            type="file"
                            name="file[]"
                            class="form-control"
                            accept=".jpg,.jpeg,.png,.mp4,.mov"
                            multiple
                            required
                            id="fileInput"
                        >
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle"></i> 
                            Anda dapat memilih beberapa file sekaligus. Format: JPG, PNG, MP4, MOV. Maks 20MB per file.
                        </small>
                        <div id="filePreview" class="mt-2"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Upload
                    </button>
                </div>

            </form>

        </div>

    </div>

</div>

@endsection

@push('scripts')

<script>

const updateModal =
document.getElementById('updateModal');

updateModal.addEventListener(
'show.bs.modal',
function(event){

    let button =
    event.relatedTarget;

    let tugasId =
    button.getAttribute(
    'data-tugas-id'
    );

    document.getElementById(
    'updateTugasId'
    ).value = tugasId;
});

const documentationModal =
document.getElementById('documentationModal');

documentationModal.addEventListener(
'show.bs.modal',
function(event){

    let button =
    event.relatedTarget;

    let tugasId =
    button.getAttribute(
    'data-tugas-id'
    );

    let tugasNama =
    button.getAttribute(
    'data-tugas-nama'
    );

    document.getElementById(
    'documentationTugasId'
    ).value = tugasId;

    document.getElementById(
    'documentationTitle'
    ).value = 'Dokumentasi ' + tugasNama;
    
    // Reset file input dan preview saat modal dibuka
    document.getElementById('fileInput').value = '';
    document.getElementById('filePreview').innerHTML = '';
});

// Preview file yang dipilih
document.getElementById('fileInput').addEventListener('change', function(e) {
    const previewContainer = document.getElementById('filePreview');
    previewContainer.innerHTML = '';
    
    const files = Array.from(e.target.files);
    
    if (files.length === 0) {
        return;
    }
    
    const fileList = document.createElement('div');
    fileList.className = 'alert alert-info py-2 px-3 mb-0';
    fileList.style.fontSize = '13px';
    
    const fileItems = files.map((file, index) => {
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        const fileType = file.type.includes('video') ? 'Ã°Å¸Å½Â¥' : 'Ã°Å¸â€œÂ·';
        return `<div>${fileType} <strong>${file.name}</strong> (${fileSize} MB)</div>`;
    }).join('');
    
    fileList.innerHTML = `
        <div style="font-weight:600; margin-bottom:4px;">
            <i class="bi bi-check-circle-fill text-success"></i> 
            ${files.length} file dipilih:
        </div>
        ${fileItems}
    `;
    
    previewContainer.appendChild(fileList);
});

</script>

@endpush

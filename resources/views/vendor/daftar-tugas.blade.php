@extends('layouts.vendor')

@section('title', 'Daftar Tugas')
@section('page-title', 'Daftar Tugas')

@section('content')

<div class="section-card">

    <div class="mb-4">
        <h2 class="section-card-title">
            Daftar Tugas
        </h2>

        <p class="text-muted mb-0">
            Perbarui progres, tambahkan catatan, dan unggah dokumentasi tugas Anda.
        </p>
    </div>

    <div class="table-responsive">

        <table class="table custom-table align-middle mb-0">

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

                            <small>100%</small>

                        @elseif($t->status == 'dikerjakan')

                            <div class="progress" style="height:8px;">
                                <div class="progress-bar bg-primary"
                                     style="width:50%">
                                </div>
                            </div>

                            <small>50%</small>

                        @else

                            <div class="progress" style="height:8px;">
                                <div class="progress-bar bg-secondary"
                                     style="width:0%">
                                </div>
                            </div>

                            <small>0%</small>

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

                    <td>

                        <button
                            class="btn btn-sm btn-outline-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#updateModal"
                            data-tugas-id="{{ $t->id }}"
                            data-tugas-nama="{{ $t->nama_tugas }}"
                        >
                            Update
                        </button>

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

</script>

@endpush
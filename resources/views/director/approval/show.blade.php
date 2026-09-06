@extends('layouts.director')

@section('title', 'Review Dokumen')
@section('page-title', 'Review Dokumen')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Review Dokumen</h1>
        <p>
            <a href="{{ route('director.approval.index') }}" class="text-decoration-none">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Approval Dashboard
            </a>
        </p>
    </div>
</div>

<div class="row g-4">

    {{-- --- HEADER ------------------------------------------- --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                    <div>
                        <h4 class="card-title mb-1 fw-bold">{{ $document->nama_file }}</h4>
                        <div class="d-flex align-items-center gap-2 flex-wrap mt-2">
                            <x-document-status-badge :status="$document->status" />
                            <x-document-source-badge :source="$document->document_source" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- --- NOMOR SURAT -------------------------------------- --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pt-3 px-3">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-hashtag me-2 text-primary"></i>Nomor Surat
                </h5>
            </div>
            <div class="card-body pt-2 px-3">
                @php
                    $docNumber = optional($document->numbering)->document_number;
                    $docStatus = $document->status;
                    $numberBadge = "";
                    $numberInfo = "";
                    if ($docStatus === \App\Enums\DocumentStatus::Draft) {
                        $numberBadge = "badge bg-warning text-dark";
                        $numberInfo = "Nomor surat masih dapat diubah oleh Admin.";
                    } elseif ($docStatus === \App\Enums\DocumentStatus::Pending) {
                        $numberBadge = "badge bg-primary";
                        $numberInfo = "Nomor surat telah dikunci dan sedang direview oleh Director.";
                    } elseif ($docStatus === \App\Enums\DocumentStatus::Approved) {
                        $numberBadge = "badge bg-success";
                        $numberInfo = "Nomor surat telah disetujui bersama dokumen.";
                    } elseif ($docStatus === \App\Enums\DocumentStatus::Published) {
                        $numberBadge = "badge bg-dark";
                        $numberInfo = "Nomor surat merupakan bagian dari arsip resmi.";
                    }
                @endphp

                <div class="d-flex align-items-center gap-3 flex-wrap">
                    @if($docNumber)
                    <span style="font-family:monospace;font-size:18px;font-weight:700;color:#0f172a;">
                        {{ $docNumber }}
                    </span>
                    @else
                    <span class="badge bg-warning text-dark" style="font-size:13px;padding:6px 14px;">
                        <i class="bi bi-exclamation-triangle me-1"></i> Nomor surat belum diisi.
                    </span>
                    @endif

                    @if($numberBadge)
                    <span class="{{ $numberBadge }}" style="font-size:12px;padding:4px 12px;">
                        @if($docStatus === \App\Enums\DocumentStatus::Draft)
                            Draft
                        @elseif($docStatus === \App\Enums\DocumentStatus::Pending || $docStatus === \App\Enums\DocumentStatus::Approved)
                            <i class="bi bi-lock-fill me-1"></i>Locked
                        @elseif($docStatus === \App\Enums\DocumentStatus::Published)
                            Published
                        @endif
                    </span>
                    @endif
                </div>

                @if($numberInfo)
                <div class="text-muted small mt-2">
                    <i class="bi bi-info-circle me-1"></i> {{ $numberInfo }}
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- --- INFORMASI DOKUMEN ------------------------------- --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0 pt-3 px-3">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-info-circle me-2 text-primary"></i>Informasi Dokumen
                </h5>
            </div>
            <div class="card-body pt-2 px-3">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted ps-0" style="width:140px;">Nama File</td>
                        <td class="fw-medium">{{ $document->nama_file }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Kategori</td>
                        <td class="fw-medium">
                            @php
                                $catLabels = ['official' => 'Official', 'general' => 'General', 'invoice' => 'Invoice', 'receipt' => 'Receipt'];
                            @endphp
                            {{ $catLabels[$document->document_category?->value] ?? ucfirst($document->document_category?->value ?? '-') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Template</td>
                        <td class="fw-medium">{{ $document->template?->nama_template ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Dibuat Oleh</td>
                        <td class="fw-medium">{{ $document->user?->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Dibuat Pada</td>
                        <td class="fw-medium">{{ $document->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Diperbarui</td>
                        <td class="fw-medium">{{ $document->updated_at->format('d M Y, H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- --- INFORMASI EVENT --------------------------------- --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0 pt-3 px-3">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-calendar-alt me-2 text-success"></i>Informasi Event
                </h5>
            </div>
            <div class="card-body pt-2 px-3">
                @if($document->event)
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted ps-0" style="width:140px;">Nama Event</td>
                        <td class="fw-medium">{{ $document->event->nama_event }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Tanggal Event</td>
                        <td class="fw-medium">{{ $document->event->tanggal_event?->format('d M Y') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Client</td>
                        <td class="fw-medium">{{ $document->event->client?->name ?? '-' }}</td>
                    </tr>
                </table>
                @else
                <p class="text-muted mb-0"><i class="fas fa-minus-circle me-1"></i> Dokumen tidak terkait dengan event.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- --- PREVIEW DOKUMEN --------------------------------- --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            @php
                $directorPdfUrl = null;
                if ($document->file_path
                    && \Illuminate\Support\Facades\Storage::disk('public')->exists($document->file_path)
                    && ($document->mime_type === 'application/pdf' || str_ends_with($document->file_path, '.pdf'))) {
                    $directorPdfUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($document->file_path);
                }
            @endphp
            <div class="card-header bg-white border-bottom-0 pt-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-file-pdf me-2 text-danger"></i>Preview Dokumen
                </h5>
                @if($directorPdfUrl)
                <div class="d-flex gap-2">
                    <a href="{{ $directorPdfUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Buka PDF
                    </a>
                    <a href="{{ $directorPdfUrl }}" download class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-download me-1"></i>Download PDF
                    </a>
                </div>
                @endif
            </div>
            <div class="card-body p-2">
                @if($directorPdfUrl)
                <iframe src="{{ $directorPdfUrl }}" class="w-100 border-0" style="width:100%;height:700px;" title="Preview Dokumen"></iframe>
                @else
                    <i class="fas fa-file-pdf text-muted" style="font-size:48px;opacity:0.3;"></i>
                    <p class="text-muted mt-3 mb-0">Preview tidak tersedia.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- --- TIMELINE ---------------------------------------- --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0 pt-3 px-3">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-clock me-2 text-warning"></i>Timeline
                </h5>
            </div>
            <div class="card-body pt-2 px-3">
                <ul class="list-unstyled mb-0">
                    <li class="d-flex align-items-center py-2 border-bottom">
                        <i class="bi bi-check-circle-fill text-success me-3 fs-5"></i>
                        <div>
                            <div class="fw-medium">Created</div>
                            <small class="text-muted">{{ $document->created_at->format('d M Y, H:i') }}</small>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- --- NOMOR SURAT ------------------------------------- --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0 pt-3 px-3">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-hashtag me-2 text-info"></i>Nomor Surat
                </h5>
            </div>
            <div class="card-body pt-2 px-3 d-flex align-items-center">
                @php $nomor = optional($document->numbering)->document_number; @endphp
                @if($nomor)
                <span class="fw-medium">{{ $nomor }}</span>
                @else
                <span class="text-muted">
                    <i class="fas fa-minus-circle me-1"></i> Belum diterbitkan.
                </span>
                @endif
            </div>
        </div>
    </div>

    {{-- --- QR CODE ----------------------------------------- --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0 pt-3 px-3">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-qr-code me-2 text-dark"></i>QR Code
                </h5>
            </div>
            <div class="card-body pt-2 px-3 d-flex flex-column align-items-center justify-content-center text-center">
                @php $qr = optional($document->qrVerification); @endphp
                @if($qr && $qr->qr_path)
                <img src="{{ Storage::disk("public")->url($qr->qr_path) }}" alt="QR Code" style="width:150px;height:150px;margin-bottom:8px;">
                <p class="text-muted small mb-0">Scan untuk verifikasi</p>
                @else
                <div class="mb-2">
                    <i class="bi bi-qr-code text-muted" style="font-size:56px;opacity:0.3;"></i>
                </div>
                <p class="text-muted fw-medium mb-1">QR belum diterbitkan.</p>
                <small class="text-muted" style="font-size:11px;">Dokumen belum dipublikasikan.</small>
                @endif
            </div>
        </div>
    </div>

    {{-- --- TAHAP BERIKUTNYA -------------------------------- --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-4">
                <div class="mb-2">
                    <i class="fas fa-arrow-right text-muted" style="font-size:32px;opacity:0.5;"></i>
                </div>
                <h5 class="fw-bold mb-1">Tahap Berikutnya</h5>
                <p class="text-muted mb-0">Dokumen siap untuk proses Approval Director.</p>
            </div>
        </div>
    </div>



    @php
        $directorStatus    = $document->status;
        $directorGenerated = $document->document_source === \App\Enums\DocumentSource::Generated;
        $directorPending   = $directorStatus === \App\Enums\DocumentStatus::Pending;
        $directorApproved  = $directorStatus === \App\Enums\DocumentStatus::Approved;
        $directorPublished = $directorStatus === \App\Enums\DocumentStatus::Published;
        $directorRejected  = $directorStatus === \App\Enums\DocumentStatus::Rejected;
    @endphp

    {{-- --- KEPUTUSAN DIRECTOR ------------------------------- --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pt-3 px-3">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-gavel me-2 text-primary"></i>Keputusan Director
                </h5>
            </div>
            <div class="card-body pt-2 px-3">
                @if($directorPending && $directorGenerated)
                    <div class="row g-3">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#approveModal">
                                <i class="bi bi-check-circle me-1"></i> Approve
                            </button>
                        </div>

                        <div class="col-md-6">
                            <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle me-1"></i> Reject
                            </button>
                        </div>
                    </div>
                @elseif($directorApproved)
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge bg-success" style="font-size:13px;padding:6px 14px;">
                            <i class="bi bi-check2-circle me-1"></i> Approved
                        </span>
                        <span class="text-muted small">Dokumen telah disetujui. Klik Publish untuk menerbitkan.</span>
                    </div>
                @elseif($directorPublished)
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge bg-dark" style="font-size:13px;padding:6px 14px;">
                            <i class="bi bi-check2-all me-1"></i> Published
                        </span>
                        <span class="text-muted small">Dokumen telah dipublish dan terkunci permanen.</span>
                    </div>
                @elseif($directorRejected)
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge bg-danger" style="font-size:13px;padding:6px 14px;">
                            <i class="bi bi-x-octagon me-1"></i> Rejected
                        </span>
                        <span class="text-muted small">Dokumen telah ditolak. Tidak ada aksi lebih lanjut.</span>
                    </div>
                @else
                    <p class="text-muted mb-0">Tidak ada aksi yang tersedia untuk status ini.</p>
                @endif

            </div>
        </div>
    </div>

    {{-- ==================================== MODAL APPROVE --}}
    @if($directorPending && $directorGenerated)
    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('director.approval.approve', $document->id) }}" id="approveForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="approveModalLabel">Konfirmasi Approve</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted mb-3">
                            Dokumen <strong>{{ $document->nama_file }}</strong> akan disetujui.
                            Masukkan PIN konfirmasi untuk melanjutkan.
                        </p>
                        <div class="mb-3">
                            <label for="approve_pin" class="form-label fw-medium">PIN Konfirmasi <span class="text-danger">*</span></label>
                            <input type="password" name="pin" id="approve_pin" class="form-control @error('pin') is-invalid @enderror" placeholder="Masukkan 6 digit PIN" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="off" required>
                            @error('pin')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i> Konfirmasi Approve
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ==================================== MODAL REJECT ---}}
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('director.approval.reject', $document->id) }}" id="rejectForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel">Konfirmasi Reject</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="reason" class="form-label fw-medium">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea name="reason" id="reason" rows="3" class="form-control @error('reason') is-invalid @enderror" placeholder="Jelaskan alasan penolakan..." required maxlength="1000"></textarea>
                            @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Maksimal 1000 karakter.</div>
                        </div>

                        <div class="mb-3">
                            <label for="reject_pin" class="form-label fw-medium">PIN Konfirmasi <span class="text-danger">*</span></label>
                            <input type="password" name="pin" id="reject_pin" class="form-control @error('pin') is-invalid @enderror" placeholder="Masukkan 6 digit PIN" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="off" required>
                            @error('pin')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-circle me-1"></i> Konfirmasi Reject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
{{-- ==================================== PUBLISH SECTION --}}
    @if($directorApproved)
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pt-3 px-3">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-check-double me-2 text-success"></i>Publish Dokumen
                </h5>
            </div>
            <div class="card-body pt-2 px-3">
                <p class="text-muted small mb-3">
                    Dokumen telah disetujui. Klik Publish untuk menerbitkan dokumen secara resmi.
                </p>
                <form method="POST" action="{{ route('director.approval.publish', $document->id) }}" id="publishForm" onsubmit="return swalPublish(this, 'Publish Dokumen?', 'Dokumen akan dipublikasikan dan dikunci secara permanen. Setelah dipublish, dokumen tidak dapat diubah kembali.');">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check2-circle me-1"></i> Publish
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var approveModalEl = document.getElementById('approveModal');
    var rejectModalEl  = document.getElementById('rejectModal');
    var approvePin     = document.getElementById('approve_pin');
    var rejectPin      = document.getElementById('reject_pin');

    // Fokus ke input PIN saat modal dibuka.
    if (approveModalEl && approvePin) {
        approveModalEl.addEventListener('shown.bs.modal', function () {
            approvePin.focus();
        });
    }
    if (rejectModalEl && rejectPin) {
        rejectModalEl.addEventListener('shown.bs.modal', function () {
            rejectPin.focus();
        });
    }

    // Jangan simpan PIN: kosongkan setiap kali modal ditutup.
    if (approveModalEl && approvePin) {
        approveModalEl.addEventListener('hidden.bs.modal', function () {
            approvePin.value = '';
        });
    }
    if (rejectModalEl && rejectPin) {
        rejectModalEl.addEventListener('hidden.bs.modal', function () {
            rejectPin.value = '';
        });
    }

    @php
        // Buka kembali modal terkait ketika ada validation error dari backend.
        $reopenApproveModal = $errors->has('pin') && old('reason') === null;
        $reopenRejectModal  = $errors->has('reason') || old('reason') !== null;
    @endphp

    @if(isset($reopenApproveModal) && $reopenApproveModal)
    if (approveModalEl && window.bootstrap) {
        bootstrap.Modal.getOrCreateInstance(approveModalEl).show();
    }
    @endif
    @if(isset($reopenRejectModal) && $reopenRejectModal)
    if (rejectModalEl && window.bootstrap) {
        bootstrap.Modal.getOrCreateInstance(rejectModalEl).show();
    }
    @endif
});
</script>
@endpush
@endsection
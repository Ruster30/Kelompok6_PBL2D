@extends('layouts.admin')

@section('title', 'Detail Dokumen')
@section('page-title', 'Detail Dokumen')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Detail Dokumen</h1>
        <p>
            <a href="{{ route('admin.document_builder.index') }}" class="text-decoration-none">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Document Builder
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
                            <span class="badge bg-label-info fs-6">{{ $document->tipe_label }}</span>
                            <x-document-status-badge :status="$document->status" />
                            <x-document-source-badge :source="$document->document_source" />
                        </div>
                    </div>
                </div>
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
                        <td class="fw-medium">{{ ucfirst($document->document_category?->value ?? '-') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-0">Source</td>
                        <td class="fw-medium">
                            <x-document-source-badge :source="$document->document_source" />
                        </td>
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
                <p class="text-muted mb-0">
                    <i class="fas fa-minus-circle me-1"></i> Dokumen tidak terkait dengan event.
                </p>
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
                    <li class="d-flex align-items-center py-2 border-bottom">
                        <i class="bi bi-clock-fill text-secondary me-3 fs-5"></i>
                        <div>
                            <div class="fw-medium">Submitted</div>
                            <small class="text-muted">-</small>
                        </div>
                    </li>
                    <li class="d-flex align-items-center py-2 border-bottom">
                        <i class="bi bi-clock-fill text-secondary me-3 fs-5"></i>
                        <div>
                            <div class="fw-medium">Approved</div>
                            <small class="text-muted">-</small>
                        </div>
                    </li>
                    <li class="d-flex align-items-center py-2">
                        <i class="bi bi-clock-fill text-secondary me-3 fs-5"></i>
                        <div>
                            <div class="fw-medium">Published</div>
                            <small class="text-muted">-</small>
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
                @php $nomor = optional($document->numbering)->nomor_dokumen; @endphp
                @if($nomor)
                <span class="fw-medium">{{ $nomor }}</span>
                @else
                <span class="text-muted">
                    <i class="fas fa-minus-circle me-1"></i> Belum diterbitkan
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
                <div class="mb-2">
                    <i class="bi bi-qr-code text-muted" style="font-size:56px;opacity:0.3;"></i>
                </div>
                <p class="text-muted fw-medium mb-1">QR belum diterbitkan.</p>
                <small class="text-muted" style="font-size:11px;">Dokumen belum dipublikasikan.</small>
            </div>
        </div>
    </div>

    {{-- --- APPROVAL HISTORY -------------------------------- --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0 pt-3 px-3">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-clipboard-list me-2 text-secondary"></i>Approval History
                </h5>
            </div>
            <div class="card-body pt-2 px-3 d-flex align-items-center justify-content-center text-center">
                <div class="py-3">
                    <i class="fas fa-inbox text-muted" style="font-size:36px;opacity:0.4;"></i>
                    <p class="text-muted mt-2 mb-0">Belum ada riwayat approval.</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
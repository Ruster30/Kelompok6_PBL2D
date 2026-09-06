@extends('layouts.director')

@section('title', 'Detail Riwayat Approval')
@section('page-title', 'Detail Riwayat Approval')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Detail Riwayat Approval</h1>
        <p>
            <a href="{{ route('director.approval.history') }}" class="text-decoration-none">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Riwayat
            </a>
        </p>
    </div>
</div>

@php $approval = $document->approvals?->firstWhere('status', $document->status->value); @endphp

<div class="row g-4">
    {{-- Informasi Dokumen --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0 pt-3 px-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-file-alt me-2 text-primary"></i>Informasi Dokumen</h5>
            </div>
            <div class="card-body pt-2 px-3">
                <table class="table table-borderless mb-0">
                    <tr><td class="text-muted ps-0" style="width:140px;">Nomor Dokumen</td>
                        <td class="fw-medium">{{ optional($document->numbering)->document_number ?? '-' }}</td></tr>
                    <tr><td class="text-muted ps-0">Nama File</td>
                        <td class="fw-medium">{{ $document->nama_file }}</td></tr>
                    <tr><td class="text-muted ps-0">Jenis</td>
                        <td class="fw-medium">{{ $document->tipe_label }}</td></tr>
                    <tr><td class="text-muted ps-0">Event</td>
                        <td class="fw-medium">{{ $document->event?->nama_event ?? '-' }}</td></tr>
                    <tr><td class="text-muted ps-0">Client</td>
                        <td class="fw-medium">{{ $document->event?->client?->name ?? '-' }}</td></tr>
                    <tr><td class="text-muted ps-0">Dibuat</td>
                        <td class="fw-medium">{{ $document->created_at->format('d M Y, H:i') }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    {{-- Informasi Approval --}}
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0 pt-3 px-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-clipboard-check me-2 text-success"></i>Informasi Approval</h5>
            </div>
            <div class="card-body pt-2 px-3">
                <table class="table table-borderless mb-0">
                    <tr><td class="text-muted ps-0" style="width:140px;">Status</td>
                        <td class="fw-medium"><x-document-status-badge :status="$document->status" /></td></tr>
                    <tr><td class="text-muted ps-0">Disetujui/Ditolak Oleh</td>
                        <td class="fw-medium">{{ $approval?->approvedBy?->name ?? '-' }}</td></tr>
                    <tr><td class="text-muted ps-0">Jabatan</td>
                        <td class="fw-medium">Director</td></tr>
                    <tr><td class="text-muted ps-0">Tanggal Approval</td>
                        <td class="fw-medium">{{ $approval?->reviewed_at?->format('d M Y, H:i') ?? '-' }}</td></tr>
                    @if($approval?->approval_note)
                    <tr><td class="text-muted ps-0">Catatan</td>
                        <td class="fw-medium"><em>"{{ $approval->approval_note }}"</em></td></tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- Preview PDF --}}
    @php
        $fileExists = $document->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($document->file_path);
    @endphp
    @if($fileExists)
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 pt-3 px-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="fas fa-file-pdf me-2 text-danger"></i>File Dokumen</h5>
                <a href="{{ route('director.approval.history-download', $document->id) }}" class="btn btn-sm btn-outline-dark">
                    <i class="fas fa-download me-1"></i> Download PDF
                </a>
            </div>
            <div class="card-body p-0">
                <iframe src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($document->file_path) }}" style="width:100%;height:500px;border:none;" title="Preview"></iframe>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
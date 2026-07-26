@extends('layouts.admin')

@section('title', 'Preview Dokumen')
@section('page-title', 'Preview Dokumen')

@section('content')
<div class="page-header" style="margin-bottom:16px;">
    <div class="page-header-left">
        <h1>Preview Dokumen</h1>
        <p>
            <a href="{{ route('admin.document_builder.index') }}" style="color:#3b82f6;text-decoration:none;">
                <i class="fas fa-arrow-left" style="margin-right:4px;"></i> Kembali ke Document Builder
            </a>
        </p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if($document->status === \App\Enums\DocumentStatus::Draft)
{{-- --- DRAFT ACTIONS ------------------------------------- --}}
<div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:24px;margin-bottom:24px;">
    <h2 style="font-size:16px;font-weight:700;color:#0f172a;margin-bottom:16px;">
        <i class="fas fa-pen" style="color:#f59e0b;margin-right:6px;"></i>
        Draft Management
    </h2>
    <p style="color:#64748b;font-size:13px;margin-bottom:20px;">
        Dokumen ini masih dalam status Draft. Anda dapat mengedit, menghapus, atau mengirimkan dokumen untuk approval.
    </p>

    {{-- Edit Nama --}}
    <div style="margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid #e2e8f0;">
        <h3 style="font-size:14px;font-weight:600;color:#0f172a;margin-bottom:12px;">
            <i class="fas fa-edit me-1"></i> Edit Nama Dokumen
        </h3>
        <form method="POST" action="{{ route("admin.document_builder.rename", $document->id) }}" style="display:flex;gap:12px;align-items:flex-end;">
            @csrf
            @method("PUT")
            <div style="flex:1;">
                <input type="text" name="nama_file" value="{{ $document->nama_file }}" class="form-input" style="width:100%;padding:10px 14px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;" required maxlength="255">
            </div>
            <button type="submit" style="background:#f59e0b;color:#fff;border:none;padding:10px 24px;border-radius:8px;font-weight:600;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:6px;white-space:nowrap;">
                <i class="fas fa-save"></i> Simpan
            </button>
        </form>
    </div>

    {{-- Submit & Delete --}}
    <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <form method="POST" action="{{ route("admin.document_builder.submit", $document->id) }}">
            @csrf
            <button type="submit" style="background:#6366f1;color:#fff;border:none;padding:10px 24px;border-radius:8px;font-weight:600;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                <i class="fas fa-paper-plane"></i> Submit Approval
            </button>
        </form>

        <form method="POST" action="{{ route("admin.document_builder.destroy", $document->id) }}" onsubmit="return confirm('Hapus draft dokumen ini? Tindakan ini tidak dapat dibatalkan.');">
            @csrf
            @method("DELETE")
            <button type="submit" style="background:#ef4444;color:#fff;border:none;padding:10px 24px;border-radius:8px;font-weight:600;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                <i class="fas fa-trash"></i> Delete Draft
            </button>
        </form>
    </div>
</div>
@endif
@php
    $fileExists = $document->file_path && Storage::disk('public')->exists($document->file_path);
    $fileUrl = $fileExists ? Storage::disk('public')->url($document->file_path) : null;
@endphp

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;">

    {{-- Informasi Dokumen --}}
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:24px;">
        <h2 style="font-size:16px;font-weight:700;color:#0f172a;margin-bottom:16px;">
            <i class="fas fa-file-alt" style="color:#6366f1;margin-right:6px;"></i>
            Informasi Dokumen
        </h2>
        <table style="width:100%;font-size:13px;">
            <tr>
                <td style="padding:6px 0;color:#64748b;width:120px;">Nama File</td>
                <td style="padding:6px 0;font-weight:600;color:#0f172a;">{{ $document->nama_file }}</td>
            </tr>
            <tr>
                <td style="padding:6px 0;color:#64748b;">Jenis</td>
                <td style="padding:6px 0;">
                    <span class="badge badge-{{ $document->tipe === 'kontrak' ? 'aktif' : ($document->tipe === 'invoice' ? 'selesai' : ($document->tipe === 'rab' ? 'pending' : 'mendatang')) }}" style="font-size:12px;padding:4px 8px;">
                        {{ $document->tipe_label }}
                    </span>
                </td>
            </tr>
            <tr>
                <td style="padding:6px 0;color:#64748b;">Status</td>
                <td style="padding:6px 0;">
                    <x-document-status-badge :status="$document->status" />
                </td>
            </tr>
            <tr>
                <td style="padding:6px 0;color:#64748b;">Event</td>
                <td style="padding:6px 0;font-weight:500;color:#475569;">
                    {{ $document->event?->nama_event ?? '-' }}
                </td>
            </tr>
            <tr>
                <td style="padding:6px 0;color:#64748b;">Client</td>
                <td style="padding:6px 0;font-weight:500;color:#475569;">
                    {{ $document->event?->client?->name ?? '-' }}
                </td>
            </tr>
            <tr>
                <td style="padding:6px 0;color:#64748b;">Nomor Dokumen</td>
                <td style="padding:6px 0;font-weight:600;color:#0f172a;">
                    @php $docNum = optional($document->numbering)->document_number; @endphp
                    @if($docNum)
                        <span style="font-family:monospace;font-size:13px;">{{ $docNum }}</span>
                    @else
                        <span style="color:#94a3b8;font-style:italic;">Belum diterbitkan</span>
                    @endif
                </td>
            </tr>            <tr>
                <td style="padding:6px 0;color:#64748b;">Dibuat</td>
                <td style="padding:6px 0;color:#475569;">
                    {{ $document->created_at->format('d M Y, H:i') }}
                </td>
            </tr>
        </table>
    </div>

    {{-- Aksi --}}
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:24px;display:flex;flex-direction:column;justify-content:center;">
        <div style="text-align:center;margin-bottom:20px;">
            <i class="fas fa-check-circle" style="font-size:48px;color:#22c55e;opacity:0.8;"></i>
            <h3 style="font-size:18px;font-weight:700;color:#0f172a;margin:12px 0 4px;">Dokumen Berhasil Dibuat</h3>
            <p style="color:#64748b;font-size:13px;margin:0;">
                Dokumen telah disimpan dan siap untuk ditindaklanjuti.
            </p>
        </div>
        @if($fileExists)
        <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
            <a href="{{ route('admin.document_builder.download-doc', $document->id) }}"
               style="background:#0f172a;color:#fff;border:none;padding:10px 24px;border-radius:8px;font-weight:600;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fas fa-download"></i> Download PDF
            </a>
            <a href="{{ route('admin.document_builder.print-doc', $document->id) }}" target="_blank"
               style="background:#f59e0b;color:#fff;border:none;padding:10px 24px;border-radius:8px;font-weight:600;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fas fa-print"></i> Print
            </a>
            <a href="{{ $fileUrl }}" target="_blank"
               style="background:#6366f1;color:#fff;border:none;padding:10px 24px;border-radius:8px;font-weight:600;font-size:13px;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fas fa-external-link-alt"></i> Lihat PDF
            </a>
        </div>
        @else
        <div style="text-align:center;padding:20px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;">
            <i class="fas fa-exclamation-triangle" style="color:#ef4444;font-size:24px;margin-bottom:8px;"></i>
            <p style="color:#991b1b;font-size:13px;margin:0;font-weight:500;">File PDF tidak ditemukan.</p>
            <p style="color:#b91c1c;font-size:12px;margin:4px 0 0;">Dokumen mungkin belum selesai diproses atau file telah dihapus.</p>
        </div>
        @endif
</div>
</div>

{{-- QR Code --}}
@php $qr = optional($document->qrVerification); @endphp
@if($qr && $qr->qr_path)
<div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:24px;margin-bottom:24px;">
    <h2 style="font-size:16px;font-weight:700;color:#0f172a;margin-bottom:16px;">
        <i class="bi bi-qr-code me-2 text-dark"></i>QR Code
    </h2>
    <div style="text-align:center;">
        <img src="{{ Storage::disk("public")->url($qr->qr_path) }}" alt="QR Code" style="width:200px;height:200px;">
        <p style="color:#64748b;font-size:12px;margin-top:8px;">Scan untuk memverifikasi keaslian dokumen</p>
    </div>
</div>
@endif

{{-- Preview PDF --}}
@if($fileExists)
<div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:24px;">
    <h2 style="font-size:16px;font-weight:700;color:#0f172a;margin-bottom:16px;">
        <i class="fas fa-file-pdf" style="color:#ef4444;margin-right:6px;"></i>
        File Dokumen
    </h2>
    <iframe src="{{ $fileUrl }}" style="width:100%;height:600px;border:1px solid #e2e8f0;border-radius:8px;" title="Preview Dokumen"></iframe>
</div>
@endif
@endsection
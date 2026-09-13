{{--
    Signature-Area QR (Phase 11G.2)
    QR hanya diambil dari qr_path yang sudah ada (tidak pernah dibuat ulang).
    Business Rule: tampil hanya jika status = Published DAN qr_path tersedia.
    Jika belum ada -> tidak tampil, tidak error. Clean layout, tanpa teks tambahan.
--}}
@php
    $qrAbsPath = null;
    if (isset($document) && $document && $document->uses_ddms 
        && $document->status === \App\Enums\DocumentStatus::Published 
        && optional($document->qrVerification)->qr_path) {
        $fullPath = storage_path('app/public/' . $document->qrVerification->qr_path);
        if (file_exists($fullPath)) {
            $qrAbsPath = $fullPath;
        }
    }
@endphp

@if($qrAbsPath)
<img src="{{ $qrAbsPath }}"
     style="width:75px;height:75px;display:block;margin:0 auto 4px;"
     alt="QR Verifikasi">
@endif
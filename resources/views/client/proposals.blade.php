{{-- resources/views/client/proposals.blade.php --}}
@extends('layouts.client')
@section('title', 'Surat Penawaran')
@section('page-title', 'Surat Penawaran')

@section('content')

<div class="page-header">
    <h1 style="font-size:26px; font-weight:800; color:var(--dark); margin-bottom:4px;">Surat Penawaran</h1>
    <p style="color:var(--text-muted);">Daftar surat penawaran yang telah dikirim oleh tim kami untuk pengajuan event Anda.</p>
</div>

<div class="penawaran-card">
    {{-- Header --}}
    <div class="penawaran-hdr">
        <div class="penawaran-icon">
            <i class="bi bi-file-earmark-text-fill"></i>
        </div>
        <div style="flex:1;">
            <div class="penawaran-name">Konser Feast</div>
            <div class="penawaran-type">Acara Korporat / Konferensi</div>
        </div>
        <span class="badge badge-diterima">
            <i class="bi bi-check-circle-fill" style="margin-right:4px;"></i>Diterima
        </span>
    </div>

    {{-- Detail rows --}}
    <div class="penawaran-rows">
        <div class="penawaran-row">
            <span class="penawaran-row-label">Tanggal Event</span>
            <span class="penawaran-row-value">2026-05-20</span>
        </div>
        <div class="penawaran-row">
            <span class="penawaran-row-label">Lokasi</span>
            <span class="penawaran-row-value">Basko</span>
        </div>
        <div class="penawaran-row">
            <span class="penawaran-row-label">Anggaran</span>
            <span class="penawaran-row-value">Rp 100 Juta – Rp 250 Juta</span>
        </div>
    </div>

    {{-- CTA --}}
    <div class="penawaran-cta">
        <a href="#" class="btn-penawaran">
            Lihat Surat Penawaran <i class="bi bi-arrow-right"></i>
        </a>
    </div>
</div>

@endsection
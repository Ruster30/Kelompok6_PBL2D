@extends('layouts.client')
@section('title','Surat Penawaran')
@section('page-title','Surat Penawaran')

@section('content')

<div class="page-header">
    <h1 style="font-size:26px;font-weight:800;color:var(--dark);margin-bottom:4px;">Surat Penawaran</h1>
    <p style="color:var(--text-muted);">Daftar surat penawaran yang telah dikirim oleh tim kami untuk event Anda.</p>
</div>

@forelse($proposals as $proposal)
<div class="penawaran-card">
    {{-- Header --}}
    <div class="penawaran-hdr">
        <div class="penawaran-icon">
            <i class="bi bi-file-earmark-text-fill"></i>
        </div>
        <div style="flex:1;">
            <div class="penawaran-name">{{ $proposal->event->nama_event }}</div>
            <div class="penawaran-type">{{ $proposal->event->jenis_event }}</div>
        </div>
        <span class="badge {{ $proposal->badge_class }}">
            <i class="bi bi-{{ $proposal->status==='disetujui' ? 'check-circle-fill' : ($proposal->status==='ditolak' ? 'x-circle-fill' : 'clock') }}"
               style="margin-right:4px;"></i>
            {{ $proposal->status_label }}
        </span>
    </div>

    {{-- Detail Rows --}}
    <div class="penawaran-rows">
        <div class="penawaran-row">
            <span class="penawaran-row-label">Tanggal Event</span>
            <span class="penawaran-row-value">
                {{ $proposal->event->tanggal_event->isoFormat('D MMMM Y') }}
            </span>
        </div>
        <div class="penawaran-row">
            <span class="penawaran-row-label">Lokasi</span>
            <span class="penawaran-row-value">{{ $proposal->event->lokasi_event }}</span>
        </div>
        <div class="penawaran-row">
            <span class="penawaran-row-label">Jumlah Tamu</span>
            <span class="penawaran-row-value">
                {{ number_format($proposal->event->jumlah_tamu) }} orang
            </span>
        </div>
        <div class="penawaran-row">
            <span class="penawaran-row-label">Versi Proposal</span>
            <span class="penawaran-row-value">v{{ $proposal->versi }}</span>
        </div>
        <div class="penawaran-row">
            <span class="penawaran-row-label">Tanggal Proposal</span>
            <span class="penawaran-row-value">
                {{ $proposal->tanggal_proposal?->isoFormat('D MMMM Y') ?? '-' }}
            </span>
        </div>
    </div>

    {{-- CTA --}}
    <div class="penawaran-cta">
        <a href="{{ route('client.proposals.show', $proposal->id) }}" class="btn-penawaran">
            Lihat Surat Penawaran <i class="bi bi-arrow-right"></i>
        </a>
    </div>
</div>
@empty
<div class="card">
    <div class="empty-state">
        <i class="bi bi-file-earmark-x"></i>
        <h4>Belum Ada Surat Penawaran</h4>
        <p>Tim kami akan mengirimkan surat penawaran setelah pengajuan event Anda diterima dan diproses.</p>
        <a href="{{ route('client.event.create') }}" class="btn btn-accent" style="margin-top:16px;">
            Ajukan Event Baru
        </a>
    </div>
</div>
@endforelse

@endsection
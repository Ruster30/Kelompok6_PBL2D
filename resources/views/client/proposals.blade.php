@extends('layouts.client')
@section('title','Surat Penawaran')
@section('page-title','Surat Penawaran')

@section('content')

<div class="page-header">
    <h1 style="font-size:26px;font-weight:800;color:var(--dark);margin-bottom:4px;">Surat Penawaran</h1>
    <p style="color:var(--text-muted);">Daftar surat penawaran yang telah dikirim oleh tim kami untuk event Anda.</p>
</div>

@if(session('success'))
<div style="background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:14px;display:flex;align-items:center;gap:8px;">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
</div>
@endif

@forelse($latestProposals as $proposal)
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
        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;">
            <span class="badge {{ $proposal->badge_class }}">
                <i class="bi bi-{{ $proposal->status_icon }}" style="margin-right:4px;"></i>
                {{ $proposal->status_label }}
            </span>
            @if($proposal->versi > 1)
            <span style="font-size:11px;background:#f1f5f9;color:#64748b;padding:2px 8px;border-radius:999px;font-weight:600;">
                Revisi v{{ $proposal->versi }}
            </span>
            @endif
        </div>
    </div>

    {{-- Detail Rows --}}
    <div class="penawaran-rows">
        <div class="penawaran-row">
            <span class="penawaran-row-label">
                Tanggal Event
            </span>

            <span class="penawaran-row-value">
                {{ $proposal->event->tanggal_event
                    ? \Carbon\Carbon::parse($proposal->event->tanggal_event)->isoFormat('D MMMM Y')
                    : '-' }}
            </span>
        </div>
        <div class="penawaran-row">
            <span class="penawaran-row-label">Lokasi</span>
            <span class="penawaran-row-value">{{ $proposal->event->lokasi_event }}</span>
        </div>
        @if($proposal->event->rentang_anggaran)
        <div class="penawaran-row">
            <span class="penawaran-row-label">Anggaran Ditawarkan</span>
            <span class="penawaran-row-value" style="font-weight:700;color:var(--accent);">
                {{ $proposal->event->rentang_anggaran }}
            </span>
        </div>
        @endif
        <div class="penawaran-row">
            <span class="penawaran-row-label">No. Surat</span>
            <span class="penawaran-row-value">{{ $proposal->nomor_proposal ?? '-' }}</span>
        </div>
        <div class="penawaran-row">
            <span class="penawaran-row-label">Tanggal Proposal</span>
            <span class="penawaran-row-value">
                {{ $proposal->tanggal_proposal ? \Carbon\Carbon::parse($proposal->tanggal_proposal)->isoFormat('D MMMM Y') : '-' }}
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
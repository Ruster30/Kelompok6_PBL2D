@extends('layouts.admin')

@section('title', 'Riwayat Negosiasi')
@section('page-title', 'Admin Dashboard')

@section('content')

{{-- Header --}}
<div class="page-header" style="margin-bottom:24px;">
    <a href="{{ route('admin.requests.index') }}"
       style="display:inline-flex; align-items:center; gap:6px; color:#64748b; font-size:14px; text-decoration:none;">
        <i class="fas fa-arrow-left"></i> Kembali ke Request
    </a>

    <div style="display:flex; gap:10px; align-items:center;">
        {{-- Tombol Lihat RAB --}}
        <a href="{{ route('admin.rab.index', ['event_id' => $event->id]) }}"
           class="btn btn-outline"
           style="border-color:#6366f1; color:#6366f1;">
            <i class="fas fa-calculator"></i> Lihat RAB
        </a>

        {{-- Tombol Revisi Penawaran --}}
        @if($event->latestProposal && $event->latestProposal->status === 'negosiasi')
            <a href="{{ route('admin.requests.surat-penawaran', $event->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Revisi Penawaran
            </a>
        @endif
    </div>
</div>

<div class="card" style="margin-bottom:20px;">
    <div style="padding:20px; display:flex; justify-content:space-between; align-items:center;">

        <div>
            <div style="font-size:13px;color:#64748b;">
                Status Proposal
            </div>

            <span class="badge {{ $event->latestProposal->badge_class }}">
                {{ $event->latestProposal->status_label }}
            </span>
        </div>

        <div style="text-align:right;">
            <div style="font-size:13px;color:#64748b;">
                Versi Proposal
            </div>

            <strong>
                V{{ $event->latestProposal->versi }}
            </strong>
        </div>

    </div>
</div>

{{-- Judul --}}
<div class="card" style="margin-bottom:20px;">
    <div style="padding:20px 24px; display:flex; align-items:center; gap:14px;">
        <div style="width:44px; height:44px; background:#fef3c7; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#f59e0b; font-size:20px;">
            <i class="fas fa-comments"></i>
        </div>
        <div>
            <h2 style="font-size:18px; font-weight:700; color:#0f172a; margin-bottom:2px;">
                Riwayat Negosiasi{{ $event->nama_event }}
            </h2>
            <div style="font-size:13px; color:#64748b;">Client: {{ $event->client->name ?? '-' }}</div>
        </div>
    </div>
</div>

{{-- Daftar Negosiasi --}}
@forelse($negotiations as $nego)
    <div style="display:flex; gap:16px; margin-bottom:20px;">
        {{-- Avatar --}}
        <div style="flex-shrink:0;">
            <div style="width:40px; height:40px; background:#fef3c7; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#f59e0b; font-size:16px;">
                <i class="fas fa-flag"></i>
            </div>
        </div>

        {{-- Konten --}}
        <div class="card" style="flex:1; padding:0; overflow:visible;">
            <div style="padding:16px 20px; border-bottom:1px solid #f1f5f9;">
                <div style="font-weight:700; color:#0f172a; font-size:15px;">{{ $nego->user->name ?? '-' }}</div>
                <div style="font-size:12px; color:#94a3b8; margin-top:3px;">
                    <i class="fas fa-clock" style="margin-right:4px;"></i>
                    {{ $nego->created_at->format('j/n/Y, H.i.s') }}
                </div>
            </div>
            <div style="padding:16px 20px; display:flex; flex-direction:column; gap:14px;">
                <div>
                    <div style="font-size:12px; font-weight:600; color:#14b8a6; margin-bottom:4px;">Pesan</div>
                    <div style="font-size:14px; color:#334155;">{{ $nego->pesan }}</div>
                </div>
                @if($nego->budget_diinginkan)
                <div>
                    <div style="font-size:12px; font-weight:600; color:#14b8a6; margin-bottom:4px;">Budget yang Diinginkan</div>
                    <div style="font-size:14px; color:#334155;">{{ number_format($nego->budget_diinginkan, 0, ',', '.') }}</div>
                </div>
                @endif
                @if($nego->catatan_tambahan)
                <div>
                    <div style="font-size:12px; font-weight:600; color:#14b8a6; margin-bottom:4px;">Catatan Tambahan</div>
                    <div style="font-size:14px; color:#334155;">{{ $nego->catatan_tambahan }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="card">
        <div class="empty-state">
            <i class="fas fa-comments"></i>
            <h3>Belum ada negosiasi</h3>
            <p>Client belum mengirimkan negosiasi untuk event ini.</p>
        </div>
    </div>
@endforelse

@endsection


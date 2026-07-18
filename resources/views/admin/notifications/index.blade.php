@extends('layouts.admin')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Notifikasi</h1>
        <p>Informasi terbaru untuk akun administrator.</p>
    </div>
    @if($notifications->isNotEmpty())
    <form action="{{ route('admin.notifications.markAllRead') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline"><i class="fas fa-check-double"></i> Tandai Semua Dibaca</button>
    </form>
    @endif
</div>

<div class="card">
    @forelse($notifications as $notif)
    @php
        $iconBackground = match($notif->tipe) {
            'sukses' => '#dcfce7',
            'peringatan' => '#fef3c7',
            'event' => '#ccfbf1',
            'proposal' => '#dbeafe',
            default => '#f1f5f9',
        };
        $iconColor = match($notif->tipe) {
            'sukses' => '#166534',
            'peringatan' => '#92400e',
            'event' => '#0f766e',
            'proposal' => '#1d4ed8',
            default => '#64748b',
        };
    @endphp
    <div style="display:flex; align-items:flex-start; gap:14px; padding:16px 24px; border-bottom:1px solid #f1f5f9; {{ $notif->dibaca ? '' : 'background:#f0fdf9;' }}">
        <div style="width:38px; height:38px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; background:{{ $iconBackground }}; color:{{ $iconColor }};">
            <i class="fas {{ $notif->icon }}"></i>
        </div>
        <div style="flex:1;">
            <div style="font-size:14px; font-weight:600; color:#0f172a;">{{ $notif->judul }}</div>
            @if($notif->pesan)
            <div style="font-size:13px; color:#64748b; margin-top:3px; line-height:1.5;">{{ $notif->pesan }}</div>
            @endif
            <div style="font-size:12px; color:#94a3b8; margin-top:7px;">{{ $notif->created_at->locale('id')->diffForHumans() }}</div>
        </div>
        @if(!$notif->dibaca)
        <form action="{{ route('admin.notifications.markRead', $notif) }}" method="POST">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-outline btn-sm" title="Tandai dibaca"><i class="fas fa-check"></i></button>
        </form>
        @endif
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-bell-slash"></i>
        <h3>Belum ada notifikasi.</h3>
        <p>Notifikasi request event baru dan aktivitas penting akan muncul di sini.</p>
    </div>
    @endforelse
</div>

@if($notifications->hasPages())
<div style="margin-top:16px;">{{ $notifications->links() }}</div>
@endif
@endsection

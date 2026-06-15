@extends('layouts.admin')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Notifikasi</h1>
    </div>
    @if($notifications->count())
    <form action="{{ route('admin.notifications.markAllRead') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline">
            <i class="fas fa-check-double"></i> Tandai Semua Dibaca
        </button>
    </form>
    @endif
</div>

<div class="card">
    @forelse($notifications as $notif)
    <div style="display:flex; align-items:flex-start; gap:14px; padding:16px 24px; border-bottom:1px solid #f1f5f9; {{ $notif->read_at ? '' : 'background:#f0fdf9;' }}">
        <div class="cms-icon-circle" style="margin:0; flex-shrink:0;">
            <i class="{{ $notif->data['icon'] ?? 'fas fa-bell' }}"></i>
        </div>
        <div style="flex:1;">
            <div style="font-size:14px; font-weight:600; color:#0f172a;">{{ $notif->data['title'] ?? 'Notifikasi' }}</div>
            <div style="font-size:13px; color:#64748b; margin-top:2px;">{{ $notif->data['message'] ?? '' }}</div>
            <div style="font-size:12px; color:#94a3b8; margin-top:6px;">{{ $notif->created_at->diffForHumans() }}</div>
        </div>
        @if(!$notif->read_at)
        <span class="notif-badge" style="position:relative; top:6px;"></span>
        @endif
    </div>
    @empty
    <div class="empty-state">
        <i class="fas fa-bell-slash"></i>
        <h3>Belum ada notifikasi baru.</h3>
    </div>
    @endforelse
</div>
@endsection
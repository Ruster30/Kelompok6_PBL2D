@extends('layouts.vendor')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')

@section('content')

<div class="section-card">

    @if(isset($unreadCount) && $unreadCount > 0)
        <div class="d-flex justify-content-end mb-4">
            <form action="{{ route('vendor.notifikasi.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-teal">
                    <i class="bi bi-check2-all me-1"></i>
                    Tandai Semua Dibaca
                </button>
            </form>
        </div>
    @endif

    <div class="notif-list">
        @forelse($notifikasi as $notif)

            <div class="notif-item {{ !$notif->dibaca ? 'unread' : '' }}">

                <div class="notif-icon-wrap me-3" style="background: {{ !$notif->dibaca ? 'var(--teal-light)' : '#f8fafc' }}; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="bi bi-bell-fill" style="color: {{ !$notif->dibaca ? 'var(--teal)' : '#94a3b8' }}; font-size: 16px;"></i>
                </div>

                <div class="flex-grow-1">

                    <div class="notif-title fw-bold" style="font-size: 15px; color: #0f172a; margin-bottom: 4px;">
                        {{ $notif->judul }}
                    </div>

                    <div class="notif-body" style="font-size: 13px; color: #475569; line-height: 1.6; margin-bottom: 6px;">
                        {{ $notif->pesan }}
                    </div>

                    <div class="notif-time text-secondary" style="font-size: 11.5px; color: #94a3b8;">
                        <i class="bi bi-clock me-1"></i>
                        {{ \Carbon\Carbon::parse($notif->created_at)->locale('id')->diffForHumans() }}
                    </div>

                </div>

                @if(!$notif->dibaca)
                    <div class="notif-unread-dot ms-2"></div>
                @endif

            </div>

        @empty

            <div class="empty-state text-center py-5">
                <i class="bi bi-bell-slash text-muted mb-3 d-block" style="font-size: 40px; color: #cbd5e1 !important;"></i>
                <h4 class="fw-semibold text-dark" style="font-size: 16px; margin-bottom: 6px;">Belum Ada Notifikasi</h4>
                <p class="text-muted mb-0" style="font-size: 13.5px;">
                    Notifikasi terbaru akan muncul di sini.
                </p>
            </div>

        @endforelse
    </div>

</div>

@endsection
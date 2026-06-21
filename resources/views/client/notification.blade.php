@extends('layouts.client')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')

@section('content')

<div class="page-header">
    <h1>Notifikasi</h1>
    <p>Informasi terbaru terkait event dan aktivitas akun Anda.</p>
</div>

<div class="card">

    <div class="card-body">

        @if(isset($unreadCount) && $unreadCount > 0)

            <div class="notification-header">

                <form action="{{ route('client.notif.read') }}"
                      method="POST">

                    @csrf

                    <button type="submit" class="btn btn-accent">
                        <i class="bi bi-check2-all"></i>
                        Tandai Semua Dibaca
                    </button>

                </form>

            </div>

        @endif

        @forelse($notifications as $notif)

            <div class="notification-item {{ !$notif->dibaca ? 'unread' : '' }}">

                <div class="notification-content">

                    <div class="notification-body">

                        <div class="notification-title">
                            {{ $notif->judul }}
                        </div>

                        <div class="notification-message">
                            {{ $notif->pesan }}
                        </div>

                        <div class="notification-time">
                            {{ $notif->created_at->locale('id')->diffForHumans() }}
                        </div>

                    </div>

                    @if(!$notif->dibaca)

                        <span class="notification-badge">
                            Baru
                        </span>

                    @endif

                </div>

            </div>

        @empty

            <div class="empty-state">

                <i class="bi bi-bell-slash"></i>

                <h4>Belum Ada Notifikasi</h4>

                <p>
                    Notifikasi terbaru akan muncul di sini.
                </p>

            </div>

        @endforelse

    </div>

</div>

@if(method_exists($notifications, 'links'))

<div class="mt-4">
    {{ $notifications->links() }}
</div>

@endif

@endsection
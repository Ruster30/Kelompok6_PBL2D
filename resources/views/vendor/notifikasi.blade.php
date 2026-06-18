@extends('layouts.vendor')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')

@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <h2 class="fw-bold mb-1">Notifikasi</h2>
    </div>

    <div class="section-card">

        @if(isset($unreadCount) && $unreadCount > 0)
            <div class="d-flex justify-content-end mb-3">
                <form action="{{ route('vendor.notifikasi.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        Tandai Semua Dibaca
                    </button>
                </form>
            </div>
        @endif

        @forelse($notifikasi as $notif)

            <div class="border-bottom py-3">

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <h6 class="fw-bold mb-1">
                            {{ $notif->judul }}
                        </h6>

                        <p class="mb-1 text-muted">
                            {{ $notif->pesan }}
                        </p>

                        <small class="text-secondary">
                            {{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}
                        </small>

                    </div>

                    @if(!$notif->dibaca)
                        <span class="badge bg-danger">
                            Baru
                        </span>
                    @endif

                </div>

            </div>

        @empty

            <div class="text-center py-5">

                <div style="font-size:18px;color:#64748b;">
                    Belum ada notifikasi.
                </div>

            </div>

        @endforelse

    </div>

</div>

@endsection
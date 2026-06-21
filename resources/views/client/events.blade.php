@extends('layouts.client')
@section('title','Event Terdaftar')
@section('page-title','Event Terdaftar')

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;">
    <h1 style="font-size:26px;font-weight:800;color:var(--dark);">Event Terdaftar</h1>
    <a href="{{ route('client.event.create') }}" class="btn btn-accent">
        <i class="bi bi-plus-lg"></i> Ajukan Event Baru
    </a>
</div>

@if($events->isEmpty())
<div class="card">
    <div class="empty-state">
        <i class="bi bi-calendar-x"></i>
        <h4>Belum Ada Event</h4>
        <p>Anda belum memiliki event yang terdaftar.</p>
    </div>
</div>
@else
<div class="events-grid">
    @foreach($events as $event)
    <div class="event-card">
        {{-- Gambar / Header --}}
        <div class="event-card-img">
            <div class="event-card-badge">
                <span class="badge {{ $event->badge_class }}">{{ $event->status_label }}</span>
            </div>
            <div class="event-card-title">{{ $event->nama_event }}</div>
        </div>

        {{-- Body --}}
        <div class="event-card-body">
            <div class="event-card-meta">
                <i class="bi bi-calendar3"></i>
                {{ $event->tanggal_event->isoFormat('D MMM Y') }}
            </div>
            <div class="event-card-meta">
                <i class="bi bi-geo-alt-fill"></i> {{ $event->lokasi_event }}
            </div>
            <div class="event-card-meta">
                <i class="bi bi-people-fill"></i> {{ number_format($event->jumlah_tamu) }} Tamu
            </div>

            <div class="event-card-footer">
                <div class="progress-row">
                    <span class="progress-label">Progres Perencanaan</span>
                    <span class="progress-pct">{{ $event->progress }}%</span>
                </div>
                <div class="progress-bar-wrap" style="margin-top:8px;">
                    <div class="progress-bar-fill" style="width: {{ $event->progress}}%"></div>
                </div>
                <a href="{{ route('client.timeline.show', $event->id) }}"
                   class="btn btn-outline" style="width:100%;justify-content:center;margin-top:14px;">
                    Lihat Timeline <i class="bi bi-arrow-right"></i>
                </a>
                @if ($event->status_event == 'selesai')
                    @if ($event->feedbacks->where('client_id', auth()->id())->count() == 0)
                        <a href="{{ route('feedback.create', $event->id) }}"
                        class="btn btn-accent"
                        style="width: 100%; justify-content: center; margin-top: 10px;">
                            <i class="bi bi-star-fill"></i>
                            Beri Feedback
                        </a>
                    @else
                        <button class="btn btn-success"
                                style="width: 100%; margin-top: 10px;"
                                disabled>
                            <i class="bi bi-check-circle"></i>
                            Feedback Terkirim
                        </button>
                    @endif
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection
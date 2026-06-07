{{-- resources/views/client/events.blade.php --}}
@extends('layouts.client')
@section('title', 'Event Terdaftar')
@section('page-title', 'Event Terdaftar')

@section('content')
<div class="page-header d-flex align-center justify-between">
    <div>
        <h1>Event Terdaftar</h1>
    </div>
    <a href="{{ route('client.event.create') }}" class="btn btn-accent">
        <i class="bi bi-plus-lg"></i> Ajukan Event Baru
    </a>
</div>

@if($events->isEmpty())
<div class="card">
    <div class="empty-state">
        <i class="bi bi-calendar-x"></i>
        <h4>Belum Ada Event</h4>
        <p>Anda belum memiliki event yang terdaftar. Mulai dengan mengajukan event baru.</p>
        <a href="{{ route('client.event.create') }}" class="btn btn-accent mt-3">
            <i class="bi bi-plus-lg"></i> Ajukan Event Baru
        </a>
    </div>
</div>
@else
<div class="events-grid">
    @foreach($events as $event)
    <div class="event-card">
        <div class="event-card-img">
            <img src="{{ $event->cover_image ? asset('images/'.$event->cover_image) : asset('images/event-placeholder.jpg') }}"
                 alt="{{ $event->name }}">
            <div class="event-card-badge">
                <span class="badge badge-{{ strtolower($event->status) }}">{{ ucfirst($event->status) }}</span>
            </div>
            <div class="event-card-title">{{ $event->name }}</div>
        </div>
        <div class="event-card-body">
            <div class="event-card-meta">
                <i class="bi bi-calendar3"></i>
                {{ $event->event_date?->format('j M Y') }}
                @if($event->event_end_date)
                – {{ $event->event_end_date->format('j M Y') }}
                @endif
            </div>
            <div class="event-card-meta">
                <i class="bi bi-geo-alt-fill"></i>
                {{ $event->location }}
            </div>
            <div class="event-card-meta">
                <i class="bi bi-people-fill"></i>
                {{ number_format($event->guest_count) }} Tamu
            </div>

            <div class="event-card-footer">
                <div class="progress-row">
                    <span class="progress-label">Progres Perencanaan</span>
                    <span class="progress-pct">{{ $event->progress }}%</span>
                </div>
                <div class="progress-bar-wrap mt-2">
                    <div class="progress-bar-fill"
                        data-width="{{ $event->progress }}">
                    </div>
                </div>
                <a href="{{ route('client.timeline', $event->id) }}" class="btn btn-outline mt-3" style="width:100%; justify-content:center;">
                    Lihat Timeline <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection

<script>
document.querySelectorAll('.progress-bar-fill').forEach(bar => {
    bar.style.width = bar.dataset.width + '%';
});
</script>
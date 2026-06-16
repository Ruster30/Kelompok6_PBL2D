@extends('layouts.vendor')

@section('title', 'Event Saya')
@section('page-title', 'Event Saya')

@section('content')

<div class="section-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-card-title">Daftar Event</h2>

        <form method="GET" action="{{ route('vendor.event-saya') }}">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari event..."
                class="form-control"
                style="width:250px"
            >
        </form>
    </div>

    @forelse($events as $event)

        <div class="event-row-item">
            <div class="d-flex justify-content-between align-items-start">

                <div>
                    <h5 class="fw-semibold mb-2">
                        {{ $event->nama_event }}
                    </h5>

                    <div class="text-muted mb-1">
                        <i class="bi bi-calendar3"></i>
                        {{ \Carbon\Carbon::parse($event->tanggal_event)->format('d M Y') }}
                    </div>

                    <div class="text-muted mb-1">
                        <i class="bi bi-geo-alt"></i>
                        {{ $event->lokasi_event }}
                    </div>

                    <div class="text-muted">
                        Client:
                        {{ $event->client->nama_client ?? '-' }}
                    </div>
                </div>

                <div>
                    @php
                        $badgeClass = match($event->status_event) {
                            'selesai' => 'bg-success',
                            'berjalan' => 'bg-primary',
                            'diproses' => 'bg-warning',
                            default => 'bg-secondary'
                        };
                    @endphp

                    <span class="badge {{ $badgeClass }}">
                        {{ ucfirst($event->status_event) }}
                    </span>
                </div>

            </div>
        </div>

    @empty

        <div class="text-center py-5">
            <i class="bi bi-calendar-x" style="font-size:40px;"></i>
            <p class="mt-3 text-muted">
                Belum ada event yang ditugaskan.
            </p>
        </div>

    @endforelse

</div>

@endsection
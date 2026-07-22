@extends('layouts.vendor')

@section('title', 'Ringkasan')
@section('page-title', 'Ringkasan')
@section('breadcrumbs')
    <a href="{{ route('vendor.ringkasan') }}">Dashboard</a><span class="separator">/</span><span>Ringkasan</span>
@endsection

@section('content')

<div class="row g-3 mb-4">

    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-calendar3-event"></i>
            </div>
            <div>
                <div class="stat-number">{{ $totalEvent }}</div>
                <div class="stat-label">Event Ditugaskan</div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-clock"></i>
            </div>
            <div>
                <div class="stat-number">{{ $tugasAktif }}</div>
                <div class="stat-label">Tugas Aktif</div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-check2-square"></i>
            </div>
            <div>
                <div class="stat-number">{{ $tugasSelesai }}</div>
                <div class="stat-label">Tugas Selesai</div>
            </div>
        </div>
    </div>

</div>

<div class="row g-3">

    <div class="col-lg-7">

        <div class="section-card h-100">

            <div class="section-card-header">
                <h2 class="section-card-title">Event Terdekat</h2>

                <a href="{{ route('vendor.event-saya') }}"
                   class="link-teal">
                    Lihat Semua
                </a>
            </div>

            @forelse($eventTerdekat as $event)

                <div class="event-row-item">

                    <div class="d-flex align-items-start justify-content-between">

                        <div>

                            <div class="fw-semibold vendor-event-title">
                                {{ $event->nama_event }}
                            </div>

                            <div class="d-flex align-items-center gap-1 mt-1 muted-text-sm">
                                <i class="bi bi-calendar3"></i>

                                {{ \Carbon\Carbon::parse($event->tanggal_event)->format('d/m/Y') }}
                            </div>

                            <div class="d-flex align-items-center gap-1 mt-1 muted-text-sm">
                                <i class="bi bi-geo-alt"></i>

                                {{ $event->lokasi_event }}
                            </div>

                        </div>

                        <span class="badge-mendatang">
                            Mendatang
                        </span>

                    </div>

                    <div class="mt-3">

                        <a href="{{ route('vendor.daftar-tugas') }}"
                           class="link-teal fw-medium">

                            Lihat Tugas
                            <i class="bi bi-arrow-right"></i>

                        </a>

                    </div>

                </div>

            @empty

                <div class="text-center py-5 text-muted">
                    Belum ada event ditugaskan
                </div>

            @endforelse

        </div>

    </div>

    <div class="col-lg-5">

        <div class="section-card h-100">

            <div class="section-card-header">
                <h2 class="section-card-title">
                    Tugas Mendatang
                </h2>
            </div>

            @forelse($tugasMendatang as $tugas)

                <div class="task-item">

                    <div class="d-flex gap-3 align-items-start">

                        <div class="task-dot pending"></div>

                        <div>

                            <div class="task-name">
                                {{ $tugas->nama_tugas }}
                            </div>

                            <div class="task-sub">

                                {{ $tugas->event->nama_event ?? '-' }}

                                @if($tugas->deadline)
                                    â€¢ Deadline:
                                    {{ \Carbon\Carbon::parse($tugas->deadline)->format('d/m/Y') }}
                                @endif

                            </div>

                        </div>

                    </div>

                    <span class="badge-status badge-pending">
                        {{ ucfirst($tugas->status) }}
                    </span>

                </div>

            @empty

                <div class="text-center py-5 text-muted">
                    Tidak ada tugas aktif
                </div>

            @endforelse

        </div>

    </div>

</div>

@endsection

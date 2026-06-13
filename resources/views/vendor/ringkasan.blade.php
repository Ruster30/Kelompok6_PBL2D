@extends('vendor.layouts.app')

@section('title', 'Ringkasan')
@section('page-title', 'Ringkasan')

@section('content')

<!-- STAT CARDS -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-calendar3-event"></i>
            </div>
            <div>
                <div class="stat-number">{{ $totalEvent ?? 1 }}</div>
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
                <div class="stat-number">{{ $tugasAktif ?? 1 }}</div>
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
                <div class="stat-number">{{ $tugasSelesai ?? 0 }}</div>
                <div class="stat-label">Tugas Selesai</div>
            </div>
        </div>
    </div>
</div>

<!-- BOTTOM SECTION -->
<div class="row g-3">

    <!-- EVENT TERDEKAT -->
    <div class="col-lg-7">
        <div class="section-card h-100">
            <div class="section-card-header">
                <h2 class="section-card-title">Event Terdekat</h2>
                <a href="{{ route('vendor.event-saya') }}" class="link-teal">Lihat Semua</a>
            </div>

            @forelse($eventTerdekat ?? [] as $event)
                <div class="event-row-item">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="fw-semibold" style="font-size:15px; color:#1a2332; margin-bottom:6px;">
                                {{ $event->nama_event }}
                            </div>
                            <div class="d-flex align-items-center gap-3 text-muted" style="font-size:13px;">
                                <span>{{ \Carbon\Carbon::parse($event->tanggal)->format('j/n/Y') }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-1 mt-1" style="font-size:13px; color:#8a9bb0;">
                                <i class="bi bi-geo-alt" style="font-size:12px;"></i>
                                {{ $event->lokasi }}
                            </div>
                        </div>
                        <span class="badge-mendatang">Mendatang</span>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('vendor.daftar-tugas', ['event' => $event->id]) }}" class="link-teal fw-medium" style="font-size:14px;">
                            Lihat Tugas &nbsp;<i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @empty
                {{-- Demo static content (hapus jika sudah ada data dinamis) --}}
                <div class="event-row-item">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="fw-semibold" style="font-size:15px; color:#1a2332; margin-bottom:6px;">
                                Konser Feast
                            </div>
                            <div class="d-flex align-items-center gap-1 mt-1" style="font-size:13px; color:#8a9bb0;">
                                <i class="bi bi-calendar3" style="font-size:12px;"></i>
                                11/6/2026
                            </div>
                            <div class="d-flex align-items-center gap-1 mt-1" style="font-size:13px; color:#8a9bb0;">
                                <i class="bi bi-geo-alt" style="font-size:12px;"></i>
                                gor hj. agus salim
                            </div>
                        </div>
                        <span class="badge-mendatang">Mendatang</span>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('vendor.daftar-tugas') }}" class="link-teal fw-medium" style="font-size:14px;">
                            Lihat Tugas &nbsp;<i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- TUGAS MENDATANG -->
    <div class="col-lg-5">
        <div class="section-card h-100">
            <div class="section-card-header">
                <h2 class="section-card-title">Tugas Mendatang</h2>
            </div>

            <div class="d-flex flex-column gap-2">
                @forelse($tugasMendatang ?? [] as $tugas)
                    <div class="task-item">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="task-dot {{ $tugas->status === 'selesai' ? 'selesai' : ($tugas->status === 'terlambat' ? 'terlambat' : 'pending') }}"></div>
                            <div>
                                <div class="task-name">{{ $tugas->nama }}</div>
                                <div class="task-sub">
                                    {{ $tugas->event->nama_event ?? '' }}
                                    @if($tugas->deadline)
                                        • Deadline: {{ \Carbon\Carbon::parse($tugas->deadline)->format('j/n/Y') }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <span class="badge-status badge-{{ $tugas->status === 'selesai' ? 'selesai-t' : ($tugas->status === 'terlambat' ? 'terlambat' : 'pending') }}">
                            {{ ucfirst($tugas->status) }}
                        </span>
                    </div>
                @empty
                    {{-- Demo static content --}}
                    <div class="task-item">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="task-dot pending"></div>
                            <div>
                                <div class="task-name">katering</div>
                                <div class="task-sub">Konser Feast • Deadline: 13/6/2026</div>
                            </div>
                        </div>
                        <span class="badge-status badge-pending">Pending</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endsection

@extends('layouts.client')
@section('title','Ringkasan Saya')
@section('page-title','Ringkasan Saya')

@section('content')

{{-- Greeting --}}
<div class="greeting-section">
    <h2>Selamat datang kembali, {{ Auth::user()->name }} </h2>
    <p>Berikut adalah ringkasan progres perencanaan event Anda.</p>
</div>

{{-- Stat Cards --}}
<div class="stat-cards skeleton-init">
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
        <div class="stat-info">
            <div class="stat-number">{{ $eventBerjalan }}</div>
            <div class="stat-label">Event Aktif</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-activity"></i></div>
        <div class="stat-info">
            <div class="stat-number">{{ $eventMenunggu }}</div>
            <div class="stat-label">Event Mendatang</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-currency-dollar"></i></div>
        <div class="stat-info">
            <div class="stat-number">Rp {{ number_format($totalDibayar,0,',','.') }}</div>
            <div class="stat-label">Total Pembayaran</div>
        </div>
    </div>
</div>

{{-- Grid --}}
<div class="dash-grid">
    <div class="dash-main skeleton-init">

        {{-- Pengajuan / Proposal --}}
        <div class="section-hdr">
            <h3>Pengajuan Event Saya</h3>
            <a href="{{ route('client.event.create') }}">+ Ajukan Baru</a>
        </div>

        @forelse($recentEvents as $event)
            @if($event->latestProposal)
            <div class="pengajuan-item">
                <div>
                    <div class="pengajuan-name">
                        {{ $event->nama_event }}
                        <span class="badge {{ $event->latestProposal->badge_class }}"
                              class="badge-spacing">
                            {{ $event->latestProposal->status_label }}
                        </span>
                    </div>
                    <div class="pengajuan-meta">
                        {{ $event->tanggal_event->format('Y-m-d') }} {{ $event->lokasi_event }}
                    </div>
                </div>
                <a href="{{ route('client.proposals.show', $event->latestProposal->id) }}"
                   class="btn btn-ghost-accent btn-sm">
                    <i class="bi bi-file-earmark-text"></i> Lihat Penawaran
                </a>
            </div>
            @endif
        @empty
        <div class="card" style="padding:24px;text-align:center;color:var(--text-muted);">
            <i class="bi bi-inbox" style="font-size:28px;display:block;margin-bottom:8px;color:var(--border);"></i>
            Belum ada pengajuan.
            <a href="{{ route('client.event.create') }}" style="color:var(--accent);font-weight:600;">
                Ajukan sekarang
            </a>
        </div>
        @endforelse

        {{-- Event Saya --}}
        <div class="section-hdr section-hdr-spacing">
            <h3>Event Saya</h3>
            <a href="{{ route('client.events') }}">Lihat Semua</a>
        </div>

        @forelse($recentEvents as $event)
        <div class="event-dash-card event-card-mb">
            <div class="event-dash-left">
                <div class="event-dash-name">
                    {{ $event->nama_event }}
                    <span class="badge {{ $event->badge_class }} badge-spacing">
                        {{ $event->status_label }}
                    </span>
                </div>
                <div class="event-dash-meta">
                    <i class="bi bi-geo-alt-fill"></i>
                    {{ $event->tanggal_event->format('j/n/Y') }} {{ $event->lokasi_event }}
                </div>
                <div class="progress-row">
                    <span class="progress-label">Progres Perencanaan</span>
                    <span class="progress-pct">{{ $event->progress }}%</span>
                </div>
                <div class="progress-bar-wrap">
                    <div class="progress-bar-fill" style="width:{{ $event->progress }}%"></div>
                </div>
            </div>
            <a href="{{ route('client.timeline.show', $event->id) }}"
               class="btn btn-primary btn-sm event-dash-btn">
                Lihat Timeline <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        @empty
        <div class="card" style="padding:24px;text-align:center;color:var(--text-muted);">
            Belum ada event terdaftar.
            <a href="{{ route('client.event.create') }}" style="color:var(--accent);font-weight:600;">
                Ajukan sekarang
            </a>
        </div>
        @endforelse

    </div>

    {{-- Pembaruan Terbaru --}}
    <div class="dash-side skeleton-init">
        <div class="activity-card">
            <div class="activity-title">Pembaruan Terbaru</div>
            @forelse($notifications as $notif)
            <div class="activity-item">
                <div class="activity-dot"
                     style="background:{{ $notif->tipe==='sukses' ? 'var(--accent)' : ($notif->tipe==='peringatan' ? '#f59e0b' : '#3b82f6') }};">
                    <i class="bi bi-{{ $notif->tipe==='sukses' ? 'check-lg' : ($notif->tipe==='peringatan' ? 'exclamation' : 'info-lg') }}"></i>
                </div>
                <div class="activity-body">
                    <div class="activity-name">{{ $notif->judul }}</div>
                    <div class="activity-desc">{{ $notif->pesan }}</div>
                    <div class="activity-date">{{ $notif->created_at->format('j/n/Y') }}</div>
                </div>
            </div>
            @empty
            <div style="text-align:center;color:var(--text-muted);padding:20px 0;font-size:13px;">
                Belum ada pembaruan.
            </div>
            @endforelse
        </div>
    </div>
</div>

@endsection


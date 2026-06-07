{{-- resources/views/client/dashboard.blade.php --}}
@extends('client.layouts.app')
@section('title', 'Ringkasan Saya')
@section('page-title', 'Ringkasan Saya')

@section('content')

{{-- Greeting --}}
<div class="greeting-section">
    <h2>Selamat datang kembali, {{ Auth::user()->name ?? 'Klien' }} 👋</h2>
    <p>Berikut adalah ringkasan progres perencanaan event Anda.</p>
</div>

{{-- Stat Cards --}}
<div class="stat-cards">
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
        <div class="stat-info">
            <div class="stat-number">0</div>
            <div class="stat-label">Event Aktif</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-activity"></i></div>
        <div class="stat-info">
            <div class="stat-number">1</div>
            <div class="stat-label">Event Mendatang</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-currency-dollar"></i></div>
        <div class="stat-info">
            <div class="stat-number">Rp 0</div>
            <div class="stat-label">Total Pengeluaran</div>
        </div>
    </div>
</div>

{{-- Dashboard Grid --}}
<div class="dash-grid">
    <div class="dash-main">

        {{-- Pengajuan Event --}}
        <div class="section-hdr">
            <h3>Pengajuan Event Saya</h3>
            <a href="{{ route('client.event.create') }}">+ Ajukan Baru</a>
        </div>

        {{-- Item pengajuan --}}
        <div class="pengajuan-item">
            <div>
                <div class="pengajuan-name">
                    Konser Feast
                    <span class="badge badge-diterima" style="margin-left:8px;">Diterima</span>
                </div>
                <div class="pengajuan-meta">2026-05-20 • Basko</div>
            </div>
            <a href="{{ route('client.proposals') }}" class="btn btn-ghost-accent btn-sm">
                <i class="bi bi-file-earmark-text"></i> Lihat Penawaran
            </a>
        </div>

        {{-- Event Saya --}}
        <div class="section-hdr" style="margin-top:28px;">
            <h3>Event Saya</h3>
            <a href="{{ route('client.events') }}">Lihat Semua</a>
        </div>

        <div class="event-dash-card">
            <div class="event-dash-left" style="flex:1;">
                <div class="event-dash-name">
                    Konser Feast
                    <span class="badge badge-mendatang" style="margin-left:8px;">Mendatang</span>
                </div>
                <div class="event-dash-meta">
                    <i class="bi bi-geo-alt-fill"></i>
                    20/5/2026 • Basko
                </div>
                <div class="progress-row">
                    <span class="progress-label">Progres Perencanaan</span>
                    <span class="progress-pct">0%</span>
                </div>
                <div class="progress-bar-wrap">
                    <div class="progress-bar-fill" style="width:0%"></div>
                </div>
            </div>
            <a href="{{ route('client.timeline') }}" class="btn btn-primary btn-sm" style="margin-left:16px; flex-shrink:0;">
                Lihat Timeline <i class="bi bi-arrow-right"></i>
            </a>
        </div>

    </div>

    {{-- Pembaruan Terbaru --}}
    <div class="dash-side">
        <div class="activity-card">
            <div class="activity-title">Pembaruan Terbaru</div>
            <div class="activity-item">
                <div class="activity-dot"><i class="bi bi-check-lg"></i></div>
                <div class="activity-body">
                    <div class="activity-name">Event Dibuat: Konser Feast</div>
                    <div class="activity-desc">Timeline default telah otomatis disusun.</div>
                    <div class="activity-date">20/5/2026</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
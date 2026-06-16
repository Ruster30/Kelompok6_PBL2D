@extends('layouts.admin')

@section('title', 'Analitik')
@section('page-title', 'Analitik')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Analitik &amp; Laporan</h1>
        <p>Ringkasan performa bisnis dan operasional</p>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-top">
            <i class="fas fa-calendar-alt stat-icon"></i>
            <span class="stat-badge">Total</span>
        </div>
        <div class="stat-value">{{ $totalEvents }}</div>
        <div class="stat-label">Total Event</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-top">
            <i class="fas fa-dollar-sign stat-icon"></i>
            <span class="stat-badge green">Revenue</span>
        </div>
        <div class="stat-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        <div class="stat-label">Total Pendapatan (Lunas)</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-top">
            <i class="fas fa-users stat-icon"></i>
            <span class="stat-badge blue">Klien</span>
        </div>
        <div class="stat-value">{{ $activeClients }}</div>
        <div class="stat-label">Klien Aktif</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-top">
            <i class="fas fa-check-square stat-icon"></i>
            <span class="stat-badge yellow">Kinerja</span>
        </div>
        <div class="stat-value">{{ $vendorPerformance }}%</div>
        <div class="stat-label">Performa Vendor (Tugas Selesai)</div>
    </div>
</div>

@if($totalEvents > 0)
<div class="dashboard-bottom" style="grid-template-columns: 1fr 1fr;">
    <div class="card">
        <div class="card-header">
            <span class="card-title">Event per Status</span>
        </div>
        <div style="padding:20px 24px;">
            @foreach($eventsByStatus as $status => $count)
            <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f8fafc; font-size:14px;">
                <span style="color:#475569;">{{ ucfirst($status) }}</span>
                <span style="font-weight:600; color:#0f172a;">{{ $count }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Pendapatan per Bulan ({{ now()->year }})</span>
        </div>
        <div style="padding:20px 24px;">
            @foreach($monthlyRevenue as $month => $amount)
            <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f8fafc; font-size:14px;">
                <span style="color:#475569;">{{ $month }}</span>
                <span style="font-weight:600; color:#0f172a;">Rp {{ number_format($amount, 0, ',', '.') }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection
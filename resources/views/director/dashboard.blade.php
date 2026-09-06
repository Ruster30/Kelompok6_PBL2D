@extends('layouts.director')

@section('title', 'Dashboard Director')
@section('page-title', 'Dashboard Director')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1>Dashboard Director</h1>
        <p>Selamat datang, {{ auth()->user()->name }}.</p>
    </div>
</div>

<div class="stats-grid-dash mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3">
            <div class="rounded-circle bg-warning bg-opacity-10 p-3 d-flex align-items-center justify-content-center" style="width:56px;height:56px;">
                <i class="bi bi-clock-history text-warning fs-3"></i>
            </div>
            <div>
                <p class="text-muted small mb-0">Pending Approval</p>
                <h2 class="fw-bold mb-0">{{ $pendingCount }}</h2>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3">
            <div class="rounded-circle bg-success bg-opacity-10 p-3 d-flex align-items-center justify-content-center" style="width:56px;height:56px;">
                <i class="bi bi-check-circle text-success fs-3"></i>
            </div>
            <div>
                <p class="text-muted small mb-0">Approved Hari Ini</p>
                <h2 class="fw-bold mb-0">{{ $approvedToday }}</h2>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3">
            <div class="rounded-circle bg-danger bg-opacity-10 p-3 d-flex align-items-center justify-content-center" style="width:56px;height:56px;">
                <i class="bi bi-x-circle text-danger fs-3"></i>
            </div>
            <div>
                <p class="text-muted small mb-0">Rejected Hari Ini</p>
                <h2 class="fw-bold mb-0">{{ $rejectedToday }}</h2>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3">
            <div class="rounded-circle bg-primary bg-opacity-10 p-3 d-flex align-items-center justify-content-center" style="width:56px;height:56px;">
                <i class="bi bi-file-earmark-text text-primary fs-3"></i>
            </div>
            <div>
                <p class="text-muted small mb-0">Menu Approval</p>
                <a href="{{ route('director.approval.index') }}" class="fw-bold text-decoration-none">Buka Approval</a>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h5 class="fw-bold mb-2"><i class="bi bi-info-circle me-2"></i>Akses Cepat</h5>
        <div class="d-flex gap-3 flex-wrap">
            <a href="{{ route('director.approval.index') }}" class="btn btn-primary">
                <i class="bi bi-list-check me-1"></i> Daftar Approval
            </a>
            <a href="{{ route('director.settings.pin') }}" class="btn btn-outline-secondary">
                <i class="bi bi-shield-lock me-1"></i> Pengaturan PIN
            </a>
        </div>
    </div>
</div>
@endsection
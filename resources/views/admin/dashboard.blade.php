@extends('layouts.admin')

@section('title', 'Ringkasan Dashboard')
@section('page-title', 'Ringkasan Dashboard')

@section('content')
{{-- Stats Grid --}}
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
            <i class="fas fa-users stat-icon"></i>
            <span class="stat-badge blue">Klien</span>
        </div>
        <div class="stat-value">{{ $totalClients }}</div>
        <div class="stat-label">Klien Aktif</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-top">
            <i class="fas fa-dollar-sign stat-icon"></i>
            <span class="stat-badge green">Revenue</span>
        </div>
        <div class="stat-value">Rp {{ number_format($revenue, 0, ',', '.') }}</div>
        <div class="stat-label">Pendapatan (Lunas)</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-top">
            <i class="fas fa-check-circle stat-icon"></i>
            <span class="stat-badge yellow">Tugas</span>
        </div>
        <div class="stat-value">{{ $totalVendors }}</div>
        <div class="stat-label">Total Vendor</div>
    </div>
</div>

{{-- Bottom section --}}
<div class="dashboard-bottom">
    {{-- Recent Events table --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Event Terbaru</span>
            <a href="{{ route('admin.events.index') }}" class="card-link">Lihat Semua</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Nama Event</th>
                    <th>Klien</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentEvents as $event)
                <tr>
                    <td style="font-weight:500;">{{ $event->nama_event }}</td>
                    <td>{{ \Carbon\Carbon::parse($event->tanggal_mulai)->format('d M Y') }}</td>
                    <td>
                        @php
                            $statusMap = ['aktif'=>'badge-active','selesai'=>'badge-done','pending'=>'badge-pending','batal'=>'badge-cancel'];
                            $cls = $statusMap[strtolower($event->status)] ?? 'badge-pending';
                        @endphp
                        <span class="badge {{ $cls }}">{{ ucfirst($event->status) }}</span>
                    </td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('admin.events.show', $event->id) }}" class="action-btn" title="Lihat">
                                <i class="fas fa-eye" style="font-size:13px;"></i>
                            </a>
                            <a href="{{ route('admin.events.edit', $event->id) }}" class="action-btn" title="Edit">
                                <i class="fas fa-edit" style="font-size:13px;"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-row"><td colspan="5">Belum ada event</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Quick Actions --}}
    <div>
        <div class="card-header" style="background:white; border-radius:12px 12px 0 0; border:1px solid #e2e8f0; border-bottom:none;">
            <span class="card-title">Aksi Cepat</span>
        </div>
        <div style="background:white; border:1px solid #e2e8f0; border-top:none; border-radius:0 0 12px 12px; padding:16px; display:flex; flex-direction:column; gap:10px;">
            <a href="{{ route('admin.events.create') }}" class="quick-action-card">
                <div class="qa-icon"><i class="fas fa-plus"></i></div>
                <div>
                    <div class="qa-title">Buat Event</div>
                    <div class="qa-desc">Mulai merencanakan event baru</div>
                </div>
            </a>
            <a href="{{ route('admin.requests.index') }}" class="quick-action-card">
                <div class="qa-icon"><i class="fas fa-file-alt"></i></div>
                <div>
                    <div class="qa-title">Kelola Permintaan</div>
                    <div class="qa-desc">Kelola permintaan event yang masuk</div>
                </div>
            </a>
            <a href="{{ route('admin.clients.create') }}" class="quick-action-card">
                <div class="qa-icon"><i class="fas fa-user-plus"></i></div>
                <div>
                    <div class="qa-title">Tambah Client</div>
                    <div class="qa-desc">Daftarkan client baru</div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection

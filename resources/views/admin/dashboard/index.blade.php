@extends('layouts.admin')

@section('title', 'Ringkasan Dashboard')
@section('page-title', 'Ringkasan Dashboard')

@push('styles')
<style>
/* â”€â”€ Dashboard-specific overrides â”€â”€ */
.stats-grid-dash {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 28px;
}

.stat-card-dash {
    background: #fff;
    border: 1px solid #e8edf5;
    border-radius: 16px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    transition: box-shadow 0.2s;
}
.stat-card-dash:hover { box-shadow: 0 4px 18px rgba(0,0,0,.07); }

.stat-card-top-dash {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.stat-icon-circle {
    width: 46px; height: 46px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
}
.stat-icon-circle.teal  { background: #e6faf8; color: #14b8a6; }
.stat-icon-circle.blue  { background: #e0edff; color: #3b82f6; }
.stat-icon-circle.green { background: #e6f9ee; color: #22c55e; }
.stat-icon-circle.amber { background: #fff8e1; color: #f59e0b; }

.stat-badge-dash {
    font-size: 11px; font-weight: 600;
    padding: 3px 11px; border-radius: 20px;
}
.stat-badge-dash.teal  { background: #ccfbf1; color: #0d9488; }
.stat-badge-dash.blue  { background: #dbeafe; color: #1d4ed8; }
.stat-badge-dash.green { background: #dcfce7; color: #15803d; }
.stat-badge-dash.amber { background: #fef3c7; color: #b45309; }

.stat-value-dash { font-size: 28px; font-weight: 700; color: #0f172a; line-height: 1; margin-bottom: 8px; }
.stat-label-dash { font-size: 13px; color: #64748b; line-height: 1.5; margin-top: 0; }

/* â”€â”€ Bottom Layout â”€â”€ */
.dash-bottom {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 20px;
}

/* â”€â”€ Recent Events Card â”€â”€ */
.dash-card {
    background: #fff;
    border: 1px solid #e8edf5;
    border-radius: 16px;
    overflow: hidden;
}
.dash-card-header {
    padding: 20px 24px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #f1f5f9;
}
.dash-card-title {
    font-size: 16px; font-weight: 700; color: #0f172a;
}
.dash-card-link {
    font-size: 13px; color: #14b8a6; text-decoration: none; font-weight: 500;
}
.dash-card-link:hover { text-decoration: underline; }

.event-table { width: 100%; border-collapse: collapse; }
.event-table thead tr { border-bottom: 1px solid #f1f5f9; }
.event-table thead th {
    padding: 10px 20px; text-align: left;
    font-size: 11px; font-weight: 600;
    color: #94a3b8; text-transform: uppercase; letter-spacing: 0.4px;
}
.event-table tbody tr { border-bottom: 1px solid #f8fafc; transition: background .15s; }
.event-table tbody tr:last-child { border-bottom: none; }
.event-table tbody tr:hover { background: #f8fafc; }
.event-table tbody td { padding: 14px 20px; font-size: 13.5px; color: #334155; }

.status-pill {
    display: inline-flex; align-items: center;
    padding: 4px 12px; border-radius: 20px;
    font-size: 12px; font-weight: 500;
}
.status-diproses  { background: #dbeafe; color: #1d4ed8; }
.status-berjalan  { background: #dcfce7; color: #15803d; }
.status-selesai   { background: #e0e7ff; color: #4338ca; }
.status-menunggu  { background: #fef3c7; color: #b45309; }
.status-dibatalkan { background: #fee2e2; color: #b91c1c; }

.eye-btn {
    width: 32px; height: 32px; border-radius: 8px; border: 1px solid #e2e8f0;
    background: #fff; display: inline-flex; align-items: center; justify-content: center;
    color: #94a3b8; cursor: pointer; text-decoration: none; transition: all .15s;
}
.eye-btn:hover { border-color: #14b8a6; color: #14b8a6; }

/* â”€â”€ Quick Actions â”€â”€ */
.quick-card {
    background: #fff;
    border: 1px solid #e8edf5;
    border-radius: 16px;
    overflow: hidden;
}
.quick-card-header {
    padding: 20px 24px 16px;
    border-bottom: 1px solid #f1f5f9;
}
.quick-actions-list {
    display: flex; flex-direction: column; gap: 2px; padding: 10px 12px;
}
.qa-item {
    display: flex; align-items: center; gap: 14px;
    padding: 12px 12px; border-radius: 12px;
    text-decoration: none; transition: background .15s;
}
.qa-item:hover { background: #f8fafc; }
.qa-icon-wrap {
    width: 42px; height: 42px; border-radius: 10px;
    background: #f0fdf9; display: flex; align-items: center;
    justify-content: center; color: #14b8a6; font-size: 17px;
    flex-shrink: 0;
}
.qa-text-title { font-size: 14px; font-weight: 600; color: #0f172a; }
.qa-text-desc  { font-size: 12px; color: #94a3b8; margin-top: 2px; }

@media (max-width: 1100px) {
    .stats-grid-dash { grid-template-columns: repeat(2, 1fr); }
    .dash-bottom { grid-template-columns: 1fr; }
}

@media (max-width: 575px) {
    .stats-grid-dash { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')

{{-- â”€â”€ Stat Cards â”€â”€ --}}
<div class="stats-grid-dash">

    {{-- Total Event --}}
    <div class="stat-card-dash">
        <div class="stat-card-top-dash">
            <div class="stat-icon-circle teal">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <span class="stat-badge-dash teal">Total</span>
        </div>
        <div>
            <div class="stat-value-dash">{{ $totalEvents }}</div>
            <div class="stat-label-dash">Total Event</div>
        </div>
    </div>

    {{-- Klien Aktif --}}
    <div class="stat-card-dash">
        <div class="stat-card-top-dash">
            <div class="stat-icon-circle blue">
                <i class="fas fa-user-friends"></i>
            </div>
            <span class="stat-badge-dash blue">Klien</span>
        </div>
        <div>
            <div class="stat-value-dash">{{ $totalClients }}</div>
            <div class="stat-label-dash">Klien Aktif</div>
        </div>
    </div>

    {{-- Revenue --}}
    <div class="stat-card-dash">
        <div class="stat-card-top-dash">
            <div class="stat-icon-circle green">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <span class="stat-badge-dash green">Revenue</span>
        </div>
        <div>
            <div class="stat-value-dash" style="font-size:22px;">Rp {{ number_format($revenue, 0, ',', '.') }}</div>
            <div class="stat-label-dash">Pendapatan (Lunas)</div>
        </div>
    </div>

    {{-- Tugas Belum Selesai --}}
    <div class="stat-card-dash">
        <div class="stat-card-top-dash">
            <div class="stat-icon-circle amber">
                <i class="fas fa-check-circle"></i>
            </div>
            <span class="stat-badge-dash amber">Tugas</span>
        </div>
        <div>
            <div class="stat-value-dash">{{ $pendingTasks }}</div>
            <div class="stat-label-dash">Tugas Belum Selesai</div>
        </div>
    </div>

</div>

{{-- â”€â”€ Bottom Grid â”€â”€ --}}
<div class="dash-bottom">

    {{-- Event Terbaru --}}
    <div class="dash-card">
        <div class="dash-card-header">
            <span class="dash-card-title">Event Terbaru</span>
            <a href="{{ route('admin.events.index') }}" class="dash-card-link">Lihat Semua</a>
        </div>
        <div class="table-responsive-wrap"><table class="event-table">
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
                    <td style="font-weight:600; color:#0f172a;">{{ $event->nama_event }}</td>
                    <td>{{ $event->client->name ?? '-' }}</td>
                    <td>{{ $event->tanggal_event ? $event->tanggal_event->format('d/m/Y') : '-' }}</td>
                    <td>
                        @php
                            $s = strtolower($event->status_event ?? 'menunggu');
                            $pillMap = [
                                'menunggu' => 'status-menunggu',
                                'diproses' => 'status-diproses',
                                'berjalan' => 'status-berjalan',
                                'selesai' => 'status-selesai',
                                'dibatalkan' => 'status-dibatalkan',
                            ];
                            $labelMap = [
                                'menunggu' => 'Menunggu',
                                'diproses' => 'Diproses',
                                'berjalan' => 'Berjalan',
                                'selesai' => 'Selesai',
                                'dibatalkan' => 'Dibatalkan',
                            ];
                            $pillCls = $pillMap[$s] ?? 'status-menunggu';
                            $pillLbl = $labelMap[$s] ?? ucfirst($s);
                        @endphp
                        <span class="status-pill {{ $pillCls }}">{{ $pillLbl }}</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.events.show', $event->id) }}" class="eye-btn" title="Lihat Detail">
                            <i class="fas fa-eye" style="font-size:13px;"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; color:#94a3b8; padding:40px; font-size:14px;">
                        Belum ada event
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

    {{-- Aksi Cepat --}}
    <div class="quick-card">
        <div class="quick-card-header">
            <span class="dash-card-title">Aksi Cepat</span>
        </div>
        <div class="quick-actions-list">
            <a href="{{ route('admin.events.create') }}" class="qa-item">
                <div class="qa-icon-wrap">
                    <i class="fas fa-plus"></i>
                </div>
                <div>
                    <div class="qa-text-title">Buat Event</div>
                    <div class="qa-text-desc">Mulai merencanakan event baru</div>
                </div>
            </a>
            <a href="{{ route('admin.requests.index') }}" class="qa-item">
                <div class="qa-icon-wrap">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div>
                    <div class="qa-text-title">Buat Proposal</div>
                    <div class="qa-text-desc">Buat proposal klien baru</div>
                </div>
            </a>
            <a href="{{ route('admin.vendors.index') }}" class="qa-item">
                <div class="qa-icon-wrap">
                    <i class="fas fa-user-friends"></i>
                </div>
                <div>
                    <div class="qa-text-title">Tambah Vendor</div>
                    <div class="qa-text-desc">Daftarkan mitra vendor baru</div>
                </div>
            </a>
        </div>
    </div>

</div>
@endsection



@extends('layouts.admin')

@section('title', 'Analitik')
@section('page-title', 'Analitik')

@section('content')
<style>
    .analytics-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
        gap: 20px;
    }
    .analytics-header-left h1 {
        font-size: 28px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
    }
    .analytics-header-left p {
        color: #64748b;
        font-size: 14px;
    }
    .analytics-actions {
        display: flex;
        gap: 10px;
    }
    .btn-export {
        padding: 10px 18px;
        border-radius: 8px;
        border: none;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    .btn-pdf {
        background: #ef4444;
        color: white;
    }
    .btn-pdf:hover {
        background: #dc2626;
        color: white;
    }
    .btn-excel {
        background: #10b981;
        color: white;
    }
    .btn-excel:hover {
        background: #059669;
        color: white;
    }
    .btn-print {
        background: #6366f1;
        color: white;
    }
    .btn-print:hover {
        background: #4f46e5;
        color: white;
    }
    .filter-card {
        background: white;
        border-radius: 12px;
        padding: 20px 24px;
        margin-bottom: 24px;
        border: 1px solid #e2e8f0;
    }
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        align-items: end;
    }
    .filter-group label {
        display: block;
        font-size: 13px;
        font-weight: 500;
        color: #475569;
        margin-bottom: 6px;
    }
    .filter-group select {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 14px;
        color: #0f172a;
        background: white;
    }
    .filter-group select:focus {
        outline: none;
        border-color: #14b8a6;
    }
    .btn-filter {
        padding: 10px 20px;
        background: #14b8a6;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-filter:hover {
        background: #0d9488;
    }
    .btn-reset {
        padding: 10px 20px;
        background: #f1f5f9;
        color: #475569;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-reset:hover {
        background: #e2e8f0;
    }
    .stats-grid-analytics {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 28px;
    }
    .stat-card-analytics {
        background: white;
        border-radius: 12px;
        padding: 22px;
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        gap: 8px;
        transition: all 0.2s;
    }
    .stat-card-analytics:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }
    .stat-card-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .stat-icon.blue { background: #dbeafe; color: #1e40af; }
    .stat-icon.green { background: #d1fae5; color: #065f46; }
    .stat-icon.purple { background: #e9d5ff; color: #6b21a8; }
    .stat-icon.orange { background: #fed7aa; color: #9a3412; }
    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: #0f172a;
        margin-top: 8px;
    }
    .stat-label {
        font-size: 13px;
        color: #64748b;
        font-weight: 500;
    }
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
        margin-bottom: 28px;
    }
    .chart-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        border: 1px solid #e2e8f0;
    }
    .chart-card h3 {
        font-size: 16px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 20px;
    }
    .chart-container {
        position: relative;
        height: 300px;
    }
    .table-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
    }
    .table-card h3 {
        font-size: 16px;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 16px;
    }
    .analytics-table {
        width: 100%;
        border-collapse: collapse;
    }
    .analytics-table th {
        text-align: left;
        padding: 12px;
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }
    .analytics-table td {
        padding: 12px;
        font-size: 14px;
        color: #0f172a;
        border-bottom: 1px solid #f1f5f9;
    }
    .analytics-table tr:hover {
        background: #f8fafc;
    }
    .period-btn {
        padding: 7px 16px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        background: white;
        color: #475569;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }
    .period-btn:hover {
        border-color: #14b8a6;
        color: #0d9488;
        background: #f0fdfa;
    }
    .period-active {
        background: #14b8a6 !important;
        color: white !important;
        border-color: #14b8a6 !important;
    }
    @media print {
        .analytics-actions, .filter-card, .btn-export { display: none; }
    }
</style>

<div class="analytics-header">
    <div class="analytics-header-left">
        <h1>Dashboard Analitik</h1>
        <p>Ringkasan performa bisnis dan operasional
            @php
                $periodLabels = ['today'=>'Hari Ini','yesterday'=>'Kemarin','last_7_days'=>'7 Hari Terakhir','last_30_days'=>'30 Hari Terakhir','this_week'=>'Minggu Ini','this_month'=>'Bulan Ini','this_year'=>'Tahun Ini','custom'=>'Rentang Tanggal','all'=>'Semua Data'];
                $label = $periodLabels[$activePeriod ?? 'all'] ?? 'Semua Data';
            @endphp
            <strong>{{ $label }}</strong>
            @if(($activePeriod ?? 'all') === 'all')&mdash; {{ $filters['year'] }}@endif
        </p>
    </div>
    <div class="analytics-actions">
        <button onclick="window.print()" class="btn-export btn-print">
            <i class="fas fa-print"></i> Print
        </button>
        <a href="{{ route('admin.analytics.export.excel', $filters) }}" class="btn-export btn-excel">
            <i class="fas fa-file-excel"></i> Export Excel
        </a>
        <a href="{{ route('admin.analytics.export.pdf', $filters) }}" class="btn-export btn-pdf">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
    </div>
</div>

<!-- Filter Card -->
<div class="filter-card">
    <form method="GET" action="{{ route('admin.analytics.index') }}" id="analyticsForm">
        <input type="hidden" name="period" id="filterPeriod" value="{{ $activePeriod ?? 'all' }}">
        <input type="hidden" name="start_date" id="filterStartDate" value="{{ $startDate ?? '' }}">
        <input type="hidden" name="end_date" id="filterEndDate" value="{{ $endDate ?? '' }}">

        <!-- Period Quick Filters -->
        <div style="margin-bottom:16px;">
            <label style="display:block; font-size:13px; font-weight:500; color:#475569; margin-bottom:8px;">Periode</label>
            <div style="display:flex; flex-wrap:wrap; gap:6px;">
                @php
                    $periods = [
                        'today' => 'Hari Ini',
                        'yesterday' => 'Kemarin',
                        'last_7_days' => '7 Hari Terakhir',
                        'last_30_days' => '30 Hari Terakhir',
                        'this_week' => 'Minggu Ini',
                        'this_month' => 'Bulan Ini',
                        'this_year' => 'Tahun Ini',
                        'custom' => 'Rentang Tanggal',
                        'all' => 'Semua Data',
                    ];
                @endphp
                @foreach($periods as $key => $label)
                    <button type="button"
                        class="period-btn {{ ($activePeriod ?? 'all') === $key ? 'period-active' : '' }}"
                        data-period="{{ $key }}"
                        onclick="setPeriod('{{ $key }}')">{{ $label }}</button>
                @endforeach
            </div>
        </div>

        <!-- Custom Date Range (shown only when period=custom) -->
        <div id="customDateRange" style="display:none; margin-bottom:16px; padding:12px; background:#f8fafc; border-radius:8px; border:1px solid #e2e8f0;">
            <div style="display:flex; gap:12px; align-items:end; flex-wrap:wrap;">
                <div class="filter-group" style="margin-bottom:0;">
                    <label>Dari Tanggal</label>
                    <input type="date" name="start_date_input" id="startDateInput"
                           value="{{ $startDate ?? '' }}"
                           style="width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px;"
                           onchange="document.getElementById('filterStartDate').value=this.value">
                </div>
                <div class="filter-group" style="margin-bottom:0;">
                    <label>Sampai Tanggal</label>
                    <input type="date" name="end_date_input" id="endDateInput"
                           value="{{ $endDate ?? '' }}"
                           style="width:100%; padding:9px 12px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px;"
                           onchange="document.getElementById('filterEndDate').value=this.value">
                </div>
                <button type="submit" class="btn-filter" style="padding:9px 16px; font-size:13px;">
                    <i class="fas fa-check"></i> Terapkan
                </button>
            </div>
        </div>

        <hr style="border:none; border-top:1px solid #e2e8f0; margin-bottom:16px;">

        <div class="filter-grid">
            <div class="filter-group">
                <label>Tahun</label>
                <select name="year" id="filterYear">
                    @foreach($availableYears as $yr)
                        <option value="{{ $yr }}" {{ ($filters['year'] ?? now()->year) == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Bulan</label>
                <select name="month" id="filterMonth">
                    <option value="">Semua Bulan</option>
                    @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $idx => $monthName)
                        <option value="{{ $idx + 1 }}" {{ ($filters['month'] ?? '') == ($idx + 1) ? 'selected' : '' }}>{{ $monthName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Status Event</label>
                <select name="status_event" id="filterStatus">
                    <option value="">Semua Status</option>
                    @foreach($availableStatuses as $status)
                        <option value="{{ $status }}" {{ ($filters['status_event'] ?? '') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Jenis Event</label>
                <select name="jenis_event" id="filterType">
                    <option value="">Semua Jenis</option>
                    @foreach($availableTypes as $type)
                        <option value="{{ $type }}" {{ ($filters['jenis_event'] ?? '') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group" style="display: flex; gap: 8px;">
                <button type="submit" class="btn-filter">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('admin.analytics.index') }}" class="btn-reset">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </div>
    </form>
</div>

<script>
function setPeriod(period) {
    document.getElementById('filterPeriod').value = period;
    document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('period-active'));
    document.querySelector(`.period-btn[data-period="${period}"]`).classList.add('period-active');

    if (period === 'custom') {
        document.getElementById('customDateRange').style.display = 'block';
    } else {
        document.getElementById('customDateRange').style.display = 'none';
        // Clear date inputs for non-custom periods
        document.getElementById('filterStartDate').value = '';
        document.getElementById('filterEndDate').value = '';
        document.getElementById('startDateInput').value = '';
        document.getElementById('endDateInput').value = '';
        // Submit form immediately for quick periods
        document.getElementById('analyticsForm').submit();
    }
}

// Show custom date range if already selected
if (document.getElementById('filterPeriod').value === 'custom') {
    document.getElementById('customDateRange').style.display = 'block';
}
</script>

<!-- Statistics Cards -->
<div class="stats-grid-analytics">
    <div class="stat-card-analytics">
        <div class="stat-card-top">
            <div class="stat-icon blue">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>
        <div class="stat-value">{{ $totalEvents }}</div>
        <div class="stat-label">Total Event</div>
    </div>
    <div class="stat-card-analytics">
        <div class="stat-card-top">
            <div class="stat-icon orange">
                <i class="fas fa-spinner"></i>
            </div>
        </div>
        <div class="stat-value">{{ $eventsBerjalan }}</div>
        <div class="stat-label">Event Berjalan</div>
    </div>
    <div class="stat-card-analytics">
        <div class="stat-card-top">
            <div class="stat-icon green">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="stat-value">{{ $eventsSelesai }}</div>
        <div class="stat-label">Event Selesai</div>
    </div>
    <div class="stat-card-analytics">
        <div class="stat-card-top">
            <div class="stat-icon purple">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-value">{{ $totalClients }}</div>
        <div class="stat-label">Total Klien</div>
    </div>
    <div class="stat-card-analytics">
        <div class="stat-card-top">
            <div class="stat-icon blue">
                <i class="fas fa-handshake"></i>
            </div>
        </div>
        <div class="stat-value">{{ $totalVendors }}</div>
        <div class="stat-label">Total Vendor</div>
    </div>
    <div class="stat-card-analytics">
        <div class="stat-card-top">
            <div class="stat-icon orange">
                <i class="fas fa-file-invoice"></i>
            </div>
        </div>
        <div class="stat-value">{{ $totalInvoices }}</div>
        <div class="stat-label">Total Invoice</div>
    </div>
    <div class="stat-card-analytics">
        <div class="stat-card-top">
            <div class="stat-icon green">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>
        <div class="stat-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        <div class="stat-label">Total Pendapatan</div>
    </div>
    <div class="stat-card-analytics">
        <div class="stat-card-top">
            <div class="stat-icon purple">
                <i class="fas fa-check-double"></i>
            </div>
        </div>
        <div class="stat-value">{{ $paidInvoices }}</div>
        <div class="stat-label">Pembayaran Lunas</div>
    </div>
</div>

<!-- Charts Grid -->
<div class="charts-grid">
    <!-- Line Chart - Monthly Revenue -->
    <div class="chart-card">
        <h3><i class="fas fa-chart-line"></i> Pendapatan per Bulan ({{ $filters['year'] }})</h3>
        <div class="chart-container">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
    
    <!-- Bar Chart - Monthly Events -->
    <div class="chart-card">
        <h3><i class="fas fa-chart-bar"></i> Event per Bulan ({{ $filters['year'] }})</h3>
        <div class="chart-container">
            <canvas id="eventsChart"></canvas>
        </div>
    </div>
    
    <!-- Pie Chart - Event Status -->
    <div class="chart-card">
        <h3><i class="fas fa-chart-pie"></i> Status Event</h3>
        <div class="chart-container">
            <canvas id="statusChart"></canvas>
        </div>
    </div>
    
    <!-- Donut Chart - Event Type -->
    <div class="chart-card">
        <h3><i class="fas fa-chart-donut"></i> Jenis Event</h3>
        <div class="chart-container">
            <canvas id="typeChart"></canvas>
        </div>
    </div>
</div>

<!-- Top Tables -->
<div class="table-card">
    <h3><i class="fas fa-trophy"></i> Top 10 Klien (Berdasarkan Nilai Event)</h3>
    <div class="table-responsive-wrap"><table class="analytics-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Klien</th>
                <th>Email</th>
                <th>Jumlah Event</th>
                <th>Total Nilai Event</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topClients as $idx => $client)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ $client->name }}</td>
                <td>{{ $client->email }}</td>
                <td>{{ $client->events_count }}</td>
                <td>Rp {{ number_format($client->total_invoice_value ?? 0, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; color: #94a3b8;">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table></div></div>

<div class="table-card">
    <h3><i class="fas fa-star"></i> Top 10 Vendor (Berdasarkan Nilai RAB)</h3>
    <div class="table-responsive-wrap"><table class="analytics-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Vendor</th>
                <th>Jenis Vendor</th>
                <th>Email</th>
                <th>Jumlah RAB</th>
                <th>Total Nilai RAB</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topVendors as $idx => $vendor)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ $vendor->nama_vendor }}</td>
                <td>{{ $vendor->jenis_vendor }}</td>
                <td>{{ $vendor->email }}</td>
                <td>{{ $vendor->rabs_count }}</td>
                <td>Rp {{ number_format($vendor->rabs_sum_subtotal_biaya ?? 0, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; color: #94a3b8;">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table></div></div>

<div class="table-card">
    <h3><i class="fas fa-medal"></i> Top 10 Event (Berdasarkan Nilai)</h3>
    <div class="table-responsive-wrap"><table class="analytics-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Event</th>
                <th>Jenis Event</th>
                <th>Klien</th>
                <th>Status</th>
                <th>Nilai Event</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topEvents as $idx => $event)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ $event->nama_event }}</td>
                <td>{{ $event->jenis_event }}</td>
                <td>{{ $event->client->name ?? '-' }}</td>
                <td>
                    <span class="badge {{ $event->badge_class }}">{{ $event->status_label }}</span>
                </td>
                <td>Rp {{ number_format($event->total_invoice_value, 0, ',', '.') }}</td>
                <td>{{ $event->tanggal_event->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; color: #94a3b8;">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table></div></div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart Colors
    const colors = {
        primary: '#14b8a6',
        blue: '#3b82f6',
        green: '#10b981',
        yellow: '#f59e0b',
        red: '#ef4444',
        purple: '#8b5cf6',
        pink: '#ec4899',
        orange: '#f97316'
    };

    // Monthly Revenue Line Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: @json(array_values($monthlyRevenue)),
                borderColor: colors.green,
                backgroundColor: colors.green + '20',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });

    // Monthly Events Bar Chart
    const eventsCtx = document.getElementById('eventsChart').getContext('2d');
    const eventsChart = new Chart(eventsCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                label: 'Jumlah Event',
                data: @json(array_values($monthlyEvents)),
                backgroundColor: colors.blue,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Event Status Pie Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusData = @json($eventsByStatus);
    const statusChart = new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: Object.keys(statusData).map(s => s.charAt(0).toUpperCase() + s.slice(1)),
            datasets: [{
                data: Object.values(statusData),
                backgroundColor: [colors.blue, colors.yellow, colors.green, colors.purple, colors.red]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Event Type Donut Chart
    const typeCtx = document.getElementById('typeChart').getContext('2d');
    const typeData = @json($eventsByType);
    const typeChart = new Chart(typeCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(typeData),
            datasets: [{
                data: Object.values(typeData),
                backgroundColor: [colors.primary, colors.orange, colors.pink, colors.purple, colors.blue, colors.green]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>

@endsection




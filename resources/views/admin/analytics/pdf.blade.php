<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Analitik</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            border-bottom: 3px solid #14b8a6;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header-content {
            display: table;
            width: 100%;
        }
        .header-left {
            display: table-cell;
            width: 70%;
            vertical-align: middle;
        }
        .header-right {
            display: table-cell;
            width: 30%;
            text-align: right;
            vertical-align: middle;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 3px;
        }
        .report-title {
            font-size: 14px;
            font-weight: bold;
            color: #14b8a6;
            margin-top: 5px;
        }
        .report-period {
            font-size: 9px;
            color: #64748b;
            margin-top: 3px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            background: #f1f5f9;
            padding: 8px 10px;
            margin-bottom: 10px;
            border-left: 4px solid #14b8a6;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .stat-row {
            display: table-row;
        }
        .stat-cell {
            display: table-cell;
            width: 25%;
            padding: 8px;
            border: 1px solid #e2e8f0;
            background: #fff;
        }
        .stat-label {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .stat-value {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th {
            background: #f8fafc;
            color: #475569;
            font-weight: bold;
            text-align: left;
            padding: 8px 6px;
            border: 1px solid #e2e8f0;
            font-size: 9px;
        }
        table td {
            padding: 6px;
            border: 1px solid #e2e8f0;
            font-size: 9px;
        }
        table tr:nth-child(even) {
            background: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: 600;
        }
        .badge-aktif {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-mendatang {
            background: #fef3c7;
            color: #92400e;
        }
        .badge-pending {
            background: #e0e7ff;
            color: #3730a3;
        }
        .badge-selesai {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-ditolak {
            background: #fee2e2;
            color: #991b1b;
        }
        .chart-placeholder {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 20px;
            text-align: center;
            color: #64748b;
            font-size: 9px;
            margin-bottom: 15px;
        }
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .summary-row {
            display: table-row;
        }
        .summary-label {
            display: table-cell;
            padding: 6px;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 500;
            width: 60%;
        }
        .summary-value {
            display: table-cell;
            padding: 6px;
            border-bottom: 1px solid #e2e8f0;
            text-align: right;
            font-weight: bold;
            width: 40%;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="header-left">
                <div class="company-name">{{ $companyName ?? 'Event Management System' }}</div>
                <div class="report-title">LAPORAN ANALITIK BISNIS</div>
                <div class="report-period">
                    Periode: {{ $filters['year'] }}
                    @if($filters['month'])
                        - {{ \Carbon\Carbon::create()->month($filters['month'])->translatedFormat('F') }}
                    @endif
                    @if($filters['status_event'])
                        | Status: {{ ucfirst($filters['status_event']) }}
                    @endif
                    @if($filters['jenis_event'])
                        | Jenis: {{ $filters['jenis_event'] }}
                    @endif
                </div>
            </div>
            <div class="header-right">
                <div style="font-size: 8px; color: #64748b;">Tanggal Cetak:</div>
                <div style="font-size: 9px; font-weight: bold;">{{ now()->translatedFormat('d F Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Statistik -->
    <div class="section">
        <div class="section-title">📊 RINGKASAN STATISTIK</div>
        
        <div class="stats-grid">
            <div class="stat-row">
                <div class="stat-cell">
                    <div class="stat-label">Total Event</div>
                    <div class="stat-value">{{ $totalEvents }}</div>
                </div>
                <div class="stat-cell">
                    <div class="stat-label">Event Berjalan</div>
                    <div class="stat-value">{{ $eventsBerjalan }}</div>
                </div>
                <div class="stat-cell">
                    <div class="stat-label">Event Selesai</div>
                    <div class="stat-value">{{ $eventsSelesai }}</div>
                </div>
                <div class="stat-cell">
                    <div class="stat-label">Total Klien</div>
                    <div class="stat-value">{{ $totalClients }}</div>
                </div>
            </div>
            <div class="stat-row">
                <div class="stat-cell">
                    <div class="stat-label">Total Vendor</div>
                    <div class="stat-value">{{ $totalVendors }}</div>
                </div>
                <div class="stat-cell">
                    <div class="stat-label">Total Invoice</div>
                    <div class="stat-value">{{ $totalInvoices }}</div>
                </div>
                <div class="stat-cell">
                    <div class="stat-label">Total Pendapatan</div>
                    <div class="stat-value" style="font-size: 11px;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                </div>
                <div class="stat-cell">
                    <div class="stat-label">Pembayaran Lunas</div>
                    <div class="stat-value">{{ $paidInvoices }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Event -->
    <div class="section">
        <div class="section-title">📈 DISTRIBUSI STATUS EVENT</div>
        <div class="summary-grid">
            @foreach($eventsByStatus as $status => $count)
            <div class="summary-row">
                <div class="summary-label">{{ ucfirst($status) }}</div>
                <div class="summary-value">{{ $count }} Event</div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Jenis Event -->
    <div class="section">
        <div class="section-title">🎯 DISTRIBUSI JENIS EVENT</div>
        <div class="summary-grid">
            @foreach($eventsByType as $type => $count)
            <div class="summary-row">
                <div class="summary-label">{{ $type }}</div>
                <div class="summary-value">{{ $count }} Event</div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Pendapatan Bulanan -->
    <div class="section">
        <div class="section-title">💰 PENDAPATAN PER BULAN ({{ $filters['year'] }})</div>
        <table>
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th style="text-align: right;">Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    $totalYear = 0;
                @endphp
                @foreach($monthlyRevenue as $idx => $amount)
                @php $totalYear += $amount; @endphp
                <tr>
                    <td>{{ $monthNames[$idx] ?? 'Bulan ' . ($idx + 1) }}</td>
                    <td style="text-align: right; font-weight: 600;">Rp {{ number_format($amount, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr style="background: #14b8a6; color: white; font-weight: bold;">
                    <td>TOTAL</td>
                    <td style="text-align: right;">Rp {{ number_format($totalYear, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Top Klien -->
    <div class="section">
        <div class="section-title">🏆 TOP 10 KLIEN (Berdasarkan Nilai Event)</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 35%;">Nama Klien</th>
                    <th style="width: 30%;">Email</th>
                    <th style="width: 10%; text-align: center;">Event</th>
                    <th style="width: 20%; text-align: right;">Total Nilai</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topClients as $idx => $client)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->email }}</td>
                    <td style="text-align: center;">{{ $client->events_count }}</td>
                    <td style="text-align: right; font-weight: 600;">Rp {{ number_format($client->total_invoice_value ?? 0, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #94a3b8;">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Top Vendor -->
    <div class="section">
        <div class="section-title">⭐ TOP 10 VENDOR (Berdasarkan Nilai RAB)</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 30%;">Nama Vendor</th>
                    <th style="width: 20%;">Jenis</th>
                    <th style="width: 25%;">Email</th>
                    <th style="width: 10%; text-align: center;">RAB</th>
                    <th style="width: 10%; text-align: right;">Total Nilai</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topVendors as $idx => $vendor)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td>{{ $vendor->nama_vendor }}</td>
                    <td>{{ $vendor->jenis_vendor }}</td>
                    <td>{{ $vendor->email }}</td>
                    <td style="text-align: center;">{{ $vendor->rabs_count }}</td>
                    <td style="text-align: right; font-weight: 600;">Rp {{ number_format($vendor->rabs_sum_subtotal_biaya ?? 0, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #94a3b8;">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Top Event -->
    <div class="section">
        <div class="section-title">🥇 TOP 10 EVENT (Berdasarkan Nilai)</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 25%;">Nama Event</th>
                    <th style="width: 15%;">Jenis</th>
                    <th style="width: 20%;">Klien</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 15%; text-align: right;">Nilai</th>
                    <th style="width: 10%;">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topEvents as $idx => $event)
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td>{{ $event->nama_event }}</td>
                    <td>{{ $event->jenis_event }}</td>
                    <td>{{ $event->client->name ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $event->badge_class }}">{{ $event->status_label }}</span>
                    </td>
                    <td style="text-align: right; font-weight: 600;">Rp {{ number_format($event->total_invoice_value, 0, ',', '.') }}</td>
                    <td>{{ $event->tanggal_event->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #94a3b8;">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Event per Bulan -->
    <div class="section">
        <div class="section-title">📅 JUMLAH EVENT PER BULAN ({{ $filters['year'] }})</div>
        <table>
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th style="text-align: center;">Jumlah Event</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    $totalEvents = 0;
                @endphp
                @foreach($monthlyEvents as $idx => $count)
                @php $totalEvents += $count; @endphp
                <tr>
                    <td>{{ $monthNames[$idx] ?? 'Bulan ' . ($idx + 1) }}</td>
                    <td style="text-align: center; font-weight: 600;">{{ $count }}</td>
                </tr>
                @endforeach
                <tr style="background: #14b8a6; color: white; font-weight: bold;">
                    <td>TOTAL</td>
                    <td style="text-align: center;">{{ $totalEvents }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini digenerate secara otomatis oleh sistem | {{ now()->translatedFormat('d F Y H:i') }} WIB</p>
    </div>

</body>
</html>

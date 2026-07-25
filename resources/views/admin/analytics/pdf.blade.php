<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>EVENT ANALYTICS REPORT</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 28px 36px 32px;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', 'Helvetica', sans-serif;
            font-size: 9.5pt;
            line-height: 1.5;
            color: #1e293b;
            background: #ffffff;
        }

        /* ===== TYPOGRAPHY ===== */
        .fw-bold { font-weight: 700; }
        .fw-normal { font-weight: 400; }
        .text-muted { color: #64748b; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-teal { color: #14b8a6; }

        /* ===== HEADER ===== */
        .pdf-header {
            width: 100%;
            margin-bottom: 10px;
            position: relative;
        }
        .pdf-header table {
            width: 100%;
            border-collapse: collapse;
        }
        .pdf-header td {
            vertical-align: middle;
            padding: 0;
            border: none;
        }
        .header-left {
            width: 15%;
            text-align: left;
        }
        .header-center {
            width: 53%;
            text-align: center;
        }
        .header-right {
            width: 32%;
            text-align: right;
        }

        .logo-wrapper {
            display: inline-block;
            vertical-align: middle;
        }
        .header-logo-img {
            width: 72px;
            height: auto;
            object-fit: contain;
            display: block;
        }
        .header-logo-fallback {
            width: 72px;
            height: 72px;
            background: #0f172a;
            border-radius: 14px;
            text-align: center;
            line-height: 72px;
            color: #14b8a6;
            font-size: 28pt;
            font-weight: 700;
        }

        .report-title-main {
            font-size: 16pt;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
        .report-subtitle {
            font-size: 7.5pt;
            font-weight: 500;
            color: #64748b;
            margin-top: 2px;
            letter-spacing: 0.5px;
        }

        .info-card {
            display: inline-block;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 5px 12px 5px 10px;
            text-align: left;
        }
        .info-card table {
            width: auto;
            border-collapse: collapse;
        }
        .info-card td {
            border: none;
            padding: 1px 0;
            vertical-align: middle;
            line-height: 1.3;
        }
        .info-card .icon-cell {
            width: 18px;
            text-align: center;
            padding-right: 6px;
        }
        .info-icon {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 3px;
            text-align: center;
            line-height: 12px;
            font-size: 6pt;
            color: #fff;
        }
        .info-label {
            font-size: 5pt;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .info-value {
            font-size: 6.5pt;
            font-weight: 700;
            color: #0f172a;
        }
        .info-divider {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 0;
        }

        .header-divider {
            border: none;
            border-top: 2.5px solid #14b8a6;
            margin-top: 0;
            margin-bottom: 1px;
        }
        .header-divider-shadow {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin-top: 0;
            margin-bottom: 0;
        }

        /* ===== SECTION TITLE ===== */
        .section-title {
            font-size: 9.5pt;
            font-weight: 700;
            color: #0f172a;
            padding: 6px 14px;
            margin-bottom: 10px;
            border-left: 4px solid #14b8a6;
            background: #f1f5f9;
            border-radius: 4px;
        }

        /* ===== STAT CARDS (8 in 1 row) ===== */
        .stats-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px;
            margin-bottom: 12px;
        }
        .stats-table td {
            padding: 0;
            border: none;
            vertical-align: top;
            width: 12.5%;
        }
        .stat-card {
            background: #ffffff;
            border-radius: 10px;
            padding: 8px 10px;
            height: 64px;
            border: 1px solid #e2e8f0;
        }
        .stat-card .stat-inner {
            width: 100%;
            height: 100%;
        }
        .stat-card .stat-inner td {
            padding: 0;
            border: none;
            vertical-align: middle;
        }
        .stat-card .stat-inner .icon-cell {
            width: 40px;
            text-align: center;
            vertical-align: middle;
        }
        .stat-card .stat-inner .text-cell {
            padding-left: 6px;
            vertical-align: middle;
        }
        .stat-badge-dot {
            display: inline-block;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            vertical-align: middle;
        }
        .stat-label {
            font-size: 5.5pt;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 1px;
        }
        .stat-number {
            font-size: 10pt;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }
        .stat-number-highlight {
            font-size: 10pt;
            font-weight: 800;
            color: #0d9488;
            line-height: 1.2;
        }

        /* ===== CHARTS 2x2 ===== */
        .charts-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
            margin-bottom: 0;
        }
        .charts-table td {
            padding: 0;
            border: none;
            vertical-align: top;
            width: 50%;
        }
        .chart-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 8px 6px;
        }
        .chart-card-title {
            font-size: 9pt;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
            padding-left: 2px;
        }
        .chart-card img.chart-full {
            width: 100%;
            height: auto;
            display: block;
        }
        .chart-card img.chart-pie {
            width: 58%;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        /* ===== DATA TABLES ===== */
        .table-wrapper {
            margin-bottom: 16px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }
        .data-table thead th {
            background: #0f172a;
            color: #ffffff;
            padding: 6px 12px;
            text-align: left;
            font-weight: 700;
            font-size: 7.5pt;
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }
        .data-table thead th:first-child {
            border-radius: 0;
        }
        .data-table thead th:last-child {
            border-radius: 0;
        }
        .data-table tbody td {
            padding: 5px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .data-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .data-table tbody tr:last-child td {
            border-bottom: none;
        }
        .no-data {
            text-align: center;
            color: #94a3b8;
            padding: 20px !important;
            font-style: italic;
        }

        /* ===== BADGES ===== */
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 10px;
            font-size: 7pt;
            font-weight: 700;
            letter-spacing: 0.2px;
            min-width: 50px;
            text-align: center;
        }
        .badge-selesai   { background: #d1fae5; color: #065f46; }
        .badge-berjalan  { background: #dbeafe; color: #1e40af; }
        .badge-menunggu  { background: #fef3c7; color: #92400e; }
        .badge-diproses  { background: #e0e7ff; color: #3730a3; }
        .badge-dibatalkan{ background: #fee2e2; color: #991b1b; }
        .badge-pending   { background: #f1f5f9; color: #475569; }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

    <!-- ============================================================ -->
    <!-- HEADER -->
    <!-- ============================================================ -->
    <div class="pdf-header">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td class="header-left">
                    <div class="logo-wrapper">
                        @php
                            $logoPaths = [
                                public_path('images/landing/logo.png'),
                                public_path('images/Logo-bg.png'),
                                public_path('images/logo.png'),
                            ];
                            $logoFound = false;
                            foreach ($logoPaths as $lp) {
                                if (file_exists($lp)) {
                                    echo '<img src="' . $lp . '" alt="ALPHA.CORP" class="header-logo-img" />';
                                    $logoFound = true;
                                    break;
                                }
                            }
                        @endphp
                        @if (!$logoFound)
                            <div class="header-logo-fallback">A</div>
                        @endif
                    </div>
                </td>

                <td class="header-center">
                    <div class="report-title-main">EVENT ANALYTICS REPORT</div>
                    <div class="report-subtitle">Ringkasan Performa Bisnis &amp; Operasional</div>
                </td>

                <td class="header-right">
                    <div class="info-card">
                        <table cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="icon-cell">
                                    <div class="info-icon" style="background: #14b8a6;">&#9733;</div>
                                </td>
                                <td>
                                    <div class="info-label">Periode Laporan</div>
                                    @php
                                        $periodLabels = ['today'=>'Hari Ini','yesterday'=>'Kemarin','last_7_days'=>'7 Hari Terakhir','last_30_days'=>'30 Hari Terakhir','this_week'=>'Minggu Ini','this_month'=>'Bulan Ini','this_year'=>'Tahun Ini','custom'=>'Rentang Tanggal','all'=>'Semua Data'];
                                        $label = $periodLabels[$filters['period'] ?? 'all'] ?? 'Semua Data';
                                    @endphp
                                    <div class="info-value">{{ $label }} &mdash; {{ $filters['year'] ?? now()->year }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><hr class="info-divider" /></td>
                            </tr>
                            <tr>
                                <td class="icon-cell">
                                    <div class="info-icon" style="background: #0f172a;">&#9679;</div>
                                </td>
                                <td>
                                    <div class="info-label">Tanggal Cetak</div>
                                    <div class="info-value">{{ now()->translatedFormat('d F Y') }}</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
        <hr class="header-divider" />
        <hr class="header-divider-shadow" />
    </div>

    <!-- ============================================================ -->
    <!-- 8 STAT CARDS -->
    <!-- ============================================================ -->
    @php
        $dotColors = ['#14b8a6','#f59e0b','#6366f1','#3b82f6','#8b5cf6','#ec4899','#0d9488','#10b981'];
        $stats = [
            ['label' => 'Total Event',    'value' => number_format($totalEvents, 0, ',', '.')],
            ['label' => 'Event Berjalan', 'value' => number_format($eventsBerjalan, 0, ',', '.')],
            ['label' => 'Event Selesai',  'value' => number_format($eventsSelesai, 0, ',', '.')],
            ['label' => 'Total Client',   'value' => number_format($totalClients, 0, ',', '.')],
            ['label' => 'Total Vendor',   'value' => number_format($totalVendors, 0, ',', '.')],
            ['label' => 'Total Invoice',  'value' => number_format($totalInvoices, 0, ',', '.')],
            ['label' => 'Total Pendapatan','value' => 'Rp '.number_format($totalRevenue, 0, ',', '.')],
            ['label' => 'Pembayaran Lunas','value' => number_format($paidInvoices, 0, ',', '.')],
        ];
    @endphp
    <table class="stats-table" cellpadding="0" cellspacing="5">
        <tr>
            @foreach($stats as $idx => $s)
            <td>
                <div class="stat-card">
                    <table class="stat-inner" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="icon-cell">
                                <div class="stat-badge-dot" style="background: {{ $dotColors[$idx] }};"></div>
                            </td>
                            <td class="text-cell">
                                <div class="stat-label">{{ $s['label'] }}</div>
                                <div class="{{ $idx === 6 ? 'stat-number-highlight' : 'stat-number' }}">{{ $s['value'] }}</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            @endforeach
        </tr>
    </table>

    <!-- ============================================================ -->
    <!-- CHARTS 2x2 -->
    <!-- ============================================================ -->
    <div style="margin-bottom: 0;">
        <div style="margin-bottom: 10px;">
            <div class="section-title">&#9670; Analitik Grafik {{ $filters['year'] ?? now()->year }}</div>
        </div>

        <table class="charts-table" cellpadding="0" cellspacing="6">
            <tr>
                <td>
                    <div class="chart-card">
                        <div class="chart-card-title">&#9679; Pendapatan per Bulan</div>
                        @php $c1 = \App\Helpers\ChartHelper::lineChart($monthlyRevenue ?? [], 'Pendapatan per Bulan'); @endphp
                        <img src="{{ $c1 }}" alt="Revenue Chart" class="chart-full" />
                    </div>
                </td>
                <td>
                    <div class="chart-card">
                        <div class="chart-card-title">&#9679; Event per Bulan</div>
                        @php $c2 = \App\Helpers\ChartHelper::barChart($monthlyEvents ?? [], 'Event per Bulan'); @endphp
                        <img src="{{ $c2 }}" alt="Events Chart" class="chart-full" />
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="chart-card">
                        <div class="chart-card-title">&#9679; Status Event</div>
                        @php $c3 = \App\Helpers\ChartHelper::pieChart($eventsByStatus ?? [], 'Status Event'); @endphp
                        <img src="{{ $c3 }}" alt="Status Chart" class="chart-pie" />
                    </div>
                </td>
                <td>
                    <div class="chart-card">
                        <div class="chart-card-title">&#9679; Jenis Event</div>
                        @php $c4 = \App\Helpers\ChartHelper::donutChart($eventsByType ?? [], 'Jenis Event'); @endphp
                        <img src="{{ $c4 }}" alt="Type Chart" class="chart-pie" />
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ============================================================ -->
    <!-- TABLES (page break) -->
    <!-- ============================================================ -->
    <div class="page-break"></div>

    <!-- TOP CLIENTS -->
    <div class="table-wrapper">
        <div class="section-title">&#9733; TOP 10 CLIENT &mdash; Berdasarkan Nilai Event</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">No</th>
                    <th style="width: 38%;">Nama Client</th>
                    <th style="width: 22%; text-align: center;">Jumlah Event</th>
                    <th style="width: 35%; text-align: right;">Total Nilai Event</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topClients as $idx => $client)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="fw-bold">{{ $client->name }}</td>
                    <td class="text-center">{{ $client->events_count }}</td>
                    <td class="text-right fw-bold">Rp {{ number_format($client->total_invoice_value ?? 0, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="no-data">Tidak ada data client</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- TOP VENDORS -->
    <div class="table-wrapper">
        <div class="section-title">&#9733; TOP 10 VENDOR &mdash; Berdasarkan Nilai RAB</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">No</th>
                    <th style="width: 38%;">Nama Vendor</th>
                    <th style="width: 22%; text-align: center;">Total Proyek</th>
                    <th style="width: 35%; text-align: right;">Total Nilai RAB</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topVendors as $idx => $vendor)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="fw-bold">{{ $vendor->nama_vendor }}</td>
                    <td class="text-center">{{ $vendor->rabs_count }}</td>
                    <td class="text-right fw-bold">Rp {{ number_format($vendor->rabs_sum_subtotal_biaya ?? 0, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="no-data">Tidak ada data vendor</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- TOP EVENTS -->
    <div class="table-wrapper">
        <div class="section-title">&#9733; TOP 10 EVENT &mdash; Berdasarkan Nilai Event</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">No</th>
                    <th style="width: 28%;">Nama Event</th>
                    <th style="width: 22%;">Client</th>
                    <th style="width: 15%;">Status</th>
                    <th style="width: 30%; text-align: right;">Nilai Event</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topEvents as $idx => $event)
                @php
                    $status = $event->status_event ?? '';
                    $badgeClass = match($status) {
                        'selesai' => 'badge-selesai',
                        'berjalan' => 'badge-berjalan',
                        'menunggu' => 'badge-menunggu',
                        'diproses' => 'badge-diproses',
                        'dibatalkan' => 'badge-dibatalkan',
                        default => 'badge-pending'
                    };
                    $statusLabel = match($status) {
                        'selesai' => 'Selesai',
                        'berjalan' => 'Berjalan',
                        'menunggu' => 'Menunggu',
                        'diproses' => 'Diproses',
                        'dibatalkan' => 'Dibatalkan',
                        default => ucfirst($status)
                    };
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="fw-bold">{{ $event->nama_event }}</td>
                    <td>{{ $event->client->name ?? '-' }}</td>
                    <td><span class="badge {{ $badgeClass }}">{{ $event->status_label ?? $statusLabel }}</span></td>
                    <td class="text-right fw-bold">Rp {{ number_format($event->total_invoice_value, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="no-data">Tidak ada data event</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- ============================================================ -->
    <!-- FOOTER -->
    <!-- ============================================================ -->
    <script type="text/php">
        if (isset($pdf)) {
            $canvas = $pdf->get_canvas();
            $font = $canvas->get_font('DejaVu Sans', 'normal');
            $gray = array(148, 163, 184);
            $lineColor = array(226, 232, 240);

            $canvas->line(30, 570, 822, 570, $lineColor, 0.5);
            $canvas->page_text(30, 575, "Generated by ALPHA.CORP Event Management System", $font, 7, $gray);
            $canvas->page_text(370, 575, "EVENT ANALYTICS REPORT", $font, 7, $gray);
            $canvas->page_text(790, 575, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 7, $gray);
        }
    </script>

</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>EVENT ANALYTICS REPORT</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 20px 36px;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', 'Helvetica', sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #1e293b;
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
            margin-bottom: 6px;
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
            width: 18%;
            text-align: left;
        }
        .header-center {
            width: 50%;
            text-align: center;
        }
        .header-right {
            width: 32%;
            text-align: right;
        }

        /* Logo area */
        .logo-wrapper {
            display: inline-block;
            vertical-align: middle;
            margin: 0;
            padding: 0;
        }
        .header-logo-img {
            width: 80px;
            height: auto;
            object-fit: contain;
            display: block;
            margin: 0;
        }
        .header-logo-fallback {
            width: 80px;
            height: 80px;
            background: #0f172a;
            border-radius: 16px;
            text-align: center;
            line-height: 80px;
            color: #14b8a6;
            font-size: 30pt;
            font-weight: 700;
        }
        
        
        

        /* Title */
        .report-title-main {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .report-subtitle {
            font-size: 8px;
            font-weight: 500;
            color: #64748b;
            margin-top: 1px;
            letter-spacing: 0.5px;
        }

        /* Right info card */
        .info-card {
            display: inline-block;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 3px 10px 3px 8px;
            text-align: left;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }
        .info-card table {
            width: auto;
            border-collapse: collapse;
        }
        .info-card td {
            border: none;
            padding: 0;
            vertical-align: middle;
            line-height: 1.2;
        }
        .info-card .icon-cell {
            width: 20px;
            text-align: center;
            padding-right: 6px;
        }
        .info-icon {
            display: inline-block;
            width: 14px;
            height: 14px;
            border-radius: 4px;
            text-align: center;
            line-height: 14px;
            font-size: 6pt;
            color: #fff;
        }
        .info-label {
            font-size: 5pt;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
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

        /* Header divider line */
        .header-divider {
            border: none;
            border-top: 1px solid #14b8a6;
            margin-top: 0;
            margin-bottom: 0;
        }
        .header-divider-shadow {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin-top: 0;
            margin-bottom: 0;
        }

        /* ===== STAT CARDS ===== */
        .stats-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 4px;
            margin-bottom: 8px;
        }
        .stats-table td {
            padding: 0;
            border: none;
            vertical-align: top;
            width: 12.5%;
        }
        .stat-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 0;
            height: 68px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        }
        .stat-card table.inner {
            width: 100%;
            height: 68px;
            border-collapse: collapse;
        }
        .stat-card table.inner td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .stat-card table.inner td.icon-cell {
            width: 48px;
            text-align: center;
            vertical-align: middle;
        }
        
        .stat-card table.inner td.text-cell {
            padding-left: 0;
            vertical-align: middle;
        }
        .stat-label {
            font-size: 5.5pt;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 1px;
        }
        .stat-value {
            font-size: 10pt;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }
        .stat-value-highlight {
            font-size: 11pt;
            font-weight: 800;
            color: #0d9488;
            line-height: 1.2;
        }/* ===== SECTION TITLE ===== */
        .section-title {
            font-size: 10px;
            font-weight: 700;
            color: #0f172a;
            padding: 5px 12px;
            margin-bottom: 8px;border-left: 4px solid #14b8a6;
            background: #f1f5f9;
            border-radius: 4px;
        }

        /* ===== CHARTS 2x2 GRID ===== */
        .charts-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 4px;
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
            border-radius: 10px;
            padding: 6px 6px 4px 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        }
        .chart-card-title {
            font-size: 10px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 3px;
            padding-left: 2px;
        }
        .chart-card img {
            display: block;
        }
        .chart-card img.chart-line-bar {
            width: 100%;
            height: auto;
        }
        .chart-card img.chart-pie-donut {
            width: 60%;
            height: auto;
            margin: 0 auto;
        }

        /* ===== TABLES ===== */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 10px;
            border-radius: 10px;
            overflow: hidden;
        }
        .data-table thead th {
            background: #0f172a;
            color: #ffffff;
            padding: 7px 12px;
            text-align: left;
            font-weight: 700;
            font-size: 9px;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }
        .data-table tbody td {
            padding: 5px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .data-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .data-table tbody tr:nth-child(odd) {
            background: #ffffff;
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

        /* ===== TABLE BADGES ===== */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: 700;
            letter-spacing: 0.3px;
            min-width: 50px;
            text-align: center;
        }
        .badge-selesai   { background: #d1fae5; color: #065f46; }
        .badge-berjalan  { background: #dbeafe; color: #1e40af; }
        .badge-menunggu  { background: #fef3c7; color: #92400e; }
        .badge-diproses  { background: #e0e7ff; color: #3730a3; }
        .badge-dibatalkan{ background: #fee2e2; color: #991b1b; }
        .badge-pending   { background: #f1f5f9; color: #475569; }

        /* ===== PAGE BREAK ===== */
        .page-break {
            page-break-before: always;
        }

        /* ===== TABLES CONTAINER ===== */
        .tables-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
        }
        .tables-grid td {
            padding: 0;
            border: none;
            vertical-align: top;
            width: 33.33%;
        }
    </style>
</head>
<body>

    <!-- ============================================================ -->
    <!-- HEADER: 3 Bagian Enterprise -->
    <!-- ============================================================ -->
    <div class="pdf-header">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <!-- KIRI: Logo resmi -->
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

                <!-- TENGAH: Title -->
                <td class="header-center">
                    <div class="report-title-main">EVENT ANALYTICS REPORT</div>
                    <div class="report-subtitle">Ringkasan Performa Bisnis &amp; Operasional</div>
                </td>

                <!-- KANAN: Info Card -->
                <td class="header-right">
                    <div class="info-card">
                        <table cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="icon-cell">
                                    <div class="info-icon" style="background: #14b8a6;">&#9733;</div>
                                </td>
                                <td>
                                    <div class="info-label">Periode Laporan</div>
                                    <div class="info-value">Tahun {{ $filters['year'] ?? now()->year }}</div>
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
    <!-- 8 CARD STATISTIK -->
    <!-- ============================================================ -->
    @php
        $stats = [
            ['label' => 'Total Event',    'value' => number_format($totalEvents, 0, ',', '.'),    'icon' => 'event-total'],
            ['label' => 'Event Berjalan', 'value' => number_format($eventsBerjalan, 0, ',', '.'),'icon' => 'event-berjalan'],
            ['label' => 'Event Selesai',  'value' => number_format($eventsSelesai, 0, ',', '.'), 'icon' => 'event-selesai'],
            ['label' => 'Total Client',   'value' => number_format($totalClients, 0, ',', '.'),  'icon' => 'total-client'],
            ['label' => 'Total Vendor',   'value' => number_format($totalVendors, 0, ',', '.'),  'icon' => 'total-vendor'],
            ['label' => 'Total Invoice',  'value' => number_format($totalInvoices, 0, ',', '.'), 'icon' => 'total-invoice'],
            ['label' => 'Total Pendapatan','value' => 'Rp '.number_format($totalRevenue, 0, ',', '.'), 'icon' => 'total-pendapatan'],
            ['label' => 'Pembayaran Lunas','value' => number_format($paidInvoices, 0, ',', '.'), 'icon' => 'pembayaran-lunas'],
        ];
    @endphp
    <table class="stats-table" cellpadding="0" cellspacing="4">
        <tr>
            @foreach($stats as $idx => $s)
            <td>
                <div class="stat-card">
                    <table class="inner" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="icon-cell">
                                <img src="{{ public_path('images/icons/'.$s['icon'].'.svg') }}" width="38" height="38" alt="" />
                            </td>
                            <td class="text-cell">
                                <div class="stat-label">{{ $s['label'] }}</div>
                                @if ($idx === 6)
                                    <div class="stat-value-highlight">{{ $s['value'] }}</div>
                                @else
                                    <div class="stat-value">{{ $s['value'] }}</div>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            @endforeach
        </tr>
    </table>

    <!-- ============================================================ -->
    <!-- GRAFIK 2x2 - Semua muat dalam 1 halaman -->
    <!-- ============================================================ -->
    <div class="chart-section" style="margin-bottom: 0;">
        <div style="margin-bottom: 10px;">
            <div class="section-title">&#9670; Analitik Grafik {{ $filters['year'] ?? now()->year }}</div>
        </div>

        <table class="charts-table" cellpadding="0" cellspacing="10">
            <tr>
                <td>
                    <div class="chart-card">
                        <div class="chart-card-title">&#9679; Pendapatan per Bulan</div>
                        @php $c1 = \App\Helpers\ChartHelper::lineChart($monthlyRevenue ?? [], 'Pendapatan per Bulan'); @endphp
                        <img src="{{ $c1 }}" alt="Revenue Chart" class="chart-full-bar" style="width:100%; height:auto;" />
                    </div>
                </td>
                <td>
                    <div class="chart-card">
                        <div class="chart-card-title">&#9679; Event per Bulan</div>
                        @php $c2 = \App\Helpers\ChartHelper::barChart($monthlyEvents ?? [], 'Event per Bulan'); @endphp
                        <img src="{{ $c2 }}" alt="Events Chart" class="chart-full-bar" style="width:100%; height:auto;" />
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="chart-card">
                        <div class="chart-card-title">&#9679; Status Event</div>
                        @php $c3 = \App\Helpers\ChartHelper::pieChart($eventsByStatus ?? [], 'Status Event'); @endphp
                        <img src="{{ $c3 }}" alt="Status Chart" class="chart-pie-donut" style="width:55%; height:auto; display:block; margin:0 auto;" />
                    </div>
                </td>
                <td>
                    <div class="chart-card">
                        <div class="chart-card-title">&#9679; Jenis Event</div>
                        @php $c4 = \App\Helpers\ChartHelper::donutChart($eventsByType ?? [], 'Jenis Event'); @endphp
                        <img src="{{ $c4 }}" alt="Type Chart" class="chart-pie-donut" style="width:55%; height:auto; display:block; margin:0 auto;" />
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ============================================================ -->
    <!-- PAGE BREAK menuju halaman tabel -->
    <!-- ============================================================ -->
    <!-- ============================================================ -->
    <!-- 3 TABEL: Top Client, Top Vendor, Top Event -->
    <!-- ============================================================ -->

    <div class="page-break"></div>
    <!-- TOP CLIENTS -->
    <div style="margin-bottom: 16px;">
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
    <div style="margin-bottom: 16px;">
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
    <div style="margin-bottom: 16px;">
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

            // Garis footer
            $canvas->line(30, 570, 822, 570, $lineColor, 0.5);

            // Kiri
            $canvas->page_text(30, 575, "Generated by ALPHA.CORP Event Management System", $font, 7, $gray);

            // Tengah
            $canvas->page_text(370, 575, "EVENT ANALYTICS REPORT", $font, 7, $gray);

            // Kanan
            $canvas->page_text(790, 575, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 7, $gray);
        }
    </script>

</body>
</html>




















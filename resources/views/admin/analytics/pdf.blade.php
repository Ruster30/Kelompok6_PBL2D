<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>EVENT ANALYTICS REPORT</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 14mm 20mm 14mm;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8.5pt;
            line-height: 1.5;
            color: #1e293b;
        }

        /* ===== HEADER ===== */
        .pdf-header {
            width: 100%;
            padding-bottom: 0;
            margin-bottom: 8px;
        }
        .pdf-header table { width: 100%; border-collapse: collapse; }
        .pdf-header td { vertical-align: middle; padding: 0; border: none; }

        .header-left { width: 28%; text-align: left; }
        .header-center { width: 42%; text-align: center; }
        .header-right { width: 30%; text-align: right; }

        .header-logo-img { width: 42px; height: 42px; }
        .header-logo-fallback {
            width: 42px; height: 42px; background: #0f172a;
            border-radius: 10px; text-align: center; line-height: 42px;
            color: #14b8a6; font-size: 20pt; font-weight: bold;
        }

        .company-block { display: inline-block; vertical-align: middle; }
        .company-name { font-size: 16pt; font-weight: bold; color: #0f172a; }
        .company-tagline { font-size: 6pt; color: #94a3b8; letter-spacing: 2px; text-transform: uppercase; margin-top: 1px; }

        .report-title-main { font-size: 22pt; font-weight: bold; color: #0f172a; letter-spacing: 1.5px; }
        .report-subtitle { font-size: 8pt; color: #64748b; margin-top: 2px; }

        .info-card {
            display: inline-block;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 14px;
            text-align: left;
        }
        .info-card table { width: auto; border-collapse: collapse; }
        .info-card td { border: none; padding: 2px 0; vertical-align: middle; }
        .info-card .icon-cell { width: 22px; text-align: center; padding-right: 6px; }
        .info-icon {
            display: inline-block;
            width: 20px; height: 20px;
            border-radius: 6px;
            text-align: center; line-height: 20px;
            font-size: 9pt; color: #fff;
        }
        .info-label { font-size: 6pt; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px; }
        .info-value { font-size: 9pt; font-weight: bold; color: #0f172a; }

        .header-divider-v {
            width: 1px; height: 50px;
            background: #e2e8f0;
            margin: 0 10px;
        }

        .header-divider-hr {
            border: none;
            border-top: 3px solid #14b8a6;
            margin-top: 8px;
            margin-bottom: 0;
        }

        /* ===== STAT CARDS ===== */
        .stats-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
            margin-bottom: 10px;
        }
        .stats-table td {
            padding: 0; border: none; vertical-align: top;
        }
        .stat-card-inner {
            border-radius: 14px;
            padding: 16px 14px;
            height: 90px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            border: 1px solid rgba(0,0,0,0.04);
        }
        .stat-card-inner table { width: 100%; border-collapse: collapse; }
        .stat-card-inner td { border: none; padding: 0; vertical-align: top; }
        .stat-icon-cell { width: 40px; }
        .stat-icon-box {
            width: 36px; height: 36px;
            border-radius: 10px;
            text-align: center; line-height: 36px;
            font-size: 14pt; color: #fff; font-weight: bold;
        }
        .stat-text-cell { padding-left: 10px !important; }
        .stat-label { font-size: 6pt; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 2px; }
        .stat-value { font-size: 14pt; font-weight: bold; color: #0f172a; line-height: 1.2; }

        /* ===== SECTION ===== */
        .section-title {
            font-size: 10pt;
            font-weight: bold;
            color: #0f172a;
            padding: 7px 12px;
            margin-bottom: 8px;
            border-left: 4px solid #14b8a6;
            background: #f1f5f9;
            border-radius: 4px;
        }

        /* ===== CHARTS 2x2 ===== */
        .charts-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
            margin-bottom: 6px;
        }
        .charts-table td {
            padding: 0; border: none; vertical-align: top;
            width: 50%;
        }
        .chart-card {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px;
            background: #fff;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        }
        .chart-card-title {
            font-size: 9pt;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 6px;
        }
        .chart-card img {
            width: 100%;
            height: auto;
        }

        /* ===== TABLES ===== */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 7.5pt;
            border-radius: 8px;
            overflow: hidden;
        }
        .data-table thead th {
            background: #0f172a;
            color: #ffffff;
            padding: 8px 10px;
            text-align: left;
            font-weight: bold;
            font-size: 6.5pt;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }
        .data-table tbody td {
            padding: 6px 10px;
            border-bottom: 1px solid #f1f5f9;
        }
        .data-table tbody tr:nth-child(even) { background: #f8fafc; }
        .data-table tbody tr:nth-child(odd) { background: #ffffff; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .no-data { text-align: center; color: #94a3b8; padding: 20px !important; font-style: italic; }

        /* ===== BADGES ===== */
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 6.5pt;
            font-weight: bold;
            letter-spacing: 0.3px;
            min-width: 55px;
            text-align: center;
        }
        .badge-selesai { background: #d1fae5; color: #065f46; }
        .badge-berjalan { background: #dbeafe; color: #1e40af; }
        .badge-menunggu { background: #fef3c7; color: #92400e; }
        .badge-diproses { background: #e0e7ff; color: #3730a3; }
        .badge-dibatalkan { background: #fee2e2; color: #991b1b; }
        .badge-pending { background: #f1f5f9; color: #475569; }
        .badge-lunas { background: #d1fae5; color: #065f46; }
        .badge-dp { background: #fef3c7; color: #92400e; }
        .badge-belum { background: #fee2e2; color: #991b1b; }

        /* ===== PAGE BREAK ===== */
        .page-break { page-break-before: always; }

        /* ===== MISC ===== */
        .fw-bold { font-weight: bold; }
        .fw-normal { font-weight: normal; }
        .text-muted { color: #64748b; }
        .text-teal { color: #14b8a6; }
        .mt-1 { margin-top: 4px; }
        .mb-1 { margin-bottom: 4px; }
    </style>
</head>
<body>

    <!-- ============================================================ -->
    <!-- HEADER -->
    <!-- ============================================================ -->
    <div class="pdf-header">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <!-- LEFT: Logo + Company -->
                <td class="header-left">
                    <table cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="width: 48px; border: none; padding: 0; vertical-align: middle;">
                                @if(!empty($companyLogo) && file_exists($companyLogo))
                                    <img src="{{ $companyLogo }}" alt="Logo" class="header-logo-img" />
                                @else
                                    <div class="header-logo-fallback">A</div>
                                @endif
                            </td>
                            <td style="border: none; padding: 0 0 0 8px; vertical-align: middle;">
                                <div class="company-name">{{ $companyName !== 'Your Company Name' ? $companyName : 'ALPHA.CORP' }}</div>
                                <div class="company-tagline">Event Organizer</div>
                            </td>
                        </tr>
                    </table>
                </td>

                <!-- CENTER: Title -->
                <td class="header-center">
                    <div class="report-title-main">EVENT ANALYTICS REPORT</div>
                    <div class="report-subtitle">Ringkasan Performa Bisnis &amp; Operasional</div>
                </td>

                <!-- RIGHT: Info Card -->
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
                                <td colspan="2" style="padding: 4px 0 !important;"><hr style="border: none; border-top: 1px solid #e2e8f0; margin: 0;" /></td>
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
        <hr class="header-divider-hr" />
    </div>

    <!-- ============================================================ -->
    <!-- STATISTICS CARDS (8 cards, single row) -->
    <!-- ============================================================ -->
    @php
        $stats = [
            ['label' => 'Total Event',    'value' => number_format($totalEvents, 0, ',', '.'),
             'icon' => '#', 'bg' => 'linear-gradient(135deg, #f0fdfa, #ccfbf1)', 'icobg' => '#14b8a6'],
            ['label' => 'Event Berjalan', 'value' => number_format($eventsBerjalan, 0, ',', '.'),
             'icon' => '►', 'bg' => 'linear-gradient(135deg, #fefce8, #fef3c7)', 'icobg' => '#f59e0b'],
            ['label' => 'Event Selesai',  'value' => number_format($eventsSelesai, 0, ',', '.'),
             'icon' => '✓', 'bg' => 'linear-gradient(135deg, #f0fdfa, #d1fae5)', 'icobg' => '#10b981'],
            ['label' => 'Total Client',   'value' => number_format($totalClients, 0, ',', '.'),
             'icon' => '♦', 'bg' => 'linear-gradient(135deg, #f0f5ff, #e0e7ff)', 'icobg' => '#6366f1'],
            ['label' => 'Total Vendor',   'value' => number_format($totalVendors, 0, ',', '.'),
             'icon' => '★', 'bg' => 'linear-gradient(135deg, #faf5ff, #f3e8ff)', 'icobg' => '#8b5cf6'],
            ['label' => 'Total Invoice',  'value' => number_format($totalInvoices, 0, ',', '.'),
             'icon' => '□', 'bg' => 'linear-gradient(135deg, #ecfeff, #cffafe)', 'icobg' => '#06b6d4'],
            ['label' => 'Total Pendapatan','value' => 'Rp' . number_format($totalRevenue, 0, ',', '.'),
             'icon' => 'Rp', 'bg' => 'linear-gradient(135deg, #f0fdfa, #ccfbf1)', 'icobg' => '#0d9488'],
            ['label' => 'Pembayaran Lunas','value' => number_format($paidInvoices, 0, ',', '.'),
             'icon' => '✔', 'bg' => 'linear-gradient(135deg, #fefce8, #fef3c7)', 'icobg' => '#d97706'],
        ];
    @endphp
    <table class="stats-table" cellpadding="0" cellspacing="6">
        <tr>
            @foreach($stats as $s)
            <td>
                <div class="stat-card-inner" style="background: {{ $s['bg'] }};">
                    <table cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="stat-icon-cell">
                                <div class="stat-icon-box" style="background: {{ $s['icobg'] }};">{{ $s['icon'] }}</div>
                            </td>
                            <td class="stat-text-cell">
                                <div class="stat-label">{{ $s['label'] }}</div>
                                <div class="stat-value">{{ $s['value'] }}</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            @endforeach
        </tr>
    </table>

    <!-- ============================================================ -->
    <!-- CHARTS (2x2 grid) -->
    <!-- ============================================================ -->
    <div style="margin-bottom: 4px;">
        <div class="section-title">&#9670; Analitik Grafik {{ $filters['year'] ?? now()->year }}</div>
    </div>

    <table class="charts-table" cellpadding="0" cellspacing="6">
        <tr>
            <td>
                <div class="chart-card">
                    <div class="chart-card-title">&#9679; Pendapatan per Bulan</div>
                    @php $c1 = \App\Helpers\ChartHelper::lineChart($monthlyRevenue ?? [], 'Pendapatan per Bulan'); @endphp
                    <img src="{{ $c1 }}" alt="Revenue Chart" />
                </div>
            </td>
            <td>
                <div class="chart-card">
                    <div class="chart-card-title">&#9679; Event per Bulan</div>
                    @php $c2 = \App\Helpers\ChartHelper::barChart($monthlyEvents ?? [], 'Event per Bulan'); @endphp
                    <img src="{{ $c2 }}" alt="Events Chart" />
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="chart-card">
                    <div class="chart-card-title">&#9679; Status Event</div>
                    @php $c3 = \App\Helpers\ChartHelper::pieChart($eventsByStatus ?? [], 'Status Event'); @endphp
                    <img src="{{ $c3 }}" alt="Status Chart" />
                </div>
            </td>
            <td>
                <div class="chart-card">
                    <div class="chart-card-title">&#9679; Jenis Event</div>
                    @php $c4 = \App\Helpers\ChartHelper::donutChart($eventsByType ?? [], 'Jenis Event'); @endphp
                    <img src="{{ $c4 }}" alt="Type Chart" />
                </div>
            </td>
        </tr>
    </table>

    <!-- ============================================================ -->
    <!-- PAGE BREAK - TABLES PAGE -->
    <!-- ============================================================ -->
    <div class="page-break"></div>

    <!-- ============================================================ -->
    <!-- TOP CLIENTS -->
    <!-- ============================================================ -->
    <div style="margin-bottom: 10px;">
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

    <!-- ============================================================ -->
    <!-- TOP VENDORS -->
    <!-- ============================================================ -->
    <div style="margin-bottom: 10px;">
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

    <!-- ============================================================ -->
    <!-- TOP EVENTS -->
    <!-- ============================================================ -->
    <div style="margin-bottom: 6px;">
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

            // Footer line
            $canvas->line(30, 568, 810, 568, $lineColor, 0.5);

            // Left
            $canvas->page_text(30, 572, "Generated by ALPHA.CORP Event Management System", $font, 6.5, $gray);

            // Center - report name
            $canvas->page_text(360, 572, "EVENT ANALYTICS REPORT", $font, 6.5, $gray);

            // Right - page number
            $canvas->page_text(755, 572, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 6.5, $gray);
        }
    </script>

</body>
</html>
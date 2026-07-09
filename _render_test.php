<?php
require_once "D:/Kelompok6_PBL2D/vendor/autoload.php";

use Dompdf\Dompdf;
use Dompdf\Options;

$totalEvents = 25;
$eventsBerjalan = 4;
$eventsSelesai = 18;
$totalClients = 30;
$totalVendors = 15;
$totalInvoices = 87;
$totalRevenue = 2450000000;
$paidInvoices = 65;

function statIcon($name) {
    $svgPath = "D:/Kelompok6_PBL2D/public/images/icons/$name.svg";
    if (file_exists($svgPath)) {
        return file_get_contents($svgPath);
    }
    return "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"38\" height=\"38\"><rect width=\"38\" height=\"38\" rx=\"10\" fill=\"#ccc\"/></svg>";
}

$stats = [
    ["label" => "Total Event",    "value" => number_format($totalEvents),    "icon" => "event-total"],
    ["label" => "Event Berjalan", "value" => number_format($eventsBerjalan), "icon" => "event-berjalan"],
    ["label" => "Event Selesai",  "value" => number_format($eventsSelesai),  "icon" => "event-selesai"],
    ["label" => "Total Client",   "value" => number_format($totalClients),   "icon" => "total-client"],
    ["label" => "Total Vendor",   "value" => number_format($totalVendors),   "icon" => "total-vendor"],
    ["label" => "Total Invoice",  "value" => number_format($totalInvoices),  "icon" => "total-invoice"],
    ["label" => "Total Pendapatan","value" => "Rp ".number_format($totalRevenue), "icon" => "total-pendapatan"],
    ["label" => "Pembayaran Lunas","value" => number_format($paidInvoices),  "icon" => "pembayaran-lunas"],
];

$html = <<<HTML
<!DOCTYPE html>
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>EVENT ANALYTICS REPORT</title>
<style>
@page { size: A4 landscape; margin: 20px 36px; }
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: "DejaVu Sans", sans-serif; font-size: 11pt; line-height: 1.6; color: #1e293b; }
.fw-bold { font-weight: 700; }
.text-right { text-align: right; }
.text-center { text-align: center; }
.pdf-header { width: 100%; margin-bottom: 6px; }
.pdf-header table { width: 100%; border-collapse: collapse; }
.pdf-header td { vertical-align: middle; padding: 0; border: none; }
.header-left { width: 18%; text-align: left; }
.header-center { width: 50%; text-align: center; }
.header-right { width: 32%; text-align: right; }
.header-logo-fallback { width: 80px; height: 80px; background: #0f172a; border-radius: 16px; text-align: center; line-height: 80px; color: #14b8a6; font-size: 30pt; font-weight: 700; display:inline-block; }
.report-title-main { font-size: 18px; font-weight: 700; color: #0f172a; letter-spacing: 2px; text-transform: uppercase; }
.report-subtitle { font-size: 8px; font-weight: 500; color: #64748b; margin-top: 1px; }
.info-card { display: inline-block; background: #fff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 3px 10px; text-align: left; }
.info-card table { width: auto; border-collapse: collapse; }
.info-card td { border: none; padding: 0; vertical-align: middle; line-height: 1.2; }
.info-card .icon-cell { width: 20px; text-align: center; padding-right: 6px; }
.info-icon { display: inline-block; width: 14px; height: 14px; border-radius: 4px; text-align: center; line-height: 14px; font-size: 6pt; color: #fff; }
.info-label { font-size: 5pt; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }
.info-value { font-size: 6.5pt; font-weight: 700; color: #0f172a; }
.header-divider { border: none; border-top: 1px solid #14b8a6; margin-top: 0; margin-bottom: 0; }
.header-divider-shadow { border: none; border-top: 1px solid #e2e8f0; margin-top: 0; margin-bottom: 10px; }

.stats-table { width: 100%; border-collapse: separate; border-spacing: 4px; margin-bottom: 8px; }
.stats-table td { padding: 0; border: none; vertical-align: top; width: 12.5%; }
.stat-card { background: #fff; border-radius: 12px; padding: 0; height: 68px; border: 1px solid #e2e8f0; }
.stat-card table.inner { width: 100%; height: 68px; border-collapse: collapse; }
.stat-card table.inner td { border: none; padding: 0; vertical-align: middle; }
.stat-card table.inner td.icon-cell { width: 48px; text-align: center; vertical-align: middle; }
.stat-card table.inner td.text-cell { padding-left: 0; vertical-align: middle; }
.stat-label { font-size: 5.5pt; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 1px; }
.stat-value { font-size: 10pt; font-weight: 700; color: #0f172a; line-height: 1.2; }
.stat-value-highlight { font-size: 11pt; font-weight: 800; color: #0d9488; line-height: 1.2; }

.section-title { font-size: 10px; font-weight: 700; padding: 5px 12px; margin-bottom: 8px; border-left: 4px solid #14b8a6; background: #f1f5f9; border-radius: 4px; }

.charts-table { width: 100%; border-collapse: separate; border-spacing: 6px; margin-bottom: 0; }
.charts-table td { padding: 0; border: none; vertical-align: top; width: 50%; }
.chart-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 6px; }
.chart-card-title { font-size: 10px; font-weight: 700; margin-bottom: 4px; padding-left: 2px; }
.chart-ph { height: 95px; background: #f1f5f9; border-radius: 6px; text-align: center; line-height: 95px; color: #94a3b8; font-size: 9px; }

.page-break { page-break-before: always; }

.data-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; font-size: 10px; }
.data-table thead th { background: #0f172a; color: #fff; padding: 7px 12px; text-align: left; font-weight: 700; font-size: 9px; text-transform: uppercase; }
.data-table tbody td { padding: 5px 12px; border-bottom: 1px solid #f1f5f9; }
.data-table tbody tr:nth-child(even) { background: #f8fafc; }
.data-table tbody tr:nth-child(odd) { background: #fff; }

.badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 8px; font-weight: 700; text-align: center; }
.badge-selesai { background: #d1fae5; color: #065f46; }
.badge-berjalan { background: #dbeafe; color: #1e40af; }
</style></head><body>
HTML;

$html .= '<div class="pdf-header"><table cellpadding="0" cellspacing="0"><tr>';
$html .= '<td class="header-left"><div class="header-logo-fallback">A</div></td>';
$html .= '<td class="header-center"><div class="report-title-main">EVENT ANALYTICS REPORT</div><div class="report-subtitle">Ringkasan Performa Bisnis &amp; Operasional</div></td>';
$html .= '<td class="header-right"><div class="info-card"><table cellpadding="0" cellspacing="0">';
$html .= '<tr><td class="icon-cell"><div class="info-icon" style="background:#14b8a6;">★</div></td><td><div class="info-label">Periode Laporan</div><div class="info-value">Tahun 2026</div></td></tr>';
$html .= '<tr><td colspan="2"><hr class="info-divider" /></td></tr>';
$html .= '<tr><td class="icon-cell"><div class="info-icon" style="background:#0f172a;">●</div></td><td><div class="info-label">Tanggal Cetak</div><div class="info-value">09 Jul 2026</div></td></tr>';
$html .= '</table></div></td></tr></table>';
$html .= '<hr class="header-divider"><hr class="header-divider-shadow"></div>';

$html .= '<div style="margin-bottom:6px;"><div class="section-title">◆ Ringkasan Data</div></div>';
$html .= '<table class="stats-table" cellpadding="0" cellspacing="4"><tr>';

foreach ($stats as $idx => $s) {
    $svgIcon = statIcon($s["icon"]);
    $valClass = ($idx === 6) ? 'stat-value-highlight' : 'stat-value';
    $html .= '<td><div class="stat-card"><table class="inner" cellpadding="0" cellspacing="0"><tr>';
    $html .= '<td class="icon-cell">' . $svgIcon . '</td>';
    $html .= '<td class="text-cell"><div class="stat-label">' . $s["label"] . '</div>';
    $html .= '<div class="' . $valClass . '">' . $s["value"] . '</div></td>';
    $html .= '</tr></table></div></td>';
}

$html .= '</tr></table>';
$html .= '<div style="margin-bottom:4px;"><div class="section-title">◆ Analitik Grafik</div></div>';
$html .= '<table class="charts-table" cellpadding="0" cellspacing="6"><tr>';
$html .= '<td><div class="chart-card"><div class="chart-card-title">● Pendapatan per Bulan</div><div class="chart-ph">Line Chart</div></div></td>';
$html .= '<td><div class="chart-card"><div class="chart-card-title">● Event per Bulan</div><div class="chart-ph">Bar Chart</div></div></td>';
$html .= '</tr><tr>';
$html .= '<td><div class="chart-card"><div class="chart-card-title">● Status Event</div><div class="chart-ph">Pie Chart</div></div></td>';
$html .= '<td><div class="chart-card"><div class="chart-card-title">● Jenis Event</div><div class="chart-ph">Donut Chart</div></div></td>';
$html .= '</tr></table>';

$html .= '<div class="page-break"></div>';

$html .= '<div style="margin-bottom:14px;"><div class="section-title">★ TOP 10 CLIENT</div></div>';
$html .= '<table class="data-table"><thead><tr><th>No</th><th>Nama Client</th><th>Jumlah Event</th><th>Total Nilai</th></tr></thead>';
$html .= '<tbody><tr><td>1</td><td class="fw-bold">Client A</td><td class="text-center">5</td><td class="text-right fw-bold">Rp 500.000.000</td></tr></tbody></table>';

$html .= '<div style="margin-bottom:14px;"><div class="section-title">★ TOP 10 VENDOR</div></div>';
$html .= '<table class="data-table"><thead><tr><th>No</th><th>Nama Vendor</th><th>Total Proyek</th><th>Total Nilai RAB</th></tr></thead>';
$html .= '<tbody><tr><td>1</td><td class="fw-bold">Vendor X</td><td class="text-center">3</td><td class="text-right fw-bold">Rp 200.000.000</td></tr></tbody></table>';

$html .= '<div style="margin-bottom:14px;"><div class="section-title">★ TOP 10 EVENT</div></div>';
$html .= '<table class="data-table"><thead><tr><th>No</th><th>Nama Event</th><th>Client</th><th>Status</th><th>Nilai Event</th></tr></thead>';
$html .= '<tbody><tr><td class="text-center">1</td><td class="fw-bold">Event Alpha</td><td>Client A</td><td><span class="badge badge-selesai">Selesai</span></td><td class="text-right fw-bold">Rp 350.000.000</td></tr></tbody></table>';

$html .= '</body></html>';

$options = new Options();
$options->setIsRemoteEnabled(true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "landscape");
$dompdf->render();
file_put_contents("D:/Kelompok6_PBL2D/test-output.pdf", $dompdf->output());
echo "PDF generated.\n";

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi</title>
    <style>
        @page { margin: 28px; }
        body { font-family: DejaVu Sans, sans-serif; color: #444; font-size: 12px; line-height: 1.5; }
        * { box-sizing: border-box; }
        .clearfix::after { content: ""; display: block; clear: both; }
        h1, h2, h3, h4, h5 { margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        .header { border-bottom: 3px solid #1E88E5; padding-bottom: 18px; margin-bottom: 28px; }
        .company { float: left; width: 55%; }
        .company img { height: 70px; margin-bottom: 8px; }
        .company-name { font-size: 22px; font-weight: bold; color: #163A70; }
        .company-info { color: #666; font-size: 11px; margin-top: 4px; }
        .receipt-box { float: right; width: 38%; text-align: right; }
        .receipt-title { font-size: 34px; font-weight: bold; color: #163A70; letter-spacing: 2px; margin-bottom: 12px; }
        .receipt-table { width: 100%; font-size: 12px; }
        .receipt-table td { padding: 4px 0; }
        .receipt-table td:first-child { font-weight: bold; width: 45%; color: #555; }
        .status-paid { display: inline-block; padding: 6px 18px; border-radius: 30px; background: #43A047; color: white; font-size: 12px; font-weight: bold; letter-spacing: 1px; }
        .card { border: 1px solid #E3E3E3; border-radius: 6px; padding: 15px; }
        .card-title { font-size: 14px; font-weight: bold; color: #163A70; border-bottom: 2px solid #1E88E5; padding-bottom: 6px; margin-bottom: 12px; }
        .detail-table td { padding: 6px 0; vertical-align: top; }
        .detail-table td:first-child { width: 30%; font-weight: bold; color: #555; }
        .amount-box { margin-top: 20px; text-align: center; padding: 20px; border: 2px dashed #1E88E5; border-radius: 8px; }
        .amount-label { font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 2px; }
        .amount-value { font-size: 28px; font-weight: bold; color: #163A70; margin-top: 6px; }
        .amount-terbilang { font-size: 12px; color: #666; margin-top: 4px; font-style: italic; }
        .footer { margin-top: 40px; text-align: center; color: #999; font-size: 10px; border-top: 1px solid #E3E3E3; padding-top: 12px; }
        .signature-area { margin-top: 30px; }
        .signature-left { float: left; width: 45%; }
        .signature-right { float: right; width: 45%; text-align: right; }
        .signature-space { height: 70px; }
        .signature-label { font-size: 11px; color: #888; }
        .badge-payment-type { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; background: #E3F2FD; color: #1565C0; }
    </style>
</head>
<body>
    <div class="header clearfix">
        <div class="company">
            @if(!empty($companyLogo))
                <img src="{{ $companyLogo }}" alt="Logo">
            @endif
            <div class="company-name">{{ $companyName ?? 'Alpha Organizer' }}</div>
            <div class="company-info">
                {{ $companyAddress ?? '' }}<br>
                Telp: {{ $companyPhone ?? '' }} | Email: {{ $companyEmail ?? '' }}
            </div>
        </div>
        <div class="receipt-box">
            <div class="receipt-title">KWITANSI</div>
            <table class="receipt-table">
                <tr><td>No. Kwitansi</td><td>: {{ $nomorKwitansi }}</td></tr>
                <tr><td>Tanggal</td><td>: {{ \Carbon\Carbon::parse($tanggalKwitansi)->format("d M Y") }}</td></tr>
                <tr><td>Status</td><td>: <span class="status-paid">LUNAS</span></td></tr>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-title">INFORMASI PEMBAYARAN</div>
        <table class="detail-table">
            <tr><td>Nama Event</td><td>: {{ $event->nama_event }}</td></tr>
            <tr><td>Klien</td><td>: {{ $event->client->name ?? '-' }}</td></tr>
            <tr><td>Jenis Pembayaran</td><td>: <span class="badge-payment-type">{{ $jenisPembayaranLabel }}</span></td></tr>
            <tr><td>No. Invoice</td><td>: {{ $invoice->nomor_invoice ?? '-' }}</td></tr>
        </table>
    </div>
    <div class="amount-box">
        <div class="amount-label">Telah Diterima Sejumlah</div>
        <div class="amount-value">Rp {{ number_format($nominal, 0, ',', '.') }}</div>
        <div class="amount-terbilang">{{ $terbilang ?? '' }}</div>
    </div>
    <div class="signature-area clearfix">
        <div class="signature-left">
            <div class="signature-label">Mengetahui,</div>
            <div class="signature-space"></div>
            <div class="signature-label">({{ $companyName ?? 'Alpha Organizer' }})</div>
        </div>
        <div class="signature-right">
            <div class="signature-label">Hormat Kami,</div>
            <div class="signature-space"></div>
            <div class="signature-label">({{ $event->client->name ?? 'Client' }})</div>
        </div>
    </div>
    <div class="footer">
        <strong>{{ $companyName ?? 'Alpha Organizer' }}</strong><br>
        {{ $companyAddress ?? '' }}<br>
        Telp: {{ $companyPhone ?? '' }} | Email: {{ $companyEmail ?? '' }}
    </div>

{{-- Approval Metadata --}}
@include('admin.pdf_templates.partials.verification')

</body>
</html>
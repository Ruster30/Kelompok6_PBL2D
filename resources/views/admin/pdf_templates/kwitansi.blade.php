<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi - {{ $companyName ?? 'Alpha Organizer' }}</title>
    <style>
        @page { margin: 25px 35px; }
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #333;
            font-size: 11px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        * { box-sizing: border-box; }

        /* ─── LAYOUT ─── */
        .main { margin-top: 22px; }

        .headline { text-align: center; margin-bottom: 20px; }
        .headline h1 {
            font-size: 32px;
            font-weight: 900;
            color: #0d9488;
            letter-spacing: 4px;
            margin: 0 0 10px 0;
            line-height: 1;
        }
        .headline-info {
            font-size: 11px;
            line-height: 1.8;
            color: #555;
        }
        .headline-info td { padding: 1px 0; vertical-align: top; }
        .headline-info td.lbl { width: 100px; font-weight: 600; color: #333; }
        .headline-info td.sep { width: 12px; text-align: center; }
        .headline-info td.val { color: #333; }

        /* ─── CARD ─── */
        .card { margin-bottom: 20px; }
        .card-title {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            padding-bottom: 6px;
            margin-bottom: 10px;
            border-bottom: 2px solid #0d9488;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-tbl { width: 100%; border-collapse: collapse; }
        .info-tbl td {
            padding: 5px 0;
            vertical-align: top;
            font-size: 11px;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-tbl tr:last-child td { border-bottom: none; }
        .info-tbl td.lbl { width: 120px; color: #64748b; font-weight: 600; }
        .info-tbl td.sep { width: 14px; text-align: center; color: #94a3b8; }
        .info-tbl td.val { color: #1e293b; }

        /* ─── AMOUNT ─── */
        .amount-box {
            margin: 24px 0;
            text-align: center;
            padding: 20px 24px;
            border: 2px solid #0d9488;
            border-radius: 6px;
            background: #f0fdfa;
        }
        .amount-label {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
        }
        .amount-value {
            font-size: 32px;
            font-weight: 900;
            color: #0f172a;
            margin: 8px 0 4px;
            letter-spacing: 1px;
        }
        .amount-terbilang {
            font-size: 11px;
            color: #64748b;
            font-style: italic;
        }

        /* ─── SIGNATURE ─── */
        .signature-area { margin-top: 36px; }
        .signature-box {
            width: 260px;
            margin: 0 auto;
            text-align: center;
        }
        .signature-label {
            font-size: 11px;
            color: #64748b;
        }
        .signature-space { height: 68px; }
        .signature-name {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 2px;
        }

        /* ─── FOOTER ─── */
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #94a3b8;
            font-size: 9.5px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            line-height: 1.7;
        }
        .footer strong { color: #64748b; }
    </style>
</head>
<body>

    @include('admin.pdf_templates.partials.header')

    {{-- ─── HEADLINE ─── ─── ─── ─── ─── ─── ─── ─── --}}
    <div class="main">
        <div class="headline">
            <h1>KWITANSI</h1>
            <table class="headline-info">
                <tr><td class="lbl">No. Kwitansi</td><td class="sep">:</td><td class="val">{{ $nomorKwitansi }}</td></tr>
                <tr><td class="lbl">Tanggal</td><td class="sep">:</td><td class="val">{{ \Carbon\Carbon::parse($tanggalKwitansi)->format('d M Y') }}</td></tr>
                <tr><td class="lbl">Status</td><td class="sep">:</td><td class="val"><span class="status-lunas">LUNAS</span></td></tr>
            </table>
        </div>

        {{-- ─── INFO ─── ─── ─── ─── ─── ─── ─── ─── --}}
        <div class="card">
            <div class="card-title">Informasi Pembayaran</div>
            <table class="info-tbl">
                <tr><td class="lbl">No. Invoice</td><td class="sep">:</td><td class="val">{{ $invoice->nomor_invoice ?? '-' }}</td></tr>
                <tr><td class="lbl">Nama Event</td><td class="sep">:</td><td class="val">{{ $event->nama_event ?? '-' }}</td></tr>
                <tr><td class="lbl">Klien</td><td class="sep">:</td><td class="val">{{ $event->client->name ?? '-' }}</td></tr>
                <tr><td class="lbl">Pembayaran</td><td class="sep">:</td><td class="val">{{ $jenisPembayaranLabel }}</td></tr>
            </table>
        </div>

        {{-- ─── AMOUNT ─── ─── ─── ─── ─── ─── ─── ─── --}}
        <div class="amount-box">
            <div class="amount-label">Telah Diterima Sejumlah</div>
            <div class="amount-value">Rp {{ number_format($nominal, 0, ',', '.') }}</div>
            <div class="amount-terbilang">{{ $terbilang ?? '' }}</div>
        </div>

        {{-- ─── SIGNATURE ─── ─── ─── ─── ─── ─── ─── ─── --}}
        <div class="signature-area">
            <div class="signature-box">
                <div class="signature-label">Hormat Kami,</div>
                <div class="signature-space"></div>
                <div class="signature-name">{{ $companyName ?? 'Alpha Organizer' }}</div>
            </div>
        </div>

        {{-- ─── FOOTER ─── ─── ─── ─── ─── ─── ─── ─── --}}
        <div class="footer">
            @php
                $footerEmail = 'alphaorganizer1209@gmail.com';
                $footerPhone = '+62 822-3318-1883';
                $footerAddress = 'Jl.Air Dingin No.25 Kec.Koto Tangah, Kota Padang';
            @endphp
            <strong>{{ $companyName ?? 'Alpha Organizer' }}</strong><br>
            {{ $footerEmail }} | {{ $footerPhone }}<br>
            {{ $footerAddress }}
        </div>
    </div>

{{-- Approval Metadata --}}
@include('admin.pdf_templates.partials.verification')

</body>
</html>
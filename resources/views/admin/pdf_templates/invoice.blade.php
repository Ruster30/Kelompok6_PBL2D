<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8" />

        <style>
            @page {
                margin:15px 20px;
            }

            body {
                font-family:
                    DejaVu Sans,
                    sans-serif;
                font-size: 11px;
                color: #222;
                line-height: 1.45;
            }

            * {
                box-sizing: border-box;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            .header {
                width: 100%;
                margin-bottom: 4px;
            }

            .header td {
                vertical-align: top;
            }

            .logo {
                width: 150px;
            }

            .company {
                text-align: right;
                font-size: 10px;
                line-height: 1.8;
            }

            .company div {
                margin-bottom: 2px;
            }

            .line {
                margin-top: 8px;
                border-top: 3px solid #18b6c9;
                border-bottom: 1px solid #18b6c9;
                height: 3px;
            }

            .title {
                text-align: center;
                margin:8px 0 10px;
            }

            .title h1 {
                margin: 0;
                font-size: 28px;
                font-weight: bold;
                letter-spacing: 1px;
            }

            .title hr{
                width:120px;
                margin-top:0;
                margin-bottom:0;
                border:none;
                border-top:2px solid #555;
            }

            .title h1{
                margin-bottom:2px;
            }

            .info-table {
                margin-top: 10px;
                margin-bottom: 18px;
            }

            .info-table td {
                border: 1px solid #bfe5e8;
                padding:6px 8px;
            }

            .info-head {
                background: #dff6f8;
                height: 18px;
            }

            .label {
                width: 28%;
                font-weight: bold;
            }

            .separator {
                width: 4%;
                text-align: center;
            }

            .value {
                width: 68%;
            }

            .section-space {
                display:none;
            }

            .terbilang {
                margin:8px 0;
                font-size:11px;
            }

            .terbilang strong{
                display:inline;
            }

            .terbilang span{
                display:inline;
                font-style:italic;
            }

            .terbilang-title {
                font-weight: bold;
                margin-bottom: 6px;
            }

            .terbilang-value {
                font-style: italic;
            }
        </style>
    </head>

    <body>
        <table class="header">
            <tr>
                <td width="45%">
                    <img src="{{ public_path('images/Logo-bg.png') }}" class="logo" />
                </td>

                <td width="55%" class="company">
                    <div>📞 +62 822-3318-1883</div>

                    <div>✉ alphaorganizer1209@gmail.com</div>

                    <div>📍 Jl. Air Dingin No.25 Kec. Koto Tangah, Kota Padang</div>
                </td>
            </tr>
        </table>

        <div class="line"></div>

        <div class="title">
            <h1>INVOICE</h1>

            <hr />
        </div>

        <table class="info-table">
            <tr class="info-head">
                <td colspan="3"></td>
            </tr>

            <tr>
                <td class="label">Invoice To</td>

                <td class="separator">:</td>

                <td class="value">{{ $invoice->event->client->name }}</td>
            </tr>

            <tr>
                <td class="label">No. Invoice</td>

                <td class="separator">:</td>

                <td class="value">{{ $invoice->nomor_invoice }}</td>
            </tr>

            <tr>
                <td class="label">Tanggal Invoice</td>

                <td class="separator">:</td>

                <td class="value">{{ \Carbon\Carbon::parse($invoice->tanggal_invoice)->translatedFormat('d F Y') }}</td>
            </tr>
        </table>

        <div class="section-space"></div>

        <table class="info-table">
            <tr class="info-head">
                <td colspan="3"></td>
            </tr>

            <tr>
                <td class="label">Pay To</td>

                <td class="separator">:</td>

                <td class="value">
                    <strong>CV ALPHA MULTI ORGANIZER</strong>
                </td>
            </tr>

            <tr>
                <td class="label">Bank</td>

                <td class="separator">:</td>

                <td class="value">BRI</td>
            </tr>

            <tr>
                <td class="label">No. Rekening</td>

                <td class="separator">:</td>

                <td class="value">0058 0100 6983 568</td>
            </tr>

            <tr>
                <td class="label">Atas Nama</td>

                <td class="separator">:</td>

                <td class="value">CV ALPHA MULTI ORGANIZER</td>
            </tr>
        </table>

        <div class="terbilang">
            <strong>Terbilang :</strong>
            <span>{{ $invoice->terbilang ?? '-' }}</span>
        </div>

        <style>
            .invoice-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 6px;
                margin-bottom: 10px;
            }

            .invoice-table th {
                background: #e8e8e8;
                border: 1px solid #999;
                padding: 7px;
                font-size: 11px;
                font-weight: bold;
            }

            .invoice-table td {
                border: 1px solid #999;
                padding: 7px;
                font-size: 11px;
                vertical-align: top;
            }

            .desc-title {
                font-weight: bold;
                margin-bottom: 8px;
            }

            .desc-item {
                margin-bottom: 5px;
            }

            .amount {
                text-align: right;
                vertical-align: middle !important;
                font-size: 16px;
            }

            .total-row td {
                background: #efefef;
                font-weight: bold;
                font-size: 12px;
            }

            .total-label {
                text-align: right;
                padding-right: 20px !important;
            }

            .total-value {
                text-align: right;
                font-size: 14px !important;
            }
        </style>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th width="70%" style="text-align: left">Deskripsi</th>

                    <th width="30%" style="text-align: right">Amount (Rp)</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>
                        <div class="desc-title">{{ $invoice->event->nama_event }}</div>

                        <div class="desc-item">Lokasi : {{ $invoice->event->lokasi }}</div>

                        <div class="desc-item">Tanggal : {{ \Carbon\Carbon::parse($invoice->event->tanggal_event)->translatedFormat('d F Y') }}</div>

                        @if($invoice->jenis_invoice === 'dp')

                        <div class="desc-item">
                            Jenis Pembayaran :
                            <strong>Down Payment (DP)</strong>
                        </div>

                        @elseif($invoice->jenis_invoice === 'pelunasan')

                        <div class="desc-item">
                            Jenis Pembayaran :
                            <strong>Pelunasan</strong>
                        </div>

                        @else

                        <div class="desc-item">
                            Jenis Pembayaran :
                            <strong>Full Payment</strong>
                        </div>

                        @endif
                    </td>

                    <td class="amount">{{ number_format($invoice->total_invoice, 0, ',', '.') }}</td>
                </tr>

                <tr class="total-row">
                    <td class="total-label">TOTAL</td>

                    <td class="total-value">{{ number_format($invoice->total_invoice, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <style>
            .footer-section {
                margin-top: 8px;
            }

            .note {
                font-size: 11px;
                line-height: 1.8;
            }

            .note-title {
                font-weight: bold;
                margin-bottom: 6px;
            }

            .note ul {
                margin: 0;
                padding-left: 18px;
            }

            .note li {
                margin-bottom: 4px;
            }

            .signature {
                margin-top: 8px;
                width: 100%;
            }

            .signature td {
                vertical-align: top;
            }

            .signature-right {
                width: 45%;
                text-align: center;
            }

            .signature-name {
                margin-top: 20px;
                font-weight: bold;
                text-decoration: underline;
            }

            .signature-position {
                margin-top: 5px;
            }

            .bottom-line {
                margin-top: 8px;
                border-top: 2px solid #18b6c9;
            }

            .footer {
                margin-top: 5px;
                text-align: center;
                font-size: 10px;
                color: #666;
            }

            .invoice-table,
            .footer-section,
            .signature{
                page-break-inside: avoid;
            }

            tr{
                page-break-inside: avoid;
            }

            table{
                page-break-inside: auto;
            }
        </style>

        <div class="footer-section">
            <div class="note">
                <div class="note-title">Catatan :</div>

                <ul>
                    <li>Invoice ini merupakan tagihan resmi Alpha Organizer.</li>

                    <li>Mohon melakukan pembayaran sesuai nominal invoice.</li>

                    <li>
                        Setelah pembayaran diverifikasi oleh Admin, sistem akan menerbitkan <strong>Kwitansi</strong>
                        sebagai bukti pembayaran.
                    </li>

                    <li>Mohon mencantumkan nomor invoice pada berita transfer.</li>
                </ul>
            </div>
        </div>

        <table class="signature">
            <tr>
                <td width="55%"></td>

                <td class="signature-right">
                    Padang, {{ \Carbon\Carbon::parse($invoice->tanggal_invoice)->translatedFormat('d F Y') }}

                    <br /><br />

                    Hormat kami,

                    <div class="signature-name">Kurnia Fajar Viliano, S.Tr.Kom</div>

                    <div class="signature-position">Direktur</div>
                </td>
            </tr>
        </table>

        <div class="bottom-line"></div>

        <div class="footer">Alpha Organizer &nbsp;&nbsp;|&nbsp;&nbsp; alphaorganizer1209@gmail.com &nbsp;&nbsp;|&nbsp;&nbsp; +62 822-3318-1883</div>
    
{{-- Approval Metadata --}}
@include('admin.pdf_templates.partials.verification')

</body>
</html>

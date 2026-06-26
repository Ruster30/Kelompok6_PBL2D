<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #1e293b; line-height: 1.6; padding: 32px 40px; }

  .header { display: table; width: 100%; margin-bottom: 28px; }
  .header-left { display: table-cell; vertical-align: middle; }
  .header-right { display: table-cell; vertical-align: middle; text-align: right; }
  .logo-box { background: #4338ca; color: #fff; font-weight: 900; font-size: 20px; padding: 8px 16px; border-radius: 6px; display: inline-block; }
  .company-name { font-size: 13px; font-weight: 700; color: #1e3a8a; margin-top: 4px; }
  .company-sub { font-size: 10px; color: #64748b; }

  .invoice-title { font-size: 28px; font-weight: 900; color: #4338ca; line-height: 1; }
  .invoice-no { font-size: 12px; color: #64748b; margin-top: 4px; }

  .status-badge { display: inline-block; padding: 3px 12px; border-radius: 999px; font-size: 10px; font-weight: 700; margin-top: 6px; }
  .status-draft    { background: #fef9c3; color: #713f12; }
  .status-terkirim { background: #dbeafe; color: #1d4ed8; }
  .status-lunas    { background: #dcfce7; color: #166534; }

  .divider { border: none; border-top: 1px solid #e2e8f0; margin: 20px 0; }

  .info-cols { display: table; width: 100%; margin-bottom: 24px; }
  .info-col  { display: table-cell; width: 50%; vertical-align: top; padding-right: 20px; }
  .info-col h3 { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #94a3b8; margin-bottom: 8px; }
  .info-col p  { font-size: 11px; line-height: 1.7; }
  .info-col strong { color: #0f172a; }

  table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
  thead th { background: #4338ca; color: #fff; padding: 9px 10px; text-align: left; font-size: 10.5px; }
  tbody tr:nth-child(even) { background: #f8faff; }
  tbody td { border-bottom: 1px solid #e2e8f0; padding: 8px 10px; font-size: 10.5px; }
  .text-right { text-align: right; }
  .text-center { text-align: center; }

  .summary-table { width: 50%; margin-left: auto; border-collapse: collapse; }
  .summary-table td { padding: 6px 10px; }
  .summary-table .label { color: #64748b; }
  .summary-table .total-row td { background: #4338ca; color: #fff; font-weight: 700; font-size: 13px; border-radius: 4px; }

  .footer-note { margin-top: 30px; font-size: 10px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 12px; }
  .payment-info { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 16px; margin-top: 16px; }
  .payment-info h4 { color: #166534; font-size: 11px; font-weight: 700; margin-bottom: 6px; }
  .payment-info p  { font-size: 10.5px; color: #15803d; }
</style>
</head>
<body>

{{-- HEADER --}}
<div class="header">
    <div class="header-left">
        <div class="logo-box">AMO</div>
        <div class="company-name">CV. Alpha Multi Organizer</div>
        <div class="company-sub">Event Organizer Professional</div>
        <div class="company-sub">Padang, Sumatera Barat</div>
    </div>
    <div class="header-right">
        <div class="invoice-title">INVOICE</div>
        <div class="invoice-no"># {{ $nomorInvoice }}</div>
        <div>
            @php
                $statusClass = match($statusInvoice) {
                    'lunas'    => 'status-lunas',
                    'terkirim' => 'status-terkirim',
                    default    => 'status-draft',
                };
                $statusLabel = match($statusInvoice) {
                    'lunas'    => '✓ LUNAS',
                    'terkirim' => '→ TERKIRIM',
                    default    => '⏳ DRAFT',
                };
            @endphp
            <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
        </div>
    </div>
</div>

<hr class="divider">

{{-- INFO BILLING --}}
<div class="info-cols">
    <div class="info-col">
        <h3>Tagihan Kepada</h3>
        <p>
            <strong>{{ $event->client->name ?? '-' }}</strong><br>
            {{ $event->client->email ?? '' }}<br>
            {{ $event->client->phone ?? '' }}
        </p>
    </div>
    <div class="info-col" style="text-align:right;padding-right:0;">
        <h3>Detail Invoice</h3>
        <p>
            <strong>Tanggal Invoice</strong>: {{ $tanggalInvoice }}<br>
            <strong>Event</strong>: {{ $event->nama_event }}<br>
            <strong>Tanggal Event</strong>: {{ $event->tanggal_event?->format('d M Y') ?? '-' }}<br>
            <strong>Lokasi</strong>: {{ $event->lokasi_event ?? '-' }}
        </p>
    </div>
</div>

{{-- TABEL ITEM --}}
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Deskripsi Item</th>
            <th>Kategori</th>
            <th>Vendor</th>
            <th class="text-center">Qty</th>
            <th class="text-right">Harga Satuan</th>
            <th class="text-right">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rabItems as $i => $r)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $r->nama_biaya }}</td>
            <td>{{ $r->kategori_biaya ?? '-' }}</td>
            <td>{{ $r->vendor->nama_vendor ?? '-' }}</td>
            <td class="text-center">{{ $r->jumlah_item }}</td>
            <td class="text-right">Rp {{ number_format($r->harga_satuan, 0, ',', '.') }}</td>
            <td class="text-right">Rp {{ number_format($r->subtotal_biaya, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center" style="color:#94a3b8;font-style:italic;">
                Belum ada item anggaran untuk event ini.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- SUMMARY --}}
<table class="summary-table">
    <tr>
        <td class="label">Subtotal</td>
        <td class="text-right">Rp {{ number_format($totalItem, 0, ',', '.') }}</td>
    </tr>
    <tr>
        <td class="label">Pajak (0%)</td>
        <td class="text-right">Rp 0</td>
    </tr>
    <tr class="total-row">
        <td>TOTAL</td>
        <td class="text-right">Rp {{ number_format($totalInvoice, 0, ',', '.') }}</td>
    </tr>
</table>

{{-- INFORMASI PEMBAYARAN --}}
<div class="payment-info">
    <h4>Informasi Pembayaran</h4>
    <p>
        Harap lakukan pembayaran ke rekening CV. Alpha Multi Organizer.<br>
        Cantumkan nomor invoice <strong>{{ $nomorInvoice }}</strong> pada keterangan transfer.<br>
        Kirimkan bukti pembayaran melalui sistem atau email resmi kami.
    </p>
</div>

<div class="footer-note">
    <p>
        Terima kasih telah mempercayakan penyelenggaraan event Anda kepada CV. Alpha Multi Organizer.<br>
        Dokumen ini digenerate otomatis pada {{ now()->format('d M Y, H:i') }} WIB.
        Invoice ini sah sebagai bukti tagihan resmi.
    </p>
</div>

</body>
</html>

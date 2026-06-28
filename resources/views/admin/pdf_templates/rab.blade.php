<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #1e293b; line-height: 1.6; padding: 28px 36px; }

  /* KOP */
  .kop { display: table; width: 100%; border-bottom: 3px solid #15803d; padding-bottom: 12px; margin-bottom: 20px; }
  .kop-left  { display: table-cell; vertical-align: middle; }
  .kop-right { display: table-cell; vertical-align: middle; text-align: right; }
  .logo      { background: #15803d; color: #fff; font-weight: 900; font-size: 18px; padding: 6px 14px; border-radius: 6px; display: inline-block; }
  .co-name   { font-size: 13px; font-weight: 700; color: #14532d; margin-top: 4px; }
  .co-sub    { font-size: 10px; color: #64748b; }
  .doc-title { font-size: 22px; font-weight: 900; color: #15803d; }
  .doc-sub   { font-size: 11px; color: #64748b; margin-top: 2px; }

  /* META */
  .meta-grid { display: table; width: 100%; margin-bottom: 20px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 14px 18px; }
  .meta-row  { display: table-row; }
  .meta-label{ display: table-cell; width: 34%; color: #166534; padding: 3px 0; }
  .meta-value{ display: table-cell; font-weight: 600; padding: 3px 0; }

  /* TABEL UTAMA */
  table { width: 100%; border-collapse: collapse; margin-top: 10px; }
  thead th { background: #15803d; color: #fff; padding: 9px 10px; text-align: left; font-size: 10.5px; }
  tbody tr:nth-child(even) { background: #f0fdf4; }
  tbody td  { border-bottom: 1px solid #dcfce7; padding: 8px 10px; font-size: 10.5px; }
  .text-right  { text-align: right; }
  .text-center { text-align: center; }

  /* SUBTOTAL ROW */
  .subtotal-row td { background: #dcfce7; font-weight: 600; color: #14532d; }
  .total-row td    { background: #15803d; color: #fff; font-weight: 700; font-size: 12.5px; }

  /* KATEGORI HEADER */
  .kat-row td { background: #e2e8f0; font-weight: 700; color: #334155; font-size: 10px; text-transform: uppercase; letter-spacing: .4px; padding: 5px 10px; }

  /* FOOTER */
  .footer { margin-top: 28px; display: table; width: 100%; }
  .footer-left { display: table-cell; vertical-align: top; font-size: 10px; color: #64748b; }
  .footer-right { display: table-cell; width: 40%; text-align: center; vertical-align: top; }
  .ttd-line { border-top: 1px solid #1e293b; display: inline-block; min-width: 150px; margin-top: 55px; padding-top: 4px; font-size: 10.5px; }
</style>
</head>
<body>

{{-- KOP --}}
<div class="kop">
    <div class="kop-left">
        <div class="logo">AMO</div>
        <div class="co-name">CV. Alpha Multi Organizer</div>
        <div class="co-sub">Event Organizer Professional | Padang, Sumatera Barat</div>
    </div>
    <div class="kop-right">
        <div class="doc-title">RAB</div>
        <div class="doc-sub">Rencana Anggaran Biaya</div>
        <div class="doc-sub" style="margin-top:4px;color:#94a3b8;">Digenerate: {{ now()->format('d M Y') }}</div>
    </div>
</div>

{{-- META EVENT --}}
<div class="meta-grid">
    <div class="meta-row">
        <div class="meta-label">Nama Event</div>
        <div class="meta-value">{{ $event->nama_event }}</div>
    </div>
    <div class="meta-row">
        <div class="meta-label">Jenis Event</div>
        <div class="meta-value">{{ $event->jenis_event ?? '-' }}</div>
    </div>
    <div class="meta-row">
        <div class="meta-label">Tanggal Pelaksanaan</div>
        <div class="meta-value">{{ $event->tanggal_event?->format('d F Y') ?? '-' }}</div>
    </div>
    <div class="meta-row">
        <div class="meta-label">Lokasi</div>
        <div class="meta-value">{{ $event->lokasi_event ?? '-' }}</div>
    </div>
    <div class="meta-row">
        <div class="meta-label">Client</div>
        <div class="meta-value">{{ $event->client->name ?? '-' }}</div>
    </div>
    <div class="meta-row">
        <div class="meta-label">Email Client</div>
        <div class="meta-value">{{ $event->client->email ?? '-' }}</div>
    </div>
</div>

{{-- TABEL RAB --}}
@php
    $grouped = $rabItems->groupBy('kategori_biaya');
@endphp

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Item</th>
            <th>Vendor</th>
            <th class="text-center">Qty</th>
            <th class="text-right">Harga Satuan</th>
            <th class="text-right">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @forelse($grouped as $kategori => $items)
            <tr class="kat-row">
                <td colspan="6">{{ $kategori ?: 'Umum' }}</td>
            </tr>
            @foreach($items as $i => $r)
            <tr>
                <td>{{ $loop->parent->iteration }}.{{ $i + 1 }}</td>
                <td>{{ $r->nama_biaya }}</td>
                <td>{{ $r->vendor->nama_vendor ?? '-' }}</td>
                <td class="text-center">{{ $r->jumlah_item }}</td>
                <td class="text-right">Rp {{ number_format($r->harga_satuan, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($r->subtotal_biaya, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="subtotal-row">
                <td colspan="5" class="text-right">Subtotal {{ $kategori ?: 'Umum' }}</td>
                <td class="text-right">Rp {{ number_format($items->sum('subtotal_biaya'), 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center" style="color:#94a3b8;font-style:italic;padding:20px;">
                    Belum ada item RAB untuk event ini.
                </td>
            </tr>
        @endforelse

        @if($rabItems->count())
        <tr class="total-row">
            <td colspan="5" class="text-right">TOTAL KESELURUHAN</td>
            <td class="text-right">Rp {{ number_format($total, 0, ',', '.') }}</td>
        </tr>
        @endif
    </tbody>
</table>

{{-- RINGKASAN PER KATEGORI --}}
@if($grouped->count() > 1)
<div style="margin-top:16px;font-size:10px;">
    <strong style="color:#15803d;">Ringkasan per Kategori:</strong>
    <table style="margin-top:6px;width:50%;margin-left:auto;">
        @foreach($grouped as $kat => $items)
        <tr style="border-bottom:1px solid #e2e8f0;">
            <td style="padding:4px 8px;color:#475569;">{{ $kat ?: 'Umum' }}</td>
            <td style="padding:4px 8px;text-align:right;font-weight:600;">
                Rp {{ number_format($items->sum('subtotal_biaya'), 0, ',', '.') }}
            </td>
        </tr>
        @endforeach
        <tr style="background:#f0fdf4;">
            <td style="padding:5px 8px;font-weight:700;color:#14532d;">TOTAL</td>
            <td style="padding:5px 8px;text-align:right;font-weight:700;color:#14532d;">
                Rp {{ number_format($total, 0, ',', '.') }}
            </td>
        </tr>
    </table>
</div>
@endif

{{-- FOOTER --}}
<div class="footer">
    <div class="footer-left">
        <p style="margin-bottom:4px;">Catatan:</p>
        <ul style="padding-left:16px;color:#64748b;">
            <li>Anggaran bersifat estimasi dan dapat berubah sesuai kondisi lapangan.</li>
            <li>Perubahan RAB harus disetujui secara tertulis oleh kedua belah pihak.</li>
            <li>Dokumen ini digenerate otomatis oleh sistem Alpha Organizer.</li>
        </ul>
    </div>
    <div class="footer-right">
        <div>Padang, {{ now()->format('d F Y') }}</div>
        <div style="font-size:10px;color:#64748b;">Disetujui oleh,</div>
        <div class="ttd-line">Admin / Direktur</div>
    </div>
</div>

</body>
</html>

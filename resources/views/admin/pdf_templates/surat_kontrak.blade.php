<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #1e293b; line-height: 1.7; padding: 32px 40px; }

  .kop { text-align: center; border-bottom: 3px double #1e3a8a; padding-bottom: 14px; margin-bottom: 20px; }
  .kop h1 { font-size: 17px; font-weight: 700; color: #1e3a8a;  }
  .kop .subtitle { font-size: 11px; color: #475569; }
  .kop .no { font-size: 11px; margin-top: 8px; color: #334155; }

  h2 { font-size: 12px; font-weight: 700; color: #1e3a8a; margin: 18px 0 6px; text-text-transform: uppercase;  }
  p { margin-bottom: 8px; text-align: justify; }

  .info-table { width: 100%; margin: 10px 0; }
  .info-table td { padding: 3px 6px; }
  .info-table td:first-child { width: 36%; color: #475569; }
  .info-table td:nth-child(2) { width: 4%; }
  .info-table td:last-child { font-weight: 600; }

  .pasal { margin-bottom: 16px; }
  .pasal-title { font-weight: 700; font-size: 11.5px; color: #1e3a8a; margin-bottom: 6px; }
  .pasal ol { padding-left: 20px; }
  .pasal li { margin-bottom: 4px; }

  .ttd-area { display: table; width: 100%; margin-top: 40px; }
  .ttd-col { display: table-cell; width: 50%; text-align: center; padding: 0 10px; }
  .ttd-line { border-top: 1px solid #1e293b; display: inline-block; min-width: 160px; margin-top: 60px; padding-top: 4px; }

  .materai { border: 1px dashed #94a3b8; border-radius: 6px; padding: 6px 14px; font-size: 9.5px; color: #64748b; margin-top: 10px; display: inline-block; }
</style>
</head>



<body>

{{-- KOP SURAT --}}
<div class="kop">
    <h1>SURAT KONTRAK JASA EVENT ORGANIZER</h1>
    <div class="subtitle">CV. Alpha Multi Organizer | Padang, Sumatera Barat</div>
    <div class="no">Nomor: {{ $document?->numbering?->document_number ?? 'BELUM DITERBITKAN' }}</div>
</div>

<p style="text-align:justify;margin-bottom:10px;">
    Pada hari ini, <strong>{{ now()->isoFormat('dddd') }}</strong>, tanggal <strong>{{ now()->format('d') }}</strong>
    bulan <strong>{{ now()->isoFormat('MMMM') }}</strong> tahun <strong>{{ now()->format('Y') }}</strong>,
    telah disepakati Surat Kontrak Jasa Event Organizer oleh dan antara:
</p>

{{-- ─═══ PIHAK I ═══─ --}}
<div class="section-title">Pihak I (Event Organizer)</div>
<table class="info-table">
    <tr><td>Nama Perusahaan</td><td>:</td><td>CV. Alpha Multi Organizer</td></tr>
    <tr><td>Bidang Usaha</td><td>:</td><td>Event Organizer &amp; Entertainment</td></tr>
    <tr><td>Alamat</td><td>:</td><td>Padang, Sumatera Barat</td></tr>
    <tr><td>Selanjutnya disebut</td><td>:</td><td><strong>PIHAK PERTAMA</strong></td></tr>
</table>

{{-- ─═══ PIHAK II ═══─ --}}
<div class="section-title">Pihak II (Client)</div>
<table class="info-table">
    <tr><td>Nama</td><td>:</td><td>{{ $event->client->name ?? '-' }}</td></tr>
    <tr><td>Email</td><td>:</td><td>{{ $event->client->email ?? '-' }}</td></tr>
    <tr><td>Telepon</td><td>:</td><td>{{ $event->client->phone ?? '-' }}</td></tr>
    <tr><td>Selanjutnya disebut</td><td>:</td><td><strong>PIHAK KEDUA</strong></td></tr>
</table>

<p style="margin-top:12px;font-size:10.5px;text-align:justify;">
    Kedua belah pihak telah sepakat untuk mengadakan perjanjian kerja sama jasa penyelenggaraan acara dengan ketentuan sebagai berikut:
</p>

{{-- ═══ PASAL 1 ═══ --}}
<div class="pasal">
    <div class="pasal-title">PASAL 1 — DATA EVENT</div>
    <table class="info-table">
        <tr><td>Nama Event</td><td>:</td><td>{{ $event->nama_event }}</td></tr>
        <tr><td>Jenis Event</td><td>:</td><td>{{ $event->jenis_event ?? '-' }}</td></tr>
        <tr><td>Tanggal Pelaksanaan</td><td>:</td><td>{{ $event->tanggal_event?->format('d F Y') ?? '-' }}</td></tr>
        <tr><td>Lokasi</td><td>:</td><td>{{ $event->lokasi_event ?? '-' }}</td></tr>
        <tr><td>Jumlah Tamu</td><td>:</td><td>{{ $event->jumlah_tamu ? number_format($event->jumlah_tamu) . ' orang' : '-' }}</td></tr>
    </table>
</div>

{{-- ═══ PASAL 2 ═══ --}}
<div class="pasal">
    <div class="pasal-title">PASAL 2 — NILAI KONTRAK</div>
    <p>
        PIHAK KEDUA setuju untuk membayar kepada PIHAK PERTAMA total biaya penyelenggaraan event sebesar:
    </p>
    <div style="background:#eef2ff;border:2px solid #c7d2fe;border-radius:8px;padding:12px 18px;margin:10px 0;font-size:15px;font-weight:900;color:#3730a3;text-align:center;">
        Rp {{ number_format($nilaiKontrak, 0, ',', '.') }}
        <div style="font-size:10px;font-weight:400;color:#6366f1;margin-top:2px;">
            ({{ ($nilaiKontrak) }} Rupiah)
        </div>
    </div>
</div>

{{-- ═══ PASAL 3 ═══ --}}
<div class="pasal">
    <div class="pasal-title">PASAL 3 — HAK DAN KEWAJIBAN PIHAK PERTAMA</div>
    <ol>
        <li>Menyediakan layanan event organizer secara profesional sesuai kesepakatan.</li>
        <li>Mengelola vendor, dekorasi, dokumentasi, dan keperluan teknis lainnya.</li>
        <li>Memberikan laporan progress kepada PIHAK KEDUA secara berkala.</li>
        <li>Berhak menolak permintaan di luar lingkup kontrak ini.</li>
        <li>Berhak menerima pembayaran sesuai jadwal yang disepakati.</li>
    </ol>
</div>

{{-- ═══ PASAL 4 ═══ --}}
<div class="pasal">
    <div class="pasal-title">PASAL 4 — HAK DAN KEWAJIBAN PIHAK KEDUA</div>
    <ol>
        <li>Memberikan informasi yang dibutuhkan untuk penyelenggaraan event.</li>
        <li>Melakukan pembayaran sesuai jadwal yang ditetapkan.</li>
        <li>Menyetujui atau memberikan masukan atas rencana yang diajukan PIHAK PERTAMA.</li>
        <li>Berhak mendapatkan laporan penyelenggaraan event secara tertulis.</li>
    </ol>
</div>

{{-- ═══ PASAL 5 ═══ --}}
<div class="pasal">
    <div class="pasal-title">PASAL 5 — KETENTUAN PEMBAYARAN</div>
    <ol>
        <li>Uang muka (DP) sebesar <strong>50%</strong> dari total nilai kontrak dibayarkan pada saat penandatanganan kontrak ini.</li>
        <li>Pelunasan sebesar <strong>50%</strong> dibayarkan selambat-lambatnya <strong>7 hari sebelum</strong> pelaksanaan event.</li>
        <li>Pembayaran dilakukan melalui transfer bank ke rekening yang ditunjuk oleh PIHAK PERTAMA.</li>
        <li>Bukti pembayaran wajib dikirimkan kepada PIHAK PERTAMA sebagai konfirmasi.</li>
    </ol>
</div>

{{-- ═══ PASAL 6 ═══ --}}
<div class="pasal">
    <div class="pasal-title">PASAL 6 — MASA BERLAKU KONTRAK</div>
    <p>
        Kontrak ini berlaku sejak tanggal penandatanganan hingga selesainya seluruh kewajiban kedua belah pihak,
        yaitu paling lambat <strong>30 hari</strong> setelah pelaksanaan event pada tanggal
        <strong>{{ $event->tanggal_event?->format('d F Y') ?? '____________' }}</strong>.
    </p>
</div>

{{-- ═══ PASAL 7 ═══ --}}
<div class="pasal">
    <div class="pasal-title">PASAL 7 — PEMBATALAN DAN FORCE MAJEURE</div>
    <ol>
        <li>Pembatalan yang dilakukan PIHAK KEDUA kurang dari 14 hari sebelum event dikenakan biaya 30% dari total kontrak.</li>
        <li>Pembatalan akibat force majeure (bencana alam, pandemi, kerusuhan) membebaskan kedua pihak dari kewajiban, dengan pengembalian dana yang telah dibayarkan dikurangi biaya yang telah dikeluarkan.</li>
    </ol>
</div>

{{-- ═══ PASAL 8 ═══ --}}
<div class="pasal">
    <div class="pasal-title">PASAL 8 — PENYELESAIAN SENGKETA</div>
    <p>
        Segala perselisihan yang timbul dari kontrak ini akan diselesaikan secara musyawarah mufakat.
        Apabila tidak tercapai kesepakatan, maka diserahkan kepada pengadilan negeri yang berwenang di wilayah Padang.
    </p>
</div>

<p style="font-size:10.5px;text-align:justify;">
    Kontrak ini dibuat dalam rangkap dua, masing-masing bermaterai cukup dan memiliki kekuatan hukum yang sama,
    ditandatangani oleh kedua belah pihak pada tanggal tersebut di atas.
</p>

{{-- ═══ TTD ═══ --}}
<table class="ttd-area">
    <tr>
        <td>
            <div><strong>PIHAK PERTAMA</strong></div>
            <div style="font-size:9px;color:#64748b;">CV. Alpha Multi Organizer</div>
            <div class="materai">Materai Rp 10.000</div>
            <div class="ttd-line">Direktur</div>
        </td>
        <td>
            <div><strong>PIHAK KEDUA</strong></div>
            <div style="font-size:9px;color:#64748b;">Client</div>
            <div class="materai">Materai Rp 10.000</div>
            <div class="ttd-line">{{ $event->client->name ?? '_____________________' }}</div>
        </td>
    </tr>
</table>

{{-- ═══ DENAH / LAYOUT (last page) ═══ --}}
@if($layoutPath)
<div class="denah-page">
    <h2>DENAH / LAYOUT LOKASI</h2>
    <img src="{{ $layoutPath }}" alt="Denah Layout">
</div>
@endif


{{-- Approval Metadata --}}
@include('admin.pdf_templates.partials.verification')

</body>
</html>



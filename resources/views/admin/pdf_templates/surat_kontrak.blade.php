<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    @page {
        margin:15px 20px;
    }
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:DejaVu Sans, Arial, sans-serif; font-size:11px; color:#1e293b; line-height:1.6; }

    /* ─── Header ─── */
    .header { width:100%; margin-bottom:4px; }
    .header td { vertical-align:top; }
    .logo { width:150px; }
    .company { text-align:right; font-size:10px; line-height:1.8; }
    .company div { margin-bottom:2px; }
    .line { margin-top:8px; border-top:3px solid #14b8a6; border-bottom:1px solid #14b8a6; height:3px; }

    /* ─── Title ─── */
    .title { text-align:center; margin:10px 0 14px; }
    .title h1 { margin:0; font-size:20px; font-weight:900; color:#0f172a; letter-spacing:1.5px; }
    .title hr { width:100px; margin:4px auto 0; border:none; border-top:2px solid #14b8a6; }
    .title .no-surat { font-size:11px; color:#475569; margin-top:6px; }

    /* ─── Info Tables ─── */
    .info-table { width:100%; margin:8px 0; border-collapse:collapse; }
    .info-table td { padding:3px 6px; font-size:10.5px; }
    .info-table td:first-child { width:30%; color:#475569; }
    .info-table td:nth-child(2) { width:3%; }
    .info-table td:last-child { font-weight:600; }

    /* ─── Section ─── */
    .section-title { font-size:11.5px; font-weight:700; color:#14b8a6; margin:14px 0 6px; text-transform:uppercase; letter-spacing:.5px; border-bottom:1px solid #ccfbf1; padding-bottom:3px; }

    /* ─── Pasal ─── */
    .pasal { margin-bottom:12px; }
    .pasal-title { font-weight:700; font-size:11px; color:#0f172a; margin-bottom:4px; }
    .pasal ol { padding-left:18px; }
    .pasal li { margin-bottom:3px; font-size:10.5px; text-align:justify; }
    .pasal p { font-size:10.5px; text-align:justify; margin-bottom:4px; }

    /* ─── Nilai Kontrak Highlight ─── */
    .nilai-box { background:#f0fdfa; border:2px solid #99f6e4; border-radius:8px; padding:10px 16px; margin:8px 0; text-align:center; }
    .nilai-box .amount { font-size:15px; font-weight:900; color:#0f766e; }
    .nilai-box .terbilang { font-size:9.5px; font-weight:400; color:#14b8a6; margin-top:2px; }

    /* ─── TTD ─── */
    .ttd-area { width:100%; margin-top:30px; }
    .ttd-area td { width:50%; text-align:center; vertical-align:bottom; padding:0 10px; }
    .ttd-line { border-top:1px solid #1e293b; display:inline-block; min-width:160px; margin-top:60px; padding-top:4px; font-weight:700; font-size:11px; }
    .materai { border:1px dashed #94a3b8; border-radius:6px; padding:4px 12px; font-size:9px; color:#64748b; display:inline-block; margin-top:8px; }

    /* ─── Denah Page ─── */
    .denah-page { page-break-before:always; text-align:center; padding:20px; }
    .denah-page h2 { font-size:16px; font-weight:700; color:#0f172a; margin-bottom:16px; }
    .denah-page img { max-width:100%; max-height:600px; border:1px solid #e2e8f0; border-radius:8px; }
</style>
</head>
<body>

{{-- ─═══ HEADER ═══─ --}}
<table class="header">
    <tr>
        <td width="55%">
            <img src="{{ public_path('images/Logo-bg.png') }}" class="logo" onerror="this.style.display='none'">
        </td>
        <td width="45%" class="company">
            <div>📞 +62 822-3318-1883</div>
            <div>✉ alphaorganizer1209@gmail.com</div>
            <div>📍 Jl. Air Dingin No.25, Kec. Koto Tangah, Kota Padang</div>
        </td>
    </tr>
</table>

<div class="line"></div>

{{-- ─═══ TITLE ═══─ --}}
<div class="title">
    <h1>SURAT KONTRAK</h1>
    <hr>
    <div class="no-surat">Nomor: {{ $nomorKontrak }}</div>
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
    <div class="nilai-box">
        <div class="amount">Rp {{ number_format($nilaiKontrak, 0, ',', '.') }}</div>
        <div class="terbilang">({{ terbilang($nilaiKontrak) }} Rupiah)</div>
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

</body>
</html>

@php
/**
 * Fungsi terbilang sederhana untuk nilai kontrak.
 */
function terbilang(float $angka): string {
    $angka  = (int) abs($angka);
    $satuan = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan',
               'sepuluh', 'sebelas'];
    if ($angka < 12) return $satuan[$angka];
    if ($angka < 20) return $satuan[$angka - 10] . ' belas';
    if ($angka < 100) return $satuan[(int)($angka / 10)] . ' puluh ' . terbilang($angka % 10);
    if ($angka < 200) return 'seratus ' . terbilang($angka % 100);
    if ($angka < 1000) return $satuan[(int)($angka / 100)] . ' ratus ' . terbilang($angka % 100);
    if ($angka < 2000) return 'seribu ' . terbilang($angka % 1000);
    if ($angka < 1000000) return terbilang((int)($angka / 1000)) . ' ribu ' . terbilang($angka % 1000);
    if ($angka < 1000000000) return terbilang((int)($angka / 1000000)) . ' juta ' . terbilang($angka % 1000000);
    return terbilang((int)($angka / 1000000000)) . ' miliar ' . terbilang($angka % 1000000000);
}
@endphp

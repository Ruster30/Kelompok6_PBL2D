<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: DejaVu Sans, Arial, sans-serif;
    font-size: 11px;
    color: #1a1a1a;
    line-height: 1.65;
    /* Sama persis dengan surat_kontrak.blade.php yang sudah bekerja */
    padding: 32px 40px;
    background: #fff;
}

table { border-collapse: collapse; }
td    { border: none; vertical-align: top; padding: 0; }

p {
    margin-bottom: 6px;
    text-align: justify;
    line-height: 1.65;
    font-size: 11px;
}
.indent { text-indent: 24px; }

/* ─── HEADER ─── */
.header-tbl { width: 100%; border-collapse: collapse; }
.header-tbl td { vertical-align: top; }
.logo-img { width: 130px; }
.contact-col { text-align: right; font-size: 9.5px; line-height: 2.1; }
.divider-thick { margin-top: 7px; border-top: 4px solid #00b8b0; }
.divider-thin  { border-top: 1.5px solid #00b8b0; margin-top: 2px; }
.ttd-space { padding-bottom: 50px; }
.ttd-nama  { font-weight: bold; text-decoration: underline; }
</style>
</head>
<body>

@php
    $pdfData      = isset($data) ? $data : (isset($d) ? $d : []);
    $nomorSurat   = $pdfData['nomor_surat']   ?? '-';
    $tanggalSurat = $pdfData['tanggal_surat'] ?? now()->format('Y-m-d');
    $perihal      = $pdfData['perihal']        ?? ($event->perihal ?? 'Surat Penawaran Pameran Otomotif');
    $document     = $pdfData['document'] ?? null; // dari AdminProposalService::exportPdfData()
    $usesDdms     = $document?->uses_ddms ?? false;
    $statusPublished = $document ? ($document->status === \App\Enums\DocumentStatus::Published) : false;
    $hasQrPath = $document?->qrVerification?->qr_path ?? false;
@endphp

@include('admin.pdf_templates.partials.header')

{{-- ── TANGGAL ──────────────────────────────────── --}}
<table style="width:100%; margin-bottom:10px;">
    <tr>
        <td align="right" style="font-size:11px;">
            Padang, {{ \Carbon\Carbon::parse($tanggalSurat)->translatedFormat('d F Y') }}
        </td>
    </tr>
</table>

{{-- ── INFO SURAT ───────────────────────────────── --}}
<table style="margin-bottom:10px; font-size:11px;">
    <tr>
        <td style="width:72px;">No. Surat</td>
        <td style="width:14px;">:</td>
        <td>
    @if($usesDdms && $document?->numbering)
        {{ $document->numbering->document_number }}
    @else
        {{ $nomorSurat }}
    @endif
</td>
    </tr>
    <tr>
        <td>Lampiran</td>
        <td>:</td>
        <td>-</td>
    </tr>
    <tr>
        <td>Perihal</td>
        <td>:</td>
        <td>{{ $perihal }}</td>
    </tr>
</table>

{{-- ── KEPADA ───────────────────────────────────── --}}
<table style="margin-bottom:3px; font-size:11px;">
    <tr>
        <td>
            Kepada Yth<br>
            Kepala Cabang Dealer Mobil
            <strong>{{ $event->client->name ?? 'Bapak/Ibu Client' }}</strong>
        </td>
    </tr>
</table>
<table style="margin-bottom:10px; font-size:11px;">
    <tr>
        <td>Di,<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tempat</td>
    </tr>
</table>

{{-- ── PARAGRAF PEMBUKA ─────────────────────────── --}}
<p class="indent" style="margin-bottom:10px;">
    Dengan hormat, kami dari <strong>CV. Alpha Multi Organizer</strong>
    Perusahaan yang bergerak di bidang Event Organizer.
    Dengan ini menawarkan kegiatan pameran pameran otomotif mobil kepada
    <strong>{{ $event->client->name ?? 'Client' }}</strong>
    di Basko City Mall Padang, maka dengan ini kami mengajukan surat penawaran
    <strong>"Special Price"</strong> sebagai berikut :
</p>

{{-- ── RINCIAN I - IV ───────────────────────────── --}}
<table style="width:100%; margin-bottom:8px; font-size:11px;">

    {{-- I. Lokasi --}}
    <tr>
        <td style="width:22px; font-weight:bold;">I.</td>
        <td style="width:100px;">Lokasi</td>
        <td style="width:12px; text-align:center;">:</td>
        <td>{{ $event->lokasi_event ?? '-' }}</td>
    </tr>
    <tr><td colspan="4" style="padding:2px 0;"></td></tr>

    {{-- II. Jenis Kegiatan --}}
    <tr>
        <td style="font-weight:bold;">II.</td>
        <td>Jenis Kegiatan</td>
        <td style="text-align:center;">:</td>
        <td>{{ $event->jenis_event ?? '-' }}</td>
    </tr>
    <tr><td colspan="4" style="padding:2px 0;"></td></tr>

    {{-- III. Jadwal --}}
    <tr>
        <td style="font-weight:bold; vertical-align:top;">III.</td>
        <td style="vertical-align:top;">Jadwal</td>
        <td style="text-align:center; vertical-align:top;">:</td>
        <td>
            <table style="width:100%; font-size:11px;">
                <tr>
                    <td style="width:14px;">a.</td>
                    <td style="width:70px;">Jadwal</td>
                    <td style="width:12px; text-align:center;">:</td>
                    <td>
                        @if($event->tanggal_event)
                            {{ $event->tanggal_event->translatedFormat('d F Y') }}
                            @if($event->tanggal_selesai)
                                s/d {{ $event->tanggal_selesai->translatedFormat('d F Y') }}
                                ({{ $event->tanggal_event->diffInDays($event->tanggal_selesai) + 1 }} hari)
                            @endif
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>b.</td>
                    <td>Luas Area</td>
                    <td style="text-align:center;">:</td>
                    <td>{{ $event->luas_area ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="vertical-align:top;">c.</td>
                    <td style="vertical-align:top;">Price</td>
                    <td style="text-align:center; vertical-align:top;">:</td>
                    <td>
                        <strong>{{ $event->rentang_anggaran ?? '-' }}
                        @if($event->include_ppn ?? true)
                            ( Include PPN &amp; PPh )*
                        @endif
                        </strong>
                        @if($event->terbilang ?? null)
                        <br><span style="font-size:10px;">({{ $event->terbilang }})</span>
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr><td colspan="4" style="padding:2px 0;"></td></tr>

    {{-- IV. Fasilitas --}}
    <tr>
        <td style="font-weight:bold; vertical-align:top;">IV.</td>
        <td style="vertical-align:top;">Fasilitas</td>
        <td style="text-align:center; vertical-align:top;">:</td>
        <td>
            @if($event->detail_kebutuhan ?? null)
                {!! nl2br(e($event->detail_kebutuhan)) !!}
            @else
                1. Manajemen acara penuh (Full Event Management)<br>
                2. Koordinasi vendor dan logistik<br>
                3. Setup dan dekorasi standar sesuai tema<br>
                4. Tim lapangan profesional selama acara berlangsung
            @endif
        </td>
    </tr>
</table>

{{-- ── V. KETENTUAN LAIN ───────────────────────── --}}
<p style="font-weight:bold; margin-bottom:3px; font-size:11px;">V.&nbsp;&nbsp;Ketentuan lain :</p>
<table style="width:100%; margin-bottom:8px; font-size:11px;">
    <tr>
        <td style="width:14px; vertical-align:top;">a.</td>
        <td style="text-align:justify;">Loading In dan Out Barang Jam 22.00 wib sd selesai dan wajib diberitahukan kepada manajemen Alpha Organizer.</td>
    </tr>
    <tr><td colspan="2" style="padding:1px 0;"></td></tr>
    <tr>
        <td style="vertical-align:top;">b.</td>
        <td style="text-align:justify;">Segala bentuk izin dan pajak diurus sendiri oleh penyewa.</td>
    </tr>
    <tr><td colspan="2" style="padding:1px 0;"></td></tr>
    <tr>
        <td style="vertical-align:top;">c.</td>
        <td style="text-align:justify;">Pembayaran dilakukan melalui Transfer <strong>Bank BRI A.n CV ALPHA MULTI ORGANIZER No Rek. 005801006983568</strong>.</td>
    </tr>
    <tr><td colspan="2" style="padding:1px 0;"></td></tr>
    <tr>
        <td style="vertical-align:top;">d.</td>
        <td style="text-align:justify;">Biaya yang tersebut diatas belum termasuk biaya SPSI (jika ada)</td>
    </tr>
    <tr><td colspan="2" style="padding:1px 0;"></td></tr>
    <tr>
        <td style="vertical-align:top;">e.</td>
        <td style="text-align:justify;">Pemakai Jasa Penyelenggara wajib mematuhi semua peraturan dan tata tertib yang berlaku di Basko City Mall Padang.</td>
    </tr>
    <tr><td colspan="2" style="padding:1px 0;"></td></tr>
    <tr>
        <td style="vertical-align:top;">f.</td>
        <td style="text-align:justify;">Pemakai Jasa Penyelenggara wajib mengasuransikan produknya selama pameran berlangsung. Kerusakan dan kehilangan barang di saat pameran yang diakibatkan oleh human error dan forced majure bukan tanggung jawab dari pemakai jasa penyelenggara.</td>
    </tr>
</table>

{{-- ── PARAGRAF PENUTUP ─────────────────────────── --}}
<p class="indent" style="margin-bottom:10px;">
    Apabila ada hal-hal yang perlu dipertanyakan, silahkan menghubungi Sdra. Fajar Viliano
    0895-4013-00022 atau melalui email : alphaorganizer1209@gmail.com. Demikianlah surat
    penawaran ini kami buat, Atas perhatian dan kerjasamanya kami ucapkan terimakasih.
</p>

{{-- ── TANDA TANGAN ────────────────────────────── --}}
<table style="margin-top:12px; font-size:11px;">
    <tr>
        <td>
            Padang, {{ \Carbon\Carbon::parse($tanggalSurat)->translatedFormat('d F Y') }}<br>
            Hormat kami,
            
            @if($usesDdms && $statusPublished && $hasQrPath)
            @include('admin.pdf_templates.partials.signature_qr')
            @endif
            
            <div class="ttd-space"></div>
            <span class="ttd-nama">Kurnia Fajar Viliano S.Tr.Kom</span><br>
            Direktur
        </td>
    </tr>
</table>

{{-- Garis teal penutup --}}
<table style="width:100%; margin-top:20px;">
    <tr><td style="border-top:2.5px solid #14b8a6; font-size:1px;">&nbsp;</td></tr>
</table>

</body>
</html>
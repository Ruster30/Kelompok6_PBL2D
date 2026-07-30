<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    @page {
        margin: 20mm 18mm 18mm 18mm;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: DejaVu Sans, Arial, sans-serif;
        font-size: 11px;
        color: #111;
        line-height: 1.5;
        padding: 76px 68px 68px 68px;
    }

    p { text-align: justify; }

    /* ═══ FOOTER EVERY PAGE ═══ */
    .page-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 8px;
        background: #00b8b0;
        z-index: 1000;
    }

    /* ─── HEADER ─── */
    .header-tbl { width: 100%; border-collapse: collapse; }
    .header-tbl td { vertical-align: top; }
    .logo-img { width: 130px; }

    .contact-col { text-align: right; font-size: 9.5px; line-height: 2.1; }

    .divider-thick { margin-top: 7px; border-top: 4px solid #00b8b0; }
    .divider-thin  { border-top: 1.5px solid #00b8b0; margin-top: 2px; }

    /* ─── META / TANGGAL ─── */
    .date-right { text-align: right; margin: 18px 0 14px; font-size: 11px; }

    .meta-tbl { border-collapse: collapse; width: 100%; margin-bottom: 18px; }
    .meta-tbl td { padding: 1px 0; font-size: 11px; vertical-align: top; }
    .meta-tbl td.lbl { width: 60px; }
    .meta-tbl td.sep { width: 16px; text-align: center; }

    /* ─── ALAMAT ─── */
    .kepada { margin-bottom: 18px; font-size: 11px; line-height: 1.5; }
    .di-line { margin-bottom: 20px; font-size: 11px; line-height: 1.5; }

    /* ─── OPENING ─── */
    .opening {
        text-align: justify;
        margin-bottom: 18px;
        font-size: 11px;
        line-height: 1.5;
        text-indent: 30px;
    }

    /* ─── DETAIL TABLE (I, II, III...) ─── */
    .detail-tbl { border-collapse: collapse; width: 100%; margin-bottom: 8px; font-size: 11px; }
    .detail-tbl td { padding: 2px 0; vertical-align: top; }
    .detail-tbl td.num { width: 28px; font-weight: bold; }
    .detail-tbl td.lbl { width: 140px; font-weight: bold; }
    .detail-tbl td.sep { width: 16px; text-align: center; }

    /* ─── SUB LIST (a, b, c) ─── */
    .sub-list { margin: 2px 0 0; padding: 0 0 0 18px; list-style: none; }
    .sub-list li { font-size: 11px; line-height: 1.5; text-align: justify; margin-bottom: 2px; }

    /* ─── PAGE BREAK ─── */
    .page-break { page-break-before: always; }

    /* ─── TTD ─── */
    .ttd-date { margin-top: 24px; margin-bottom: 2px; font-size: 11px; }
    .ttd-tbl  { width: 100%; border-collapse: collapse; margin-top: 4px; }
    .ttd-tbl td { width: 50%; vertical-align: top; font-size: 11px; padding: 0; }
    .ttd-tbl td.right { text-align: right; }

    .sig-space { height: 68px; }

    .ttd-line {
        border-top: 1px solid #111;
        display: inline-block;
        min-width: 170px;
        padding-top: 3px;
    }

    .name-bold { font-weight: bold; text-decoration: underline; font-size: 11px; }
    .jab { font-size: 10px; }

    .contact-footer {
        margin-top: 20px;
        text-align: right;
        color: #cc0000;
        font-size: 9.5px;
        line-height: 1.9;
        font-weight: bold;
    }

    /* ─── DENAH ─── */
    .denah-wrap { text-align: center; margin-top: 16px; }
    .denah-img  { max-width: 100%; border: 1px solid #ccc; }
</style>
</head>
<body>

{{-- FOOTER --}}
<div class="page-footer"></div>

{{-- ════════════════════════ PAGE 1 ════════════════════════ --}}

@include('admin.pdf_templates.partials.header')

{{-- TANGGAL --}}
<div class="date-right">
    Padang, {{ $event->tanggal_event?->isoFormat('D MMMM Y') ?? now()->isoFormat('D MMMM Y') }}
</div>

{{-- META --}}
<table class="meta-tbl">
    <tr>
        <td class="lbl">Nomor</td>
        <td class="sep">:</td>
        <td>{{ $document?->numbering?->document_number ?? 'BELUM DITERBITKAN' }}</td>
    </tr>
    <tr>
        <td class="lbl">Lampiran</td>
        <td class="sep">:</td>
        <td>Layout {{ $event->jenis_event ?? 'Pameran' }}</td>
    </tr>
    <tr>
        <td class="lbl">Perihal</td>
        <td class="sep">:</td>
        <td>Surat Kontrak Kerjasama {{ $event->jenis_event ?? 'Pameran' }} &mdash; {{ $event->nama_event ?? '' }}</td>
    </tr>
</table>

{{-- KEPADA --}}
<div class="kepada">
    Kepada Yth<br>
    Pimpinan <strong>{{ $event->client->name ?? '______________________' }}</strong>
</div>

<div class="di-line">
    Di<br>
    &nbsp;&nbsp;&nbsp;&nbsp;Tempat
</div>

{{-- OPENING --}}
<p class="opening">
    Dengan hormat, Kami dari CV. <strong>Alpha Multi Organizer</strong> adalah perusahaan yang
    bergerak di bidang Event Organizer dengan ini mengirimkan surat kontrak kerja sama
    kegiatan <strong>{{ strtolower($event->jenis_event ?? 'pameran') }}</strong>
    <strong>&ldquo;{{ $event->nama_event ?? '' }}&rdquo;</strong> kepada
    <strong>{{ $event->client->name ?? '______________________' }}</strong>
    yang berlokasi di {{ $event->lokasi_event ?? '______________________' }},
    maka dengan ini kami mengajukan surat kontrak kerja sama
    {{ strtolower($event->jenis_event ?? 'pameran') }} sebagai berikut :
</p>

{{-- DETAIL --}}
<table class="detail-tbl">
    <tr>
        <td class="num">I.</td>
        <td class="lbl">Lokasi</td>
        <td class="sep">:</td>
        <td>{{ $event->lokasi_event ?? '-' }}</td>
    </tr>
    <tr>
        <td class="num">II.</td>
        <td class="lbl">Jenis Kegiatan</td>
        <td class="sep">:</td>
        <td>{{ $event->jenis_event ?? '-' }}</td>
    </tr>
    <tr>
        <td class="num">III.</td>
        <td class="lbl">Jadwal Kegiatan</td>
        <td class="sep">:</td>
        <td>
            @if($event->tanggal_event)
                {{ $event->tanggal_event->isoFormat('D MMMM Y') }}
                @if($event->tanggal_selesai ?? null)
                    &ndash; {{ $event->tanggal_selesai->isoFormat('D MMMM Y') }}
                    ({{ $event->tanggal_event->diffInDays($event->tanggal_selesai) + 1 }} Hari)
                @endif
            @else
                -
            @endif
        </td>
    </tr>
    <tr>
        <td class="num">IV.</td>
        <td class="lbl">Jumlah Tamu</td>
        <td class="sep">:</td>
        <td>{{ $event->jumlah_tamu ? number_format($event->jumlah_tamu) . ' orang' : '-' }}</td>
    </tr>
    <tr>
        <td class="num">V.</td>
        <td class="lbl">Price</td>
        <td class="sep">:</td>
        <td><strong>Rp. {{ number_format($nilaiKontrak, 0, ',', '.') }}</strong></td>
    </tr>
    <tr>
        <td class="num">VI.</td>
        <td class="lbl">Fasilitas</td>
        <td class="sep">:</td>
        <td>
            @if($event->detail_kebutuhan)
                <ul class="sub-list">
                    @foreach(explode("\n", $event->detail_kebutuhan) as $i => $item)
                        @php $abjad = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l']; @endphp
                        <li>{{ $abjad[$i] ?? ($i + 1) }}. {{ $item }}</li>
                    @endforeach
                </ul>
            @else
                <ul class="sub-list">
                    <li>a. Dekorasi dan tata ruang sesuai tema acara</li>
                    <li>b. Dokumentasi (foto &amp; video)</li>
                    <li>c. Sound system dan lighting</li>
                </ul>
            @endif
        </td>
    </tr>
    <tr>
        <td class="num">VII.</td>
        <td class="lbl">Ketentuan lain</td>
        <td class="sep">:</td>
        <td>
            <ul class="sub-list">
                <li>a. Pembayaran selambat-lambatnya H-3 sebelum acara berlangsung.</li>
                <li>b. Pembayaran dilakukan melalui Transfer Bank <strong>BRI a.n. Kurnia Fajar Viliano No. 005801123790503</strong>.</li>
                <li>c. Seluruh koordinasi teknis wajib diberitahukan kepada manajemen Alpha Organizer.</li>
                <li>d. Pemasangan dekorasi dan perlengkapan dikoordinasikan dengan EO Alpha Organizer.</li>
                <li>e. Pemakai jasa penyelenggara wajib mematuhi semua peraturan dan tata tertib yang berlaku di lokasi acara.</li>
            </ul>
        </td>
    </tr>
</table>


{{-- ════════════════════════ PAGE 2 ════════════════════════ --}}
<div class="page-break">

@include('admin.pdf_templates.partials.header')

{{-- Lanjutan ketentuan f & g --}}
<table class="detail-tbl" style="margin-top: 20px;">
    <tr>
        <td class="num"></td>
        <td class="lbl"></td>
        <td class="sep"></td>
        <td>
            <ul class="sub-list">
                <li>f. Pemakai jasa penyelenggara wajib mengasuransikan propertinya selama acara berlangsung.</li>
                <li>g. Kerusakan dan kehilangan barang yang diakibatkan oleh human error dan force majeure bukan tanggung jawab dari pemakai jasa penyelenggara.</li>
            </ul>
        </td>
    </tr>
</table>

{{-- TTD --}}
<div class="ttd-date">
    Padang, {{ $event->tanggal_event?->isoFormat('D MMMM Y') ?? now()->isoFormat('D MMMM Y') }}
</div>

<table class="ttd-tbl">
    <tr>
        <td style="font-weight:bold;">Hormat kami,</td>
        <td class="right" style="font-weight:bold;">Menyetujui,</td>
    </tr>
    <tr>
        <td>
            @if(file_exists(public_path('images/ttd-fajar.png')))
                <img src="{{ public_path('images/ttd-fajar.png') }}" style="height:62px; display:block; margin-bottom:3px;">
            @else
                <div class="sig-space"></div>
            @endif
        </td>
        <td class="right">
            <div class="sig-space"></div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="ttd-line"></div>
            <div class="name-bold">Kurnia Fajar Viliano S.Tr.Kom</div>
            <div class="jab">Head Of Alpha Organizer</div>
        </td>
        <td class="right">
            <div class="ttd-line" style="margin-left:auto;"></div>
            <div class="name-bold">&nbsp;</div>
            <div class="jab">&nbsp;</div>
        </td>
    </tr>
</table>

<div class="contact-footer">
    * Contact Person +62 895 4013 00022 (Fajar)<br>
    Instagram @alphaorganizer.co
</div>

</div>{{-- /page-break --}}


{{-- ════════════════════════ PAGE 3: DENAH ════════════════════════ --}}
@if($layoutPath)
<div class="page-break">

@include('admin.pdf_templates.partials.header')

<div class="denah-wrap">
    <p style="font-size:13px; font-weight:bold; margin:18px 0 14px;">DENAH / LAYOUT LOKASI</p>
    <img src="{{ $layoutPath }}" class="denah-img" alt="Denah Layout">
</div>

</div>{{-- /page-break --}}
@endif


{{-- ════════════════════════ VERIFICATION ════════════════════════ --}}
@include('admin.pdf_templates.partials.verification')

</body>
</html>
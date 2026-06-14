<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Company Profile - {{ $company['name'] }}</title>
<style>
        /* =============================================
           DomPDF-compatible CSS
           Semua inline / style tag — no external fonts
           ============================================= */
        * { margin: 0; padding: 0; box-sizing: border-box; }
 
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1a2540;
            background: #fff;
        }
 
        /* -------- COVER PAGE -------- */
        .cover {
            width: 100%;
            height: 297mm;
            background: #0a0e1a;
            position: relative;
            page-break-after: always;
        }
        .cover-accent-bar {
            width: 100%;
            height: 5px;
            background: #2dd4bf;
        }
        .cover-body {
            padding: 80px 60px 60px;
        }
        .cover-logo {
            font-size: 32px;
            font-weight: bold;
            color: #fff;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .cover-logo span { color: #2dd4bf; }
        .cover-tagline {
            font-size: 11px;
            color: #8a9bb5;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 80px;
        }
        .cover-title {
            font-size: 40px;
            font-weight: bold;
            color: #fff;
            line-height: 1.2;
            margin-bottom: 16px;
        }
        .cover-title span { color: #2dd4bf; }
        .cover-subtitle {
            font-size: 13px;
            color: #8a9bb5;
            line-height: 1.7;
            max-width: 420px;
            margin-bottom: 60px;
        }
        .cover-meta {
            border-top: 1px solid #1e2d40;
            padding-top: 24px;
            display: table;
            width: 100%;
        }
        .cover-meta-item {
            display: table-cell;
            width: 33%;
        }
        .cover-meta-label {
            font-size: 9px;
            color: #8a9bb5;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 4px;
        }
        .cover-meta-value {
            font-size: 12px;
            color: #fff;
            font-weight: bold;
        }
        .cover-bottom-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 80px;
            background: #2dd4bf;
            padding: 0 60px;
            display: table;
            width: 100%;
        }
        .cover-bottom-text {
            display: table-cell;
            vertical-align: middle;
            font-size: 11px;
            color: #0a0e1a;
            font-weight: bold;
        }
        .cover-bottom-year {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            font-size: 28px;
            font-weight: bold;
            color: rgba(10,14,26,.3);
        }
 
        /* -------- COMMON PAGE -------- */
        .page {
            padding: 48px 52px;
            page-break-after: always;
        }
        .page:last-child { page-break-after: auto; }
 
        /* Section header */
        .section-label {
            font-size: 8px;
            font-weight: bold;
            color: #2dd4bf;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 6px;
        }
        .section-title {
            font-size: 22px;
            font-weight: bold;
            color: #1a2540;
            margin-bottom: 6px;
            line-height: 1.3;
        }
        .section-divider {
            width: 40px;
            height: 3px;
            background: #2dd4bf;
            margin-bottom: 20px;
            border-radius: 2px;
        }
        .section-body {
            font-size: 10.5px;
            color: #4a5568;
            line-height: 1.75;
        }
 
        /* -------- PAGE HEADER -------- */
        .page-header {
            display: table;
            width: 100%;
            margin-bottom: 32px;
            border-bottom: 1px solid #e4eaf3;
            padding-bottom: 12px;
        }
        .page-header-logo {
            display: table-cell;
            vertical-align: middle;
            font-size: 14px;
            font-weight: bold;
            color: #1a2540;
        }
        .page-header-logo span { color: #2dd4bf; }
        .page-header-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            font-size: 9px;
            color: #8a9bb5;
        }
 
        /* -------- STATS TABLE -------- */
        .stats-table {
            display: table;
            width: 100%;
            margin: 24px 0;
        }
        .stat-cell {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 16px 8px;
            background: #f7fafc;
            border: 1px solid #e4eaf3;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #1a2540;
        }
        .stat-label {
            font-size: 9px;
            color: #2dd4bf;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 4px;
        }
 
        /* -------- VISI MISI -------- */
        .vm-table {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        .vm-cell {
            display: table-cell;
            width: 50%;
            padding: 20px;
            vertical-align: top;
        }
        .visi-cell {
            background: linear-gradient(145deg, #2ccdc0, #127f78);
            border-radius: 8px;
            padding-right: 32px;
        }
        .visi-cell .section-title { color: #fff; }
        .visi-cell .section-body  { color: rgba(255,255,255,.85); font-style: italic; }
        .misi-cell { padding-left: 32px; }
        .misi-item {
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e4eaf3;
        }
        .misi-item:last-child { border-bottom: none; margin-bottom: 0; }
        .misi-item-title {
            font-size: 11px;
            font-weight: bold;
            color: #1a2540;
            margin-bottom: 4px;
        }
        .misi-item-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #2dd4bf;
            border-radius: 50%;
            margin-right: 6px;
            vertical-align: middle;
        }
        .misi-item-desc {
            font-size: 10px;
            color: #6b7a99;
            line-height: 1.6;
        }
 
        /* -------- SERVICES GRID -------- */
        .services-grid {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        .services-row { display: table-row; }
        .service-cell {
            display: table-cell;
            width: 33.33%;
            padding: 14px;
            vertical-align: top;
        }
        .service-box {
            border: 1px solid #e4eaf3;
            border-radius: 8px;
            padding: 16px;
            height: 100%;
        }
        .service-box-dot {
            width: 32px;
            height: 32px;
            background: rgba(45,212,191,.12);
            border-radius: 8px;
            margin-bottom: 10px;
            text-align: center;
            line-height: 32px;
            font-size: 14px;
            color: #2dd4bf;
        }
        .service-box-title {
            font-size: 11px;
            font-weight: bold;
            color: #1a2540;
            margin-bottom: 6px;
        }
        .service-box-desc {
            font-size: 9.5px;
            color: #6b7a99;
            line-height: 1.6;
        }
        .service-left  { padding-left: 0; }
        .service-right { padding-right: 0; }
 
        /* -------- TEAM -------- */
        .team-table {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        .team-cell {
            display: table-cell;
            width: 25%;
            padding: 0 8px;
            vertical-align: top;
            text-align: center;
        }
        .team-cell:first-child { padding-left: 0; }
        .team-cell:last-child  { padding-right: 0; }
        .team-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #e4eaf3;
            margin: 0 auto 10px;
            overflow: hidden;
        }
        .team-avatar img { width: 80px; height: 80px; object-fit: cover; }
        .team-name {
            font-size: 11px;
            font-weight: bold;
            color: #1a2540;
            margin-bottom: 3px;
        }
        .team-role {
            font-size: 9.5px;
            color: #2dd4bf;
        }
 
        /* -------- WHY US -------- */
        .why-table { display: table; width: 100%; margin-top: 16px; }
        .why-row   { display: table-row; }
        .why-cell  {
            display: table-cell;
            width: 50%;
            padding: 10px;
            vertical-align: top;
        }
        .why-box {
            border-left: 3px solid #2dd4bf;
            padding: 10px 14px;
            margin-bottom: 4px;
        }
        .why-title { font-size: 11px; font-weight: bold; color: #1a2540; margin-bottom: 4px; }
        .why-desc  { font-size: 9.5px; color: #6b7a99; line-height: 1.6; }
 
        /* -------- CONTACT -------- */
        .contact-table { display: table; width: 100%; margin-top: 20px; }
        .contact-left  {
            display: table-cell;
            width: 55%;
            vertical-align: top;
            padding-right: 40px;
        }
        .contact-right {
            display: table-cell;
            width: 45%;
            vertical-align: top;
            background: #f7fafc;
            border: 1px solid #e4eaf3;
            border-radius: 8px;
            padding: 20px;
        }
        .contact-item { margin-bottom: 16px; }
        .contact-item-label {
            font-size: 8.5px;
            font-weight: bold;
            color: #2dd4bf;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }
        .contact-item-value {
            font-size: 11px;
            color: #1a2540;
        }
        .qr-placeholder {
            width: 80px; height: 80px;
            border: 2px dashed #e4eaf3;
            border-radius: 6px;
            text-align: center;
            padding-top: 28px;
            font-size: 9px;
            color: #8a9bb5;
            margin-bottom: 8px;
        }
 
        /* -------- FOOTER -------- */
        .page-footer {
            margin-top: 32px;
            padding-top: 12px;
            border-top: 1px solid #e4eaf3;
            display: table;
            width: 100%;
        }
        .page-footer-left {
            display: table-cell;
            font-size: 9px;
            color: #8a9bb5;
        }
        .page-footer-right {
            display: table-cell;
            text-align: right;
            font-size: 9px;
            color: #8a9bb5;
        }
        .accent { color: #2dd4bf; }
        .back-cover {
            background: #0a0e1a;
            height: 297mm;
            text-align: center;
            padding-top: 120px;
        }
        .back-cover-logo {
            font-size: 28px;
            font-weight: bold;
            color: #fff;
            margin-bottom: 12px;
        }
        .back-cover-logo span { color: #2dd4bf; }
        .back-cover-line {
            width: 60px;
            height: 3px;
            background: #2dd4bf;
            margin: 16px auto;
        }
        .back-cover-text {
            font-size: 11px;
            color: #8a9bb5;
        }
    </style>
</head>
<body>

{{-- ========================================
     HALAMAN 1: COVER
     ======================================== --}}
<div class="cover-page">
    <div class="cover-bg-circle-1"></div>
    <div class="cover-bg-circle-2"></div>
    <div class="cover-content">

        {{-- Logo --}}
        <div class="cover-logo-wrap">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="{{ $company['name'] }}" class="cover-logo">
            @else
                <span class="cover-logo-text">ALPHA</span>
            @endif
        </div>

        {{-- Badge --}}
        <div class="cover-badge">Company Profile</div>

        {{-- Title --}}
        <div class="cover-divider-top"></div>
        <div class="cover-label">Event Organizer Profesional</div>
        <div class="cover-title">
            {{ $company['name'] }}<br>
            <span>{{ $company['tagline'] }}</span>
        </div>
        <div class="cover-tagline">
            Memberikan layanan event organizer terbaik untuk setiap momen berharga Anda.
        </div>

        {{-- Stats --}}
        <div class="cover-stats-row">
            @foreach($stats as $stat)
            <div class="cover-stat-box">
                <div class="cover-stat-value">{{ $stat['value'] }}</div>
                <div class="cover-stat-label">{{ $stat['label'] }}</div>
            </div>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="cover-footer">
            <span class="cover-footer-left">
                Dokumen ini dibuat otomatis pada {{ $generatedAt }}
            </span>
            <span class="cover-footer-right">{{ $company['website'] }}</span>
        </div>
    </div>
</div>




{{-- ========================================
     HALAMAN 2: TENTANG KAMI
     ======================================== --}}
<div class="page">
    <div class="page-header clearfix">
        <span class="page-header-brand">ALPHA <span>Organizer</span></span>
        <span class="page-header-section">Tentang Kami</span>
    </div>

    <div class="section-label">Siapa Kami</div>
    <div class="section-title">Kami Tidak Hanya Merencanakan Event,<br>Kami Menciptakan <span>Warisan</span></div>
    <div class="section-divider"></div>

    <p class="about-text">{{ $company['desc1'] }}</p>
    <p class="about-text">{{ $company['desc2'] }}</p>

    <div style="margin-top: 28px;">
        <div class="section-label" style="margin-bottom:12px;">Pencapaian Kami</div>
        <table class="stats-table">
            <tr>
                @foreach($stats as $stat)
                <td class="stat-cell">
                    <div class="stat-cell-value">{{ $stat['value'] }}</div>
                    <div class="stat-cell-label">{{ $stat['label'] }}</div>
                </td>
                @endforeach
            </tr>
        </table>
    </div>

    <div class="page-footer clearfix">
        <span>{{ $company['name'] }} — Company Profile</span>
        <span class="page-footer-right">Halaman 2</span>
    </div>
</div>


{{-- ========================================
     HALAMAN 3: VISI & MISI
     ======================================== --}}
<div class="page">
    <div class="page-header clearfix">
        <span class="page-header-brand">ALPHA <span>Organizer</span></span>
        <span class="page-header-section">Visi &amp; Misi</span>
    </div>

    <div class="section-label">Arah &amp; Tujuan</div>
    <div class="section-title">Visi <span>&amp;</span> Misi Kami</div>
    <div class="section-divider"></div>

    <div class="vm-container clearfix">
        {{-- Visi --}}
        <div class="visi-box">
            <div class="visi-box-title">🎯 Visi Kami</div>
            <div class="visi-box-text">"{{ $visi }}"</div>
        </div>

        {{-- Misi --}}
        <div class="misi-col">
            <div class="misi-title-row">
                <div class="misi-title">🚀 Misi Kami</div>
            </div>
            @foreach($misi as $i => $item)
            <div class="misi-item clearfix">
                <div class="misi-item-num">{{ $i + 1 }}</div>
                <div class="misi-item-body">
                    <div class="misi-item-title">{{ $item['title'] }}</div>
                    <div class="misi-item-desc">{{ $item['desc'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="page-footer clearfix">
        <span>{{ $company['name'] }} — Company Profile</span>
        <span class="page-footer-right">Halaman 3</span>
    </div>
</div>


{{-- ========================================
     HALAMAN 4: LAYANAN
     ======================================== --}}
<div class="page">
    <div class="page-header clearfix">
        <span class="page-header-brand">ALPHA <span>Organizer</span></span>
        <span class="page-header-section">Layanan Kami</span>
    </div>

    <div class="section-label">Keahlian Kami</div>
    <div class="section-title">Layanan <span>Kami</span></div>
    <div class="section-divider"></div>

    <p style="font-size:10.5px; color:#556680; margin-bottom:20px;">
        Kami menawarkan spektrum penuh layanan manajemen acara, yang disesuaikan untuk
        memenuhi tujuan unik setiap klien.
    </p>

    @php $chunks = array_chunk($services, 3); @endphp
    @foreach($chunks as $row)
    <table class="services-grid" style="margin-bottom:0;">
        <tr>
            @foreach($row as $i => $svc)
            <td class="service-cell">
                <span class="service-num">{{ ($loop->parent->index * 3) + $loop->index + 1 }}</span>
                <div class="service-accent"></div>
                <div class="service-title">{{ $svc['title'] }}</div>
                <div class="service-desc">{{ $svc['desc'] }}</div>
            </td>
            @endforeach
            {{-- Fill empty cells --}}
            @for($e = count($row); $e < 3; $e++)
            <td style="width:33%;"></td>
            @endfor
        </tr>
    </table>
    @endforeach

    <div class="page-footer clearfix">
        <span>{{ $company['name'] }} — Company Profile</span>
        <span class="page-footer-right">Halaman 4</span>
    </div>
</div>


{{-- ========================================
     HALAMAN 5: MENGAPA MEMILIH KAMI
     ======================================== --}}
<div class="page">
    <div class="page-header clearfix">
        <span class="page-header-brand">ALPHA <span>Organizer</span></span>
        <span class="page-header-section">Keunggulan</span>
    </div>

    <div class="section-label">Keunggulan Kami</div>
    <div class="section-title">Mengapa Memilih <span>Kami</span></div>
    <div class="section-divider"></div>

    <p style="font-size:10.5px; color:#556680; margin-bottom:20px;">
        Kami memberikan nilai lebih dari sekadar layanan event biasa. Berikut yang membuat kami berbeda.
    </p>

    @php $whyChunks = array_chunk($whyUs, 3); @endphp
    @foreach($whyChunks as $row)
    <table class="why-grid" style="margin-bottom:8px;">
        <tr>
            @foreach($row as $item)
            <td class="why-cell">
                <div class="why-dot"></div>
                <div class="why-cell-title">{{ $item['title'] }}</div>
                <div class="why-cell-desc">{{ $item['desc'] }}</div>
            </td>
            @endforeach
            @for($e = count($row); $e < 3; $e++)
            <td style="width:33%;"></td>
            @endfor
        </tr>
    </table>
    @endforeach

    <div class="page-footer clearfix">
        <span>{{ $company['name'] }} — Company Profile</span>
        <span class="page-footer-right">Halaman 5</span>
    </div>
</div>


{{-- ========================================
     HALAMAN 6: TIM KAMI
     ======================================== --}}
<div class="page">
    <div class="page-header clearfix">
        <span class="page-header-brand">ALPHA <span>Organizer</span></span>
        <span class="page-header-section">Tim Kami</span>
    </div>

    <div class="section-label">Kenali Lebih Dekat</div>
    <div class="section-title">Tim <span>Kami</span></div>
    <div class="section-divider"></div>

    <p style="font-size:10.5px; color:#556680; margin-bottom:20px;">
        Sosok-sosok berbakat di balik kesuksesan setiap event yang dikelola oleh ALPHA Organizer.
    </p>

    @php $teamChunks = array_chunk($team, 3); @endphp
    @foreach($teamChunks as $row)
    <table class="team-table" style="margin-bottom:8px;">
        <tr>
            @foreach($row as $member)
            <td class="team-cell">
                <div class="team-avatar">
                    <img src="{{ $member['img'] }}" alt="{{ $member['name'] }}">
                </div>
                <div class="team-name">{{ $member['name'] }}</div>
                <div style="margin-top:5px;"><span class="team-role">{{ $member['role'] }}</span></div>
            </td>
            @endforeach
            @for($e = count($row); $e < 3; $e++)
            <td style="width:33%;"></td>
            @endfor
        </tr>
    </table>
    @endforeach

    <div class="page-footer clearfix">
        <span>{{ $company['name'] }} — Company Profile</span>
        <span class="page-footer-right">Halaman 6</span>
    </div>
</div>


{{-- ========================================
     HALAMAN 7: PORTOFOLIO & KLIEN
     ======================================== --}}
<div class="page">
    <div class="page-header clearfix">
        <span class="page-header-brand">ALPHA <span>Organizer</span></span>
        <span class="page-header-section">Portofolio &amp; Klien</span>
    </div>

    {{-- Portofolio --}}
    <div class="section-label">Karya Kami</div>
    <div class="section-title">Portofolio <span>Proyek</span></div>
    <div class="section-divider"></div>

    @php $pfChunks = array_chunk($portfolio, 3); @endphp
    @foreach($pfChunks as $row)
    <table class="portfolio-table" style="margin-bottom:8px;">
        <tr>
            @foreach($row as $item)
            <td class="portfolio-cell">
                <div style="margin-bottom: 8px;">
                    <img src="{{ $item['img'] }}" alt="{{ $item['title'] }}" style="width:100%; height:90px;">
                </div>
                <div class="portfolio-cat">{{ $item['cat'] }}</div>
                <div class="portfolio-title">{{ $item['title'] }}</div>
            </td>
            @endforeach
            @for($e = count($row); $e < 3; $e++)
            <td style="width:33%;"></td>
            @endfor
        </tr>
    </table>
    @endforeach

    {{-- Klien --}}
    <div style="margin-top:28px;">
        <div class="section-label">Dipercaya Oleh</div>
        <div class="section-title" style="font-size:17px;">Klien <span>Kami</span></div>
        <div class="section-divider"></div>

        <div class="clients-wrap">
            @foreach($clients as $client)
            <span class="client-tag">
                @if(file_exists($client['logo']))
                    <img src="{{ $client['logo'] }}" alt="{{ $client['name'] }}" style="height:12px; vertical-align:middle; margin-right:4px;">
                @endif
                {{ $client['name'] }}
            </span>
            @endforeach
        </div>
    </div>

    <div class="page-footer clearfix">
        <span>{{ $company['name'] }} — Company Profile</span>
        <span class="page-footer-right">Halaman 7</span>
    </div>
</div>


{{-- ========================================
     HALAMAN 8: KONTAK / BACK COVER
     ======================================== --}}
<div class="contact-page">

    <div style="margin-bottom:16px;">
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" alt="{{ $company['name'] }}" style="height:40px; width:auto;">
        @else
            <span style="font-size:20px; font-weight:bold; color:#d4af37; letter-spacing:2px;">ALPHA</span>
        @endif
    </div>

    <div class="contact-title">
        Hubungi <span>Kami</span>
    </div>
    <div class="contact-subtitle">
        Siap mewujudkan event impian Anda bersama tim profesional kami.
        Hubungi kami sekarang dan mulai perjalanan menuju event sempurna.
    </div>

    <div class="contact-item clearfix">
        <div class="contact-icon-box">📧</div>
        <div class="contact-info">
            <div class="contact-label">Email</div>
            <div class="contact-value">{{ $company['email'] }}</div>
        </div>
    </div>

    <div class="contact-item clearfix">
        <div class="contact-icon-box">📞</div>
        <div class="contact-info">
            <div class="contact-label">Telepon / WhatsApp</div>
            <div class="contact-value">{{ $company['phone'] }}</div>
        </div>
    </div>

    <div class="contact-item clearfix">
        <div class="contact-icon-box">📍</div>
        <div class="contact-info">
            <div class="contact-label">Alamat</div>
            <div class="contact-value">{{ $company['address'] }}</div>
        </div>
    </div>

    <div class="contact-item clearfix">
        <div class="contact-icon-box">🌐</div>
        <div class="contact-info">
            <div class="contact-label">Website</div>
            <div class="contact-value">{{ $company['website'] }}</div>
        </div>
    </div>

    <hr class="contact-divider">

    <div class="back-cover-footer">
        Dokumen ini dibuat secara otomatis dan selalu mencerminkan informasi terbaru.<br>
        Dihasilkan pada <span>{{ $generatedAt }}</span> &nbsp;|&nbsp; &copy; {{ date('Y') }} {{ $company['name'] }}. Hak Cipta Dilindungi.
    </div>
</div>

</body>
</html>

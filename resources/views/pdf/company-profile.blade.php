<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Company Profile - {{ $company['name'] }}</title>
<style>
    /* ====== RESET & BASE ====== */
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
        font-family: 'DejaVu Sans', 'Arial', sans-serif;
        font-size: 11px;
        color: #1a2540;
        background: #ffffff;
        line-height: 1.6;
    }

    /* ====== COVER PAGE ====== */
    .cover-page {
        width: 100%;
        height: 1122px; /* A4 height approx */
        background: #0d1b2e;
        position: relative;
        page-break-after: always;
    }
    .cover-bg-circle-1 {
        position: absolute;
        top: -80px; right: -80px;
        width: 380px; height: 380px;
        background: radial-gradient(circle, rgba(212,175,55,0.15) 0%, transparent 70%);
        border-radius: 50%;
    }
    .cover-bg-circle-2 {
        position: absolute;
        bottom: 100px; left: -60px;
        width: 280px; height: 280px;
        background: radial-gradient(circle, rgba(212,175,55,0.10) 0%, transparent 70%);
        border-radius: 50%;
    }
    .cover-content {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        padding: 80px 60px;
    }
    .cover-logo-wrap {
        margin-bottom: 60px;
    }
    .cover-logo {
        height: 50px;
        width: auto;
    }
    .cover-logo-text {
        font-size: 22px;
        font-weight: bold;
        color: #d4af37;
        letter-spacing: 2px;
    }
    .cover-divider-top {
        width: 60px;
        height: 3px;
        background: #d4af37;
        margin-bottom: 30px;
    }
    .cover-label {
        font-size: 10px;
        color: #d4af37;
        letter-spacing: 4px;
        text-transform: uppercase;
        margin-bottom: 18px;
    }
    .cover-title {
        font-size: 38px;
        font-weight: bold;
        color: #ffffff;
        line-height: 1.25;
        margin-bottom: 12px;
    }
    .cover-title span {
        color: #d4af37;
    }
    .cover-tagline {
        font-size: 14px;
        color: #8899bb;
        margin-bottom: 50px;
        max-width: 420px;
    }
    .cover-stats-row {
        margin-bottom: 60px;
    }
    .cover-stat-box {
        display: inline-block;
        width: 22%;
        text-align: center;
        border: 1px solid rgba(212,175,55,0.3);
        border-radius: 8px;
        padding: 16px 8px;
        margin-right: 2%;
        background: rgba(212,175,55,0.05);
    }
    .cover-stat-value {
        font-size: 24px;
        font-weight: bold;
        color: #d4af37;
    }
    .cover-stat-label {
        font-size: 9px;
        color: #8899bb;
        margin-top: 4px;
    }
    .cover-footer {
        position: absolute;
        bottom: 50px; left: 60px; right: 60px;
        border-top: 1px solid rgba(255,255,255,0.1);
        padding-top: 18px;
    }
    .cover-footer-left {
        display: inline-block;
        font-size: 9px;
        color: #556680;
    }
    .cover-footer-right {
        float: right;
        font-size: 9px;
        color: #d4af37;
    }
    .cover-badge {
        display: inline-block;
        border: 1px solid #d4af37;
        border-radius: 20px;
        padding: 6px 16px;
        font-size: 9px;
        color: #d4af37;
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 20px;
    }

    /* ====== CONTENT PAGES ====== */
    .page {
        padding: 50px 55px;
        page-break-after: always;
    }
    .page:last-child {
        page-break-after: auto;
    }

    /* Section header */
    .section-label {
        font-size: 9px;
        color: #d4af37;
        letter-spacing: 3px;
        text-transform: uppercase;
        margin-bottom: 6px;
    }
    .section-title {
        font-size: 20px;
        font-weight: bold;
        color: #0d1b2e;
        margin-bottom: 6px;
    }
    .section-title span { color: #d4af37; }
    .section-divider {
        width: 45px;
        height: 3px;
        background: #d4af37;
        margin-bottom: 24px;
        border-radius: 2px;
    }

    /* Page header (top bar) */
    .page-header {
        border-bottom: 2px solid #0d1b2e;
        padding-bottom: 12px;
        margin-bottom: 30px;
        overflow: hidden;
    }
    .page-header-brand {
        display: inline-block;
        font-size: 13px;
        font-weight: bold;
        color: #0d1b2e;
    }
    .page-header-brand span { color: #d4af37; }
    .page-header-section {
        float: right;
        font-size: 9px;
        color: #8899bb;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-top: 3px;
    }

    /* Page footer */
    .page-footer {
        border-top: 1px solid #e5e8f0;
        padding-top: 10px;
        margin-top: 30px;
        overflow: hidden;
        font-size: 8px;
        color: #aab0c0;
    }
    .page-footer-right { float: right; }

    /* ====== ABOUT SECTION ====== */
    .about-text {
        font-size: 11px;
        color: #334466;
        line-height: 1.75;
        margin-bottom: 14px;
    }
    .stats-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 8px 0;
        margin-top: 20px;
    }
    .stat-cell {
        width: 25%;
        background: #0d1b2e;
        border-radius: 8px;
        padding: 16px 10px;
        text-align: center;
    }
    .stat-cell-value {
        font-size: 22px;
        font-weight: bold;
        color: #d4af37;
    }
    .stat-cell-label {
        font-size: 8.5px;
        color: #8899bb;
        margin-top: 3px;
    }

    /* ====== VISI MISI ====== */
    .vm-container {
        overflow: hidden;
        margin-bottom: 0;
    }
    .visi-box {
        float: left;
        width: 44%;
        background: #0d1b2e;
        border-radius: 10px;
        padding: 28px 24px;
        min-height: 230px;
    }
    .visi-box-icon {
        width: 36px; height: 36px;
        background: rgba(212,175,55,0.15);
        border-radius: 50%;
        text-align: center;
        margin-bottom: 14px;
        padding-top: 8px;
        font-size: 16px;
        color: #d4af37;
    }
    .visi-box-title {
        font-size: 14px;
        font-weight: bold;
        color: #ffffff;
        margin-bottom: 12px;
    }
    .visi-box-text {
        font-size: 10px;
        color: #8899bb;
        line-height: 1.75;
        font-style: italic;
    }
    .misi-col {
        float: right;
        width: 52%;
    }
    .misi-title-row {
        margin-bottom: 16px;
    }
    .misi-title {
        font-size: 16px;
        font-weight: bold;
        color: #0d1b2e;
    }
    .misi-item {
        padding: 12px 0;
        border-bottom: 1px solid #e8ecf5;
        overflow: hidden;
    }
    .misi-item:last-child { border-bottom: none; }
    .misi-item-num {
        float: left;
        width: 24px; height: 24px;
        background: #d4af37;
        border-radius: 50%;
        text-align: center;
        font-size: 10px;
        font-weight: bold;
        color: #0d1b2e;
        padding-top: 5px;
        margin-right: 12px;
    }
    .misi-item-body { overflow: hidden; }
    .misi-item-title {
        font-size: 11px;
        font-weight: bold;
        color: #0d1b2e;
        margin-bottom: 3px;
    }
    .misi-item-desc {
        font-size: 9.5px;
        color: #556680;
        line-height: 1.6;
    }

    /* ====== SERVICES ====== */
    .services-grid {
        width: 100%;
        border-collapse: separate;
        border-spacing: 8px;
    }
    .service-cell {
        width: 33%;
        border: 1px solid #e8ecf5;
        border-radius: 10px;
        padding: 18px 16px;
        vertical-align: top;
        background: #f9faff;
    }
    .service-num {
        width: 28px; height: 28px;
        background: #0d1b2e;
        border-radius: 50%;
        text-align: center;
        font-size: 11px;
        font-weight: bold;
        color: #d4af37;
        margin-bottom: 10px;
        padding-top: 6px;
        display: block;
    }
    .service-title {
        font-size: 12px;
        font-weight: bold;
        color: #0d1b2e;
        margin-bottom: 6px;
    }
    .service-desc {
        font-size: 9.5px;
        color: #556680;
        line-height: 1.6;
    }
    .service-accent {
        width: 25px;
        height: 2px;
        background: #d4af37;
        margin-bottom: 8px;
    }

    /* ====== WHY US ====== */
    .why-grid {
        width: 100%;
        border-collapse: separate;
        border-spacing: 8px;
    }
    .why-cell {
        width: 33%;
        background: #0d1b2e;
        border-radius: 10px;
        padding: 18px 16px;
        vertical-align: top;
    }
    .why-cell-title {
        font-size: 11px;
        font-weight: bold;
        color: #ffffff;
        margin-bottom: 6px;
    }
    .why-cell-desc {
        font-size: 9.5px;
        color: #8899bb;
        line-height: 1.6;
    }
    .why-dot {
        width: 8px; height: 8px;
        background: #d4af37;
        border-radius: 50%;
        margin-bottom: 10px;
    }

    /* ====== TEAM ====== */
    .team-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 8px;
    }
    .team-cell {
        width: 33%;
        border: 1px solid #e8ecf5;
        border-radius: 10px;
        padding: 16px 14px;
        vertical-align: top;
        text-align: center;
        background: #f9faff;
    }
    .team-avatar {
        width: 50px; height: 50px;
        margin: 0 auto 10px;
        text-align: center;
    }
    .team-avatar img {
        width: 50px; height: 50px;
        border-radius: 50%;
    }
    .team-name {
        font-size: 10.5px;
        font-weight: bold;
        color: #0d1b2e;
        margin-bottom: 3px;
    }
    .team-role {
        font-size: 9px;
        color: #d4af37;
        background: rgba(212,175,55,0.1);
        border-radius: 20px;
        padding: 2px 8px;
        display: inline-block;
    }

    /* ====== PORTFOLIO ====== */
    .portfolio-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 8px;
    }
    .portfolio-cell {
        width: 33%;
        border-left: 3px solid #d4af37;
        background: #f9faff;
        padding: 14px 14px;
        border-radius: 0 8px 8px 0;
        vertical-align: top;
    }
    .portfolio-cat {
        font-size: 8px;
        color: #d4af37;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 4px;
    }
    .portfolio-title {
        font-size: 10.5px;
        font-weight: bold;
        color: #0d1b2e;
    }

    /* ====== CLIENTS ====== */
    .clients-wrap {
        background: #f5f7fc;
        border-radius: 10px;
        padding: 20px;
    }
    .client-tag {
        display: inline-block;
        border: 1px solid #d0d8ec;
        border-radius: 20px;
        padding: 5px 12px;
        font-size: 9.5px;
        color: #334466;
        margin: 4px;
        background: #ffffff;
    }

    /* ====== CONTACT / BACK COVER ====== */
    .contact-page {
        background: #0d1b2e;
        padding: 60px 55px;
    }
    .contact-title {
        font-size: 26px;
        font-weight: bold;
        color: #ffffff;
        margin-bottom: 8px;
    }
    .contact-title span { color: #d4af37; }
    .contact-subtitle {
        font-size: 11px;
        color: #8899bb;
        margin-bottom: 40px;
        max-width: 400px;
    }
    .contact-item {
        overflow: hidden;
        margin-bottom: 18px;
    }
    .contact-icon-box {
        float: left;
        width: 36px; height: 36px;
        background: rgba(212,175,55,0.15);
        border-radius: 8px;
        text-align: center;
        padding-top: 8px;
        margin-right: 14px;
        font-size: 14px;
        color: #d4af37;
    }
    .contact-info { overflow: hidden; }
    .contact-label {
        font-size: 8.5px;
        color: #556680;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 2px;
    }
    .contact-value {
        font-size: 11px;
        color: #ffffff;
        font-weight: bold;
    }
    .contact-divider {
        border: none;
        border-top: 1px solid rgba(255,255,255,0.08);
        margin: 30px 0;
    }
    .back-cover-footer {
        font-size: 9px;
        color: #556680;
        text-align: center;
        margin-top: 60px;
    }
    .back-cover-footer span { color: #d4af37; }

    /* ====== UTILITY ====== */
    .clearfix::after { content:''; display:table; clear:both; }
    .text-gold { color: #d4af37; }
    .mt-8 { margin-top: 8px; }
    .mt-16 { margin-top: 16px; }
    .mb-4 { margin-bottom: 4px; }
    .mb-8 { margin-bottom: 8px; }
    .mb-16 { margin-bottom: 16px; }
    .mb-24 { margin-bottom: 24px; }
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

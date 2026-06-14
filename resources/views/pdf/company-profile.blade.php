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
            background: #127f78;
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
 
        /* -------- SERVICES -------- */

        .services-grid {
            margin-top: 20px;
        }

        .service-box {
            width: 100%;
            border: 1px solid #e4eaf3;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        .service-box-dot {
            width: 28px;
            height: 28px;
            background: rgba(45,212,191,.12);
            border-radius: 6px;
            text-align: center;
            line-height: 28px;
            font-size: 12px;
            color: #2dd4bf;
            margin-bottom: 8px;
        }

        .service-box-title {
            font-size: 11px;
            font-weight: bold;
            color: #1a2540;
            margin-bottom: 4px;
        }

        .service-box-desc {
            font-size: 9px;
            color: #6b7a99;
            line-height: 1.5;
        }
 
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
        /* ==========================================
        PORTFOLIO
        ========================================== */

        .portfolio-grid{
            margin-top:20px;
        }

        .portfolio-row{
            display:table;
            width:100%;
            margin-bottom:15px;
        }

        .portfolio-card{
            display:table-cell;
            width:33.33%;
            padding:8px;
            vertical-align:top;
        }

        .portfolio-box{
            border:1px solid #e4eaf3;
            border-radius:8px;
            overflow:hidden;
            background:#fff;
        }

        .portfolio-img{
            width:100%;
            height:120px;
            object-fit:cover;
        }

        .portfolio-info{
            padding:10px;
        }

        .portfolio-category{
            font-size:8px;
            color:#2dd4bf;
            text-transform:uppercase;
            letter-spacing:1px;
            margin-bottom:4px;
        }

        .portfolio-name{
            font-size:11px;
            font-weight:bold;
            color:#1a2540;
        }

        /* ==========================================
        CLIENTS
        ========================================== */

        .clients-grid{
            margin-top:20px;
        }

        .client-row{
            display:table;
            width:100%;
            margin-bottom:12px;
        }

        .client-cell{
            display:table-cell;
            width:25%;
            text-align:center;
            vertical-align:middle;
            padding:12px;
        }

        .client-box{
            border:1px solid #e4eaf3;
            border-radius:8px;
            background:#fff;
            padding:15px;
            height:80px;
        }

        .client-logo{
            max-height:35px;
            margin-bottom:6px;
        }

        .client-name{
            font-size:9px;
            color:#1a2540;
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

{{-- ================================================
     HALAMAN 1: COVER
     ================================================ --}}
<div class="cover">
    
    <div class="cover-accent-bar"></div>

    <div class="cover-body">

        {{-- Logo Perusahaan --}}
        <div class="cover-logo">
            {{ $company['name'] }}
        </div>

        {{-- Sub Logo --}}
        <div class="cover-tagline">
            Event Organizer Profesional
        </div>

        {{-- Judul --}}
        <div class="cover-title">
            Company<br>
            <span>Profile</span>
        </div>

        {{-- Tagline --}}
        <div class="cover-subtitle">
            {{ $company['tagline'] }}
        </div>

        {{-- Statistik --}}
        <div class="cover-meta">

            {{-- Statistik dari Controller --}}
            @foreach($stats as $stat)
                <div class="cover-meta-item">
                    <div class="cover-meta-label">
                        {{ $stat['label'] }}
                    </div>

                    <div class="cover-meta-value">
                        {{ $stat['value'] }}
                    </div>
                </div>
            @endforeach

        </div>

    </div>

    {{-- Footer Cover --}}
    <div class="cover-bottom-bar">

        <div class="cover-bottom-text">
            Company Profile • {{ $company['name'] }}
        </div>

        <div class="cover-bottom-year">
            {{ date('Y') }}
        </div>

    </div>

</div>


{{-- ================================================
     HALAMAN 2: TENTANG + STATISTIK
     ================================================ --}}
<div class="page">

    <div class="page-header">
        <div class="page-header-logo">
            {{ $company['name'] }}
        </div>

        <div class="page-header-right">
            Company Profile {{ date('Y') }}
            &nbsp;|&nbsp;
            Tentang Kami
        </div>
    </div>

    {{-- Judul --}}
    <div class="section-label">
        Tentang Kami
    </div>

    <div class="section-title">
        {{ $company['about_title'] }}
    </div>

    <div class="section-divider"></div>

    {{-- Deskripsi --}}
    <div class="section-body">
        {{ $company['about_desc1'] }}
    </div>

    <br>

    <div class="section-body">
        {{ $company['about_desc2'] }}
    </div>

    {{-- Statistik --}}
    <div class="stats-table">

        @foreach($stats as $stat)

        <div class="stat-cell">

            <div class="stat-number">
                {{ $stat['value'] }}
            </div>

            <div class="stat-label">
                {{ $stat['label'] }}
            </div>

        </div>

        @endforeach

    </div>

    {{-- Footer --}}
    <div class="page-footer">

        <div class="page-footer-left">
            © {{ date('Y') }}
            {{ $company['name'] }}
            — Semua hak dilindungi undang-undang
        </div>

        <div class="page-footer-right">
            Halaman 2
        </div>

    </div>

</div>


{{-- ================================================
     HALAMAN 3: VISI & MISI
     ================================================ --}}
<div class="page">

    <div class="page-header">

        <div class="page-header-logo">
            {{ $company['name'] }}
        </div>

        <div class="page-header-right">
            Company Profile {{ date('Y') }}
            &nbsp;|&nbsp;
            Visi &amp; Misi
        </div>

    </div>

    {{-- Judul --}}
    <div class="section-label">
        Arah Perusahaan
    </div>

    <div class="section-title">
        Visi &amp; Misi Kami
    </div>

    <div class="section-divider"></div>

    <div class="vm-table">

        
        {{-- VISI --}}
        <div class="vm-cell visi-cell">

            <div class="section-title">
                Visi Kami
            </div>

            <br>

            <div class="section-body">
                {{ $visi }}
            </div>

        </div>

        {{-- MISI --}}
        <div class="vm-cell misi-cell">

            <div class="section-label">
                Misi Kami
            </div>

            <br>

            @foreach($misi as $item)

            <div class="misi-item">

                <div class="misi-item-title">
                    <span class="misi-item-dot"></span>
                    {{ $item['title'] }}
                </div>

                <div class="misi-item-desc">
                    {{ $item['desc'] }}
                </div>

            </div>

            @endforeach

        </div>

    </div>

    {{-- Footer --}}
    <div class="page-footer">

        <div class="page-footer-left">
            © {{ date('Y') }}
            {{ $company['name'] }}
            — Semua hak dilindungi undang-undang
        </div>

        <div class="page-footer-right">
            Halaman 3
        </div>

    </div>

</div>


{{-- ================================================
     HALAMAN 4: LAYANAN
     ================================================ --}}
<div class="page">

    <div class="page-header">
        <div class="page-header-logo">
            {{ $company['name'] }}
        </div>

        <div class="page-header-right">
            Company Profile {{ date('Y') }}
            &nbsp;|&nbsp;
            Layanan
        </div>
    </div>

    <div class="section-label">
        Keahlian Kami
    </div>

    <div class="section-title">
        Layanan Kami
    </div>

    <div class="section-divider"></div>

    <div class="section-body">
        Kami menawarkan spektrum penuh layanan manajemen acara yang disesuaikan untuk memenuhi kebutuhan unik setiap klien.
    </div>

    <div class="services-grid">

        @foreach($services as $svc)

        <div class="service-box">

            <div class="service-box-dot">
                {{ $svc['icon'] ?? '★' }}
            </div>

            <div class="service-box-title">
                {{ $svc['title'] }}
            </div>

            <div class="service-box-desc">
                {{ $svc['desc'] }}
            </div>

        </div>

        @endforeach

    </div>

    <div class="page-footer">

        <div class="page-footer-left">
            © {{ date('Y') }}
            {{ $company['name'] }}
            — Semua hak dilindungi undang-undang
        </div>

        <div class="page-footer-right">
            Halaman 4
        </div>

    </div>

</div>


{{-- ================================================
     HALAMAN 5: TIM + KEUNGGULAN
     ================================================ --}}
<div class="page">

    <div class="page-header">

        <div class="page-header-logo">
            {{ $company['name'] }}
        </div>

        <div class="page-header-right">
            Company Profile {{ date('Y') }}
            &nbsp;|&nbsp;
            Tim &amp; Keunggulan
        </div>

    </div>

    {{-- TIM --}}
    <div class="section-label">
        Kenali Lebih Dekat
    </div>

    <div class="section-title">
        Tim Kami
    </div>

    <div class="section-divider"></div>

    <div class="team-table">

        @foreach($team as $member)

        <div class="team-cell">

            <div class="team-avatar">

                @if(!empty($member['img']))
                    <img
                        src="{{ $member['img'] }}"
                        alt="{{ $member['name'] }}">
                @endif

            </div>

            <div class="team-name">
                {{ $member['name'] }}
            </div>

            <div class="team-role">
                {{ $member['role'] }}
            </div>

        </div>

        @endforeach

    </div>

    <br><br>

    {{-- WHY US --}}
    <div class="section-label">
        Mengapa Memilih Kami
    </div>

    <div class="section-title">
        Keunggulan Kami
    </div>

    <div class="section-divider"></div>

    <div class="why-table">

        @foreach($whyUs as $item)

        <div class="why-box">

            <div class="why-title">
                {{ $item['title'] }}
            </div>

            <div class="why-desc">
                {{ $item['desc'] }}
            </div>

        </div>

        @endforeach

    </div>

    <div class="page-footer">

        <div class="page-footer-left">
            © {{ date('Y') }}
            {{ $company['name'] }}
            — Semua hak dilindungi undang-undang
        </div>

        <div class="page-footer-right">
            Halaman 5
        </div>

    </div>

</div>

{{-- ================================================
     HALAMAN 6: PORTOFOLIO
     ================================================ --}}
<div class="page">

    <div class="page-header">
        <div class="page-header-logo">
            {{ $company['name'] }}
        </div>

        <div class="page-header-right">
            Company Profile {{ date('Y') }}
            &nbsp;|&nbsp;
            Portofolio
        </div>
    </div>

    <div class="section-label">
        Karya Terbaik Kami
    </div>

    <div class="section-title">
        Portofolio Proyek
    </div>

    <div class="section-divider"></div>

    <div class="portfolio-grid">

        @php
            $rows = array_chunk($portfolio, 3);
        @endphp

        @foreach($rows as $row)

        <div class="portfolio-row">

            @foreach($row as $item)

            <div class="portfolio-card">

                <div class="portfolio-box">

                    <img
                        src="{{ $item['img'] }}"
                        class="portfolio-img">

                    <div class="portfolio-info">

                        <div class="portfolio-category">
                            {{ $item['cat'] }}
                        </div>

                        <div class="portfolio-name">
                            {{ $item['title'] }}
                        </div>

                    </div>

                </div>

            </div>

            @endforeach

        </div>

        @endforeach

    </div>

    <div class="page-footer">
        <div class="page-footer-left">
            © {{ date('Y') }} {{ $company['name'] }}
        </div>

        <div class="page-footer-right">
            Halaman 6
        </div>
    </div>

</div>

{{-- ================================================
     HALAMAN 7: KLIEN
     ================================================ --}}
<div class="page">

    <div class="page-header">

        <div class="page-header-logo">
            {{ $company['name'] }}
        </div>

        <div class="page-header-right">
            Company Profile {{ date('Y') }}
            &nbsp;|&nbsp;
            Klien Kami
        </div>

    </div>

    <div class="section-label">
        Dipercaya Oleh
    </div>

    <div class="section-title">
        Klien Kami
    </div>

    <div class="section-divider"></div>

    <div class="section-body">
        Berbagai perusahaan, organisasi, dan instansi telah mempercayakan kebutuhan event mereka kepada kami.
    </div>

    <div class="clients-grid">

        @php
            $clientRows = array_chunk($clients, 4);
        @endphp

        @foreach($clientRows as $row)

        <div class="client-row">

            @foreach($row as $client)

            <div class="client-cell">

                <div class="client-box">

                    @if(isset($client['logo']) && file_exists($client['logo']))
                        <img
                            src="{{ $client['logo'] }}"
                            class="client-logo">
                    @endif

                    <div class="client-name">
                        {{ $client['name'] }}
                    </div>

                </div>

            </div>

            @endforeach

        </div>

        @endforeach

    </div>

    <div class="page-footer">

        <div class="page-footer-left">
            © {{ date('Y') }} {{ $company['name'] }}
        </div>

        <div class="page-footer-right">
            Halaman 7
        </div>

    </div>

</div>

{{-- ================================================
     HALAMAN 8: KONTAK
     ================================================ --}}
<div class="page">

    <div class="page-header">

        <div class="page-header-logo">
            {{ $company['name'] }}
        </div>

        <div class="page-header-right">
            Company Profile {{ date('Y') }}
            &nbsp;|&nbsp;
            Kontak
        </div>

    </div>

    <div class="section-label">
        Hubungi Kami
    </div>

    <div class="section-title">
        Mari Diskusikan Event Anda
    </div>

    <div class="section-divider"></div>

    <div class="contact-table">

        {{-- Kiri --}}
        <div class="contact-left">

            <div class="contact-item">
                <div class="contact-item-label">
                    Alamat Kantor
                </div>

                <div class="contact-item-value">
                    {{ $company['address'] }}
                </div>
            </div>

            <div class="contact-item">
                <div class="contact-item-label">
                    Telepon
                </div>

                <div class="contact-item-value">
                    {{ $company['phone'] }}
                </div>
            </div>

            <div class="contact-item">
                <div class="contact-item-label">
                    Email
                </div>

                <div class="contact-item-value">
                    {{ $company['email'] }}
                </div>
            </div>

            <div class="contact-item">
                <div class="contact-item-label">
                    Website
                </div>

                <div class="contact-item-value">
                    {{ $company['website'] }}
                </div>
            </div>

            <div class="contact-item">
                <div class="contact-item-label">
                    Jam Operasional
                </div>

                <div class="contact-item-value">
                    Senin – Jumat, 08.00 – 17.00 WIB
                </div>
            </div>

        </div>

        {{-- Kanan --}}
        <div class="contact-right">

            <div class="section-label">
                Dokumen Dibuat Pada
            </div>

            <br>

            <div class="stat-number" style="font-size:18px;">
                {{ $generatedAt }}
            </div>

            <br><br>

            <div class="section-label">
                Versi Dokumen
            </div>

            <br>

            <div class="contact-item-value">
                v{{ date('Y.m') }}
            </div>

            <br>

            <div style="font-size:9px; color:#8a9bb5; line-height:1.6;">
                Dokumen ini dibuat secara otomatis dari sistem Alpha Organizer
                dan selalu menampilkan informasi terbaru yang tersedia pada
                database perusahaan.
            </div>

        </div>

    </div>

    <div class="page-footer">

        <div class="page-footer-left">
            © {{ date('Y') }}
            {{ $company['name'] }}
            — Semua hak dilindungi undang-undang
        </div>

        <div class="page-footer-right">
            Halaman 6
        </div>

    </div>

</div>

{{-- ================================================
     BACK COVER
     ================================================ --}}
<div class="back-cover">

    <div class="back-cover-logo">
        {{ $company['name'] }}
    </div>

    <div class="back-cover-line"></div>

    <div class="back-cover-text" style="color:#8a9bb5;">

        Terima kasih telah meluangkan waktu untuk membaca
        Company Profile {{ $company['name'] }}.

        <br><br>

        Kami siap menjadi mitra terpercaya dalam mewujudkan
        event yang profesional, kreatif, dan berkesan.

    </div>

    <br><br>

    <div class="back-cover-text"
         style="color:#2dd4bf; font-weight:bold;">

        {{ $company['email'] }}
        &nbsp;|&nbsp;
        {{ $company['phone'] }}

    </div>

    <br>

    <div style="
        font-size:10px;
        color:#8a9bb5;
        margin-top:10px;
    ">
        {{ $company['website'] }}
    </div>

</div>

</body>
</html>

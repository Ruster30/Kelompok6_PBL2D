<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Company Profile - {{ $company['name'] }}</title>
    <style>
    {!! file_get_contents(public_path('css/company-profil-pdf.css')) !!}
    </style>
</head>
<body>
@php
$slideNo = 2;
@endphp

{{-- ═══════════════════════════════════════════════════
     SLIDE 1 — COVER
     ═══════════════════════════════════════════════════ --}}
<div class="slide cover">
    <div class="cover-strip"></div>
    <div class="cover-topbar"></div>
    <div class="cover-circle"></div>
    <div class="cover-circle2"></div>

    <div class="cover-content">
        <div class="cover-bottom">
            <div class="cover-bottom-inner">
                <div class="cover-bottom-left">Company Profile &bull; {{ $company['name'] }}</div>
                <div class="cover-bottom-right">{{ date('Y') }}</div>
            </div>
        </div>
        <div class="cover-inner">
            <div class="cover-eyebrow">Event Organizer Profesional</div>
            <div class="cover-name">{{ $company['name'] }}</div>
            <div class="cover-title-main">Company</div>
            <div class="cover-title-accent">Profile</div>
            <div class="cover-tagline">{{ $company['tagline'] }}</div>

            <div class="cover-stats-row tbl">
                <div class="tr">
                    @foreach($stats as $stat)
                    <div class="td cover-stat-item">
                        <div class="cover-stat-val">{{ $stat['value'] }}</div>
                        <div class="cover-stat-lbl">{{ $stat['label'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     SLIDE 2 — TENTANG KAMI
     ═══════════════════════════════════════════════════ --}}
<div class="slide">
    <div class="slide-lbar"></div>

    <div class="slide-hdr tbl">
        <div class="tr">
            <div class="td slide-hdr-logo">{{ $company['name'] }}</div>
            <div class="td slide-hdr-right">Company Profile {{ date('Y') }} &nbsp;|&nbsp; Tentang Kami</div>
        </div>
    </div>

    {{-- Body 2-kolom: konten kiri, stats kanan --}}
    <div class="slide-body">
        <div class="tbl" style="height: 5.in;">
            <div class="tr">

                {{-- KIRI: teks about --}}
                <div class="td about-left" style="padding-top:5px;">
                    <div class="sec-eyebrow">Tentang Kami</div>
                    <div class="sec-title">{{ $company['about_title'] }}</div>
                    <div class="sec-divider"></div>
                    <div class="sec-body">
                        <p style="margin-bottom:30px;">{{ $company['about_desc1'] }}</p>
                        <p>{{ $company['about_desc2'] }}</p>
                    </div>

                    {{-- Decorative quote strip --}}
                    <div style="margin-top:30px; border-left:4px solid #2dd4bf; padding:14px 18px; background:#f8fafc; border-radius:0 8px 8px 0;">
                        <div style="font-size:25px; color:#4a5568; font-style:italic; line-height:1.7;">
                            "Profesional, Kreatif, dan Berkesan — itulah komitmen kami dalam setiap event."
                        </div>
                    </div>
                </div>

                {{-- KANAN: stat cards 2x2 --}}
                <div class="td about-right">
                    <div class="tbl">
                        <div class="tr">
                            @foreach(array_slice($stats, 0, 2) as $i => $stat)
                                @if($i > 0)<div class="td stat-grid-space"></div>@endif
                                <div class="td">
                                    <div class="stat-card">
                                        <div class="stat-card-val">{{ $stat['value'] }}</div>
                                        <div class="stat-card-lbl">{{ $stat['label'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="tbl">
                        <div class="tr">
                            @foreach(array_slice($stats, 2) as $i => $stat)
                                @if($i > 0)<div class="td stat-grid-space"></div>@endif
                                <div class="td">
                                    <div class="stat-card">
                                        <div class="stat-card-val">{{ $stat['value'] }}</div>
                                        <div class="stat-card-lbl">{{ $stat['label'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Established banner --}}
                    <div style="margin-top:20px; background:#2dd4bf; border-radius:10px; padding:20px; text-align:center;">
                        <div style="font-size:20px; color:#07101f; text-transform:uppercase; letter-spacing:2px; font-weight:bold;">Berdiri Sejak</div>
                        <div style="font-size:35px; font-weight:bold; color:#07101f; margin-top:4px;">2021</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="slide-ftr tbl">
        <div class="tr">
            <div class="td slide-ftr-left">&copy; {{ date('Y') }} {{ $company['name'] }} — Semua hak dilindungi</div>
            <div class="td slide-ftr-right">Slide {{ $slideNo++ }}</div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     SLIDE 3 — VISI & MISI
     ═══════════════════════════════════════════════════ --}}
<div class="slide">
    <div class="slide-lbar"></div>

    <div class="slide-hdr tbl">
        <div class="tr">
            <div class="td slide-hdr-logo">{{ $company['name'] }}</div>
            <div class="td slide-hdr-right">Company Profile {{ date('Y') }} &nbsp;|&nbsp; Visi &amp; Misi</div>
        </div>
    </div>

    <div class="slide-body">
        <div class="sec-eyebrow">Arah Perusahaan</div>
        <div class="sec-title">Visi &amp; Misi Kami</div>
        <div class="sec-divider"></div>

        <div class="tbl" style="margin-top: 100px;">
            <div class="tr">

                <div class="td visi-panel">
                    <div class="sec-eyebrow" style="color:#2dd4bf;">Visi Kami</div>
                    <div class="sec-title" style="color:#fff; font-size:27px; margin-top:8px;">Menjadi yang Terdepan</div>
                    <div class="visi-quote">"{{ $visi }}"</div>
                </div>

                <div class="td vm-gap"></div>

                <div class="td misi-panel">
                    <div class="sec-eyebrow" style="margin-bottom:14px;">Misi Kami</div>
                    @foreach($misi as $item)
                    <div class="misi-item">
                        <div class="misi-item-title">{{ $item['title'] }}</div>
                        <div class="misi-item-desc">{{ $item['desc'] }}</div>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    <div class="slide-ftr tbl">
        <div class="tr">
            <div class="td slide-ftr-left">&copy; {{ date('Y') }} {{ $company['name'] }}</div>
            <div class="td slide-ftr-right">Slide {{ $slideNo++ }}</div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     SLIDE 4 — LAYANAN
     ═══════════════════════════════════════════════════ --}}

@foreach(array_chunk($services, 6) as $slideIndex => $serviceChunk)
<div class="slide">
    <div class="slide-lbar"></div>

    <div class="slide-hdr tbl">
        <div class="tr">
            <div class="td slide-hdr-logo">{{ $company['name'] }}</div>
            <div class="td slide-hdr-right">Company Profile {{ date('Y') }} &nbsp;|&nbsp; Layanan</div>
        </div>
    </div>

    <div class="slide-body">
        <div class="sec-eyebrow">Keahlian Kami</div>
        <div class="sec-title">Layanan Kami</div>
        <div class="sec-divider"></div>
        <div class="sec-body" style="margin-bottom:22px;">
            Kami menawarkan spektrum penuh layanan manajemen acara yang disesuaikan untuk memenuhi kebutuhan unik setiap klien.
        </div>

        @foreach(array_chunk($serviceChunk, 3) as $row)

        <div class="svc-tbl">
            <div class="tr">

                @foreach($row as $svc)

                <div class="td svc-card-wrap">

                    @php
                        $title = strtolower($svc['title'] ?? '');

                        if(str_contains($title,'mice') || str_contains($title,'meeting') || str_contains($title,'convention') || str_contains($title,'exhibition'))
                            $symbol='&#9830;';
                        elseif(str_contains($title,'produksi') || str_contains($title,'production'))
                            $symbol='&#9654;';
                        elseif(str_contains($title,'market') || str_contains($title,'branding') || str_contains($title,'grand opening'))
                            $symbol='&#9650;';
                        elseif(str_contains($title,'special') || str_contains($title,'expo') || str_contains($title,'fashion'))
                            $symbol='&#9733;';
                        elseif(str_contains($title,'corporate') || str_contains($title,'conference'))
                            $symbol='&#9632;';
                        else
                            $symbol='&#9733;';
                    @endphp

                    <div class="svc-card-inner">
                        <div class="svc-card-badge">{!! $symbol !!}</div>
                        <div class="svc-card-accent"></div>
                        <div class="svc-card-title">{{ $svc['title'] }}</div>
                        <div class="svc-card-desc">{{ $svc['desc'] }}</div>
                    </div>

                </div>

                @endforeach

            </div>
        </div>

        <div style="height:18px;"></div>

        @endforeach
    </div>

    <div class="slide-ftr tbl">
        <div class="tr">
            <div class="td slide-ftr-left">&copy; {{ date('Y') }} {{ $company['name'] }}</div>
            <div class="td slide-ftr-right">Slide {{ $slideNo++ }}</div>
        </div>
    </div>
</div>
@endforeach

{{-- ═══════════════════════════════════════════════════
     SLIDE 5 — TIM KAMI (full, no keunggulan)
     ═══════════════════════════════════════════════════ --}}

@foreach(array_chunk($team, 6) as $slideIndex => $teamChunk)
<div class="slide">
    <div class="slide-lbar"></div>

    <div class="slide-hdr tbl">
        <div class="tr">
            <div class="td slide-hdr-logo">{{ $company['name'] }}</div>
            <div class="td slide-hdr-right">Company Profile {{ date('Y') }} &nbsp;|&nbsp; Tim Kami</div>
        </div>
    </div>

    <div class="slide-body">
        <div class="sec-eyebrow">Kenali Lebih Dekat</div>
        <div class="sec-title">Tim Kami</div>
        <div class="sec-divider"></div>
        <div class="sec-body" style="margin-bottom:22px; text-align: center;">
            Dikelola oleh para profesional berpengalaman yang berdedikasi untuk menghadirkan event terbaik bagi setiap klien kami.
        </div>

        <div class="team-grid-wrap tbl">
            @foreach(array_chunk($teamChunk, 3) as $row)
            <div class="tr">
                    @foreach($row as $index => $member)
                    @if($index > 0)<div class="td team-gap"></div>@endif
                    <div class="td team-card">
                        <div class="team-avatar-wrap">
                            @if(!empty($member['img']))
                                <img src="{{ $member['img'] }}" alt="{{ $member['name'] }}">
                            @endif
                        </div>
                        <div class="team-name">{{ $member['name'] }}</div>
                        <div class="team-role">{{ $member['role'] }}</div>
                    </div>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>

    <div class="slide-ftr tbl">
        <div class="tr">
            <div class="td slide-ftr-left">&copy; {{ date('Y') }} {{ $company['name'] }}</div>
            <div class="td slide-ftr-right">Slide {{ $slideNo++ }}</div>
        </div>
    </div>
</div>
@endforeach

{{-- ═══════════════════════════════════════════════════
     SLIDE 6 — KEUNGGULAN KAMI (pisah dari Tim)
     ═══════════════════════════════════════════════════ --}}
@foreach(array_chunk($whyUs, 6) as $slideIndex => $whyUsChunk)
<div class="slide">
    <div class="slide-lbar"></div>

    <div class="slide-hdr tbl">
        <div class="tr">
            <div class="td slide-hdr-logo">{{ $company['name'] }}</div>
            <div class="td slide-hdr-right">Company Profile {{ date('Y') }} &nbsp;|&nbsp; Keunggulan</div>
        </div>
    </div>

    <div class="slide-body">
        <div class="sec-eyebrow">Mengapa Memilih Kami</div>
        <div class="sec-title">Keunggulan Kami</div>
        <div class="sec-divider"></div>
        <div class="sec-body" style="margin-bottom:22px;">
            Kami hadir dengan komitmen penuh untuk memberikan pengalaman event yang tak terlupakan bagi setiap klien.
        </div>

        
        <div class="why-grid-wrap tbl">
            @foreach(array_chunk($whyUsChunk, 3) as $row)
            <div class="tr">
                @foreach($row as $index => $item)
                    @if($index > 0)<div class="td why-gap"></div>@endif
                    <div class="td why-card">
                        <div class="why-num">0{{ $index + 1 }}</div>
                        <div class="why-title">{{ $item['title'] }}</div>
                        <div class="why-desc">{{ $item['desc'] }}</div>
                    </div>
                @endforeach
            </div>
            <div style="height:30px;"></div>
            @endforeach
        </div>
    </div>

    <div class="slide-ftr tbl">
        <div class="tr">
            <div class="td slide-ftr-left">&copy; {{ date('Y') }} {{ $company['name'] }}</div>
            <div class="td slide-ftr-right">Slide {{ $slideNo++ }}</div>
        </div>
    </div>
</div>
@endforeach

{{-- ═══════════════════════════════════════════════════
     SLIDE 7 — PORTOFOLIO PROYEK (3+3 grid, foto besar)
     ═══════════════════════════════════════════════════ --}}
@foreach(array_chunk($portfolio, 6) as $slideIndex => $portfolioChunk)
<div class="slide">
    <div class="slide-lbar"></div>

    <div class="slide-hdr tbl">
        <div class="tr">
            <div class="td slide-hdr-logo">{{ $company['name'] }}</div>
            <div class="td slide-hdr-right">Company Profile {{ date('Y') }} &nbsp;|&nbsp; Portofolio</div>
        </div>
    </div>

    <div class="slide-body" style="padding-top:20px; padding-bottom:10px;">
        <div class="sec-eyebrow">Karya Terbaik Kami</div>
        <div class="sec-title">Portofolio Proyek</div>
        <div class="sec-divider"></div>

        {{-- ROW 1: 3 cards --}}
        <div class="port-tbl">
            @foreach(array_chunk($portfolioChunk,3) as $row)
            <div class="tr">
                @foreach($row as $index => $item)
                    <div class="td port-card">
                        <div class="port-card-inner">
                            <div class="port-img-wrap">
                                <img src="{{ $item['img'] }}" alt="portfolio">
                            </div>
                            <div class="port-info">
                                <div class="port-cat">{{ $item['cat'] }}</div>
                                <div class="port-name">{{ $item['title'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @endforeach
        </div>

    </div>

    <div class="slide-ftr tbl">
        <div class="tr">
            <div class="td slide-ftr-left">&copy; {{ date('Y') }} {{ $company['name'] }}</div>
            <div class="td slide-ftr-right">Slide {{ $slideNo++ }}</div>
        </div>
    </div>
</div>
@endforeach

{{-- ═══════════════════════════════════════════════════
     SLIDE 8 — KLIEN KAMI
═══════════════════════════════════════════════════ --}}

@foreach(array_chunk($clients, 12) as $slideIndex => $clientChunk)
<div class="slide">
    <div class="slide-lbar"></div>

    <div class="slide-hdr tbl">
        <div class="tr">
            <div class="td slide-hdr-logo">{{ $company['name'] }}</div>
            <div class="td slide-hdr-right">Company Profile {{ date('Y') }} &nbsp;|&nbsp; Klien Kami</div>
        </div>
    </div>

    <div class="slide-body">
        <div class="sec-eyebrow">Dipercaya Oleh</div>
        <div class="sec-title">Klien Kami</div>
        <div class="sec-divider"></div>
        <div class="sec-body" style="margin-bottom:28px;">
            Berbagai perusahaan, organisasi, dan instansi telah mempercayakan kebutuhan event mereka kepada kami.
        </div>

        @foreach(array_chunk($clientChunk, 4) as $row)
        <div class="tbl">
            <div class="tr">

                @foreach($row as $i => $client)

                    @if($i > 0)
                        <div class="td client-gap"></div>
                    @endif

                    <div class="td client-card">

                        <div class="client-logo-wrap">
                            @if(isset($client['logo']) && file_exists($client['logo']))
                                <img src="{{ $client['logo'] }}" alt="{{ $client['name'] }}">
                            @else
                                <div style="height:55px;line-height:55px;font-size:22px;color:#2dd4bf;font-weight:bold;">
                                    {{ mb_substr($client['name'], 0, 1) }}
                                </div>
                            @endif
                        </div>

                        <div class="client-name">
                            {{ $client['name'] }}
                        </div>

                    </div>

                @endforeach

            </div>
        </div>

        <div style="height:14px;"></div>

        @endforeach

    </div>

    <div class="slide-ftr tbl">
        <div class="tr">
            <div class="td slide-ftr-left">
                &copy; {{ date('Y') }} {{ $company['name'] }}
            </div>

            <div class="td slide-ftr-right">
                Slide {{ $slideNo++ }}
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- ═══════════════════════════════════════════════════
     SLIDE 9 — KONTAK (split layout)
     ═══════════════════════════════════════════════════ --}}
<div class="slide">
    <div class="tbl">
        <div class="tr">

            {{-- KIRI: dark panel --}}
            <div class="td contact-slide-left">
                <div class="sec-eyebrow">Hubungi Kami</div>
                <div class="sec-title" style="color:#fff; margin-top:8px;">Mari Diskusikan<br>Event Anda</div>
                <div class="sec-divider" style="background:#2dd4bf; margin-top:12px; margin-bottom:28px;"></div>

                <div class="cinfo-item">
                    <div class="cinfo-label">Alamat Kantor</div>
                    <div class="cinfo-val">{{ $company['address'] }}</div>
                </div>
                <div class="cinfo-item">
                    <div class="cinfo-label">Telepon</div>
                    <div class="cinfo-val">{{ $company['phone'] }}</div>
                </div>
                <div class="cinfo-item">
                    <div class="cinfo-label">Email</div>
                    <div class="cinfo-val">{{ $company['email'] }}</div>
                </div>
                <div class="cinfo-item">
                    <div class="cinfo-label">Website</div>
                    <div class="cinfo-val">{{ $company['website'] }}</div>
                </div>

                {{-- Decorative teal bar at bottom --}}
                <div style="position:absolute; bottom:0; left:0; right:0; height:5px; background:#2dd4bf;"></div>
            </div>

            {{-- KANAN: info boxes --}}
            <div class="td contact-slide-right">
                <div class="sec-eyebrow" style="margin-bottom:20px; margin-top:10px;">Informasi Dokumen</div>

                <div class="cright-box">
                    <div class="cright-label">Dokumen Dibuat Pada</div>
                    <div class="cright-val">{{ $generatedAt }}</div>
                </div>

                <div class="cright-box">
                    <div class="cright-label">Versi Dokumen</div>
                    <div class="cright-val">v{{ date('Y.m') }}</div>
                    <div class="cright-note">
                        Dokumen ini dibuat secara otomatis dari sistem {{ $company['name'] }} dan selalu menampilkan informasi terbaru yang tersedia pada database perusahaan.
                    </div>
                </div>

                <div class="cright-box" style="background:#07101f; border-color:#07101f;">
                    <div class="cright-label">Siap Berkolaborasi?</div>
                    <div style="font-size:12px; color:#8a9bb5; margin-top:6px; line-height:1.7;">
                        Tim kami siap membantu mewujudkan event impian Anda. Hubungi kami sekarang dan dapatkan konsultasi gratis.
                    </div>
                    <div style="margin-top:12px; font-size:13px; font-weight:bold; color:#2dd4bf;">
                        {{ $company['email'] }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     SLIDE 10 — BACK COVER
     ═══════════════════════════════════════════════════ --}}
<div class="slide back-cover">
    <div class="back-cover-strip"></div>
    <div class="back-cover-strip-bottom"></div>
    <div class="back-cover-deco"></div>
    <div class="back-cover-deco2"></div>

    <div class="back-cover-center">
        <div class="back-cover-center-inner">
            <div class="back-logo">{{ $company['name'] }}</div>
            <div class="back-tag">Event Organizer Profesional</div>
            <div class="back-line"></div>
            <div class="back-msg">
                Terima kasih telah meluangkan waktu untuk membaca Company Profile {{ $company['name'] }}.<br>
                Kami siap menjadi mitra terpercaya dalam mewujudkan event yang profesional, kreatif, dan berkesan.
            </div>
            <div class="back-contact-row">
                {{ $company['email'] }} &nbsp;&bull;&nbsp; {{ $company['phone'] }}
            </div>
            <div class="back-web">{{ $company['website'] }}</div>
        </div>
    </div>
</div>

</body>
</html>
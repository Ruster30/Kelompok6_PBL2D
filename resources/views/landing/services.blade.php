{{-- resources/views/sections/services.blade.php --}}
<section class="services-section py-6" id="layanan">
    <div class="container">

        {{-- Header --}}
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="services-label d-flex align-items-center justify-content-center gap-2 mb-2">
                <span class="label-line"></span>
                <span class="services-label-text">Keahlian Kami</span>
                <span class="label-line"></span>
            </div>
            <h2 class="services-title fw-bold mb-3">Layanan Kami</h2>
            <p class="services-subtitle mx-auto">
                Kami menawarkan spektrum penuh layanan manajemen acara, yang disesuaikan untuk<br class="d-none d-md-block">
                memenuhi tujuan unik setiap klien.
            </p>
        </div>

        {{-- Cards Grid --}}
        @php
        $services = [
            [
                'icon' => 'bi-people-fill',
                'title' => 'M.I.C.E',
                'desc' => 'Meeting, Incentive, Convention, dan Exhibition yang dirancang profesional untuk kebutuhan perusahaan dan organisasi.'
            ],
            [
                'icon' => 'bi-camera-video-fill',
                'title' => 'Production',
                'desc' => 'Layanan produksi event mulai dari desain grafis, aplikasi pendukung, maintenance service hingga LED Videotron.'
            ],
            [
                'icon' => 'bi-megaphone-fill',
                'title' => 'Marketing',
                'desc' => 'Grand Opening, Activation, Selling Program, dan Branding untuk meningkatkan citra serta jangkauan bisnis Anda.'
            ],
            [
                'icon' => 'bi-stars',
                'title' => 'Special Event',
                'desc' => 'Pengelolaan expo, fashion show, kompetisi, acara virtual, hingga berbagai event spesial lainnya.'
            ],
            [
                'icon' => 'bi-briefcase-fill',
                'title' => 'Corporate Event',
                'desc' => 'Product Launching, Conference Gathering, dan Corporate Meeting dengan konsep profesional dan terstruktur.'
            ],
        ];
        @endphp

        <div class="row g-4 justify-content-center">
            @foreach($services as $i => $service)
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $i * 70 }}">
                <div class="svc-card h-100 p-4 p-md-5">
                    {{-- Icon --}}
                    <div class="svc-icon-wrap mb-4">
                        <i class="bi {{ $service['icon'] }}"></i>
                    </div>
                    {{-- Title --}}
                    <h5 class="svc-title fw-bold mb-3">{{ $service['title'] }}</h5>
                    {{-- Desc --}}
                    <p class="svc-desc mb-0">{{ $service['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</section>
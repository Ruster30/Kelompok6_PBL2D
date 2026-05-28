{{-- resources/views/sections/services.blade.php --}}
<section class="services-section py-6" id="layanan">
    <div class="container">

        {{-- Header --}}
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="services-label d-flex align-items-center justify-content-center gap-2 mb-2">
                <span class="services-label-line"></span>
                <span class="services-label-text">Keahlian Kami</span>
                <span class="services-label-line"></span>
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
                'icon'  => 'bi-briefcase',
                'title' => 'Acara Korporat',
                'desc'  => 'Konferensi, seminar, dan retret perusahaan yang dirancang untuk menginspirasi dan menghubungkan tim Anda.',
            ],
            [
                'icon'  => 'bi-heart',
                'title' => 'Pernikahan',
                'desc'  => 'Perencanaan pernikahan khusus yang mewujudkan hari impian Anda dengan keanggunan dan eksekusi sempurna.',
            ],
            [
                'icon'  => 'bi-mic',
                'title' => 'Konferensi',
                'desc'  => 'Konferensi industri berskala besar dengan manajemen pendaftaran, panggung, dan pembicara yang mulus.',
            ],
            [
                'icon'  => 'bi-send',
                'title' => 'Peluncuran Produk',
                'desc'  => 'Acara peluncuran berdampak tinggi yang menciptakan kehebohan dan meninggalkan kesan mendalam pada audiens Anda.',
            ],
            [
                'icon'  => 'bi-scissors',
                'title' => 'Gala Dinner',
                'desc'  => 'Acara malam yang canggih, upacara penghargaan, dan gala amal dengan katering premium.',
            ],
            [
                'icon'  => 'bi-music-note-list',
                'title' => 'Festival & Konser',
                'desc'  => 'Manajemen ujung ke ujung untuk acara publik besar, mulai dari pemesanan artis hingga pengendalian massa.',
            ],
        ];
        @endphp

        <div class="row g-4">
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
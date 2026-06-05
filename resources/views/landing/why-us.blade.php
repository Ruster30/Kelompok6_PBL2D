{{-- resources/views/sections/why-us.blade.php --}}
<section class="why-section py-6">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <p class="text-accent small fw-semibold text-uppercase tracking-wide mb-2">Keunggulan Kami</p>
            <h2 class="display-5 fw-bold text-white mb-3">Mengapa Memilih Kami</h2>
            <p class="text-light-muted mx-auto" style="max-width:520px;">
                Kami memberikan nilai lebih dari sekadar layanan event biasa. Berikut yang membuat kami berbeda dari yang lain.
            </p>
        </div>

        <div class="row g-4">
            @php
            $features = [
                [
                    'icon'  => 'bi-award-fill',
                    'title' => 'Keahlian dan Pengalaman',
                    'desc'  => 'Tim kami terdiri dari para profesional yang memiliki pengalaman dan keahlian dalam mengelola berbagai jenis event dengan kualitas terbaik.'
                ],
                [
                    'icon'  => 'bi-diagram-3-fill',
                    'title' => 'Komprehensif dan Terintegrasi',
                    'desc'  => 'Menyediakan layanan event organizer yang lengkap dan terintegrasi untuk memenuhi berbagai kebutuhan klien dalam satu solusi.'
                ],
                [
                    'icon'  => 'bi-lightbulb-fill',
                    'title' => 'Kreativitas dan Inovasi',
                    'desc'  => 'Selalu menghadirkan ide-ide baru, konsep kreatif, dan mengikuti tren terkini untuk menciptakan pengalaman event yang berkesan.'
                ],
                [
                    'icon'  => 'bi-shield-check',
                    'title' => 'Integritas dan Etika',
                    'desc'  => 'Menjaga transparansi, kejujuran, dan profesionalisme dalam setiap kerja sama demi membangun kepercayaan klien.'
                ],
                [
                    'icon'  => 'bi-stars',
                    'title' => 'Kualitas Tanpa Kompromi',
                    'desc'  => 'Mengutamakan standar pelayanan tinggi dan perhatian terhadap setiap detail pelaksanaan acara.'
                ],
                [
                    'icon'  => 'bi-emoji-smile-fill',
                    'title' => 'Kepuasan Klien Prioritas',
                    'desc'  => 'Berkomitmen memberikan pelayanan terbaik dan membangun hubungan jangka panjang dengan setiap klien.'
                ],
            ];
            @endphp

            @foreach($features as $i => $f)
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $i * 60 }}">
                <div class="feature-card p-4 rounded-3 h-100">
                    <div class="feature-icon mb-3">
                        <i class="bi {{ $f['icon'] }} fs-4 text-accent"></i>
                    </div>
                    <h5 class="text-white fw-semibold mb-2">{{ $f['title'] }}</h5>
                    <p class="text-light-muted small mb-0">{{ $f['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
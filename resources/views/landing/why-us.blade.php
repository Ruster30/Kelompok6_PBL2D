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
                ['icon' => 'bi-people-fill',        'title' => 'Tim Profesional',        'desc' => 'Tenaga ahli yang telah berpengalaman lebih dari 12 tahun di industri event organizer profesional.'],
                ['icon' => 'bi-calendar-check-fill','title' => 'Pengalaman Luas',         'desc' => 'Tidak terhitung ratusan event sukses yang telah kami tangani di berbagai kota di seluruh Indonesia.'],
                ['icon' => 'bi-bar-chart-fill',     'title' => 'Transparansi Anggaran',   'desc' => 'Setiap pengeluaran anggaran dikelola secara transparan dan akuntabel sesuai kebutuhan klien.'],
                ['icon' => 'bi-cpu-fill',            'title' => 'Teknologi Modern',        'desc' => 'Menggunakan platform dan perangkat manajemen event terkini untuk efisiensi dan hasil terbaik.'],
                ['icon' => 'bi-globe',               'title' => 'Jaringan Investor Luas',  'desc' => 'Memiliki relasi yang kuat dengan ratusan vendor dan sponsor terpercaya di seluruh nusantara.'],
                ['icon' => 'bi-emoji-smile-fill',    'title' => 'Kepuasan Klien',          'desc' => 'Tingkat kepuasan klien yang konsisten tinggi dengan testimoni nyata yang bisa Anda buktikan sendiri.'],
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
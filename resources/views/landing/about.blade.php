{{-- resources/views/sections/about.blade.php --}}
<section class="about-section py-6" id="tentang">
    <div class="container">
        <div class="row align-items-center g-5">
            <!-- Left: Text -->
            <div class="col-lg-6" data-aos="fade-right">
                <p class="text-accent small fw-semibold text-uppercase tracking-wide mb-2">Tentang Kami</p>
                <h2 class="display-5 fw-bold text-white mb-4">
                    Kami Tidak Hanya Merencanakan Event, Kami Menciptakan
                    <span class="text-accent font-playfair fst-italic"> Warisan</span>
                </h2>
                <p class="text-light-muted mb-4">
                    Dengan pengalaman lebih dari 12 tahun di industri event organizer, kami telah membantu ratusan klien mewujudkan impian mereka. Dari acara korporat berskala besar hingga pernikahan mewah yang intim, ALPHA.COM hadir memastikan setiap detail berjalan sempurna.
                </p>
                <p class="text-light-muted mb-5">
                    Tim kami yang berpengalaman dan berdedikasi siap memberikan solusi kreatif dan eksekusi tanpa cela untuk setiap event yang Anda percayakan.
                </p>

                <!-- Stats -->
                <div class="row g-4">
                    <div class="col-6 col-sm-3">
                        <div class="stat-card text-center p-3 rounded-3">
                            <h3 class="fw-bold text-white mb-1 fs-2">500+</h3>
                            <p class="text-accent small mb-0">Event Terselesaikan</p>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="stat-card text-center p-3 rounded-3">
                            <h3 class="fw-bold text-white mb-1 fs-2">98%</h3>
                            <p class="text-accent small mb-0">Klien Puas</p>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="stat-card text-center p-3 rounded-3">
                            <h3 class="fw-bold text-white mb-1 fs-2">12+</h3>
                            <p class="text-accent small mb-0">Tahun Pengalaman</p>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="stat-card text-center p-3 rounded-3">
                            <h3 class="fw-bold text-white mb-1 fs-2">24</h3>
                            <p class="text-accent small mb-0">Kota Terjangkau</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Image + floating card -->
            <div class="col-lg-6 position-relative" data-aos="fade-left">
                <div class="about-img-wrap">
                    <img src="{{ asset('images/about-event.jpg') }}" alt="Event ALPHA.COM"
                         class="img-fluid rounded-4 about-main-img w-100" style="object-fit:cover; height:480px;">
                    <!-- Floating card -->
                    <div class="about-float-card rounded-3 p-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="float-avatar-stack d-flex">
                                <img src="{{ asset('images/team1.jpg') }}" class="float-avatar rounded-circle" alt="">
                                <img src="{{ asset('images/team2.jpg') }}" class="float-avatar rounded-circle" alt="">
                                <img src="{{ asset('images/team3.jpg') }}" class="float-avatar rounded-circle" alt="">
                            </div>
                            <span class="text-white small fw-semibold">Tim Profesional Kami</span>
                        </div>
                        <p class="text-light-muted small mb-0">Penanganan Event</p>
                        <div class="progress mt-2" style="height:6px;">
                            <div class="progress-bar bg-accent" style="width: 92%"></div>
                        </div>
                        <p class="text-accent small mt-1 mb-0 fw-semibold">92% Kepuasan Klien</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mission, Quality, Solution Row -->
        <div class="row g-4 mt-5">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="0">
                <div class="mission-card p-4 rounded-3 h-100">
                    <div class="mission-icon mb-3">
                        <i class="bi bi-bullseye fs-4 text-accent"></i>
                    </div>
                    <h5 class="text-white fw-bold mb-2">Misi Kami</h5>
                    <p class="text-light-muted small mb-0">Menjadi penyedia layanan event organizer terdepan di tingkat global yang selalu mengutamakan inovasi dan kepuasan klien dalam menjalankan setiap momen.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="mission-card p-4 rounded-3 h-100">
                    <div class="mission-icon mb-3">
                        <i class="bi bi-shield-check fs-4 text-accent"></i>
                    </div>
                    <h5 class="text-white fw-bold mb-2">Kualitas Tanpa Kompromi</h5>
                    <p class="text-light-muted small mb-0">Setiap event yang kami tangani melewati proses seleksi ketat, mulai dari vendor hingga dekorasi untuk memastikan standar terbaik.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="mission-card p-4 rounded-3 h-100">
                    <div class="mission-icon mb-3">
                        <i class="bi bi-star fs-4 text-accent"></i>
                    </div>
                    <h5 class="text-white fw-bold mb-2">Kepuasan Klien Prioritas</h5>
                    <p class="text-light-muted small mb-0">Kami menempatkan kepuasan klien sebagai prioritas utama dalam setiap langkah pelaksanaan event, dari konsultasi hingga eksekusi.</p>
                </div>
            </div>
        </div>
    </div>
</section>
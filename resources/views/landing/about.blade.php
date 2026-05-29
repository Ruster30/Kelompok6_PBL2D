{{-- resources/views/sections/about.blade.php --}}
<section class="about-section py-6" id="tentang">
    <div class="container">
        <div class="row align-items-center g-5">
            <!-- Left: Text -->
            <div class="col-lg-6" data-aos="fade-right">
                <p class="text-accent small fw-semibold text-uppercase tracking-wide mb-2">Siapa Kami</p>
                <h2 class="display-5 fw-bold text-white mb-4">
                    Kami Tidak Hanya Merencanakan Event, Kami Menciptakan
                    <span class="text-accent font-playfair fst-italic"> Warisan</span>
                </h2>
                <p class="text-light-muted mb-4">
                    Didirikan dengan prinsip presisi, kreativitas, dan eksekusi tanpa
                    cacat, ALPHA.CORP telah berkembang menjadi agensi
                    manajemen event terkemuka. Kami percaya setiap event
                    menceritakan sebuah kisah, dan kami adalah pencerita
                    utamanya.
                </p>
                <p class="text-light-muted mb-5">
                    Dari retret perusahaan yang intim hingga festival musik berskala
                    besar, tim kami yang berdedikasi menangani setiap detail dengan
                    kualitas tanpa kompromi, memastikan visi Anda terwujud persis
                    seperti yang dibayangkan.
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
                            <p class="text-accent small mb-0">Klien <br> Puas</p>
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
                    <img src="{{ asset('images/landing/aboutimage.png') }}" alt="Event ALPHA.COM"
                         class="img-fluid rounded-4 about-main-img w-100" style="object-fit:cover; height:480px;">
                    <!-- Floating card -->
                    <div class="about-float-card rounded-3 p-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="award-icon">
                                <i class="bi bi-award"></i>
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
    </div>
</section>
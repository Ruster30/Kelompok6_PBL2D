{{-- resources/views/sections/hero.blade.php --}}
<section class="hero-section d-flex align-items-center" id="beranda">
    {{-- Background Carousel --}}
    <div id="heroCarousel"
         class="carousel slide carousel-fade hero-carousel"
         data-bs-ride="carousel"
         data-bs-interval="3000">

        <div class="carousel-inner">

            <div class="carousel-item active">
                <img src="{{ asset('images/landing/hero/hero1.png') }}"
                     class="d-block w-100 hero-bg-img"
                     alt="Hero 1">
            </div>

            <div class="carousel-item">
                <img src="{{ asset('images/landing/hero/hero2.png') }}"
                     class="d-block w-100 hero-bg-img"
                     alt="Hero 2">
            </div>

            <div class="carousel-item">
                <img src="{{ asset('images/landing/hero/hero3.png') }}"
                     class="d-block w-100 hero-bg-img"
                     alt="Hero 3">
            </div>

        </div>
    </div>

    <div class="hero-overlay"></div>
    <div class="container position-relative z-3">
        <div class="row justify-content-center align-items-center min-vh-100 py-5">
            <div class="col-lg-8 col-xl-8 pt-5">
                <p class="text-accent small fw-semibold mb-3 text-center text-uppercase tracking-wide" data-aos="fade-up">Event Organizer Terpercaya</p>
                <h1 class="display-3 fw-bold text-white lh-sm mb-4 mt-5" data-aos="fade-up" data-aos-delay="100">
                    <span>Menciptakan Event yang</span>
                    <span class="text-accent font-playfair fst-italic"> Sempurna</span>
                </h1>
                <p class="text-light-muted fs-5 mb-4 pe-lg-4 text-center" data-aos="fade-up" data-aos-delay="200">
                    Tingkatkan acara korporat, pernikahan, dan festival Anda bersama ALPHA.CORP, kami mengubah visi menjadi pengalaman yang sempurna.
                </p>
                <div class="d-flex gap-3 flex-wrap justify-content-center mt-5" data-aos="fade-up" data-aos-delay="300">
                    <a href="{{ route('login') }}" class="btn btn-accent btn-lg px-4 fw-semibold">
                        Mulai Sekarang <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                    <a href="#portofolio" class="btn btn-outline-light btn-lg px-4 fw-semibold">
                        Lihat Karya Kami
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Scroll indicator -->
    <div class="scroll-indicator">
        <div class="scroll-dot"></div>
    </div>
</section>
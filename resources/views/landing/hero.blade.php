{{-- resources/views/sections/hero.blade.php --}}
<section class="hero-section d-flex align-items-center" id="beranda">
    {{-- Background Carousel --}}
    <style>
        /* Mobile responsive hero */
        @media (max-width: 768px) {
            .hero-section h1.display-3 {
                font-size: 2.2rem !important;
                margin-top: 2rem !important;
            }
            .hero-section .fs-5 {
                font-size: 1rem !important;
            }
            .hero-section .btn-lg {
                font-size: 0.9rem !important;
                padding: 0.5rem 1rem !important;
            }
            .hero-section .min-vh-100 {
                min-height: 90vh !important;
            }
        }
        @media (max-width: 575px) {
            .hero-section h1.display-3 {
                font-size: 1.8rem !important;
            }
        }
        </style>
    <div id="heroCarousel"
         class="carousel slide carousel-fade hero-carousel"
         data-bs-ride="carousel"
         data-bs-interval="3000"
         style="background: linear-gradient(135deg, #091220, #0d1f35);">

        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>

        <div class="carousel-inner">

            <div class="carousel-item active" style="background: linear-gradient(135deg, #091220 0%, #0d1f35 50%, #082030 100%);">
                <img src="{{ asset('images/landing/hero/hero1.png') }}"
                     class="d-block w-100 hero-bg-img"
                     alt="Hero 1"
                     onerror="this.style.display='none'">
            </div>

            <div class="carousel-item" style="background: linear-gradient(135deg, #0d1f35 0%, #091220 50%, #0b2240 100%);">
                <img src="{{ asset('images/landing/hero/hero2.png') }}"
                     class="d-block w-100 hero-bg-img"
                     alt="Hero 2"
                     onerror="this.style.display='none'">
            </div>

            <div class="carousel-item" style="background: linear-gradient(135deg, #082030 0%, #0d1f35 50%, #091220 100%);">
                <img src="{{ asset('images/landing/hero/hero3.png') }}"
                     class="d-block w-100 hero-bg-img"
                     alt="Hero 3"
                     onerror="this.style.display='none'">
            </div>

        </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
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


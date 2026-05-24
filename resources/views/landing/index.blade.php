<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Alpha Event Organizer</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    {{-- Custom CSS --}}
    <link rel="stylesheet"
          href="{{ asset('css/landing.css') }}">
</head>

<body class="bg-light">

    {{-- Navbar --}}
    @include('components.navbar')

    {{-- Hero --}}
    <section id="beranda"
             class="hero-section d-flex align-items-center text-center text-white">

        <div class="container">

            <div class="row justify-content-center">

                <div class="col-lg-10">

                    <h1 class="hero-title">
                        Menciptakan Event yang
                        <span class="text-teal">
                            Tak Terlupakan
                        </span>
                    </h1>

                    <p class="hero-subtitle mt-4 mx-auto">
                        Tingkatkan acara korporat,
                        pernikahan, dan festival Anda
                        bersama ALPHA.CORP.
                        Kami mengubah visi menjadi
                        pengalaman luar biasa
                        yang sempurna.
                    </p>

                    <div class="d-flex
                                justify-content-center
                                gap-3
                                flex-wrap
                                mt-5">

                        <a href="#contact"
                           class="btn btn-alpha px-5 py-3">
                            Mulai Sekarang →
                        </a>

                        <a href="#portfolio"
                           class="btn btn-dark-custom px-5 py-3">
                            Lihat Karya Kami
                        </a>

                    </div>

                </div>
            </div>
        </div>
    </section>

    {{-- Tentang --}}
    <section id="tentang" class="py-5">
        <div class="container">

            <div class="text-center">
                <h2 class="fw-bold">
                    Tentang Kami
                </h2>
            </div>

        </div>
    </section>

    {{-- Layanan --}}
    <section id="layanan" class="py-5 bg-white">
        <div class="container">

            <div class="text-center">
                <h2 class="fw-bold">
                    Layanan Kami
                </h2>
            </div>

        </div>
    </section>

    {{-- Portofolio --}}
    <section id="portfolio" class="py-5">
        <div class="container">

            <div class="text-center">
                <h2 class="fw-bold">
                    Portofolio
                </h2>
            </div>

        </div>
    </section>

    {{-- Tim --}}
    <section id="tim" class="py-5 bg-white">
        <div class="container">

            <div class="text-center">
                <h2 class="fw-bold">
                    Tim Kami
                </h2>
            </div>

        </div>
    </section>

    {{-- Contact --}}
    <section id="contact" class="py-5">
        <div class="container">

            <div class="text-center">
                <h2 class="fw-bold">
                    Hubungi Kami
                </h2>
            </div>

        </div>
    </section>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
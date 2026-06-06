{{-- resources/views/components/navbar.blade.php --}}
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNavbar">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="{{ asset('images/landing/logo.png') }}"
                alt="Alpha Organizer"
                class="navbar-logo">
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto gap-lg-2">
                <li class="nav-item">
                    <a class="nav-link" href="#beranda">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#layanan">Layanan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#tim">Tim</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#portofolio">Portofolio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#tentang">Tentang</a>
                </li>
            </ul>

            <div class="d-flex gap-2 mt-3 mt-lg-0">
                <a href="#" class="btn btn-outline-light btn-sm px-3">Masuk</a>
                <a href="#kontak" class="btn btn-accent btn-sm px-3">Hubungi Kami</a>
            </div>
        </div>
    </div>
</nav>
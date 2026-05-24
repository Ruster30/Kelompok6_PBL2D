<nav class="navbar navbar-expand-lg navbar-alpha fixed-top">
    <div class="container">

        {{-- logo kiri --}}
        <a class="navbar-brand d-flex align-items-center p-0 m-0"
           href="#beranda">

            <img src="{{ asset('images/landing/logo.jpex`g') }}"
                 alt="Alpha Event"
                 class="logo-alpha">
        </a>

        {{-- mobile --}}
        <button class="navbar-toggler border-0 shadow-none"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarMenu">

            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse"
             id="navbarMenu">

            {{-- tengah --}}
            <ul class="navbar-nav mx-auto nav-center">

                <li class="nav-item">
                    <a class="nav-link nav-alpha-link"
                       href="#beranda">
                        Beranda
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link nav-alpha-link"
                       href="#tentang">
                        Tentang
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link nav-alpha-link"
                       href="#layanan">
                        Layanan
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link nav-alpha-link"
                       href="#portfolio">
                        Portofolio
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link nav-alpha-link"
                       href="#tim">
                        Tim
                    </a>
                </li>

            </ul>

            {{-- kanan --}}
            <div class="d-flex align-items-center nav-right">

                <a href="/login"
                   class="login-alpha">
                    Masuk
                </a>

                <a href="#contact"
                   class="btn btn-alpha-navbar">
                    Mulai Sekarang
                </a>

            </div>

        </div>
    </div>
</nav>
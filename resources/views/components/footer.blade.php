{{-- resources/views/components/footer.blade.php --}}
<footer class="footer-main pt-5 pb-3">
    <div class="container">
        <!-- CTA Band -->
        <div class="footer-cta-band rounded-3 p-5 mb-5 text-center text-white" data-aos="fade-up">
            <p class="text-accent small fw-semibold mb-2 text-uppercase tracking-wide">Hubungi Kami</p>
            <h2 class="fw-bold mb-3">Siap Membuat Sesuatu yang Luar Biasa?</h2>
            <p class="text-light-muted mb-4">Mari diskusikan event Anda bersama kami. Tim ahli kami siap menghadirkan pengalaman yang tak terlupakan untuk setiap momen Anda.</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ route('register') }}" class="btn btn-accent px-4 py-2 fw-semibold">Ajukan Event</a>
                <a href="#" class="btn btn-outline-light px-4 py-2 fw-semibold">Lihat Katalog</a>
            </div>
        </div>

        <!-- Download Profile -->
        <div class="text-center mb-5" data-aos="fade-up">
            <p class="text-light-muted small mb-2">Dapatkan profil perusahaan kami lengkap dengan layanan dan portofolio terbaik kami.</p>
            <a href="{{ route('company-profile.pdf') }}" class="btn btn-outline-accent px-4 py-2 fw-semibold">
                <i class="bi bi-download me-2"></i>Unduh Company Profile
            </a>
        </div>

        <hr class="footer-divider mb-5">

        <!-- Footer Links -->
        <div class="row g-4 mb-5">
            <div class="col-lg-4 col-md-6">
                <a class="navbar-brand fw-bold fs-4 text-decoration-none mb-3 d-inline-block" href="#">
                    <span class="text-white">ALPHA</span><span class="text-accent">.</span><span class="text-white">COM</span>
                </a>
                <p class="text-light-muted small mb-3">Kami adalah perusahaan event organizer profesional yang berdedikasi menghadirkan pengalaman tak terlupakan untuk setiap momen spesial Anda.</p>
                <div class="d-flex gap-2">
                    <a href="https://www.instagram.com/alphaorganizer.co?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" class="social-icon"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="social-icon"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="social-icon"><i class="bi bi-linkedin"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-6 col-6">
                <h6 class="text-white fw-semibold mb-3">Layanan</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="#">M.I.C.E</a></li>
                    <li><a href="#">Production</a></li>
                    <li><a href="#">Marketing</a></li>
                    <li><a href="#">Special Event</a></li>
                    <li><a href="#">Corporat event</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-6 col-6">
                <h6 class="text-white fw-semibold mb-3">Perusahaan</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="#">Tentang Kami</a></li>
                    <li><a href="#">Tim Kami</a></li>
                    <li><a href="#">Portofolio</a></li>
                    <li><a href="#">Karir</a></li>
                    <li><a href="#">Blog</a></li>
                </ul>
            </div>

            <div class="col-lg-4 col-md-6">
                <h6 class="text-white fw-semibold mb-3">Hubungi Kami</h6>
                <ul class="list-unstyled footer-contact">
                    <li>
                        <i class="bi bi-geo-alt-fill text-accent me-2"></i>
                        <span class="text-light-muted small">Jl.Kenangan Air Dingin No.25, Kec Koto Tangah, Kota Padang</span>
                    </li>
                    <li>
                        <i class="bi bi-telephone-fill text-accent me-2"></i>
                        <span class="text-light-muted small">+62 822 3318 1883</span>
                    </li>
                    <li>
                        <i class="bi bi-telephone-fill text-accent me-2"></i>
                        <span class="text-light-muted small">+62 895 4013 000 22</span>
                    </li>
                    <li>
                        <i class="bi bi-telephone-fill text-accent me-2"></i>
                        <span class="text-light-muted small">+62 852 6334 6575</span>
                    </li>
                    <li>
                        <i class="bi bi-envelope-fill text-accent me-2"></i>
                        <span class="text-light-muted small">alphaorganizer1209@gmail.com </span>
                    </li>
                </ul>
            </div>
        </div>

        <hr class="footer-divider mb-3">

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <p class="text-light-muted small mb-0">&copy; {{ date('Y') }} ALPHA.CORP. Semua hak dilindungi undang-undang.</p>
            <div class="d-flex gap-3">
                <a href="#" class="text-light-muted small text-decoration-none">Kebijakan Privasi</a>
                <a href="#" class="text-light-muted small text-decoration-none">Syarat & Ketentuan</a>
            </div>
        </div>
    </div>
</footer>
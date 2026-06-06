{{-- resources/views/sections/visi-misi.blade.php --}}
{{-- Include section ini di landing.blade.php setelah about:
     @include('sections.visi-misi')
--}}

<section class="visi-misi-section py-6" id="visi-misi">
    <div class="container">
        <div class="row align-items-center g-5">

            {{-- ======== KIRI: Card Visi ======== --}}
            <div class="col-lg-5" data-aos="fade-right">
                <div class="visi-card rounded-4 p-4 p-md-5">
                    {{-- Icon --}}
                    <div class="visi-icon-wrap mb-4">
                        <i class="bi bi-eye fs-4"></i>
                    </div>

                    <h3 class="fw-bold text-white mb-4">Visi Kami</h3>

                    <p class="visi-text mb-0">
                        "Menjadi perusahaan terkemuka yang memberikan solusi dan layanan terbaik
                        dalam bidang Jasa Event Organizer, Exhibition, Convention dan Musical
                        Entertainment.
                        Kami bertujuan untuk menjadi mitra yang terpercaya dan bisa diandalkan
                        dalam mencapai kesuksesan dan perkembangan bisnis klien kami."
                    </p>
                </div>
            </div>

            {{-- ======== KANAN: Misi + List ======== --}}
            <div class="col-lg-7" data-aos="fade-left">
                {{-- Judul Misi --}}
                <div class="d-flex align-items-center gap-3 mb-5">
                    <div class="misi-title-icon">
                        <i class="bi bi-bullseye fs-5"></i>
                    </div>
                    <h3 class="fw-bold mb-0" style="color:#1a2540;">Misi Kami</h3>
                </div>

                {{-- List Misi --}}
                <div class="d-flex flex-column gap-4">

                    {{-- Item 1 --}}
                    <div class="misi-item d-flex align-items-start gap-3" data-aos="fade-up" data-aos-delay="100">
                        <div class="misi-item-icon flex-shrink-0">
                            <i class="bi bi-stars fs-6"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1" style="color:#1a2540;">Solusi Kreatif</h6>
                            <p class="mb-0" style="color:#6b7a99; font-size:.9rem; line-height:1.65;">
                                Memberikan solusi yang inovatif dan kreatif.
                            </p>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="misi-divider"></div>

                    {{-- Item 2 --}}
                    <div class="misi-item d-flex align-items-start gap-3" data-aos="fade-up" data-aos-delay="200">
                        <div class="misi-item-icon flex-shrink-0">
                            <i class="bi bi-lightning-charge fs-6"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1" style="color:#1a2540;">Layanan Berkualitas</h6>
                            <p class="mb-0" style="color:#6b7a99; font-size:.9rem; line-height:1.65;">
                                Memberikan layanan berkualitas tinggi yang memenuhi kebutuhan dan harapan klien.
                            </p>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="misi-divider"></div>

                    {{-- Item 3 --}}
                    <div class="misi-item d-flex align-items-start gap-3" data-aos="fade-up" data-aos-delay="300">
                        <div class="misi-item-icon flex-shrink-0">
                            <i class="bi bi-heart fs-6"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1" style="color:#1a2540;">Pengalaman Tak Terlupakan</h6>
                            <p class="mb-0" style="color:#6b7a99; font-size:.9rem; line-height:1.65;">
                                Menciptakan pengalaman yang berkesan bagi setiap klien.
                            </p>
                        </div>
                    </div>
                    {{-- Divider --}}
                    <div class="misi-divider"></div>

                    {{-- Item 4 --}}
                    <div class="misi-item d-flex align-items-start gap-3" data-aos="fade-up" data-aos-delay="300">
                        <div class="misi-item-icon flex-shrink-0">
                            <i class="bi bi-heart fs-6"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1" style="color:#1a2540;">Standar Profesional</h6>
                            <p class="mb-0" style="color:#6b7a99; font-size:.9rem; line-height:1.65;">
                                Menyediakan produk dan layanan berkualitas terbaik.
                            </p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>
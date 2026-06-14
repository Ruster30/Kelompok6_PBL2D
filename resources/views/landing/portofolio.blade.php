{{-- resources/views/sections/portfolio.blade.php --}}
<section class="portfolio-section py-6" id="portofolio">
    <div class="container">

        {{-- ===== Header + Filter (inline seperti screenshot) ===== --}}
        <div class="pf-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4 mb-5" data-aos="fade-up">

            {{-- Kiri: Label + Judul --}}
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="pf-label-line"></span>
                    <span class="pf-label-text">Portofolio Kami</span>
                </div>
                <h2 class="pf-title fw-bold mb-0">Portofolio Proyek</h2>
            </div>

            {{-- Kanan: Filter pills --}}
            <div class="pf-filters d-flex flex-wrap gap-2">
                <button class="pf-btn active" data-filter="all">Semua</button>
                <button class="pf-btn" data-filter="korporat">Korporat</button>
                <button class="pf-btn" data-filter="pernikahan">Pernikahan</button>
                <button class="pf-btn" data-filter="konser">Konser</button>
                <button class="pf-btn" data-filter="peluncuran">Peluncuran</button>
                <button class="pf-btn" data-filter="gala">Gala</button>
            </div>
        </div>


        <div class="row g-4" id="portfolioGrid">
            @foreach($portfolios as $i => $item)
            @php $filter = strtolower(str_replace(' ', '-', $item->kategori)); @endphp
            <div class="col-md-6 col-lg-4 pf-item" data-filter="{{ $filter }}" data-aos="fade-up" data-aos-delay="{{ $i * 70 }}">
                <div class="pf-card rounded-4 overflow-hidden position-relative">
                    <img
                        src="{{ asset('images/landing/portofolio/' . ($item->gambar ?? 'portofolio'.(($i%6)+1).'.png')) }}"
                        alt="{{ $item->judul }}"
                        class="pf-img w-100"
                        style="height:260px; object-fit:cover; display:block;">
                    {{-- Overlay on hover --}}
                    <div class="pf-overlay">
                        <span class="pf-badge">{{ $item->kategori }}</span>
                        <h6 class="pf-overlay-title">{{ $item->judul }}</h6>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Empty state --}}
        <div class="pf-empty text-center py-5 d-none" id="pfEmpty">
            <i class="bi bi-image fs-1 mb-3" style="color:#cbd5e0;"></i>
            <p style="color:#8a9bb5;">Belum ada proyek dalam kategori ini.</p>
        </div>

        {{-- CTA Button --}}
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="#" class="pf-cta-btn">Lihat Semua Proyek</a>
        </div>

    </div>
</section>

@push('scripts')
<script>
(function () {
    const btns  = document.querySelectorAll('.pf-btn');
    const items = document.querySelectorAll('.pf-item');
    const empty = document.getElementById('pfEmpty');

    btns.forEach(btn => {
        btn.addEventListener('click', function () {
            // Active state
            btns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const filter = this.dataset.filter;
            let visible = 0;

            items.forEach(item => {
                const match = filter === 'all' || item.dataset.filter === filter;
                if (match) {
                    item.classList.remove('pf-hidden');
                    visible++;
                } else {
                    item.classList.add('pf-hidden');
                }
            });

            // Toggle empty state
            empty.classList.toggle('d-none', visible > 0);
        });
    });
})();
</script>
@endpush
{{-- resources/views/sections/team.blade.php --}}
<section class="team-section py-6" id="tim">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                <span class="label-line"></span>
                <p class="text-accent small fw-semibold text-uppercase tracking-wide mb-2">Kenali Lebih Dekat</p>
                <span class="label-line"></span>
            </div>
            
            <h2 class="display-5 fw-bold text-white mb-3">Tim Kami</h2>
            <p class="text-light-muted mx-auto" style="max-width:520px;">
                Kami memperkenalkan sosok-sosok berbakat di balik kesuksesan setiap event yang dikelola oleh ALPHA.COM.
            </p>
        </div>

        <div class="row g-4 justify-content-center">
            @foreach($teams as $i => $member)
            <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                <div class="team-card rounded-3 overflow-hidden text-center">
                    <div class="team-img-wrap position-relative overflow-hidden">
                        <img src="{{ asset('images/landing/' . ($member->foto ?? 'team/team'.(($i%6)+1).'.png')) }}"
                             alt="{{ $member->nama }}"
                             class="img-fluid w-100 team-img"
                             style="height:280px; object-fit:cover; object-position: center top;">
                        <div class="team-overlay d-flex align-items-center justify-content-center gap-2">
                            <a href="#" target="_blank" class="team-social-btn"><i class="bi bi-linkedin"></i></a>
                            <a href="#" target="_blank" class="team-social-btn"><i class="bi bi-instagram"></i></a>
                        </div>
                    </div>
                    <div class="team-info p-3">
                        <h6 class="text-white fw-bold mb-1">{{ $member->nama }}</h6>
                        <p class="text-accent small mb-0">{{ $member->jabatan }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
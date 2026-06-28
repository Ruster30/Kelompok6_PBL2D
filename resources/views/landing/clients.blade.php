{{-- resources/views/sections/clients.blade.php --}}
<section class="clients-section py-5">
    <div class="container">
        <div class="text-center mb-4" data-aos="fade-up">
            <p class="text-accent small fw-semibold text-uppercase tracking-wide mb-2">Klien Kami</p>
            <p class="text-light-muted">Dipercaya oleh perusahaan-perusahaan dan brand ternama di indonesia.</p>
        </div>
    </div>
        <div class="clients-track-wrap overflow-hidden" data-aos="fade-up">
            <div class="clients-track d-flex align-items-center gap-5">
                @foreach($clients as $client)
                <div class="client-logo-item flex-shrink-0">
                    <img
                    src="{{ asset('images/landing/clients/' . $client->logo) }}"
                    alt="{{ $client->nama_client }}"
                    class="client-logo-img"
                    style="max-width:120px; max-height:50px; width:auto; height:auto; object-fit:contain;">
                </div>
                @endforeach
                @foreach($clients as $client)
                <div class="client-logo-item flex-shrink-0">
                    <img
                    src="{{ asset('images/landing/clients/' . $client->logo) }}"
                    alt="{{ $client->nama_client }}"
                    class="client-logo-img"
                    style="max-width:120px; max-height:50px; width:auto; height:auto; object-fit:contain;">
                </div>
                @endforeach

            </div>
        </div>
</section>
{{-- resources/views/sections/clients.blade.php --}}
<section class="clients-section py-5">
    <div class="container">
        <div class="text-center mb-4" data-aos="fade-up">
            <p class="text-accent small fw-semibold text-uppercase tracking-wide mb-2">Klien Kami</p>
            <p class="text-light-muted">Dipercaya oleh perusahaan-perusahaan dan brand ternama di indonesia.</p>
        </div>

        <div class="clients-track-wrap overflow-hidden" data-aos="fade-up">
            <div class="clients-track d-flex align-items-center gap-5">
                @php
                $clients = [
                    ['logo' => 'colony', 'name' => 'Colony'],
                    ['logo' => 'citilink', 'name' => 'Citilink'],
                    ['logo' => 'yamaha', 'name' => 'Yamaha'],
                    ['logo' => 'lenovo', 'name' => 'Lenovo'],
                    ['logo' => 'pos-indonesia', 'name' => 'Pos Indonesia'],
                    ['logo' => 'bri', 'name' => 'Bank BRI'],
                    ['logo' => 'hyundai', 'name' => 'Hyundai'],
                    ['logo' => 'honda', 'name' => 'Honda'],
                    ['logo' => 'nissan', 'name' => 'Nissan'],
                    ['logo' => 'rexvin', 'name' => 'Rexvin'],
                    ['logo' => 'dofla-jaya-properti', 'name' => 'Dofla Jaya Properti'],
                    ['logo' => 'motul', 'name' => 'Motul'],
                    ['logo' => 'iqos', 'name' => 'IQOS'],
                    ['logo' => 'toyota', 'name' => 'Toyota'],
                    ['logo' => 'mandiri', 'name' => 'Bank Mandiri'],
                    ['logo' => 'telkomsel', 'name' => 'Telkomsel'],
                    ['logo' => 'xxi', 'name' => 'Cinema XXI'],
                    ['logo' => 'hokben', 'name' => 'HokBen'],
                    ['logo' => 'tri', 'name' => 'Tri'],
                    ['logo' => 'makeover', 'name' => 'Make Over'],
                    ['logo' => 'red-modani', 'name' => 'Red Modani'],
                    ['logo' => 'wuling', 'name' => 'Wuling'],
                    ['logo' => 'transmart', 'name' => 'Transmart'],
                    ['logo' => 'huawei', 'name' => 'Huawei'],
                ];
                @endphp

                @foreach($clients as $client)
                <div class="client-logo-item flex-shrink-0">
                    <img src="{{ asset('images/landing/clients/' . $client['logo'] . '.png') }}"
                         alt="{{ $client['name'] }}"
                         class="client-logo-img"
                         style="max-width:120px; max-height:50px; width:auto; height:auto; object-fit:contain;">
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
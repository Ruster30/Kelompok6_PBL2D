{{-- resources/views/sections/clients.blade.php --}}
<section class="clients-section py-5">
    <div class="container">
        <div class="text-center mb-4" data-aos="fade-up">
            <p class="text-accent small fw-semibold text-uppercase tracking-wide mb-2">Klien Kami</p>
            <p class="text-light-muted">Dipercaya oleh perusahaan-perusahaan dan brand ternama di indonesia.</p>
        </div>

        <div class="clients-track-wrap overflow-hidden" data-aos="fade-up">
            <div class="clients-track d-flex align-items-center gap-5">
                @if(!isset($clients) || (is_object($clients) && $clients->isEmpty()))
                    @php
                    $clients = collect([
                        (object)['logo' => 'colony', 'nama_client' => 'Colony'],
                        (object)['logo' => 'citilink', 'nama_client' => 'Citilink'],
                        (object)['logo' => 'yamaha', 'nama_client' => 'Yamaha'],
                        (object)['logo' => 'lenovo', 'nama_client' => 'Lenovo'],
                        (object)['logo' => 'pos-indonesia', 'nama_client' => 'Pos Indonesia'],
                        (object)['logo' => 'bri', 'nama_client' => 'Bank BRI'],
                        (object)['logo' => 'hyundai', 'nama_client' => 'Hyundai'],
                        (object)['logo' => 'honda', 'nama_client' => 'Honda'],
                        (object)['logo' => 'nissan', 'nama_client' => 'Nissan'],
                        (object)['logo' => 'rexvin', 'nama_client' => 'Rexvin'],
                        (object)['logo' => 'dofla-jaya-properti', 'nama_client' => 'Dofla Jaya Properti'],
                        (object)['logo' => 'motul', 'nama_client' => 'Motul'],
                        (object)['logo' => 'iqos', 'nama_client' => 'IQOS'],
                        (object)['logo' => 'toyota', 'nama_client' => 'Toyota'],
                        (object)['logo' => 'mandiri', 'nama_client' => 'Bank Mandiri'],
                        (object)['logo' => 'telkomsel', 'nama_client' => 'Telkomsel'],
                        (object)['logo' => 'xxi', 'nama_client' => 'Cinema XXI'],
                        (object)['logo' => 'hokben', 'nama_client' => 'HokBen'],
                        (object)['logo' => 'tri', 'nama_client' => 'Tri'],
                        (object)['logo' => 'makeover', 'nama_client' => 'Make Over'],
                        (object)['logo' => 'red-modani', 'nama_client' => 'Red Modani'],
                        (object)['logo' => 'wuling', 'nama_client' => 'Wuling'],
                        (object)['logo' => 'transmart', 'nama_client' => 'Transmart'],
                        (object)['logo' => 'huawei', 'nama_client' => 'Huawei'],
                    ]);
                    @endphp
                @endif

                @foreach($clients as $client)
                <div class="client-logo-item flex-shrink-0">
                    @php
                        $logoName = $client->logo ?? strtolower(str_replace(' ', '-', $client->nama_client));
                    @endphp
                    <img src="{{ asset('images/landing/clients/' . $logoName . '.png') }}"
                         alt="{{ $client->nama_client }}"
                         class="client-logo-img"
                         style="max-width:120px; max-height:50px; width:auto; height:auto; object-fit:contain;">
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
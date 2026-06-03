{{-- resources/views/sections/clients.blade.php --}}
<section class="clients-section py-5">
    <div class="container">
        <div class="text-center mb-4" data-aos="fade-up">
            <p class="text-accent small fw-semibold text-uppercase tracking-wide mb-2">Klien Kami</p>
            <p class="text-light-muted">Dipercaya oleh perusahaan-perusahaan terkemuka dan brand ternama di seluruh dunia.</p>
        </div>

        <div class="clients-track-wrap overflow-hidden" data-aos="fade-up">
            <div class="clients-track d-flex align-items-center gap-5">
                @php
                $clients = [
                    ['logo' => 'amazon',    'name' => 'Amazon'],
                    ['logo' => 'google',    'name' => 'Google'],
                    ['logo' => 'microsoft', 'name' => 'Microsoft'],
                    ['logo' => 'ibm',       'name' => 'IBM'],
                    ['logo' => 'oracle',    'name' => 'Oracle'],
                    // Duplicate for seamless scroll
                    ['logo' => 'amazon',    'name' => 'Amazon'],
                    ['logo' => 'google',    'name' => 'Google'],
                    ['logo' => 'microsoft', 'name' => 'Microsoft'],
                    ['logo' => 'ibm',       'name' => 'IBM'],
                    ['logo' => 'oracle',    'name' => 'Oracle'],
                ];
                @endphp

                @foreach($clients as $client)
                <div class="client-logo-item flex-shrink-0">
                    <img src="{{ asset('images/landing/clients/' . $client['logo'] . '.png') }}"
                         alt="{{ $client['name'] }}"
                         class="client-logo-img"
                         style="height:32px; object-fit:contain;">
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
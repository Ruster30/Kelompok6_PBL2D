<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\LandingSection;
use App\Models\AboutSection;
use App\Models\AboutStatistic;
use App\Models\Service;
use App\Models\Portfolio;
use App\Models\Client;
use App\Models\Team;

class CompanyProfileController extends Controller
{
    /**
     * Generate Company Profile PDF.
     * Data diambil langsung dari konten landing page,
     * sehingga PDF selalu mengikuti perubahan terbaru.
     */
    public function downloadPdf()
    {
        // ====== DATA PROFIL PERUSAHAAN ======
        $company = [
            'name'    => 'ALPHA Organizer',
            'tagline' => 'Menciptakan Event yang Sempurna',
            'about_title' => 'Kami Tidak Hanya Merencanakan Event, Kami Menciptakan Warisan',
            'about_desc1'   => 'Alpha Organizer adalah perusahaan Event Organizer profesional yang bergerak di bidang Event Organizer, Exhibition, Convention, dan Musical Entertainment. Sejak berdiri, kami telah dipercaya menangani berbagai acara dengan standar kualitas tinggi dan reputasi yang kuat di industri event.',
            'about_desc2'   => 'Didukung oleh tim yang berpengalaman, kreatif, dan berdedikasi, kami berkomitmen menghadirkan solusi terbaik untuk setiap kebutuhan klien. Dengan mengedepankan profesionalisme, inovasi, dan integritas, kami menciptakan pengalaman yang berkesan serta memastikan setiap acara berjalan sukses sesuai harapan.',
            'email'   => 'alphaorganizer1209@gmail.com',
            'phone'   => '+62 822 3318 1883',
            'address' => 'JL. Kenangan Air Dingin No.25, Kec Koto Tangah, Kota Padang',
            'website' => 'www.alphaorganizer.com',
        ];

        $stats = [
            ['value' => '50+',  'label' => 'Event Terselesaikan'],
            ['value' => '98%',  'label' => 'Klien Puas'],
            ['value' => '3+',   'label' => 'Tahun Pengalaman'],
            ['value' => '1',    'label' => 'Kota Operasional'],
        ];

        $visi = 'Menjadi perusahaan terkemuka yang memberikan solusi dan layanan terbaik dalam bidang Jasa Event Organizer, Exhibition, Convention dan Musical Entertainment. Kami bertujuan untuk menjadi mitra yang terpercaya dan bisa diandalkan dalam mencapai kesuksesan dan perkembangan bisnis klien kami.';

        $misi = [
            ['title' => 'Solusi Kreatif',           'desc' => 'Memberikan solusi yang inovatif dan kreatif.'],
            ['title' => 'Layanan Berkualitas',       'desc' => 'Memberikan layanan berkualitas tinggi yang memenuhi kebutuhan dan harapan klien.'],
            ['title' => 'Pengalaman Tak Terlupakan', 'desc' => 'Menciptakan pengalaman yang berkesan bagi setiap klien.'],
            ['title' => 'Standar Profesional',       'desc' => 'Menyediakan produk dan layanan berkualitas terbaik.'],
        ];

        $whyUs = [
            ['title' => 'Keahlian dan Pengalaman',      'desc' => 'Tim kami terdiri dari para profesional yang memiliki pengalaman dan keahlian dalam mengelola berbagai jenis event dengan kualitas terbaik.'],
            ['title' => 'Komprehensif dan Terintegrasi', 'desc' => 'Menyediakan layanan event organizer yang lengkap dan terintegrasi untuk memenuhi berbagai kebutuhan klien dalam satu solusi.'],
            ['title' => 'Kreativitas dan Inovasi',       'desc' => 'Selalu menghadirkan ide-ide baru, konsep kreatif, dan mengikuti tren terkini untuk menciptakan pengalaman event yang berkesan.'],
            ['title' => 'Integritas dan Etika',          'desc' => 'Menjaga transparansi, kejujuran, dan profesionalisme dalam setiap kerja sama demi membangun kepercayaan klien.'],
            ['title' => 'Kualitas Tanpa Kompromi',       'desc' => 'Mengutamakan standar pelayanan tinggi dan perhatian terhadap setiap detail pelaksanaan acara.'],
            ['title' => 'Kepuasan Klien Prioritas',      'desc' => 'Berkomitmen memberikan pelayanan terbaik dan membangun hubungan jangka panjang dengan setiap klien.'],
        ];

        $services = [];
        $services2 = [];
                $servicesQuery = Service::where('is_active', true)->orderBy('urutan')->get();
                if ($servicesQuery->isNotEmpty()) {
                    $servicesAll = $servicesQuery->map(function($s) { return ['icon'  => $s->icon ?? '★', 'title' => $s->nama_layanan, 'desc' => $s->deskripsi]; })->toArray();
                    $services  = array_slice($servicesAll, 0, 5);
                    $services2 = array_slice($servicesAll, 5, 3);
                } 

        $teamQuery = Team::where('is_active', true)->orderBy('urutan')->get();
        if ($teamQuery->isNotEmpty()) {
            $team = $teamQuery->map(function($t) { 
               $image = $t->foto ?: 'default.png';
                return [
                    'name' => $t->nama,
                    'role' => $t->jabatan,
                    'img' => public_path('images/landing/team/'.$image)
                ];
            })->toArray();
        } 

        $portfolioQuery = Portfolio::where('is_active', true)->get();
        if ($portfolioQuery->isNotEmpty()) {

            $portfolio = $portfolioQuery->map(function ($p) {

                $image = $p->gambar ?: 'portofolio1.png';

                return [
                    'title' => $p->judul,
                    'cat'   => $p->kategori,
                    'img' => public_path('images/landing/portofolio/'.$p->gambar)
                ];

            })->toArray();
        }

        $clientQuery = Client::where('is_active', true)->get();
        if ($clientQuery->isNotEmpty()) {
            $clients = $clientQuery->map(function ($c) {

                return [
                    'name' => $c->nama_client,
                    'logo' => public_path('images/landing/clients/' . $c->logo),
                ];

            })->toArray();
        }

        $logoPath  = public_path('images/landing/logo.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoData   = file_get_contents($logoPath);
            $logoMime   = mime_content_type($logoPath);
            $logoBase64 = 'data:' . $logoMime . ';base64,' . base64_encode($logoData);
        }

        $generatedAt = now()->setTimezone('Asia/Jakarta')->format('d F Y, H:i') . ' WIB';

        $pdf = Pdf::loadView('pdf.company-profile', compact(
            'company', 'stats', 'visi', 'misi',
            'services', 'services2', 'whyUs', 'team', 'portfolio', 'clients',
            'logoBase64', 'generatedAt'
        ))
        ->setPaper([0,0,1280,720], 'landscape')
        ->setOptions([
            'dpi'                       => 150,
            'defaultFont'               => 'sans-serif',
            'isRemoteEnabled'           => false,
            'isHtml5ParserEnabled'      => true,
            'chroot'                    => public_path(),
        ]);

        $filename = 'Company-Profile-Alpha-Organizer-' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }
}

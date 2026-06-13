<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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
        // (Sesuaikan data ini di sini — PDF otomatis ikut berubah)

        $company = [
            'name'    => 'ALPHA Organizer',
            'tagline' => 'Menciptakan Event yang Sempurna',
            'desc1'   => 'Alpha Organizer adalah perusahaan Event Organizer profesional yang bergerak di bidang Event Organizer, Exhibition, Convention, dan Musical Entertainment. Sejak berdiri, kami telah dipercaya menangani berbagai acara dengan standar kualitas tinggi dan reputasi yang kuat di industri event.',
            'desc2'   => 'Didukung oleh tim yang berpengalaman, kreatif, dan berdedikasi, kami berkomitmen menghadirkan solusi terbaik untuk setiap kebutuhan klien. Dengan mengedepankan profesionalisme, inovasi, dan integritas, kami menciptakan pengalaman yang berkesan serta memastikan setiap acara berjalan sukses sesuai harapan.',
            'email'   => 'info@alphaorganizer.com',
            'phone'   => '+62 812 3456 7890',
            'address' => 'Padang, Sumatera Barat, Indonesia',
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

        $services = [
            ['title' => 'M.I.C.E',         'desc' => 'Meeting, Incentive, Convention, dan Exhibition yang dirancang profesional untuk kebutuhan perusahaan dan organisasi.'],
            ['title' => 'Production',       'desc' => 'Layanan produksi event mulai dari desain grafis, aplikasi pendukung, maintenance service hingga LED Videotron.'],
            ['title' => 'Marketing',        'desc' => 'Grand Opening, Activation, Selling Program, dan Branding untuk meningkatkan citra serta jangkauan bisnis Anda.'],
            ['title' => 'Special Event',    'desc' => 'Pengelolaan expo, fashion show, kompetisi, acara virtual, hingga berbagai event spesial lainnya.'],
            ['title' => 'Corporate Event',  'desc' => 'Product Launching, Conference Gathering, dan Corporate Meeting dengan konsep profesional dan terstruktur.'],
        ];

        $whyUs = [
            ['title' => 'Keahlian dan Pengalaman',      'desc' => 'Tim kami terdiri dari para profesional yang memiliki pengalaman dan keahlian dalam mengelola berbagai jenis event dengan kualitas terbaik.'],
            ['title' => 'Komprehensif dan Terintegrasi', 'desc' => 'Menyediakan layanan event organizer yang lengkap dan terintegrasi untuk memenuhi berbagai kebutuhan klien dalam satu solusi.'],
            ['title' => 'Kreativitas dan Inovasi',       'desc' => 'Selalu menghadirkan ide-ide baru, konsep kreatif, dan mengikuti tren terkini untuk menciptakan pengalaman event yang berkesan.'],
            ['title' => 'Integritas dan Etika',          'desc' => 'Menjaga transparansi, kejujuran, dan profesionalisme dalam setiap kerja sama demi membangun kepercayaan klien.'],
            ['title' => 'Kualitas Tanpa Kompromi',       'desc' => 'Mengutamakan standar pelayanan tinggi dan perhatian terhadap setiap detail pelaksanaan acara.'],
            ['title' => 'Kepuasan Klien Prioritas',      'desc' => 'Berkomitmen memberikan pelayanan terbaik dan membangun hubungan jangka panjang dengan setiap klien.'],
        ];

        $team = [
            ['name' => 'Fajar Villiano',           'role' => 'Founder'],
            ['name' => 'Valdy Dwi Wahyu',          'role' => 'CO Founder'],
            ['name' => 'Intan Prasywi',             'role' => 'Finance Manager'],
            ['name' => 'Muhammad Pinda Rahmadan',   'role' => 'Creative Director'],
            ['name' => 'Baghaztra',                 'role' => 'IT Support'],
            ['name' => 'Fadil Febrianto',           'role' => 'Graphic Designer'],
        ];

        $portfolio = [
            ['title' => 'Tech Summit 2024',                  'cat' => 'Korporat'],
            ['title' => 'Pernikahan Mewah Safira & Hadaffi', 'cat' => 'Pernikahan'],
            ['title' => 'Konser Musik Nusantara',            'cat' => 'Konser'],
            ['title' => 'Peluncuran Produk X Brand',         'cat' => 'Peluncuran'],
            ['title' => 'Gala Amal Charity Night',           'cat' => 'Gala'],
            ['title' => 'Wedding Dinner Eksklusif 2024',     'cat' => 'Pernikahan'],
        ];

        $clients = [
            'Colony', 'Citilink', 'Yamaha', 'Lenovo', 'Pos Indonesia',
            'Bank BRI', 'Hyundai', 'Honda', 'Nissan', 'Rexvin',
            'Dofla Jaya Properti', 'Motul', 'IQOS', 'Toyota',
            'Bank Mandiri', 'Telkomsel', 'Cinema XXI', 'HokBen',
            'Tri', 'Make Over', 'Red Modani', 'Wuling', 'Transmart', 'Huawei',
        ];

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
            'services', 'whyUs', 'team', 'portfolio', 'clients',
            'logoBase64', 'generatedAt'
        ))
        ->setPaper('a4', 'portrait')
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

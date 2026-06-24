<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LandingSection;
use App\Models\AboutSection;
use App\Models\AboutStatistic;
use App\Models\Service;
use App\Models\Portfolio;
use App\Models\Client;
use App\Models\Team;

class LandingPageSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Landing Section (Hero, Visi, Misi, Contact dll)
        $sections = [
            [
                'section_key' => 'hero',
                'title' => 'Menciptakan Event yang',
                'subtitle' => 'Sempurna',
                'content' => 'Tingkatkan acara korporat, pernikahan, dan festival Anda bersama ALPHA.CORP, kami mengubah visi menjadi pengalaman yang sempurna.',
                'image' => null,
                'is_active' => true,
            ],
            [
                'section_key' => 'company_info',
                'title' => 'ALPHA Organizer',
                'subtitle' => 'Menciptakan Event yang Sempurna',
                'content' => json_encode([
                    'email'   => 'info@alphaorganizer.com',
                    'phone'   => '+62 812 3456 7890',
                    'address' => 'Padang, Sumatera Barat, Indonesia',
                    'website' => 'www.alphaorganizer.com',
                ]),
                'image' => null,
                'is_active' => true,
            ],
            [
                'section_key' => 'visi',
                'title' => 'Visi Kami',
                'subtitle' => null,
                'content' => 'Menjadi perusahaan terkemuka yang memberikan solusi dan layanan terbaik dalam bidang Jasa Event Organizer, Exhibition, Convention dan Musical Entertainment. Kami bertujuan untuk menjadi mitra yang terpercaya dan bisa diandalkan dalam mencapai kesuksesan dan perkembangan bisnis klien kami.',
                'image' => null,
                'is_active' => true,
            ],
            [
                'section_key' => 'misi_1',
                'title' => 'Solusi Kreatif',
                'subtitle' => null,
                'content' => 'Memberikan solusi yang inovatif dan kreatif.',
                'image' => null,
                'is_active' => true,
            ],
            [
                'section_key' => 'misi_2',
                'title' => 'Layanan Berkualitas',
                'subtitle' => null,
                'content' => 'Memberikan layanan berkualitas tinggi yang memenuhi kebutuhan dan harapan klien.',
                'image' => null,
                'is_active' => true,
            ],
            [
                'section_key' => 'misi_3',
                'title' => 'Pengalaman Tak Terlupakan',
                'subtitle' => null,
                'content' => 'Menciptakan pengalaman yang berkesan bagi setiap klien.',
                'image' => null,
                'is_active' => true,
            ],
            [
                'section_key' => 'misi_4',
                'title' => 'Standar Profesional',
                'subtitle' => null,
                'content' => 'Menyediakan produk dan layanan berkualitas terbaik.',
                'image' => null,
                'is_active' => true,
            ],
            [
                'section_key' => 'whyus_1',
                'title' => 'Keahlian dan Pengalaman',
                'subtitle' => null,
                'content' => 'Tim kami terdiri dari para profesional yang memiliki pengalaman dan keahlian dalam mengelola berbagai jenis event dengan kualitas terbaik.',
                'image' => null,
                'is_active' => true,
            ],
            [
                'section_key' => 'whyus_2',
                'title' => 'Komprehensif dan Terintegrasi',
                'subtitle' => null,
                'content' => 'Menyediakan layanan event organizer yang lengkap dan terintegrasi untuk memenuhi berbagai kebutuhan klien dalam satu solusi.',
                'image' => null,
                'is_active' => true,
            ],
            [
                'section_key' => 'whyus_3',
                'title' => 'Kreativitas dan Inovasi',
                'subtitle' => null,
                'content' => 'Selalu menghadirkan ide-ide baru, konsep kreatif, dan mengikuti tren terkini untuk menciptakan pengalaman event yang berkesan.',
                'image' => null,
                'is_active' => true,
            ],
            [
                'section_key' => 'whyus_4',
                'title' => 'Integritas dan Etika',
                'subtitle' => null,
                'content' => 'Menjaga transparansi, kejujuran, dan profesionalisme dalam setiap kerja sama demi membangun kepercayaan klien.',
                'image' => null,
                'is_active' => true,
            ],
            [
                'section_key' => 'whyus_5',
                'title' => 'Kualitas Tanpa Kompromi',
                'subtitle' => null,
                'content' => 'Mengutamakan standar pelayanan tinggi dan perhatian terhadap setiap detail pelaksanaan acara.',
                'image' => null,
                'is_active' => true,
            ],
            [
                'section_key' => 'whyus_6',
                'title' => 'Kepuasan Klien Prioritas',
                'subtitle' => null,
                'content' => 'Berkomitmen memberikan pelayanan terbaik dan membangun hubungan jangka panjang dengan setiap klien.',
                'image' => null,
                'is_active' => true,
            ]
        ];

        foreach ($sections as $sec) {
            LandingSection::updateOrCreate(['section_key' => $sec['section_key']], $sec);
        }

        // 2. About Section
        $abouts = [
            [
                'item' => 'deskripsi_1',
                'subtitle' => 'Tentang Alpha Organizer',
                'content' => 'Alpha Organizer adalah perusahaan Event Organizer profesional yang bergerak di bidang Event Organizer, Exhibition, Convention, dan Musical Entertainment. Sejak berdiri, kami telah dipercaya menangani berbagai acara dengan standar kualitas tinggi dan reputasi yang kuat di industri event.',
                'image' => null,
                'urutan' => 1,
                'is_active' => true,
            ],
            [
                'item' => 'deskripsi_2',
                'subtitle' => 'Dedikasi Kami',
                'content' => 'Didukung oleh tim yang berpengalaman, kreatif, dan berdedikasi, kami berkomitmen menghadirkan solusi terbaik untuk setiap kebutuhan klien. Dengan mengedepankan profesionalisme, inovasi, dan integritas, kami menciptakan pengalaman yang berkesan serta memastikan setiap acara berjalan sukses sesuai harapan.',
                'image' => null,
                'urutan' => 2,
                'is_active' => true,
            ]
        ];

        foreach ($abouts as $about) {
            AboutSection::updateOrCreate(['item' => $about['item']], $about);
        }

        // 3. About Statistic
        $stats = [
            ['label' => 'Event Terselesaikan', 'value' => '50+', 'urutan' => 1],
            ['label' => 'Klien Puas', 'value' => '98%', 'urutan' => 2],
            ['label' => 'Tahun Pengalaman', 'value' => '3+', 'urutan' => 3],
            ['label' => 'Kota Operasional', 'value' => '1', 'urutan' => 4],
        ];

        foreach ($stats as $stat) {
            AboutStatistic::updateOrCreate(['label' => $stat['label']], $stat);
        }

        // 4. Services
        $services = [
            ['nama_layanan' => 'M.I.C.E', 'icon' => 'bi-briefcase', 'deskripsi' => 'Meeting, Incentive, Convention, dan Exhibition yang dirancang profesional untuk kebutuhan perusahaan dan organisasi.', 'urutan' => 1, 'is_active' => true],
            ['nama_layanan' => 'Production', 'icon' => 'bi-camera-video', 'deskripsi' => 'Layanan produksi event mulai dari desain grafis, aplikasi pendukung, maintenance service hingga LED Videotron.', 'urutan' => 2, 'is_active' => true],
            ['nama_layanan' => 'Marketing', 'icon' => 'bi-graph-up-arrow', 'deskripsi' => 'Grand Opening, Activation, Selling Program, dan Branding untuk meningkatkan citra serta jangkauan bisnis Anda.', 'urutan' => 3, 'is_active' => true],
            ['nama_layanan' => 'Special Event', 'icon' => 'bi-star', 'deskripsi' => 'Pengelolaan expo, fashion show, kompetisi, acara virtual, hingga berbagai event spesial lainnya.', 'urutan' => 4, 'is_active' => true],
            ['nama_layanan' => 'Corporate Event', 'icon' => 'bi-building', 'deskripsi' => 'Product Launching, Conference Gathering, dan Corporate Meeting dengan konsep profesional dan terstruktur.', 'urutan' => 5, 'is_active' => true],
        ];

        foreach ($services as $srv) {
            Service::updateOrCreate(['nama_layanan' => $srv['nama_layanan']], $srv);
        }

        // 5. Team
        $teams = [
            ['nama' => 'Fajar Villiano', 'jabatan' => 'Founder', 'foto' => 'team1.png', 'urutan' => 1, 'is_active' => true],
            ['nama' => 'Valdy Dwi Wahyu', 'jabatan' => 'CO Founder', 'foto' => 'team2.png', 'urutan' => 2, 'is_active' => true],
            ['nama' => 'Intan Prasywi', 'jabatan' => 'Finance Manager', 'foto' => 'team3.png', 'urutan' => 3, 'is_active' => true],
            ['nama' => 'Muhammad Pinda Rahmadan', 'jabatan' => 'Creative Director', 'foto' => 'team4.png', 'urutan' => 4, 'is_active' => true],
            ['nama' => 'Baghaztra', 'jabatan' => 'IT Support', 'foto' => 'team5.png', 'urutan' => 5, 'is_active' => true],
            ['nama' => 'Fadil Febrianto', 'jabatan' => 'Graphic Designer', 'foto' => 'team6.png', 'urutan' => 6, 'is_active' => true],
        ];

        foreach ($teams as $team) {
            Team::updateOrCreate(['nama' => $team['nama']], $team);
        }

        // 6. Portfolio
        $portfolios = [
            ['judul' => 'Tech Summit 2024', 'kategori' => 'Korporat', 'gambar' => null, 'tips_file' => null, 'is_active' => true],
            ['judul' => 'Pernikahan Mewah Safira & Hadaffi', 'kategori' => 'Pernikahan', 'gambar' => null, 'tips_file' => null, 'is_active' => true],
            ['judul' => 'Konser Musik Nusantara', 'kategori' => 'Konser', 'gambar' => null, 'tips_file' => null, 'is_active' => true],
            ['judul' => 'Peluncuran Produk X Brand', 'kategori' => 'Peluncuran', 'gambar' => null, 'tips_file' => null, 'is_active' => true],
            ['judul' => 'Gala Amal Charity Night', 'kategori' => 'Gala', 'gambar' => null, 'tips_file' => null, 'is_active' => true],
            ['judul' => 'Wedding Dinner Eksklusif 2024', 'kategori' => 'Pernikahan', 'gambar' => null, 'tips_file' => null, 'is_active' => true],
        ];

        foreach ($portfolios as $port) {
            Portfolio::updateOrCreate(['judul' => $port['judul']], $port);
        }

        // 7. Client
        $clients = [
            'Colony', 'Citilink', 'Yamaha', 'Lenovo', 'Pos Indonesia',
            'bri', 'Hyundai', 'Honda', 'Nissan', 'Rexvin',
            'Dofla Jaya Properti', 'Motul', 'IQOS', 'Toyota',
            'Bank Mandiri', 'Telkomsel', 'Cinema XXI', 'HokBen',
            'Tri', 'Make Over', 'Red Modani', 'Wuling', 'Transmart', 'Huawei',
        ];

        foreach ($clients as $clientName) {
            Client::updateOrCreate(
                ['nama_client' => $clientName],
                [
                    'logo' => null,
                    'website' => null,
                    'status' => 'Aktif',
                    'is_active' => true,
                ]
            );
        }
    }
}

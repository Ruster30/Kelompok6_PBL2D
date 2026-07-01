<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Portfolio;
use App\Models\Service;
use App\Models\Team;

class CompanyProfileService
{
    public function getCompanyData(): array
    {
        $company = [
            "name"    => "ALPHA Organizer",
            "tagline" => "Menciptakan Event yang Sempurna",
            "about_title" => "Kami Tidak Hanya Merencanakan Event, Kami Menciptakan Warisan",
            "about_desc1" => "Alpha Organizer adalah perusahaan Event Organizer profesional yang bergerak di bidang Event Organizer, Exhibition, Convention, dan Musical Entertainment. Sejak berdiri, kami telah dipercaya menangani berbagai acara dengan standar kualitas tinggi dan reputasi yang kuat di industri event.",
            "about_desc2" => "Didukung oleh tim yang berpengalaman, kreatif, dan berdedikasi, kami berkomitmen menghadirkan solusi terbaik untuk setiap kebutuhan klien. Dengan mengedepankan profesionalisme, inovasi, dan integritas, kami menciptakan pengalaman yang berkesan serta memastikan setiap acara berjalan sukses sesuai harapan.",
            "email"   => "alphaorganizer1209@gmail.com",
            "phone"   => "+62 822 3318 1883",
            "address" => "JL. Kenangan Air Dingin No.25, Kec Koto Tangah, Kota Padang",
            "website" => "www.alphaorganizer.com",
        ];

        $stats = [
            ["value" => "50+",  "label" => "Event Terselesaikan"],
            ["value" => "98%",  "label" => "Klien Puas"],
            ["value" => "3+",   "label" => "Tahun Pengalaman"],
            ["value" => "1",    "label" => "Kota Operasional"],
        ];

        $visi = "Menjadi perusahaan terkemuka yang memberikan solusi dan layanan terbaik dalam bidang Jasa Event Organizer, Exhibition, Convention dan Musical Entertainment. Kami bertujuan untuk menjadi mitra yang terpercaya dan bisa diandalkan dalam mencapai kesuksesan dan perkembangan bisnis klien kami.";

        $misi = [
            ["title" => "Solusi Kreatif",           "desc" => "Memberikan solusi yang inovatif dan kreatif."],
            ["title" => "Layanan Berkualitas",       "desc" => "Memberikan layanan berkualitas tinggi yang memenuhi kebutuhan dan harapan klien."],
            ["title" => "Pengalaman Tak Terlupakan", "desc" => "Menciptakan pengalaman yang berkesan bagi setiap klien."],
            ["title" => "Standar Profesional",       "desc" => "Menyediakan produk dan layanan berkualitas terbaik."],
        ];

        $whyUs = [
            ["title" => "Keahlian dan Pengalaman",      "desc" => "Tim kami terdiri dari para profesional yang memiliki pengalaman dan keahlian dalam mengelola berbagai jenis event dengan kualitas terbaik."],
            ["title" => "Komprehensif dan Terintegrasi", "desc" => "Menyediakan layanan event organizer yang lengkap dan terintegrasi untuk memenuhi berbagai kebutuhan klien dalam satu solusi."],
            ["title" => "Kreativitas dan Inovasi",       "desc" => "Selalu menghadirkan ide-ide baru, konsep kreatif, dan mengikuti tren terkini untuk menciptakan pengalaman event yang berkesan."],
            ["title" => "Integritas dan Etika",          "desc" => "Menjaga transparansi, kejujuran, dan profesionalisme dalam setiap kerja sama demi membangun kepercayaan klien."],
            ["title" => "Kualitas Tanpa Kompromi",       "desc" => "Mengutamakan standar pelayanan tinggi dan perhatian terhadap setiap detail pelaksanaan acara."],
            ["title" => "Kepuasan Klien Prioritas",      "desc" => "Berkomitmen memberikan pelayanan terbaik dan membangun hubungan jangka panjang dengan setiap klien."],
        ];

        $allServices  = $this->getServices();
        $services     = array_slice($allServices, 0, 5);
        $services2    = array_slice($allServices, 5, 3);
        $team         = $this->getTeam();
        $portfolio    = $this->getPortfolio();
        $clients      = $this->getClientLogos();
        $logoPath     = public_path("images/landing/logo.png");
        $logoBase64   = $this->getLogoBase64($logoPath);
        $generatedAt  = now()->setTimezone("Asia/Jakarta")->format("d F Y, H:i") . " WIB";

        return compact(
            "company", "stats", "visi", "misi",
            "services", "services2", "whyUs", "team", "portfolio",
            "clients", "logoBase64", "generatedAt"
        );
    }

    private function getServices(): array
    {
        return Service::where("is_active", true)->orderBy("urutan")->get()
            ->map(fn($s) => [
                "icon"  => $s->icon ?? "★",
                "title" => $s->nama_layanan,
                "desc"  => $s->deskripsi,
            ])->toArray();
    }

    private function getTeam(): array
    {
        return Team::where("is_active", true)->orderBy("urutan")->get()
            ->map(fn($t) => [
                "name" => $t->nama,
                "role" => $t->jabatan,
                "img"  => public_path("images/landing/team/" . ($t->foto ?: "default.png")),
            ])->toArray();
    }

    private function getPortfolio(): array
    {
        return Portfolio::where("is_active", true)->get()
            ->map(fn($p) => [
                "title" => $p->judul,
                "cat"   => $p->kategori,
                "img"   => public_path("images/landing/portofolio/" . ($p->gambar ?: "portofolio1.png")),
            ])->toArray();
    }

    private function getClientLogos(): array
    {
        return Client::where("is_active", true)->get()
            ->map(fn($c) => [
                "name" => $c->nama_client,
                "logo" => public_path("images/landing/clients/" . $c->logo),
            ])->toArray();
    }

    private function getLogoBase64(string $path): string
    {
        if (!file_exists($path)) return "";

        $logoData = file_get_contents($path);
        $logoMime = mime_content_type($path);
        return "data:" . $logoMime . ";base64," . base64_encode($logoData);
    }
}
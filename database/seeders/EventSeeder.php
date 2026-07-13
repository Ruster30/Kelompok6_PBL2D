<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;
use App\Models\Proposal;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentScheme;
use App\Models\Rab;
use App\Models\RabAdditionalDetail;
use App\Models\Timeline;
use App\Models\Jadwal;
use App\Models\Task;
use App\Models\Negotiation;
use App\Models\Documentation;
use App\Models\DocumentationFile;
use App\Models\Report;
use App\Models\Document;
use App\Models\Feedback;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $admin   = User::where("role", "admin")->first();
        $clients = User::where("role", "client")->get();
        $vendors = \App\Models\Vendor::all();

        if ($clients->isEmpty()) return;

        $pic  = $admin ?? User::factory()->create(["role" => "admin"]);
        $v1   = $vendors->first() ?? \App\Models\Vendor::factory()->create();
        $v2   = $vendors->skip(1)->first() ?? \App\Models\Vendor::factory()->create();
        $now  = now();

        $eventsData = [
            [
                "client" => $clients[0], "nama" => "Grand Launching Honda Brio 2026",
                "jenis" => "Launching Produk", "tgl" => "2026-08-15",
                "lokasi" => "Jakarta Convention Center", "tamu" => 1500,
                "anggaran" => "100-500 Juta", "status" => "diproses", "bayar" => "belum_lunas",
            ],
            [
                "client" => $clients[0], "nama" => "Honda Anniversary Gathering",
                "jenis" => "Gathering", "tgl" => "2026-09-20",
                "lokasi" => "Hotel Mulia Senayan", "tamu" => 300,
                "anggaran" => "< 50 Juta", "status" => "menunggu", "bayar" => "belum_lunas",
            ],
            [
                "client" => $clients[1], "nama" => "Nissan EV Technology Expo",
                "jenis" => "Pameran", "tgl" => "2026-10-01",
                "lokasi" => "ICE BSD", "tamu" => 3000,
                "anggaran" => "> 500 Juta", "status" => "menunggu", "bayar" => "belum_lunas",
            ],
            [
                "client" => $clients[2], "nama" => "Lenovo ThinkPad User Conference",
                "jenis" => "Seminar", "tgl" => "2026-07-25",
                "lokasi" => "Hotel Raffles Jakarta", "tamu" => 500,
                "anggaran" => "100-500 Juta", "status" => "berjalan", "bayar" => "belum_lunas",
            ],
            [
                "client" => $clients[3], "nama" => "Pertamina Energy Fest 2026",
                "jenis" => "Festival", "tgl" => "2026-11-10",
                "lokasi" => "GBK Senayan", "tamu" => 5000,
                "anggaran" => "> 500 Juta", "status" => "menunggu", "bayar" => "belum_lunas",
            ],
            [
                "client" => $clients[4], "nama" => "Telkom Digital Summit",
                "jenis" => "Konferensi", "tgl" => "2026-12-05",
                "lokasi" => "The Westin Jakarta", "tamu" => 800,
                "anggaran" => "100-500 Juta", "status" => "diproses", "bayar" => "belum_lunas",
            ],
            [
                "client" => $clients[1], "nama" => "Nissan Dealer Gathering 2026",
                "jenis" => "Gathering", "tgl" => "2026-07-10",
                "lokasi" => "Hotel Santika Padang", "tamu" => 200,
                "anggaran" => "< 50 Juta", "status" => "selesai", "bayar" => "lunas",
            ],
        ];

        foreach ($eventsData as $d) {
            $event = Event::create([
                "client_id"          => $d["client"]->id,
                "pic_admin_id"       => $pic->id,
                "nama_event"         => $d["nama"],
                "jenis_event"        => $d["jenis"],
                "tanggal_event"      => $d["tgl"],
                "lokasi_event"       => $d["lokasi"],
                "jumlah_tamu"        => $d["tamu"],
                "rentang_anggaran"   => $d["anggaran"],
                "detail_kebutuhan"   => "Kebutuhan untuk " . $d["nama"] . ": " . fake()->paragraph(2),
                "status_event"       => $d["status"],
                "status_pembayaran"  => $d["bayar"],
                "perihal"            => "Penawaran Jasa Event - " . $d["nama"],
            ]);

            // Proposal
            Proposal::create([
                "event_id"         => $event->id,
                "nomor_proposal"   => "PEN-" . $now->format("Ymd") . "-" . str_pad($event->id, 3, "0", STR_PAD_LEFT),
                "file_proposal"    => "proposals/proposal-" . $event->id . ".pdf",
                "versi"            => 1,
                "status"           => in_array($d["status"], ["berjalan", "selesai"]) ? "diterima" : "menunggu_konfirmasi",
                "is_active"        => true,
                "tanggal_proposal" => $now->subDays(rand(5, 20)),
            ]);

            // RabAdditionalDetail
            RabAdditionalDetail::create([
                "event_id"     => $event->id,
                "fee_enabled"  => true,
                "fee_percent"  => 10.00,
                "ppn_enabled"  => true,
                "ppn_percent"  => 11.00,
                "pph_enabled"  => false,
                "pph_percent"  => 0,
            ]);

            // RAB
            $rabItems = [
                ["nama" => "Sewa Venue",          "kategori" => "Tempat",    "qty" => 1, "harga" => 50000000],
                ["nama" => "Dekorasi Panggung",    "kategori" => "Dekorasi",  "qty" => 1, "harga" => 25000000],
                ["nama" => "Sound System",         "kategori" => "Teknik",    "qty" => 1, "harga" => 15000000],
                ["nama" => "Katering",             "kategori" => "Konsumsi",  "qty" => $d["tamu"], "harga" => 75000],
                ["nama" => "Dokumentasi",          "kategori" => "Dokumentasi","qty" => 1, "harga" => 10000000],
                ["nama" => "Cetak Banner",         "kategori" => "Cetak",    "qty" => 5, "harga" => 500000],
            ];

            foreach (array_slice($rabItems, 0, rand(2, 4)) as $rab) {
                $vid = rand(0, 1) ? $v1->id : $v2->id;
                Rab::create([
                    "event_id"       => $event->id,
                    "vendor_id"      => $vid,
                    "nama_biaya"     => $rab["nama"],
                    "kategori_biaya" => $rab["kategori"],
                    "jumlah_item"    => $rab["qty"],
                    "satuan"         => "Unit",
                    "harga_satuan"   => $rab["harga"],
                    "subtotal_biaya" => $rab["qty"] * $rab["harga"],
                ]);
            }

            // PaymentScheme
            PaymentScheme::create([
                "event_id"          => $event->id,
                "jenis_pembayaran"  => "dp_dan_pelunasan",
                "mode_dp"           => "persentase",
                "persentase_dp"     => 50.00,
            ]);

            // Invoice & Payment
            $totalInv = rand(50000000, 300000000);
            $invoice = Invoice::create([
                "event_id"       => $event->id,
                "nomor_invoice"  => "INV-" . $now->format("Ymd") . "-" . str_pad($event->id, 3, "0", STR_PAD_LEFT),
                "total_invoice"  => $totalInv,
                "status_invoice" => $d["bayar"] === "lunas" ? "lunas" : "belum_bayar",
                "tanggal_invoice"=> $now->subDays(rand(1, 10)),
            ]);

            if ($d["bayar"] === "lunas") {
                Payment::create([
                    "invoice_id"         => $invoice->id,
                    "nominal"            => $totalInv,
                    "tanggal_pembayaran" => $now->subDays(rand(1, 5)),
                    "status_pembayaran"  => "diverifikasi",
                    "jenis_pembayaran"   => "pelunasan",
                    "bukti_pembayaran"   => "payments/bukti-" . $event->id . ".jpg",
                ]);
            }

            // Timeline
            $kegiatans = [
                ["nama" => "Rapat Perencanaan",        "hari" => -30],
                ["nama" => "Survey Lokasi",            "hari" => -21],
                ["nama" => "Desain & Konsep",          "hari" => -14],
                ["nama" => "Produksi & Persiapan",     "hari" => -7],
                ["nama" => "Pelaksanaan Event",        "hari" => 0],
                ["nama" => "Evaluasi & Pelaporan",     "hari" => 3],
            ];

            foreach ($kegiatans as $k) {
                Timeline::create([
                    "event_id"         => $event->id,
                    "nama_kegiatan"    => $k["nama"],
                    "tanggal_kegiatan" => date("Y-m-d", strtotime($d["tgl"] . " " . $k["hari"] . " days")),
                    "status_kegiatan"  => $k["hari"] < 0 ? "selesai" : ($k["hari"] == 0 ? "berjalan" : "belum_mulai"),
                    "penanggung_jawab" => $pic->name,
                    "deskripsi"        => "Kegiatan: " . $k["nama"] . " untuk " . $d["nama"],
                ]);
            }

            // Jadwal
            Jadwal::create([
                "event_id"  => $event->id,
                "tanggal"   => $d["tgl"],
                "judul"     => $d["nama"],
                "deskripsi" => "Jadwal pelaksanaan " . $d["nama"],
            ]);

            // Task
            Task::create([
                "event_id"   => $event->id,
                "vendor_id"  => $v1->id,
                "nama_tugas" => "Persiapan " . $d["jenis"] . " - " . $v1->nama_vendor,
                "prioritas"  => "tinggi",
                "deadline"   => date("Y-m-d", strtotime($d["tgl"] . " -7 days")),
                "status"     => "ditugaskan",
                "deskripsi"  => "Tugas untuk vendor " . $v1->nama_vendor . " terkait " . $d["nama"],
            ]);

            Task::create([
                "event_id"   => $event->id,
                "vendor_id"  => $v2->id,
                "nama_tugas" => "Dukungan Teknis " . $d["jenis"] . " - " . $v2->nama_vendor,
                "prioritas"  => "sedang",
                "deadline"   => date("Y-m-d", strtotime($d["tgl"] . " -3 days")),
                "status"     => "ditugaskan",
                "deskripsi"  => "Tugas untuk vendor " . $v2->nama_vendor . " terkait " . $d["nama"],
            ]);

            // Event-Vendor Pivot
            foreach ([$v1, $v2] as $v) {
                $event->vendors()->attach($v->id, [
                    "jadwal_vendor" => $d["tgl"],
                    "status_vendor" => "ditugaskan",
                    "harga_vendor"  => rand(5000000, 50000000),
                    "deskripsi"     => "Vendor " . $v->nama_vendor . " untuk " . $d["nama"],
                ]);
            }

            // Negotiation
            if (in_array($d["status"], ["menunggu", "diproses"])) {
                Negotiation::create([
                    "event_id"          => $event->id,
                    "user_id"           => $d["client"]->id,
                    "pesan"             => "Kami ingin diskusi lebih lanjut mengenai budget untuk " . $d["nama"],
                    "budget_diinginkan" => $totalInv * 0.8,
                    "catatan_tambahan"  => "Mohon diberikan penawaran terbaik.",
                ]);
            }

            // Documentation
            if (in_array($d["status"], ["berjalan", "selesai"])) {
                $doc = Documentation::create([
                    "event_id"   => $event->id,
                    "judul"      => "Dokumentasi " . $d["nama"],
                    "deskripsi"  => "Foto dan video dokumentasi " . $d["nama"],
                ]);

                DocumentationFile::create([
                    "documentation_id" => $doc->id,
                    "file_path"        => "documentations/foto-" . $event->id . "-1.jpg",
                    "status"           => "disetujui",
                    "tipe_file"        => "foto",
                ]);
                DocumentationFile::create([
                    "documentation_id" => $doc->id,
                    "file_path"        => "documentations/video-" . $event->id . "-1.mp4",
                    "status"           => "menunggu",
                    "tipe_file"        => "video",
                ]);
            }

            // Report
            if ($d["status"] === "selesai") {
                Report::create([
                    "event_id"       => $event->id,
                    "file_laporan"   => "reports/laporan-" . $event->id . ".pdf",
                    
                    "tanggal_laporan"=> $now->subDays(rand(1, 3)),
                ]);
            }

            // Document
            Document::create([
                "event_id"  => $event->id,
                "user_id"   => $pic->id,
                "nama_file" => "Dokumen Pendukung " . $d["nama"] . ".pdf",
                "file_path" => "documents/dokumen-" . $event->id . ".pdf",
                "tipe"      => "lainnya",
            ]);

            // Feedback
            if ($d["status"] === "selesai") {
                Feedback::create([
                    "event_id"  => $event->id,
                    "client_id" => $d["client"]->id,
                    "rating"    => rand(4, 5),
                    "ulasan"    => "Acara " . $d["nama"] . " sangat memuaskan! Terima kasih Alpha Corp.",
                ]);
            }
        }
    }
}

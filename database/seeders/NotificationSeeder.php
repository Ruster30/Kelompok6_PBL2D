<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Notification;
use App\Models\AdminClientNotification;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $admin   = User::where("role", "admin")->first();
        $clients = User::where("role", "client")->get();

        if (!$admin || $clients->isEmpty()) return;

        // Notifikasi sistem untuk semua client
        foreach ($clients as $client) {
            Notification::create([
                "user_id" => $client->id,
                "judul"   => "Selamat Datang di Alpha Corp",
                "pesan"   => "Akun Anda telah berhasil dibuat. Silakan lengkapi data event Anda.",
                "tipe"    => "sukses",
                "dibaca"  => false,
            ]);
        }

        // Notifikasi dari admin ke client
        $templates = [
            ["judul" => "Pengumuman Penting", "pesan" => "Kami akan melakukan maintenance sistem pada hari Minggu pukul 02.00-04.00 WIB.", "tipe" => "peringatan"],
            ["judul" => "Promo Spesial",       "pesan" => "Dapatkan diskon 10% untuk paket event bulan ini!", "tipe" => "promo"],
            ["judul" => "Info Terbaru",         "pesan" => "Fitur baru Negosiasi Penawaran sudah tersedia!", "tipe" => "info"],
        ];

        foreach ($templates as $notif) {
            foreach ($clients->random(2) as $client) {
                AdminClientNotification::create([
                    "sender_id"    => $admin->id,
                    "recipient_id" => $client->id,
                    "judul"        => $notif["judul"],
                    "pesan"        => $notif["pesan"],
                    "tipe"         => $notif["tipe"],
                    "email_sent"   => (bool) rand(0, 1),
                ]);
            }
        }
    }
}
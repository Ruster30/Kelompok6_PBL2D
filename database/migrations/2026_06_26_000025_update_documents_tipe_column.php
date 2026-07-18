<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration ini memperluas kolom `tipe` di tabel `documents`
 * untuk mendukung jenis dokumen baru: 'invoice' dan 'rab'.
 *
 * Tabel documents sebelumnya hanya memiliki enum: proposal, kontrak, lainnya.
 * Kita ubah menjadi VARCHAR agar lebih fleksibel tanpa perlu ALTER ENUM berulang.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Ubah kolom tipe dari enum ke string(50) agar mendukung nilai baru
            // tanpa ALTER TABLE per penambahan nilai enum (lebih aman di MySQL/MariaDB)
            $table->string('tipe', 50)->default('lainnya')->change();
        });
    }

    public function down(): void
    {
        // Kembalikan ke enum lama (hati-hati: data 'invoice'/'rab' akan jadi 'lainnya')
        DB::statement("ALTER TABLE documents MODIFY COLUMN tipe ENUM('proposal','kontrak','lainnya') DEFAULT 'lainnya'");
    }
};

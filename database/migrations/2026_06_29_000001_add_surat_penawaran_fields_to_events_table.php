<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom yang dapat diedit admin pada Surat Penawaran.
     * Kolom ini menyimpan override/tambahan data dari event untuk surat.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Override nomor surat (jika admin edit manual)
            $table->string('nomor_surat_override', 100)->nullable()->after('rentang_anggaran');
            // Tanggal selesai event (untuk rentang jadwal)
            $table->date('tanggal_selesai')->nullable()->after('tanggal_event');
            // Luas area stand/pameran
            $table->string('luas_area', 100)->nullable()->after('tanggal_selesai');
            // Terbilang (price dalam huruf)
            $table->string('terbilang', 255)->nullable()->after('rentang_anggaran');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_surat_override',
                'tanggal_selesai',
                'luas_area',
                'terbilang',
            ]);
        });
    }
};
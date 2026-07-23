<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom include_ppn ke tabel events.
     * Digunakan untuk menentukan apakah harga pada Surat Penawaran
     * menyertakan PPN & PPh atau tidak.
     * Default true (include) agar data lama tetap konsisten.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('include_ppn')->default(true)->after('terbilang');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('include_ppn');
        });
    }
};

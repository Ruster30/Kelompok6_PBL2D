<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom revision_number ke tabel proposals.
     *
     * revision_number = 0 → Original (bukan revisi)
     * revision_number = 1 → Revisi Ke-1
     * revision_number = N → Revisi Ke-N
     *
     * Kolom ini khusus untuk Surat Penawaran DDMS (uses_ddms = true).
     * Proposal NON-DDMS tidak terpengaruh — mereka menggunakan kolom 'versi' existing.
     */
    public function up(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->unsignedTinyInteger('revision_number')
                ->default(0)
                ->after('versi')
                ->comment('0=original, 1=Revisi Ke-1, dst. Hanya relevan untuk DDMS proposals.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->dropColumn('revision_number');
        });
    }
};

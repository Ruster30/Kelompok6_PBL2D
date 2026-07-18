<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Perihal surat penawaran — dapat diedit admin, tampil di dokumen client & PDF
            $table->string('perihal', 255)->nullable()->after('nomor_surat_override');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('perihal');
        });
    }
};
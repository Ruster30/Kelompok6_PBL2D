<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('timelines', function (Blueprint $table) {
            $table->text('deskripsi')->nullable()->after('nama_kegiatan');
            $table->string('penanggung_jawab', 100)->nullable()->after('deskripsi');
            $table->date('deadline')->nullable()->after('tanggal_kegiatan');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->string('rentang_anggaran', 100)->nullable()->after('jumlah_tamu');
        });
    }

    public function down(): void
    {
        Schema::table('timelines', function (Blueprint $table) {
            $table->dropColumn(['deskripsi', 'penanggung_jawab', 'deadline']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('rentang_anggaran');
        });
    }
};

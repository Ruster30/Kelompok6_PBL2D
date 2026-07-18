<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('nomor_proposal', 100)->nullable();
            $table->string('file_proposal');
            $table->integer('versi')->default(1);
            $table->enum('status', [
                'menunggu_konfirmasi',
                'negosiasi',
                'direvisi',
                'diterima',
                'ditolak',
            ])->default('menunggu_konfirmasi');
            $table->date('tanggal_proposal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};

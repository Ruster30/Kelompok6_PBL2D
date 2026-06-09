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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pic_admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nama_event');
            $table->string('jenis_event', 100);
            $table->date('tanggal_event');
            $table->text('lokasi_event');
            $table->integer('jumlah_tamu');
            $table->text('detail_kebutuhan');
            $table->enum('status_event', ['menunggu', 'diproses', 'berjalan', 'selesai', 'dibatalkan'])->default('menunggu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

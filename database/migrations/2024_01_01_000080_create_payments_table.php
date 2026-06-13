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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->decimal('nominal', 15, 2);
            $table->date('tanggal_pembayaran');
            $table->enum('status_pembayaran', ['menunggu', 'diverifikasi', 'ditolak'])->default('menunggu');
            $table->string('bukti_pembayaran')->nullable();
            $table->enum('jenis_pembayaran', ['dp', 'pelunasan']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

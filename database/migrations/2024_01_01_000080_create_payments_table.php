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
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->decimal('nominal', 15, 2)->default(0);
            $table->date('tanggal_pembayaran');
            $table->enum('status_pembayaran', ['menunggu','diverifikasi','ditolak'])->default('menunggu');
            $table->string('bukti_pembayaran', 255)->nullable();
            $table->enum('jenis_pembayaran', ['dp','pelunasan'])->default('dp');
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

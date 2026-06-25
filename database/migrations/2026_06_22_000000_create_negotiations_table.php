<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel ini menyimpan riwayat negosiasi dari client terhadap suatu event/penawaran.
     */
    public function up(): void
    {
        Schema::create('negotiations', function (Blueprint $table) {
            $table->id();
            // Event terkait
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            // siapa yang mengirim negosiasi (client)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Isi negosiasi
            $table->text('pesan');
            $table->decimal('budget_diinginkan', 15, 2)->nullable();
            $table->text('catatan_tambahan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('negotiations');
    }
};

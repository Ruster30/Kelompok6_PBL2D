<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel riwayat pengiriman dokumen dari admin ke client.
 * Mencatat siapa yang mengirim, dokumen apa, ke client mana,
 * dan apakah email berhasil dikirim.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_sends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();   // admin
            $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete(); // client
            $table->text('pesan')->nullable();
            $table->boolean('email_sent')->default(false);
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamps();

            $table->index(['document_id', 'recipient_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_sends');
    }
};
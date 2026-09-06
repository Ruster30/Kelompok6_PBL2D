<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel document_verification_logs sebagai audit trail verifikasi QR Code.
     * Setiap scan QR (publik maupun internal) mencatat satu record baru.
     * One QR -> Many verification logs.
     */
    public function up(): void
    {
        Schema::create('document_verification_logs', function (Blueprint $table) {
            $table->id();

            // FK ke QR yang diverifikasi
            $table->foreignId('verification_id')
                  ->constrained('document_qr_verifications')
                  ->cascadeOnDelete();

            // Waktu verifikasi
            $table->timestamp('verified_at')->useCurrent();

            // Status hasil verifikasi — string (bukan enum) untuk fleksibilitas
            // Nilai: valid, expired, invalid, tampered
            $table->string('status', 20);

            // IP address pengakses (mendukung IPv4 dan IPv6)
            $table->string('ip_address', 45)->nullable();

            // User agent browser/perangkat
            $table->text('user_agent')->nullable();

            // User terautentikasi (nullable: scan publik = NULL)
            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // Sumber verifikasi: public, admin, api, mobile, system
            $table->string('verification_source', 20)->default('public');

            // Laravel timestamps
            $table->timestamps();

            // Index manual
            $table->index('status');
            $table->index('verified_at');
            $table->index('verification_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_verification_logs');
    }
};

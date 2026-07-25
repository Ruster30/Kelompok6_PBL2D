<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel document_qr_verifications untuk menyimpan QR Code dokumen resmi.
     * Workflow: Approved -> Generate Number -> Generate QR -> Generate PDF -> Repository
     */
    public function up(): void
    {
        Schema::create('document_qr_verifications', function (Blueprint $table) {
            // 1. Primary Key
            $table->id();

            // 2. Foreign Key ke dokumen
            // UNIQUE: satu dokumen hanya boleh memiliki satu QR aktif
            // CASCADE: jika dokumen dihapus, QR ikut terhapus
            $table->foreignId('document_id')
                  ->unique()
                  ->constrained('documents')
                  ->cascadeOnDelete();

            // 3. Token verifikasi unik (32 hex chars)
            // char(32) — fixed-length, performa index optimal
            // Dihasilkan dengan: bin2hex(random_bytes(16))
            // Contoh: a94d8c5e2f814ca2b5a7d1ef8d93ab12
            $table->char('verification_token', 32)->unique();

            // 4. Path file QR image di storage
            // Hanya path relatif, bukan URL penuh
            // Contoh: qrcodes/SP-2026-001.png
            $table->string('qr_path', 255);

            // 5. Foreign Key ke user yang generate QR
            // RESTRICT: user dengan riwayat generate tidak boleh dihapus
            $table->foreignId('generated_by')
                  ->constrained('users')
                  ->restrictOnDelete();

            // 6. Waktu generate QR
            $table->timestamp('generated_at')->useCurrent();

            // 7. Masa berlaku QR (nullable = permanen)
            // Ditambahkan untuk future expiry feature tanpa ALTER TABLE
            $table->timestamp('expires_at')->nullable();

            // 8-9. Laravel timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * InnoDB otomatis menghapus seluruh foreign key dan index saat tabel di-drop.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_qr_verifications');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel document_approvals untuk menyimpan proses approval dokumen.
     * Workflow: Draft -> Submit -> Pending -> Approved/Rejected
     */
    public function up(): void
    {
        Schema::create('document_approvals', function (Blueprint $table) {
            $table->id();

            // FK ke dokumen — constrained() otomatis membuat index
            $table->foreignId('document_id')
                  ->constrained('documents')
                  ->cascadeOnDelete();

            // FK ke admin pengaju — constrained() otomatis membuat index
            $table->foreignId('submitted_by')
                  ->constrained('users')
                  ->restrictOnDelete();

            // FK ke direktur reviewer — nullable, constrained() otomatis membuat index
            $table->foreignId('approver_id')
                  ->nullable()
                  ->constrained('users')
                  ->restrictOnDelete();

            // Status approval — string (bukan ENUM)
            // Nilai: pending, approved, rejected
            $table->string('status', 50)->default('pending');

            // Catatan direktur saat approve/reject
            $table->text('approval_note')->nullable();

            // Waktu pengajuan approval oleh admin
            $table->timestamp('submitted_at')->nullable();

            // Waktu direktur selesai review (approve/reject)
            $table->timestamp('reviewed_at')->nullable();

            // Laravel timestamps (created_at, updated_at)
            $table->timestamps();

            // === COMPOSITE INDEX ===
            // Laravel tidak membuat composite index secara otomatis

            // Filter status approval per dokumen
            $table->index(['document_id', 'status'], 'appr_doc_status_index');

            // Daftar pending untuk dashboard direktur (FIFO by submitted_at)
            $table->index(['status', 'submitted_at'], 'appr_status_submitted_index');
        });
    }

    /**
     * Reverse the migrations.
     * InnoDB otomatis menghapus seluruh foreign key dan index saat tabel di-drop.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_approvals');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // 1. Status dokumen untuk workflow approval
            // Nilai yang digunakan aplikasi:
            //   draft     — Dokumen sedang dibuat, belum diajukan
            //   pending   — Dokumen menunggu approval direktur
            //   approved  — Dokumen telah disetujui direktur
            //   rejected  — Dokumen ditolak direktur
            //   published — Dokumen final telah diterbitkan (PDF + QR)
            //   archived  — Dokumen diarsipkan (ditandai is_archived = true)
            // String (bukan enum) — fleksibel tanpa ALTER ENUM
            $table->string('status', 50)->default('draft')->after('tipe');

            // 2. Kategori dokumen: official (perlu approval), general, invoice, receipt
            $table->string('document_category', 50)->default('general')->after('status');

            // 3. Counter versi aktif
            $table->integer('current_version')->default(1)->after('document_category');

            // 4. Template ID — FK langsung ke document_templates (sudah tersedia)
            $table->foreignId('template_id')->nullable()->after('current_version')
                  ->constrained('document_templates')->nullOnDelete();

            // 5. Metadata file — unsigned karena ukuran file tidak mungkin negatif
            $table->unsignedBigInteger('file_size')->nullable()->after('file_path');
            $table->string('mime_type')->nullable()->after('file_size');

            // 6. User terakhir pengubah
            $table->foreignId('updated_by')->nullable()->after('user_id')
                  ->constrained('users')->nullOnDelete();

            // 7. Status arsip
            $table->boolean('is_archived')->default(false)->after('mime_type');
            $table->timestamp('archived_at')->nullable()->after('is_archived');

            // 8. Index untuk query yang sering digunakan
            // event_id sudah memiliki index otomatis dari foreignId()->constrained() di migration asal
            $table->index('status', 'documents_status_index');
            $table->index('document_category', 'documents_category_index');
            $table->index('is_archived', 'documents_archived_index');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Hapus foreign key terlebih dahulu
            $table->dropForeign(['template_id']);
            $table->dropForeign(['updated_by']);

            // Hapus index
            $table->dropIndex('documents_status_index');
            $table->dropIndex('documents_category_index');
            $table->dropIndex('documents_archived_index');

            // Hapus kolom
            $table->dropColumn([
                'status',
                'document_category',
                'current_version',
                'template_id',
                'file_size',
                'mime_type',
                'updated_by',
                'is_archived',
                'archived_at',
            ]);
        });
    }
};

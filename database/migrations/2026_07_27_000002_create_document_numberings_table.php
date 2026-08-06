<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel document_numberings untuk menyimpan nomor resmi dokumen.
     * Workflow: Approved -> Generate Number -> Generate QR -> PDF Final -> Repository
     */
    public function up(): void
    {
        Schema::create('document_numberings', function (Blueprint $table) {
            // 1. Primary Key
            $table->id();

            // 2. Foreign Key ke dokumen
            // UNIQUE: satu dokumen hanya boleh memiliki satu nomor
            // CASCADE: jika dokumen dihapus, nomor ikut terhapus
            $table->foreignId('document_id')
                  ->unique()
                  ->constrained('documents')
                  ->cascadeOnDelete();

            // 3. Nomor dokumen lengkap
            // Format: ALPHA/SP/2026/001
            // UNIQUE: tidak boleh ada dua nomor yang sama
            $table->string('document_number', 100)->unique();

            // 4. Prefix jenis dokumen
            // Contoh: SP, KTR, INV, KW, RAB
            $table->string('prefix', 20);

            // 5. Tahun penerbitan
            // unsignedSmallInteger: nilai 0-65535, cukup untuk tahun 2024-2099
            $table->unsignedSmallInteger('year');

            // 6. Nomor urut dalam (prefix, year)
            // unsignedInteger: 0-4294967295, cukup untuk jutaan dokumen per tahun
            $table->unsignedInteger('sequence_number');

            // 7. Foreign Key ke user yang generate nomor
            // RESTRICT: user dengan riwayat generate tidak boleh dihapus
            $table->foreignId('generated_by')
                  ->constrained('users')
                  ->restrictOnDelete();

            // 8-9. Laravel timestamps
            // created_at: juga berfungsi sebagai waktu generate (generated_at)
            $table->timestamps();

            // === INDEX ===

            // Index untuk query: MAX(sequence) WHERE prefix = ? AND year = ?
            // Juga untuk laporan pengelompokan berdasarkan prefix dan tahun
            $table->index(['prefix', 'year'], 'num_prefix_year_index');

            // Catatan:
            // - document_id sudah otomatis index dari foreignId()->unique()
            // - generated_by sudah otomatis index dari foreignId()->constrained()
            // - document_number sudah otomatis index dari string()->unique()
            // Index manual tidak diperlukan untuk kolom-kolom tersebut
        });
    }

    /**
     * Reverse the migrations.
     * InnoDB otomatis menghapus seluruh foreign key dan index saat tabel di-drop.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_numberings');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();

            // Nama template (human-readable)
            $table->string('name', 255);

            // Kode unik template untuk referensi di kode maupun dokumen
            $table->string('code', 100)->unique();

            // Path ke file Blade view template
            $table->string('blade_view', 255);

            // Penjelasan singkat fungsi template
            // Cukup string karena hanya untuk dokumentasi internal
            $table->string('description')->nullable();

            // Status aktif — hanya template aktif yang bisa digunakan
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_templates');
    }
};

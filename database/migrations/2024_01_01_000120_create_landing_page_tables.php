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
        // Tabel 14: Landing Sections (CMS bagian hero, contact, footer, dll.)
        Schema::create('landing_sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_key', 100)->unique(); // hero, contact, footer, tentang
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->longText('content')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabel 15: About Sections (konten halaman tentang kami)
        Schema::create('about_sections', function (Blueprint $table) {
            $table->id();
            $table->string('subtitle')->nullable();
            $table->text('item')->nullable();
            $table->longText('content')->nullable();
            $table->string('image')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabel 16: About Statistics (angka-angka pencapaian: 50+ Event, 96% Kepuasan Klien, dst.)
        Schema::create('about_statistics', function (Blueprint $table) {
            $table->id();
            $table->string('label', 100);
            $table->string('value', 50);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('about_statistics');
        Schema::dropIfExists('about_sections');
        Schema::dropIfExists('landing_sections');
    }
};

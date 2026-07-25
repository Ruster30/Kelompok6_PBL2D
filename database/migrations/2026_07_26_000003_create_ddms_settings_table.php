<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ddms_settings', function (Blueprint $table) {
            $table->id();

            // Nama konfigurasi dengan format snake_case
            $table->string('setting_key', 100)->unique();

            // Nilai konfigurasi — text agar muat untuk JSON panjang
            $table->text('setting_value')->nullable();

            // Keterangan fungsi setting untuk dokumentasi
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ddms_settings');
    }
};

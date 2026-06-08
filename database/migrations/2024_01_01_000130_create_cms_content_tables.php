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
        // Tabel 17: Services (layanan yang ditawarkan: M.I.C.E, Production, Marketing, Special Event, Corporate Event)
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('nama_layanan');
            $table->string('icon')->nullable();
            $table->text('deskripsi')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabel 18: Teams (anggota tim: Fahri Afiliasi, Valdy Dwi Wahyu, Intan Prasyel, dll.)
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('jabatan');
            $table->string('foto')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabel 19: Portfolios (portofolio project/event: Wedding, Corporate, Concert, Seminar, Launching, Expo)
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('kategori', 100)->nullable(); // Wedding, Corporate, Concert, dll.
            $table->string('gambar')->nullable();
            $table->string('tips_file')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tabel 20: Clients (klien/mitra: Honda, Nissan, Lenovo, Pertamina, Telkom, dll.)
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('nama_client');
            $table->string('logo')->nullable();
            $table->string('website')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
        Schema::dropIfExists('portfolios');
        Schema::dropIfExists('teams');
        Schema::dropIfExists('services');
    }
};

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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            // Vendor dapat dicatat lebih dulu tanpa harus dibuatkan akun login.
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('nama_vendor');
            $table->string('jenis_vendor', 100)->nullable();
            $table->text('alamat')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};

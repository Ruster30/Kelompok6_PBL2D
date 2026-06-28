<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel ini mencatat riwayat notifikasi yang dikirim admin ke klien,
 * terpisah dari tabel notifications (yang dipakai sistem untuk semua role).
 * Dengan ini, halaman "Notifikasi Terkirim" di Kelola Klien bisa difilter
 * berdasarkan pengirim (admin) dan penerima (user dengan role=client).
 */
return new class extends Migration
{
    public function up(): void
    {
        // Pastikan kolom 'last_active_at' ada di tabel users supaya
        // fitur "Terakhir Aktif" dan "Klien Aktif" bisa berjalan.
        if (!Schema::hasColumn('users', 'last_active_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('last_active_at')->nullable()->after('avatar');
            });
        }

        // Tabel khusus untuk riwayat pengiriman notifikasi dari admin ke klien.
        Schema::create('admin_client_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();  // admin
            $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete(); // client
            $table->string('judul');
            $table->text('pesan');
            $table->string('tipe')->default('info'); // info, promo, pengingat, peringatan, dll
            $table->boolean('email_sent')->default(false);
            $table->timestamps();

            $table->index(['recipient_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_client_notifications');

        if (Schema::hasColumn('users', 'last_active_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('last_active_at');
            });
        }
    }
};

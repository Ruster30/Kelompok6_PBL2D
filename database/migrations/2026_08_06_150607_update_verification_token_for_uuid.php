<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("document_qr_verifications", function (Blueprint $table) {
            // Token UUID v4 = 36 karakter (dengan hyphen)
            $table->string("verification_token", 36)->change();

            // QR path belum tersedia saat token dibuat (Phase 11F.5)
            $table->string("qr_path", 255)->nullable()->change();

            // generated_by belum tersedia saat token dibuat
            $table->foreignId("generated_by")->nullable()->change();

            // generated_at belum tersedia saat token dibuat
            $table->timestamp("generated_at")->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table("document_qr_verifications", function (Blueprint $table) {
            $table->char("verification_token", 32)->change();
            $table->string("qr_path", 255)->nullable(false)->change();
            $table->foreignId("generated_by")->nullable(false)->change();
            $table->timestamp("generated_at")->nullable(false)->change();
        });
    }
};
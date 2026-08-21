<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-document DDMS mode (Phase 11I.6).
     *
     * Backfill deterministik (non-destruktif):
     * - document_source = 'generated'  -> uses_ddms = true  (sudah melalui alur DDMS)
     * - document_source = 'uploaded' / default -> uses_ddms = false (surat biasa)
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->boolean('uses_ddms')->default(false)->after('document_source');
        });

        DB::table('documents')
            ->where('document_source', 'generated')
            ->update(['uses_ddms' => true]);
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('uses_ddms');
        });
    }
};
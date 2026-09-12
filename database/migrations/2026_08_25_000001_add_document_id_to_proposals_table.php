<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Proposal = Surat Penawaran (single versioning entity, Proposal.versi).
     *
     * DDMS becomes a thin Document layer for Proposal:
     *   Proposal v1 -> Document A
     *   Proposal v2 -> Document B
     *   Proposal v3 -> Document C
     *
     * Old proposals simply keep document_id = NULL.
     */
    public function up(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->foreignId('document_id')
                ->nullable()
                ->constrained('documents')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('document_id');
        });
    }
};

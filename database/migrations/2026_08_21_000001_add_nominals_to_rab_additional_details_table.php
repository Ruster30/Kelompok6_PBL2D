<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rab_additional_details', function (Blueprint $table) {
            // Kolom nominal manual (nullable). Jika null, dihitung dari persentase.
            $table->decimal('fee_nominal', 15, 2)->nullable()->after('fee_percent');
            $table->decimal('ppn_nominal', 15, 2)->nullable()->after('ppn_percent');
            $table->decimal('pph_nominal', 15, 2)->nullable()->after('pph_percent');
        });
    }

    public function down(): void
    {
        Schema::table('rab_additional_details', function (Blueprint $table) {
            $table->dropColumn(['fee_nominal', 'ppn_nominal', 'pph_nominal']);
        });
    }
};

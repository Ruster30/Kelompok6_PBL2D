<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rab_additional_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->boolean('fee_enabled')->default(false);
            $table->decimal('fee_percent', 5, 2)->default(10.00);
            $table->boolean('ppn_enabled')->default(false);
            $table->decimal('ppn_percent', 5, 2)->default(11.00);
            $table->boolean('pph_enabled')->default(false);
            $table->decimal('pph_percent', 5, 2)->default(2.00);
            $table->timestamps();
            $table->unique('event_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rab_additional_details');
    }
};
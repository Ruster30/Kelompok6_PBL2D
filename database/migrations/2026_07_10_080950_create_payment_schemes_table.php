<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("payment_schemes", function (Blueprint $table) {
            $table->id();
            $table->foreignId("event_id")->constrained()->cascadeOnDelete();
            $table->enum("jenis_pembayaran", ["full_payment", "dp_dan_pelunasan"])->default("full_payment");
            $table->enum("mode_dp", ["persentase", "nominal"])->nullable();
            $table->decimal("nilai_dp", 15, 2)->nullable();
            $table->decimal("persentase_dp", 5, 2)->nullable();
            $table->timestamps();
            $table->unique("event_id");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("payment_schemes");
    }
};

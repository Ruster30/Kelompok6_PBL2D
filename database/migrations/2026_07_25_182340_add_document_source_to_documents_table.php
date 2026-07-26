<?php

use App\Enums\DocumentSource;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("documents", function (Blueprint $table) {
            $table->string("document_source", 20)->default("uploaded");
            $table->index("document_source", "documents_source_index");
        });
    }

    public function down(): void
    {
        Schema::table("documents", function (Blueprint $table) {
            $table->dropIndex("documents_source_index");
            $table->dropColumn("document_source");
        });
    }
};

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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_theme_id')->constrained('sub_themes')->onDelete('cascade');
            $table->string('material_code');
            $table->string('material_name');
            $table->longtext('material_description');
            $table->string('material_document')->nullable()->default(null);
            $table->boolean('material_is_active')->default(true);
            $table->boolean('material_on_report')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};

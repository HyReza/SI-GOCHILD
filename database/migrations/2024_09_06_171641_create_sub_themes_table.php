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
        Schema::create('sub_themes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('theme_id')->constrained('themes')->onDelete('cascade');
            $table->string('sub_theme_code');
            $table->string('sub_theme_name');
            $table->longtext('sub_theme_description');
            $table->string('sub_theme_document')->nullable()->default(null);
            $table->date('sub_theme_start');
            $table->date('sub_theme_end');
            $table->boolean('sub_theme_is_active')->default(true);
            $table->boolean('sub_theme_on_report')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_themes');
    }
};

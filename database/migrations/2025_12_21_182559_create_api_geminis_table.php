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
        Schema::create('api_geminis', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->comment('Label untuk key ini, misal: Key Utama');
            $table->string('api_key'); // Kolom untuk API Key
            $table->string('model')->default('gemini-1.5-flash'); // Kolom untuk Model (default bisa diubah)
            $table->boolean('is_active')->default(true); // Penanda konfigurasi mana yang sedang dipakai
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_geminis');
    }
};

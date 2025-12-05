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
        Schema::create('report_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');

            // Tiga kolom ini menampung ID dari item yang dinilai.
            // Semuanya dibuat nullable karena hanya salah satu (atau kombinasi) yang akan diisi di setiap baris.

            // Untuk menyimpan skor level TEMA
            $table->foreignId('theme_id')->nullable()->constrained('themes')->onDelete('cascade');

            // Untuk menyimpan skor level SUB-TEMA dan MATERI
            $table->foreignId('sub_theme_id')->nullable()->constrained('sub_themes')->onDelete('cascade');

            // Untuk menyimpan skor level MATERI
            $table->foreignId('material_id')->nullable()->constrained('materials')->onDelete('cascade');

            // Kolom penilaiannya
            $table->enum('score', ['BB', 'MB', 'BSH', 'BSB']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_details');
    }
};

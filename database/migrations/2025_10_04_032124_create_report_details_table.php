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

            // Relasi ke Tabel Report Utama
            $table->foreignId('report_id')
                ->constrained('reports')
                ->onDelete('cascade');

            // Item yang dinilai (Materi)
            // Wajib diisi (tidak nullable) karena ini inti dari baris penilaian
            $table->foreignId('material_id')
                ->constrained('materials')
                ->onDelete('cascade');

            // Konteks Hierarchy (Tema & Sub-tema)
            // Disimpan sebagai referensi (cache) agar query cetak raport lebih cepat
            // Boleh nullable jika suatu saat ada materi di luar sub-tema, tapi sebaiknya diisi otomatis oleh Controller
            $table->foreignId('theme_id')
                ->nullable()
                ->constrained('themes')
                ->onDelete('cascade');

            $table->foreignId('sub_theme_id')
                ->nullable()
                ->constrained('sub_themes')
                ->onDelete('cascade');

            // Skor Penilaian
            $table->enum('score', ['BB', 'MB', 'BSH', 'BSB']);

            $table->timestamps();

            // --- OPTIMASI PENTING (UNIQUE CONSTRAINT) ---
            // Kode ini mencegah duplikasi data.
            // Artinya: Dalam 1 No. Raport, Materi "X" hanya boleh punya 1 nilai.
            $table->unique(['report_id', 'material_id']);
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

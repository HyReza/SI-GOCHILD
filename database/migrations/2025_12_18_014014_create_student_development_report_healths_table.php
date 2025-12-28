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
        Schema::create('student_development_report_healths', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel utama (student_development_reports)
            // Menggunakan constraint & cascade delete (jika laporan dihapus, data kesehatan ikut terhapus)
            $table->foreignId('student_development_report_id')
                ->constrained('student_development_reports', 'id') // Pastikan nama tabel induk sesuai
                ->cascadeOnDelete()
                ->name('fk_dev_rep_health_id'); // Custom index name agar tidak error 'identifier too long'

            // Kolom-kolom aspek kesehatan (disimpan sebagai string untuk nilai Dropdown: Baik/Cukup/dll)
            $table->string('vision')->nullable();   // Mata / Penglihatan
            $table->string('hearing')->nullable();  // Telinga / Pendengaran
            $table->string('teeth')->nullable();    // Gigi
            $table->string('nails')->nullable();    // Kuku
            $table->string('skin')->nullable();     // Kulit
            $table->string('hygiene')->nullable();  // Kebersihan

            // Catatan tambahan kesehatan
            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_development_report_healths');
    }
};

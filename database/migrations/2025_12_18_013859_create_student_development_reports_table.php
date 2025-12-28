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
        Schema::create('student_development_reports', function (Blueprint $table) {
            $table->id();

            // Relasi ke Siswa
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();

            // Relasi ke Data Asli MMDST (Opsional/Nullable)
            // Fungsinya: Jika ingin melihat detail instrumen asli (P/F) di masa depan
            $table->foreignId('mmdst_assessment_id')
                ->nullable()
                ->constrained('mmdst_assessments')
                ->onDelete('set null');

            // --- A. Periode & Info Dasar ---
            $table->date('report_date');          // Tanggal Cetak Raport
            $table->string('academic_year');      // Cth: 2024/2025
            $table->string('semester');           // Ganjil / Genap
            $table->date('period_start_date');    // Awal periode data
            $table->date('period_end_date');      // Akhir periode data

            // --- B. Snapshot Data Fisik (Ambil dari tabel measurements terakhir) ---
            $table->integer('age_in_months');               // Usia saat raport dibuat
            $table->double('height_cm')->nullable();        // TB
            $table->double('weight_kg')->nullable();        // BB
            $table->double('head_circumference_cm')->nullable(); // LK
            $table->double('bmi')->nullable();              // IMT (Hitung manual/ambil dr calculation_results)

            // --- C. Snapshot Hasil MMDST (Ambil dari tabel mmdst_assessments) ---
            // Kita simpan string hasilnya agar tidak perlu query ulang yg ribet
            $table->string('mmdst_final_result')->nullable();           // Diagnosa Akhir (Normal/Suspect/Untestable)
            $table->string('mmdst_personal_social_result')->nullable(); // Hasil Sektor Personal Sosial
            $table->string('mmdst_fine_motor_result')->nullable();      // Hasil Sektor Motorik Halus
            $table->string('mmdst_language_result')->nullable();        // Hasil Sektor Bahasa
            $table->string('mmdst_gross_motor_result')->nullable();     // Hasil Sektor Motorik Kasar

            // --- D. Narasi / Deskripsi (Hasil Generate AI) ---
            $table->text('personal_social_desc')->nullable();
            $table->text('fine_motor_desc')->nullable();
            $table->text('language_desc')->nullable();
            $table->text('gross_motor_desc')->nullable();
            $table->text('growth_analysis_desc')->nullable(); // Analisis Pertumbuhan Fisik

            // --- E. Snapshot Grafik (PENTING: JSON) ---
            // Menyimpan array data history pengukuran [{x: umur, y: nilai}, ...]
            // Ini menjamin grafik di raport PDF tidak akan berubah selamanya.
            $table->json('weight_chart_snapshot')->nullable(); // Grafik BB/U
            $table->json('height_chart_snapshot')->nullable(); // Grafik TB/U
            $table->json('head_chart_snapshot')->nullable();   // Grafik LK/U
            $table->json('bmi_chart_snapshot')->nullable();    // Grafik IMT/U

            // GRAFIK
            $table->string('chart_bbu_image')->nullable();   // Berat/Umur
            $table->string('chart_tbu_image')->nullable();   // Tinggi/Umur (atau PB/U)
            $table->string('chart_bbtb_image')->nullable();  // BB/TB (atau BB/PB)
            $table->string('chart_imtu_image')->nullable();  // IMT/Umur

            // --- F. Catatan & Rekomendasi Guru ---
            $table->text('teacher_notes')->nullable();
            $table->text('teacher_recommendations')->nullable();

            // --- G. Snapshot Presensi (Hitung otomatis dari tabel attendance) ---
            $table->integer('attendance_present')->default(0);
            $table->integer('attendance_sick')->default(0);
            $table->integer('attendance_permission')->default(0);
            $table->integer('attendance_alpha')->default(0);

            // --- H. Tanda Tangan ---
            $table->string('teacher_name')->nullable();
            $table->string('teacher_signature')->nullable(); // Path gambar

            $table->string('principal_name')->nullable();
            $table->string('principal_signature')->nullable();

            $table->string('consultant_name')->nullable();
            $table->string('consultant_signature')->nullable();

            $table->string('parent_name')->nullable();
            $table->string('parent_signature')->nullable();

            $table->enum('status', ['draft', 'published'])->default('published');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_development_reports');
    }
};

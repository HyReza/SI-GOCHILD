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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            // RELASI
            // Mengambil siswa & kelas dari transaksi aktivitas saat itu
            $table->foreignId('activity_transaction_id')
                ->constrained('activity_transactions')
                ->onDelete('cascade');

            // Menyimpan ID siswa secara langsung agar query history lebih cepat
            $table->foreignId('student_id')
                ->constrained('students')
                ->onDelete('cascade');

            $table->foreignId('created_by')->constrained('users'); // Guru pembuat

            // INFO RAPORT
            $table->string('report_title'); // Cth: "Laporan Akhir Semester 1"
            $table->string('semester');
            $table->string('class_name')->nullable();
            $table->date('start_date'); // Awal Periode (untuk filter harian)
            $table->date('end_date');   // Akhir Periode
            $table->date('report_date'); // Tanggal pembagian raport (Cth: Pekalongan, 15 October 2025)

            // --- BAGIAN 1: DATA FISIK (Snapshot saat raport dibuat) ---
            $table->decimal('height', 5, 2)->nullable(); // Tinggi Badan
            $table->decimal('weight', 5, 2)->nullable(); // Berat Badan
            $table->decimal('head_circumference', 5, 2)->nullable();

            // --- BAGIAN 2: NARASI & FOTO (Sesuai Halaman Raport PDF) ---
            // Nanti di Controller, teks ini di-generate via Gemini API

            // A. Nilai Agama dan Budi Pekerti
            $table->longText('religious_values_text')->nullable();
            $table->string('religious_values_photo')->nullable(); // Path foto upload

            // B. Jati Diri
            $table->longText('identity_text')->nullable();
            $table->string('identity_photo')->nullable();

            // C. Dasar-dasar Literasi, Matematika, Sains, Teknologi (STEAM)
            $table->longText('literacy_steam_text')->nullable();
            $table->string('literacy_steam_photo')->nullable();

            // D. Projek Penguatan Profil Pelajar Pancasila (P5)
            $table->longText('p5_text')->nullable();
            $table->string('p5_photo')->nullable();

            // E. Refleksi Orang Tua (Bisa diisi manual/generate)
            $table->longText('parent_reflection_text')->nullable();
            $table->string('parent_reflection_photo')->nullable();

            // F. Informasi Mengenai Perkembangan Anak (Info tambahan)
            $table->longText('development_info_text')->nullable();
            $table->string('development_info_photo')->nullable();

            // G. Kesimpulan Perkembangan (General Summary)
            $table->longText('overall_summary')->nullable();

            // --- BAGIAN 3: CATATAN GURU ---
            $table->text('teacher_notes')->nullable();
            $table->text('recommendations')->nullable();

            // --- BAGIAN 4: PRESENSI (Disimpan sebagai JSON) ---
            // Contoh isi: {"sakit": 2, "izin": 1, "alpha": 0}
            // Data diambil otomatis dari tabel attendance, tapi disimpan permanen di sini sebagai 'snapshot'.
            $table->json('attendance_summary')->nullable();

            // --- BAGIAN 5: TANDA TANGAN (Digital Signature) ---
            // Disimpan path gambarnya. Nama disimpan text agar jika user ganti nama, raport lama tetap sesuai.

            // 1. Orang Tua / Wali
            $table->string('parent_name')->nullable();
            $table->string('parent_signature')->nullable();

            // 2. Wali Kelas
            $table->string('teacher_name')->nullable();
            $table->string('teacher_signature')->nullable();

            // 3. Konsultan Tumbuh Kembang
            $table->string('consultant_name')->nullable();
            $table->string('consultant_signature')->nullable();

            // 4. Kepala Sekolah
            $table->string('principal_name')->nullable();
            $table->string('principal_signature')->nullable();

            // STATUS
            // Draft = Masih diedit, Published = Sudah final (dikunci)
            $table->enum('status', ['draft', 'published'])->default('draft');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};

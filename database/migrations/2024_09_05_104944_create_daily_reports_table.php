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
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();

            // Kaitan ke transaksi aktivitas (sudah ada service_id di sana, tapi simpan lagi utk denormalisasi)
            $table->foreignId('activity_transaction_id')
                ->constrained('activity_transactions')
                ->onDelete('cascade');

            // Simpan service_id di sini supaya mudah filter/report tanpa join tambahan
            $table->foreignId('service_id')
                ->constrained('services')
                ->onDelete('cascade');

            // Field umum (dipakai dua-duanya)
            $table->date('period');                           // tanggal laporan
            $table->decimal('body_temperature', 5, 2)->nullable();

            // Makan pagi (umum, bisa dipakai di dua service bila perlu)
            $table->enum('breakfast', ['sudah', 'belum'])->nullable();

            // Kesehatan umum
            $table->enum('health_status', ['sehat', 'sakit'])->nullable();
            $table->string('sickness_description')->nullable();
            $table->enum('medication_status', ['disertai obat', 'tanpa obat'])->nullable();

            // Kondisi umum anak hari itu
            $table->enum('condition', ['tenang', 'rewel', 'temper tantrum'])->nullable();

            // Rekomendasi/isi stimulasi (akan kita auto-isi berdasarkan MMDST di controller)
            $table->text('stimulation_description')->nullable();

            // Catatan bebas & ttd
            $table->text('notes')->nullable();
            $table->string('parent_guardian_signature')->nullable();
            $table->string('parent_guardian_name')->nullable();
            $table->string('teacher_signature')->nullable();
            $table->string('teacher_name')->nullable();


            $table->timestamps();

            $table->index(['period', 'service_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};

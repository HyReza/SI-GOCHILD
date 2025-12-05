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
        Schema::create('absence_fines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            // foreignId('attendance_id') akan ditambahkan di migrasi selanjutnya (attendances)

            $table->date('fine_date');
            $table->string('description'); // Contoh: "Denda Keterlambatan Check-In 15 menit"
            $table->unsignedBigInteger('amount'); // Jumlah nominal denda

            $table->boolean('is_billed')->default(false); // Flag: Sudah masuk dokumen tagihan?

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absence_fines');
    }
};

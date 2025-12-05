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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendances_transaction_id')->constrained('attendance_transactions')->onDelete('cascade');
            $table->foreignId('activity_transaction_id')->constrained('activity_transactions')->onDelete('cascade');

            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();

            $table->enum('check_in_status', ['Present', 'Excused', 'Sick', 'Absent'])->nullable();
            $table->enum('check_out_status', ['on_time', 'late', 'Absent', 'sick', 'not_yet', 'Excused'])->nullable();

            $table->integer('late_duration')->default(0); // Durasi keterlambatan (menit)

            // Kolom baru: Foreign key ke record denda di tabel absence_fines
            // Nullable karena tidak semua absensi dikenai denda.
            $table->foreignId('absence_fine_id')
                ->nullable()
                ->constrained('absence_fines')
                ->onDelete('set null');

            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

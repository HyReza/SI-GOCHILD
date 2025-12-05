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
        Schema::create('mmdst_assessments', function (Blueprint $table) {
            $table->id();

            // Rujuk ke students (bukan children)
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();

            $table->date('assessment_date');
            $table->unsignedInteger('age_in_days'); // usia anak (hari) pada tanggal penilaian

            // Siapa user yang input
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();

            $table->enum('overall_result', ['NORMAL', 'QUESTIONABLE', 'ABNORMAL', 'UNTESTABLE'])->nullable();
            $table->json('counters')->nullable();   // opsional: ringkasan total P/F/R/OP
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'assessment_date']);
            $table->index(['created_by', 'assessment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mmdst_assessments');
    }
};

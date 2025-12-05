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
        Schema::create('daily_report_children_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('daily_report_id')
                ->constrained('daily_reports')
                ->onDelete('cascade')
                ->unique(); // one-to-one

            // Salam & doa pagi
            $table->enum('greeting_and_morning_prayer', ['mengikuti', 'tidak mengikuti'])->nullable();

            // Sesi 1
            $table->foreignId('session1_material_id')
                ->nullable()
                ->constrained('materials')
                ->nullOnDelete();
            $table->enum('session1_activity', ['BB', 'MB', 'BSH', 'BSB'])->nullable();

            // Toilet training & Dhuha
            $table->enum('toilet_training_and_duha_prayer', ['mengikuti', 'tidak mengikuti'])->nullable();

            // Sesi 2
            $table->foreignId('session2_material_id')
                ->nullable()
                ->constrained('materials')
                ->nullOnDelete();
            $table->enum('session2_activity', ['BB', 'MB', 'BSH', 'BSB'])->nullable();

            // Snack, makan, kerapian, kebersihan, salat, tidur, mandi, snack sore, ashar
            $table->enum('morning_snack', ['habis', 'tidak habis'])->nullable();
            $table->enum('neatness_and_independence', ['mandiri', 'kurang mandiri', 'tidak mandiri'])->nullable();
            $table->enum('cheerful_lunch', ['habis', 'sisa sedikit', 'sisa banyak'])->nullable();
            $table->enum('cleanliness_and_brushing_training', ['kurang', 'cukup', 'baik'])->nullable();
            $table->enum('dhuhr_prayer', ['mengikuti', 'tidak mengikuti'])->nullable();
            $table->enum('healthy_sleep', ['tidur', 'tidur sebentar', 'tidak tidur'])->nullable();
            $table->enum('afternoon_bath', ['mengikuti', 'tidak mengikuti'])->nullable();
            $table->enum('afternoon_snack', ['habis', 'tidak habis'])->nullable();
            $table->enum('asr_prayer', ['mengikuti', 'tidak mengikuti'])->nullable();

            // Ekstra stimulasi & permainan ceria
            $table->text('extra_stimulation_description')->nullable();
            $table->enum('extra_stimulation', ['BB', 'MB', 'BSH', 'BSB'])->nullable();
            $table->text('cheerful_play_description')->nullable();
            $table->enum('cheerful_play', ['BB', 'MB', 'BSH', 'BSB'])->nullable();

            // INDEX: beri nama pendek biar < 64 char
            $table->index(['session1_material_id', 'session2_material_id'], 'drch_s1_s2_idx');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_report_children_details');
    }
};

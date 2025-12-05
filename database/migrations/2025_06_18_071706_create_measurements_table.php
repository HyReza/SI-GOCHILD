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
        Schema::create('measurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('activity_transaction_id')->constrained('activity_transactions')->onDelete('cascade');
            $table->date('date_measurement');
            $table->decimal('weight', 6, 2);
            $table->decimal('height', 6, 2);
            $table->decimal('head_circumference', 6, 2);
            $table->decimal('arm_circumference', 6, 2);
            $table->string('measurement_condition')->nullable();
            $table->json('sd_category')->nullable(); // hasil klasifikasi: -2SD, Median, +1SD, dll.
            $table->json('calculation_results')->nullable(); //kondisi anak  berdasarkan hasil klasifikasi
            $table->json('measurement_results')->nullable();
            $table->text('note_measurement')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('measurements');
    }
};

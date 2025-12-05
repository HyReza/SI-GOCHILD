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
        Schema::create('growth_standards', function (Blueprint $table) {
            $table->id();
            $table->enum('gender', ['male', 'female']);

            $table->enum('reference_type', ['age', 'length', 'height']);
            // age = pakai umur (bulan), length = panjang badan (terlentang), height = tinggi badan (berdiri)

            $table->integer('age_months')->nullable();   // umur bulan
            $table->decimal('body_length', 6, 2)->nullable(); // cm
            $table->decimal('body_height', 6, 2)->nullable(); // cm

            $table->decimal('minus_3_sd', 6, 2);
            $table->decimal('minus_2_sd', 6, 2);
            $table->decimal('minus_1_sd', 6, 2);
            $table->decimal('median', 6, 2);
            $table->decimal('plus_1_sd', 6, 2);
            $table->decimal('plus_2_sd', 6, 2);
            $table->decimal('plus_3_sd', 6, 2);

            $table->string('parameter'); // contoh: BB/U, TB/U, IMT/U, BB/PB, BB/TB
            $table->string('measurement_condition')->nullable(); // terlentang / berdiri (opsional)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('growth_standards');
    }
};

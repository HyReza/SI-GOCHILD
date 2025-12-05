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
        Schema::create('mmdst_assessment_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('assessment_id')->constrained('mmdst_assessments')->cascadeOnDelete();
            $table->foreignId('mmdst_parameter_id')->constrained('mmdst_parameters')->cascadeOnDelete();

            // denormalisasi kategori untuk query cepat per sektor
            $table->foreignId('stimulation_category_id')->constrained('category_parameters');

            $table->enum('result_code', ['P', 'F', 'R', 'OP', 'NR']); // Pass, Fail, Refusal, No-Opportunity, NR = Not Reqired(Tidak Wajib Dites)
            $table->boolean('is_delay')->default(false);      // F pada item usia >= percent_100
            $table->boolean('is_age_line')->default(false);   // item relevan (usia >= percent_25)
            $table->text('note')->nullable();

            $table->timestamps();

            $table->unique(['assessment_id', 'mmdst_parameter_id'], 'm_ai_assess_param_uq');
            $table->index(['assessment_id', 'stimulation_category_id'], 'm_ai_assess_cat_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mmdst_assessment_items');
    }
};

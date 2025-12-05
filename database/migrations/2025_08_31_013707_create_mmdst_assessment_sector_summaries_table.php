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
        Schema::create('mmdst_assessment_sector_summaries', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('assessment_id');
            $table->unsignedBigInteger('stimulation_category_id');

            $table->unsignedInteger('total_items')->default(0);
            $table->unsignedInteger('delays_count')->default(0);
            $table->unsignedInteger('refusals_count')->default(0);
            $table->unsignedInteger('pass_at_age_line_count')->default(0);

            $table->enum('sector_result', ['NORMAL', 'QUESTIONABLE', 'ABNORMAL', 'UNTESTABLE'])->nullable();
            $table->timestamps();

            // FK dengan nama pendek (ini yang tadi error)
            $table->foreign('assessment_id', 'm_ass_sum_ass_fk')
                ->references('id')->on('mmdst_assessments')->cascadeOnDelete();

            $table->foreign('stimulation_category_id', 'm_ass_sum_cat_fk')
                ->references('id')->on('category_parameters')->restrictOnDelete();

            // unique pendek
            $table->unique(['assessment_id', 'stimulation_category_id'], 'm_ass_sum_uq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mmdst_assessment_sector_summaries');
    }
};

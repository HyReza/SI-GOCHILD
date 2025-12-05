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
        Schema::create('mmdst_parameters', function (Blueprint $table) {
            $table->id();
            $table->string('test_element_name');
            $table->text('test_element_description')->nullable();
            $table->integer('percent_25')->nullable();
            $table->integer('percent_50')->nullable();
            $table->integer('percent_75')->nullable();
            $table->integer('percent_100')->nullable();
            $table->foreignId('stimulation_category_id')->constrained('category_parameters')->onDelete('cascade');
            $table->boolean('parameter_is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mmdst_parameters');
    }
};

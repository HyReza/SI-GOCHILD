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
        Schema::create('report_health_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->string('item_name'); // Cth: "Mata - Penglihatan", "Kebersihan", "Rambut", "Kuku"
            $table->string('item_value'); // Cth: "Baik", "Cukup", "Kurang", "Karies"
            $table->timestamps();

            $table->unique(['report_id', 'item_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_health_details');
    }
};

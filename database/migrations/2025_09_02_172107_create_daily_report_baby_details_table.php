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
        Schema::create('daily_report_baby_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')
                ->constrained('daily_reports')
                ->onDelete('cascade')
                ->unique(); // one-to-one

            // FEEDS: data berulang → JSON (array of objects)
            // contoh payload:
            // asi_formula_items: [{ "jam":"08:30", "takaran":120, "asi":true }, ...]
            $table->json('asi_formula_items')->nullable();

            // mpasi_items: [{ "jam":"11:30", "jumlah":"banyak|sedikit" }, ...]
            $table->json('mpasi_items')->nullable();

            // Makan (teks bebas)
            $table->string('infant_breakfast_text')->nullable();
            $table->string('infant_lunch_text')->nullable();
            $table->string('infant_dinner_text')->nullable();

            // Naps: [{ "tidur":"12:30", "bangun":"13:10" }, ...]
            $table->json('naps')->nullable();

            // Diapers: [{ "jam":"10:20", "bak":true, "bab":false }, ...]
            $table->json('diapers')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_report_baby_details');
    }
};

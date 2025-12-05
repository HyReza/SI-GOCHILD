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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            // PERUBAHAN UTAMA: Relasi ke activity_transactions
            $table->foreignId('activity_transaction_id')->constrained('activity_transactions')->onDelete('cascade');
            $table->string('report_title');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('overall_summary')->nullable();
            $table->text('recommendations')->nullable();
            $table->json('attendance_summary')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};

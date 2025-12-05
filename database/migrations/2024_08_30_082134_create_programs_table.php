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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('program_name'); // 'nama_program' changed to 'program_name'
            $table->string('program_description'); // 'deskripsi_program' changed to 'program_description'
            $table->integer('program_price'); // 'harga_program' changed to 'program_price'
            $table->time('start_time'); // 'jam_mulai' changed to 'start_time'
            $table->time('end_time'); // 'jam_selesai' changed to 'end_time'
            $table->timestamps(); // 'timestamps' remains the same
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};

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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name'); // 'nama_service' changed to 'service_name'
            $table->string('service_description'); // 'deskripsi_service' changed to 'service_description'
            $table->integer('service_price'); // 'harga_service' changed to 'service_price'
            $table->timestamps(); // 'timestamps' remains the same
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};

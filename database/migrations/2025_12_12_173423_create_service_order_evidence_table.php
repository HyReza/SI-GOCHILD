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
        Schema::create('service_order_evidence', function (Blueprint $table) {
            $table->id();
            // Terhubung ke service_orders, jika order dihapus, foto bukti ikut terhapus
            $table->foreignId('service_order_id')->constrained('service_orders')->cascadeOnDelete();

            // Path file foto bukti di storage
            $table->string('file_path');

            // Opsional: Keterangan per foto (misal: "Foto sebelum treatment", "Foto sesudah")
            $table->string('description')->nullable();

            // Siapa yang mengupload bukti ini
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_order_evidence');
    }
};

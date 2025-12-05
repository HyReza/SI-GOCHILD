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
        Schema::create('billing_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billing_id')->constrained('billings')->onDelete('cascade');

            // Tipe item ini menentukan dari mana sumber harga diambil saat tagihan dibuat
            $table->enum('item_type', [
                'program_fee',      // Biaya Program Utama (ambil harga terbaru dari programs)
                'service_fee',      // Biaya Service Daycare/Childhood (ambil harga terbaru dari services)
                'extra_service',    // Dari service_orders (harga sudah distamp di service_orders)
                'absence_fine',     // Dari absence_fines (harga/denda sudah distamp di absence_fines)
                'manual_charge',    // Nominal Tambahan Admin
                'manual_discount'   // Diskon Admin
            ]);

            $table->string('item_description');

            // Relasi Polimorfik: Menghubungkan ke tabel sumber transaksi (optional untuk program/service fee)
            $table->nullableMorphs('source'); // source_id & source_type (misal: ActivityTransaction.id atau ServiceOrder.id)

            // KUNCI PRICE STAMPING: Jumlah Nominal yang dicatat untuk tagihan ini
            $table->unsignedBigInteger('amount');
            $table->enum('sign', ['debit', 'credit']); // debit=menambah tagihan, credit=mengurangi tagihan

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_items');
    }
};

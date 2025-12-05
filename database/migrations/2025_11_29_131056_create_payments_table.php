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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Relasi Polimorfik:
            // paymentable_type = 'App\Models\ServiceOrder' (Untuk Pay Now)
            // paymentable_type = 'App\Models\Billing' (Untuk Bayar Tagihan Bulanan)
            $table->morphs('paymentable');

            $table->unsignedBigInteger('amount'); // Nominal yang dibayar
            $table->string('proof_image'); // Path file gambar di storage

            $table->string('bank_destination')->nullable(); // Misal: 'BCA Sekolah'
            $table->string('sender_name')->nullable(); // Nama di rekening pengirim
            $table->string('sender_bank')->nullable(); // Misal: 'Mandiri', 'BCA'

            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('rejection_note')->nullable(); // Alasan jika admin menolak bukti

            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

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
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('extra_service_id')->constrained('extra_services')->restrictOnDelete();

            $table->date('order_date');
            $table->integer('quantity')->default(1);

            // Snapshot Harga
            $table->unsignedBigInteger('base_price_at_order');
            $table->unsignedBigInteger('final_price_per_unit');
            $table->unsignedBigInteger('total_final_price');

            $table->text('discount_note')->nullable();

            // Opsi Bayar
            $table->enum('payment_method', ['pay_now', 'bill_later'])->default('bill_later');

            // Status Order
            $table->enum('status', [
                'pending_payment',      // (Pay Now) Menunggu upload bukti bayar
                'pending_confirmation', // (Pay Now) Sudah upload, tunggu admin cek
                'pending_process',      // Sedang diproses guru/admin (menunggu dikerjakan)
                'completed',            // Selesai dikerjakan (Foto bukti sudah diupload)
                'cancelled',            // Batal
                'rejected'              // Ditolak
            ])->default('pending_process');

            // Relasi ke Tagihan (Jika bill_later)
            $table->foreignId('billing_id')->nullable()->constrained('billings')->nullOnDelete();

            // Siapa yang memproses/mengubah status
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();

            // Catatan Penyelesaian (Misal: "Anak kooperatif saat dipijat")
            $table->text('completion_note')->nullable();

            // Waktu selesai dikerjakan
            $table->dateTime('completed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_orders');
    }
};

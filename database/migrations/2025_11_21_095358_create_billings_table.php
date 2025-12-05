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
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');

            $table->date('billing_date');
            $table->date('due_date');

            $table->unsignedBigInteger('total_base_amount'); // Total biaya (debit)
            $table->unsignedBigInteger('total_discount_amount')->default(0); // Total diskon (credit)
            $table->unsignedBigInteger('final_amount'); // total_base_amount - total_discount_amount

            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'cancelled'])->default('unpaid');
            $table->dateTime('paid_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};

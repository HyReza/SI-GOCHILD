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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary(); // ID unik berupa UUID (bukan angka urut)
            $table->string('type'); // Menyimpan nama class notifikasi (misal: App\Notifications\NewOrderNotification)

            // Kolom Morph (polimorfik)
            // Akan otomatis membuat 'notifiable_type' (User/Student) dan 'notifiable_id'
            $table->morphs('notifiable');

            $table->text('data'); // Menyimpan data JSON (pesan, url, icon, dll)
            $table->timestamp('read_at')->nullable(); // Menandai kapan notifikasi dibaca
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

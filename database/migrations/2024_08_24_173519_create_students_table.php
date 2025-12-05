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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_number')->unique(); // 'no_induk' changed to 'student_number'
            $table->string('national_id')->nullable()->default(null); // 'nik' changed to 'national_id'
            $table->string('student_name'); // 'nama' changed to 'name'
            $table->string('nickname')->nullable()->default(null); // 'nama_panggilan' changed to 'nickname'
            $table->date('entry_date'); // 'tanggal_masuk' changed to 'entry_date'
            $table->boolean('gender'); // 'gender' remains the same
            $table->string('birth_place')->nullable()->default(null); // 'tempat_lahir' changed to 'birth_place'
            $table->date('birth_date')->nullable()->default(null); // 'tanggal_lahir' changed to 'birth_date'
            $table->string('father_name')->nullable()->default(null); // 'nama_ayah' changed to 'father_name'
            $table->string('mother_name')->nullable()->default(null); // 'nama_ibu' changed to 'mother_name'
            $table->string('street')->nullable()->default(null); // 'jalan' changed to 'street'
            $table->string('village')->nullable()->default(null); // 'desa' changed to 'village'
            $table->string('subdistrict')->nullable()->default(null); // 'kecamatan' changed to 'subdistrict'
            $table->string('district')->nullable()->default(null); // 'kabupaten' changed to 'district'
            $table->string('phone_number')->nullable()->default(null); // 'no_telephone' changed to 'phone_number'
            $table->string('user_photo')->nullable()->default(null); // 'foto_user' changed to 'user_photo'
            $table->text('student_description')->nullable()->default(null); //  'student_description' for abnormal students
            $table->string('password'); // 'password' remains the same
            $table->timestamps(); // 'timestamps' remains the same
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};

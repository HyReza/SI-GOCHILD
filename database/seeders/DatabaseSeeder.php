<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use App\Models\Role;
use App\Models\Program;
use App\Models\Service;
use App\Models\CategoryParameter;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Menambahkan User
        User::factory()->create([
            'user_name' => 'Test Admin',
            'email' => 'test@gmail.com',
        ]);

        // Menambahkan Role
        Role::create([
            'role_name' => 'admin',
        ]);
        Role::create([
            'role_name' => 'teacher',
        ]);

        // Menambahkan Program
        Program::create([
            'program_name' => 'Program A',
            'program_description' => 'Sampai siang hari',
            'program_price' => 100000,
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
        ]);
        Program::create([
            'program_name' => 'Program B',
            'program_description' => 'Full time sampai selesai',
            'program_price' => 200000,
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
        ]);

        Program::create([
            'program_name' => 'Program C',
            'program_description' => 'Full time sampai selesai',
            'program_price' => 200000,
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
        ]);

        Program::create([
            'program_name' => 'Program D',
            'program_description' => 'Full time sampai selesai',
            'program_price' => 200000,
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
        ]);

        // Menambahkan Service
        Service::create([
            'service_name' => 'Baby Childhood',
            'service_description' => 'Untuk bayi',
            'service_price' => 50000, // Ganti dengan harga yang sesuai
        ]);
        Service::create([
            'service_name' => 'Children Daycare',
            'service_description' => 'Untuk anak-anak',
            'service_price' => 100000, // Ganti dengan harga yang sesuai
        ]);

        CategoryParameter::create([
            'category_parameter_name' => 'Personal - Sosial',
            'category_parameter_description' => 'Aspek perkembangan anak terkait interaksi sosial, kemandirian, dan hubungan dengan orang lain.',
        ]);

        CategoryParameter::create([
            'category_parameter_name' => 'Motorik Halus',
            'category_parameter_description' => 'Aspek keterampilan menggunakan otot kecil, koordinasi mata-tangan, dan aktivitas detail.',
        ]);

        CategoryParameter::create([
            'category_parameter_name' => 'Bahasa',
            'category_parameter_description' => 'Aspek kemampuan komunikasi, pemahaman, dan penggunaan bahasa anak.',
        ]);

        CategoryParameter::create([
            'category_parameter_name' => 'Motorik Kasar',
            'category_parameter_description' => 'Aspek perkembangan gerakan otot besar seperti berjalan, berlari, dan keseimbangan tubuh.',
        ]);
    }
}

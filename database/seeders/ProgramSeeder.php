<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Program;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { {
            // Program A
            Program::create([
                'program_name' => 'Program A',
                'program_description' => 'Sampai siang hari',
                'program_price' => 100000,
                'start_time' => '08:00:00',
                'end_time' => '12:00:00',
            ]);

            // Program B
            Program::create([
                'program_name' => 'Program B',
                'program_description' => 'Full time sampai selesai',
                'program_price' => 200000,
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
            ]);
        }
    }
}

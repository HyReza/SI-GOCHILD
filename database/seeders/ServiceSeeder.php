<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;


class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Service Baby Childhood
        Service::create([
            'service_name' => 'Baby Childhood',
            'service_description' => 'Untuk bayi',
            'service_price' => 50000, // Ganti dengan harga yang sesuai
        ]);

        // Service Children Daycare
        Service::create([
            'service_name' => 'Children Daycare',
            'service_description' => 'Untuk anak-anak',
            'service_price' => 100000, // Ganti dengan harga yang sesuai
        ]);
    }
}

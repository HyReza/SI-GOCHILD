<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Service Baby Childhood
        Role::create([
            'role_name' => 'admin',
        ]);

        // Service Children Daycare
        Role::create([
            'role_name' => 'teacher',
        ]);
    }
}

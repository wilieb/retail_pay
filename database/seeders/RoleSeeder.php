<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::upsert([
            ['id' => 1, 'name' => 'admin'],
            ['id' => 2, 'name' => 'branch_manager'],
            ['id' => 3, 'name' => 'store_manager'],
        ], ['name']);
    }
}

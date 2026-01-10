<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::upsert([
            ['id' => 1, 'name' => 'admin', 'description' => 'Administrator with full access'],
            ['id' => 2, 'name' => 'branch_manager', 'description' => 'Branch manager with branch-level access'],
            ['id' => 3, 'name' => 'store_manager', 'description' => 'Store manager with store-level access'],
        ], ['name']);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Store::upsert([
            ['id' => 1, 'name' => 'Store One', 'branch_id' => 1],
            ['id' => 2, 'name' => 'Store Two', 'branch_id' => 2],
            ['id' => 3, 'name' => 'Store Three', 'branch_id' => 2],
        ], ['name']);
    }
}

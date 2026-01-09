<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Branch::upsert([
            ['id' => 1, 'name' => 'Branch One'],
            ['id' => 2, 'name' => 'Branch Two'],
        ], ['name']);
    }
}

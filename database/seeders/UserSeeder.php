<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::upsert([
            [
                'email' => 'admin@mail.com',
                'password' => bcrypt('password'),
                'role_id' => 1,
                'name' => 'Administrator',
                'branch_id' => null,
                'store_id' => null,
            ],
            [
                'email' => 'branch1@mail.com',
                'password' => bcrypt('password'),
                'role_id' => 2,
                'name' => 'Branch Manager One',
                'branch_id' => 1,
                'store_id' => null,
            ],
            [
                'email' => 'branc2@mail.com',
                'password' => bcrypt('password'),
                'role_id' => 2,
                'name' => 'Branch Manager Two',
                'branch_id' => 2,
                'store_id' => null,
            ],
            [
                'email' => 'store1@mail.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'name' => 'Store Manager One',
                'branch_id' => null,
                'store_id' => 1,
            ],
            [
                'email' => 'store2@mail.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'name' => 'Store Manager Two',
                'branch_id' => null,
                'store_id' => 2,
            ],
            [
                'email' => 'store3@mail.com',
                'password' => bcrypt('password'),
                'role_id' => 3,
                'name' => 'Store Manager Three',
                'branch_id' => null,
                'store_id' => 2,
            ],
        ], ['email']);
    }
}

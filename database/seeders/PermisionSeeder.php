<?php

namespace Database\Seeders;

use App\Models\Permision;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'view_inventory', 'description' => 'View inventory data'],
            ['name' => 'create_sales', 'description' => 'Create sales transactions'],
            ['name' => 'request_transfer', 'description' => 'Request inventory transfers'],
            ['name' => 'view_activity_logs', 'description' => 'View activity logs'],
            ['name' => 'approve_transfer', 'description' => 'Approve inventory transfers'],
            ['name' => 'manage_branches', 'description' => 'Manage branches and stores'],
            ['name' => 'manage_users', 'description' => 'Manage users and roles'],
            ['name' => 'manage_permissions', 'description' => 'Assign permissions to roles'],
        ];

        foreach ($permissions as $permission) {
            Permision::firstOrCreate(['name' => $permission['name']], $permission);
        }

        $adminRole = Role::where('name', 'admin')->first();
        $branchManagerRole = Role::where('name', 'branch_manager')->first();
        $storeManagerRole = Role::where('name', 'store_manager')->first();

        if ($adminRole) {
            $adminRole->permissions()->syncWithoutDetaching(
                Permision::pluck('id')->toArray()
            );
        }

        if ($branchManagerRole) {
            $branchManagerRole->permissions()->syncWithoutDetaching([
                Permision::where('name', 'view_inventory')->first()->id,
                Permision::where('name', 'create_sales')->first()->id,
                Permision::where('name', 'request_transfer')->first()->id,
                Permision::where('name', 'approve_transfer')->first()->id,
                Permision::where('name', 'view_activity_logs')->first()->id,
            ]);
        }

        if ($storeManagerRole) {
            $storeManagerRole->permissions()->syncWithoutDetaching([
                Permision::where('name', 'view_inventory')->first()->id,
                Permision::where('name', 'create_sales')->first()->id,
                Permision::where('name', 'view_activity_logs')->first()->id,
            ]);
        }
    }
}

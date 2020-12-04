<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'Classic User',
            ],
            [
                'name' => 'Admin',
            ],
            [
                'name' => 'Owner',
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}

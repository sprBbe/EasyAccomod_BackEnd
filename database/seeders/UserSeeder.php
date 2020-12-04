<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'SpringBbe',
                'email' => 'admin@gmail.com',
                'password' => '123456',
                'detail_address' => 'Số 53 Xuân Thủy',
                'national_id_number' => '1234567890',
                'phone' => '0123456789',
                'id_role' => 2,
                'id_ward' => 1,
            ],
            [
                'name' => 'SpringBbe',
                'email' => 'owner@gmail.com',
                'password' => '123456',
                'detail_address' => 'Số 55 Xuân Thủy',
                'national_id_number' => '1234567890',
                'phone' => '0123456789',
                'id_role' => 3,
                'id_ward' => 1,
            ],
            [
                'name' => 'SpringBbe',
                'email' => 'classic_user@gmail.com',
                'password' => '123456',
                'detail_address' => 'Số 53 Xuân Thủy',
                'national_id_number' => '1234567890',
                'phone' => '0123456789',
                'id_role' => 1,
                'id_ward' => 1,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}

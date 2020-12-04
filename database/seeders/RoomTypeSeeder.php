<?php

namespace Database\Seeders;

use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roomtypes = [
            [
                'name' => 'Phòng trọ',
            ],
            [
                'name' => 'Chung cư mini',
            ],
            [
                'name' => 'Nhà nguyên căn',
            ],
            [
                'name' => 'Chung cư nguyên căn',
            ]
        ];

        foreach ($roomtypes as $roomtype) {
            RoomType::create($roomtype);
        }
    }
}

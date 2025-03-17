<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shift::create([
            'code' => 'PGS001',
            'name' => 'PAGI',
            'shift_start' => '08:00:00',
            'shift_end' => '17:00:00',
            'status' => true,
        ]);

        Shift::create([
            'code' => 'SGN001',
            'name' => 'SIANG',
            'shift_start' => '12:00:00',
            'shift_end' => '21:00:00',
            'status' => true,
        ]);

        Shift::create([
            'code' => 'SRI001',
            'name' => 'SORE',
            'shift_start' => '15:00:00',
            'shift_end' => '00:00:00',
            'status' => true,
        ]);

        Shift::create([
            'code' => 'MLM001',
            'name' => 'MALAM',
            'shift_start' => '00:00:00',
            'shift_end' => '08:00:00',
            'status' => true,
        ]);
    }
}

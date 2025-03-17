<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create a few shift types for testing
        $shifts = [
            [
                'name' => 'Reguler Pagi',
                'shift_start' => '08:00:00',
                'shift_end' => '17:00:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Siang',
                'shift_start' => '12:00:00',
                'shift_end' => '21:00:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Malam',
                'shift_start' => '21:00:00',
                'shift_end' => '06:00:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Weekend',
                'shift_start' => '10:00:00',
                'shift_end' => '19:00:00',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        foreach ($shifts as $shift) {
            Shift::create($shift);
        }
    }
}
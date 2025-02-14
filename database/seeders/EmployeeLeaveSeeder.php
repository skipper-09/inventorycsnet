<?php

namespace Database\Seeders;

use App\Models\Leave;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class EmployeeLeaveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $startDate = $faker->date();

        $endDate = date('Y-m-d', strtotime($startDate . ' +5 days'));
        for ($i = 0; $i < 10; $i++) {
            Leave::insert([
                'employee_id' => $faker->randomNumber(1),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'reason' => $faker->sentence(),
                'status' => $faker->randomElement(['approved', 'pending','rejected']),
                'year' => $faker->year(),
            ]);
        }
    }
}

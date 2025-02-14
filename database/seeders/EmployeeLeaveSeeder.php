<?php

namespace Database\Seeders;

use App\Models\Employee;
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

        $employeeIds = Employee::pluck('id')->toArray();
        for ($i = 0; $i < 10; $i++) {
            $startDate = $faker->date();
            $endDate = date('Y-m-d', strtotime($startDate . ' +5 days'));
            $employeeId = $faker->randomElement($employeeIds);
            Leave::insert([
                'employee_id' => $employeeId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'reason' => $faker->sentence(),
                'status' => $faker->randomElement(['approved', 'pending','rejected']),
                'year' => $faker->year(),
            ]);
        }
    }
}

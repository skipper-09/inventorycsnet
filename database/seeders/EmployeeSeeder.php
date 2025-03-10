<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Create 2 employees with associated users
        for ($i = 0; $i < 2; $i++) {
            // Create employee data
            $employee = Employee::create([
                'department_id' => rand(1, 5), // Assuming there are 5 departments
                'position_id' => rand(1, 10), // Assuming there are 10 positions
                'name' => $faker->name,
                'address' => $faker->address,
                'phone' => $faker->phoneNumber,
                'email' => $faker->unique()->safeEmail,
                'date_of_birth' => $faker->date('Y-m-d', '-20 years'),
                'gender' => $faker->randomElement(['male', 'female']),
                'nik' => $faker->unique()->numerify('##############'),
                'identity_card' => 'identity_card_' . $i . '.pdf', // Use a placeholder for file
            ]);

            // Create associated user data
            $user = User::create([
                'employee_id' => $employee->id, // Link user to employee
                'name' => $employee->name,
                'username' => Str::lower(Str::random(10)), // Random username, lowercase
                'email' => $faker->unique()->safeEmail, // Unique email
                'password' => Hash::make('password'), // Default password
                'picture' => 'picture_' . $i . '.jpg', // Placeholder for user picture
                'is_block' => false, // Default user status
            ]);

            // Optionally assign roles to the user (e.g., 'employee' role)
            $user->assignRole('Employee'); // Assuming the 'employee' role exists
        }
    }

}

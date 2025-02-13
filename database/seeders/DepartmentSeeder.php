<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Human Resources', 'location' => 'Floor 1'],
            ['name' => 'Information Technology', 'location' => 'Floor 2'],
            ['name' => 'Finance', 'location' => 'Floor 3'],
            ['name' => 'Marketing', 'location' => 'Floor 4'],
            ['name' => 'Operations', 'location' => 'Floor 5'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}

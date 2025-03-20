<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            ['name' => 'Manager'],
            ['name' => 'Supervisor'],
            ['name' => 'Staff'],
            ['name' => 'Senior Developer'],
            ['name' => 'Junior Developer'],
            ['name' => 'HR Specialist'],
            ['name' => 'Accountant'],
            ['name' => 'Marketing Specialist'],
            ['name' => 'Technician'],
            ['name' => 'Operator'],
            ['name' => 'NOC Support'],
            ['name' => 'NOC'],
            ['name' => 'Customer Service'],
        ];

        foreach ($positions as $position) {
            Position::create($position);
        }
    }
}

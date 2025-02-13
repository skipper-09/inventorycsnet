<?php

namespace Database\Seeders;

use App\Models\DeductionType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeductionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $deductionTypes = [
            ['name' => 'Late Arrival'],
            ['name' => 'Early Departure'],
            ['name' => 'Absence'],
            ['name' => 'Insurance Premium'],
            ['name' => 'Tax Deduction'],
            ['name' => 'Loan Payment'],
            ['name' => 'Equipment Damage'],
            ['name' => 'Uniform Cost'],
            ['name' => 'Administrative Fee'],
            ['name' => 'Performance Penalty'],
        ];

        foreach ($deductionTypes as $type) {
            DeductionType::create($type);
        }
    }
}

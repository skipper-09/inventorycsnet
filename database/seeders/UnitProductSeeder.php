<?php

namespace Database\Seeders;

use App\Models\UnitProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UnitProduct::insert([
            [
                'name' => 'Unit',
            ],
            [
                'name' => 'Meter',
            ],
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\AllowanceType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AllowanceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allowanceTypes = [
            ['name' => 'Tunjangan Makan'],
            ['name' => 'Tunjangan Transportasi'],
            ['name' => 'Tunjangan Kesehatan'],
            ['name' => 'Tunjangan Hari Raya'],
            ['name' => 'Tunjangan Jabatan'],
            ['name' => 'Bonus Kinerja'],
            ['name' => 'Uang Lembur'],
        ];

        foreach ($allowanceTypes as $type) {
            AllowanceType::create($type);
        }
    }
}

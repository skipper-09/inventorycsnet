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
            ['name' => 'Potongan BPJS'],
            ['name' => 'Potongan PPh 21'],
            ['name' => 'Potongan Keterlambatan'],
            ['name' => 'Potongan Pinjaman'],
            ['name' => 'Potongan Absensi'],
            ['name' => 'Sanksi/Denda'],
        ];

        foreach ($deductionTypes as $type) {
            DeductionType::create($type);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::insert([
            [
                'name' => 'PT. Cahaya Solusindo Internusa',
                'address' => 'jln. Letjen S. Parman No. 58, Pakis, Banyuwangi ( 68418  )'
            ],
            [
                'name' => 'PT. Internusa Duta Makmur',
                'address' => 'Ruko Gajah Mada Square, Jl. Gajah Mada.187 Blok A 21, Kaliwates Kidul, Kaliwates, Kec. Kaliwates, Kabupaten Jember, Jawa Timur 68121'
            ],
        ]);
    }
}

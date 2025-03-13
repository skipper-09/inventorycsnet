<?php

namespace Database\Seeders;

use App\Models\Office;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfficeSeedeer extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Office::insert([
            [
                'name' => 'Kantor CSNET',
                'company_id' => 1,
                'lat' => '-8.24063251629653',
                'long' => '114.35511160310098',
                'radius' => 200,
                'address' => 'Jl. Letjen S Parman No.58, Sumberrejo, Pakis, Kec. Banyuwangi, Kabupaten Banyuwangi, Jawa Timur 68419'
            ],
        ]);
    }
}

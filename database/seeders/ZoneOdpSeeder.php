<?php

namespace Database\Seeders;

use App\Models\ZoneOdp;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZoneOdpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ZoneOdp::insert([
            'name' => 'tes',
            'zone_id' => 1,
        ]);
    }
}

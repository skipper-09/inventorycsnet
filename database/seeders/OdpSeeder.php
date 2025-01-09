<?php

namespace Database\Seeders;

use App\Models\Odp;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OdpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Odp::insert([
            [
                'zone_id' => 1,
                'odp_id' => 'odp-1',
                'name' => 'odp-1',
                'latitude' => '123',
                'longitude' => '123'
            ],
            [
                'zone_id' => 1,
                'odp_id' => 'odp-2',
                'name' => 'odp-2',
                'latitude' => '123',
                'longitude' => '123'
            ],
            [
                'zone_id' => 1,
                'odp_id' => 'odp-3',
                'name' => 'odp-3',
                'latitude' => '123',
                'longitude' => '123'
            ],
        ]);
    }
}

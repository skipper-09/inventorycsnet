<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::insert([
            [
                'name' => 'Zte',
                'description' => 'Modem Zte, Xpon',
                'is_modem' => true,
                'unit_id' => 1
            ],
            
            [
                'name' => 'Router',
                'description' => 'Router Zte, Xpon',
                'is_modem' => false,
                'unit_id' => 1
            ],

            [
                'name' => 'Switch',
                'description' => 'Switch Zte, Xpon',
                'is_modem' => false,
                'unit_id' => 1
            ],
        ]);
    }
}

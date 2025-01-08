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
                'name' => 'Product 1',
                'description' => 'Description 1',
                'unit_id' => 1
            ],
            [
                'name' => 'Product 2',
                'description' => 'Description 2',
                'unit_id' => 2
            ],
            [
                'name' => 'Product 3',
                'description' => 'Description 3',
                'unit_id' => 2
            ],
            [
                'name' => 'Product 4',
                'description' => 'Description 4',
                'unit_id' => 1
            ],
            [
                'name' => 'Product 5',
                'description' => 'Description 5',
                'unit_id' => 1
            ],
            [
                'name' => 'Product 6',
                'description' => 'Description 6',
                'unit_id' => 2
            ],
        ]);
    }
}

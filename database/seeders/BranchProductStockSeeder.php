<?php

namespace Database\Seeders;

use App\Models\BranchProductStock;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchProductStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BranchProductStock::insert([
            [
                'branch_id' => 1,
                'product_id' => 1,
                'stock' => 10
            ],
            [
                'branch_id' => 2,
                'product_id' => 1,
                'stock' => 20
            ],
            [
                'branch_id' => 3,
                'product_id' => 1,
                'stock' => 30
            ],
            [
                'branch_id' => 4,
                'product_id' => 1,
                'stock' => 40
            ],
        ]);
    }
}

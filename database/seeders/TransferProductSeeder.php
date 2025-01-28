<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Branches if not exists
        if (Branch::count() > 0) {
            $branches = [
                ['name' => 'Cabang Pusat', 'address' => 'Jl. Pusat No. 1'],
                ['name' => 'Cabang Utara', 'address' => 'Jl. Utara No. 2'],
                ['name' => 'Cabang Selatan', 'address' => 'Jl. Selatan No. 3'],
            ];

            foreach ($branches as $branch) {
                Branch::create($branch);
            }
        }

        // Create Products if = 1
        if (Product::count() === 1) {
            $products = [
                [
                    'name' => 'Router WiFi',
                    'description' => 'Router WiFi Dual Band',
                    'unit_id' => 1,
                ],
                [
                    'name' => 'Kabel LAN',
                    'description' => 'Kabel LAN Cat 6',
                    'unit_id' => 2,
                ],
            ];

            foreach ($products as $product) {
                Product::create($product);
            }
        }

        // Create Transfer Transactions
        try {
            DB::beginTransaction();

            // Initial stock in for branches
            $branches = Branch::all();
            $products = Product::all();

            foreach ($branches as $branch) {
                // Create initial stock transaction for each branch
                $stockIn = Transaction::create([
                    'branch_id' => $branch->id,
                    'type' => 'in',
                    'user_id'=>1,
                    'purpose' => 'stock_in',
                ]);

                // Add initial stock for each product
                foreach ($products as $product) {
                    TransactionProduct::create([
                        'transaction_id' => $stockIn->id,
                        'product_id' => $product->id,
                        'quantity' => rand(50, 100), // Random initial stock
                    ]);
                }
            }

            // Create sample transfers between branches
            $transferScenarios = [
                [
                    'from' => 1, // Pusat
                    'to' => 2,   // Utara
                    'user_id'=>1,
                    'products' => [
                        ['id' => 2, 'qty' => 5],  // Router
                        ['id' => 3, 'qty' => 10], // Kabel
                    ]
                ],
                [
                    'from' => 1, // Pusat
                    'to' => 3,   // Selatan
                    'user_id'=>1,
                    'products' => [
                        ['id' => 2, 'qty' => 3],  // Switch
                        ['id' => 3, 'qty' => 4],  // AP
                    ]
                ],
                [
                    'from' => 2, // Utara
                    'to' => 3,   // Selatan
                    'user_id'=>1,
                    'products' => [
                        ['id' => 2, 'qty' => 2],  // Router
                        ['id' => 3, 'qty' => 1],  // Switch
                    ]
                ],
            ];

            foreach ($transferScenarios as $scenario) {
                // Create outgoing transfer
                $transfer = Transaction::create([
                    'branch_id' => $scenario['from'],
                    'to_branch' => $scenario['to'],
                    'user_id' => $scenario['user_id'],
                    'type' => 'out',
                    'purpose' => 'transfer',
                ]);

                // Add products to transfer
                foreach ($scenario['products'] as $product) {
                    TransactionProduct::create([
                        'transaction_id' => $transfer->id,
                        'product_id' => $product['id'],
                        'quantity' => $product['qty'],
                    ]);
                }

                // Create incoming transfer
                $receiveTransfer = Transaction::create([
                    'branch_id' => $scenario['to'],
                    'type' => 'in',
                    'user_id'=>1,
                    'purpose' => 'transfer',
                ]);

                // Add products to receiving transfer
                foreach ($scenario['products'] as $product) {
                    TransactionProduct::create([
                        'transaction_id' => $receiveTransfer->id,
                        'product_id' => $product['id'],
                        'quantity' => $product['qty'],
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(''. $e->getMessage());
            throw $e;
        }
    }
}
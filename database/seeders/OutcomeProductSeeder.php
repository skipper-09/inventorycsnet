<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionProduct;
use App\Models\BranchProductStock;
use Illuminate\Support\Facades\DB;
use Exception;

class OutcomeProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        try {
            // Data Cabang dan Produk Dummy
            $branches = Branch::all();
            $products = Product::all();

            if ($branches->isEmpty() || $products->isEmpty()) {
                throw new Exception('Branch or Product data is missing. Please seed them first.');
            }

            foreach ($branches as $branch) {
                foreach ($products as $product) {
                    // Ambil stok cabang
                    $branchProductStock = BranchProductStock::where('branch_id', $branch->id)
                        ->where('product_id', $product->id)
                        ->first();

                    if (!$branchProductStock || $branchProductStock->stock <= 0) {
                        continue; // Lewati jika stok tidak ada atau kosong
                    }

                    // Jumlah produk pengeluaran random (contoh: 1-50)
                    $quantity = rand(1, min(50, $branchProductStock->stock));

                    // Create Transaction
                    $transaction = Transaction::create([
                        'branch_id' => $branch->id,
                        'type' => 'out', // Tipe transaksi pengeluaran barang
                    ]);

                    // Create Transaction Product
                    TransactionProduct::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                    ]);

                    // Kurangi stok produk di cabang
                    $branchProductStock->stock -= $quantity;
                    $branchProductStock->save();
                }
            }

            DB::commit();

            $this->command->info('OutcomeProductSeeder has successfully seeded data.');
        } catch (Exception $e) {
            DB::rollBack();
            $this->command->error('Failed to seed OutcomeProductSeeder: ' . $e->getMessage());
        }
    }
}

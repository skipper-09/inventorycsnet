<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchProductStock;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionProduct;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class IncomeProductController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Pemasukan Barang',
            'branch' => Branch::all(),
            'product' => Product::all(),
        ];
        return view('pages.transaction.incomeproduct.index', $data);
    }

    //getdata
    public function getData()
    {
        $data = TransactionProduct::with(['product', 'transaksi']) ->whereHas('transaksi', function ($query) {
            $query->where('type', 'in');
        })->orderByDesc('id')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            // $userauth = User::with('roles')->where('id', Auth::id())->first();
            $button = '';
            // $button .= ' <a href="' . route('dashboard') . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
            //                                             class="fas fa-pen "></i></a>';

            $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('incomeproduct.edit', ['id' => $data->id]) . '" data-proses="' . route('incomeproduct.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
                             data-action="edit" data-title="Unit Produk" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                                         class="fas fa-pen "></i></button>';

            $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->transaction_id . ' data-type="delete" data-route="' . route('incomeproduct.delete', ['id' => $data->transaction_id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                         class="fas fa-trash "></i></button>';
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->editColumn('branch', function ($data) {
            return $data->transaksi->branch->name;
        })->editColumn('product', function ($data) {
            return $data->product->name;
        })->editColumn('quantity', function ($data) {
            return $data->quantity . " " . $data->product->unit->name;
        })->editColumn('created_at', function ($data) {
            return \Carbon\Carbon::parse($data->created_at)->format('d M Y, H:i');
        })->rawColumns(['action', 'branch', 'product', 'created_at'])->make(true);
    }


    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required',
            'product_id' => 'required',
            'qty' => 'required|integer|min:1',
        ], [
            'branch_id.required' => 'Cabang wajib diisi.',
            'product_id.required' => 'Barang wajib diisi.',
            'qty.required' => 'Jumlah produk wajib diisi.',
            'qty.integer' => 'Jumlah produk harus berupa angka.',
            'qty.min' => 'Jumlah produk tidak bisa kurang dari 1.',
        ]);
        DB::beginTransaction();
        try {

            $transaction = Transaction::create([
                'branch_id' => $request->branch_id,
            ]);

            $transactionProduct = TransactionProduct::create([
                'transaction_id' => $transaction->id,
                'product_id' => $request->product_id,
                'quantity' => $request->qty,
            ]);

            $branchProductStock = BranchProductStock::where('branch_id', $request->branch_id)
                ->where('product_id', $request->product_id)
                ->first();

            if ($branchProductStock) {
                $branchProductStock->stock += $request->qty;
                $branchProductStock->save();
            } else {
                BranchProductStock::create([
                    'branch_id' => $request->branch_id,
                    'product_id' => $request->product_id,
                    'stock' => $request->qty,
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Data Pemasukan Berhasil dibuat.'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }


    public function show($id)
    {
        $transactionproduct = TransactionProduct::with('transaksi')->findOrFail($id);
        return response()->json([
            'transactionproduct' => $transactionproduct,
        ], 200);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'branch_id' => 'required',
            'product_id' => 'required',
            'qty' => 'required|integer|min:1',
        ], [
            'branch_id.required' => 'Cabang wajib diisi.',
            'product_id.required' => 'Barang wajib diisi.',
            'qty.required' => 'Jumlah produk wajib diisi.',
            'qty.integer' => 'Jumlah produk harus berupa angka.',
            'qty.min' => 'Jumlah produk tidak bisa kurang dari 1.',
        ]);

        DB::beginTransaction();
        try {
            $transaction = Transaction::findOrFail($id);
            $transactionProduct = TransactionProduct::where('transaction_id', $transaction->id)
                ->first();
            if (!$transactionProduct) {
                throw new Exception('Produk yang ingin diubah tidak ditemukan pada transaksi ini.');
            }

            $previousQty = $transactionProduct->quantity;
            $previousBranchId = $transaction->branch_id;
            $previousProductId = $transactionProduct->product_id;

            $transaction->update([
                'branch_id' => $request->branch_id,
            ]);

            $transactionProduct->update([
                'product_id' => $request->product_id,
                'quantity' => $request->qty,
            ]);

            $branchProductStockOld = BranchProductStock::where('branch_id', $previousBranchId)
                ->where('product_id', $previousProductId)
                ->first();

            $branchProductStockNew = BranchProductStock::where('branch_id', $request->branch_id)
                ->where('product_id', $request->product_id)
                ->first();

            $qtyDifference = $request->qty - $previousQty;

            if ($previousBranchId != $request->branch_id) {
                if ($branchProductStockOld) {
                    $branchProductStockOld->stock -= $previousQty;
                    if ($branchProductStockOld->stock < 0) {
                        throw new Exception('Stok produk di cabang lama tidak cukup untuk dikurangi.');
                    }
                    $branchProductStockOld->save();
                }

                if ($branchProductStockNew) {
                    $branchProductStockNew->stock += $request->qty;
                    $branchProductStockNew->save();
                } else {
                    BranchProductStock::create([
                        'branch_id' => $request->branch_id,
                        'product_id' => $request->product_id,
                        'stock' => $request->qty,
                    ]);
                }
            } else {
                if ($previousProductId != $request->product_id) {
                    if ($branchProductStockOld) {
                        $branchProductStockOld->stock -= $previousQty;
                        if ($branchProductStockOld->stock < 0) {
                            throw new Exception('Stok produk di cabang lama tidak cukup untuk dikurangi.');
                        }
                        $branchProductStockOld->save();
                    }

                    if ($branchProductStockNew) {
                        $branchProductStockNew->stock += $request->qty;
                        $branchProductStockNew->save();
                    } else {
                        BranchProductStock::create([
                            'branch_id' => $request->branch_id,
                            'product_id' => $request->product_id,
                            'stock' => $request->qty,
                        ]);
                    }
                } else {
                    if ($branchProductStockNew) {
                        $branchProductStockNew->stock += $qtyDifference;
                        if ($branchProductStockNew->stock < 0) {
                            throw new Exception('Stok produk tidak cukup untuk perubahan.');
                        }
                        $branchProductStockNew->save();
                    } else {
                        BranchProductStock::create([
                            'branch_id' => $request->branch_id,
                            'product_id' => $request->product_id,
                            'stock' => $request->qty,
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Data Pemasukan Berhasil diupdate.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }



    //destroy data
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $transaction = Transaction::findOrFail($id);
            $transactionProducts = TransactionProduct::where('transaction_id', $transaction->id)->get();

            foreach ($transactionProducts as $transactionProduct) {
                $branchProductStock = BranchProductStock::where('branch_id', $transaction->branch_id)
                    ->where('product_id', $transactionProduct->product_id)
                    ->first();

                if ($branchProductStock) {
                    $branchProductStock->stock -= $transactionProduct->quantity;

                    if ($branchProductStock->stock < 0) {
                        throw new Exception('Stok produk di cabang tidak cukup untuk pengurangan.');
                    }
                    $branchProductStock->save();
                }
            }

            TransactionProduct::where('transaction_id', $transaction->id)->delete();
            $transaction->delete();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Data Pemasukan Berhasil Dihapus!.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal Menghapus Data Pemasukan!',
                'trace' => $e->getTrace()
            ]);
        }
    }
}

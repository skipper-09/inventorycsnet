<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchProductStock;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionProduct;
use Exception;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OutcomeProductController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Pengeluaran Barang',
            'branch' => Branch::all(),
            'product' => Product::all(),
        ];
        return view('pages.transaction.outcomeproduct.index', $data);
    }

    public function getData()
    {
        $data = TransactionProduct::with(['product', 'transaksi'])
            ->whereHas('transaksi', function ($query) {
                $query->where('type', 'out');
            })
            ->orderByDesc('id')
            ->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $button = '';
            $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('outcomeproduct.edit', ['id' => $data->id]) . '" data-proses="' . route('outcomeproduct.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
                             data-action="edit" data-title="Unit Produk" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                                         class="fas fa-pen "></i></button>';

            $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->transaction_id . ' data-type="delete" data-route="' . route('outcomeproduct.delete', ['id' => $data->transaction_id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                         class="fas fa-trash "></i></button>';
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->editColumn('branch', function ($data) {
            return $data->transaksi->branch->name;
        })->editColumn('product', function ($data) {
            return $data->product->name;
        })->editColumn('purpose', function ($data) {
            $result = "";

            if ($data->transaksi->purpose == "psb") {
                $result = 'Pemasangan Baru';
            }else if($data->transaksi->purpose == "repair"){
                $result = 'Perbaikan';
            }
            return $result;
        })->editColumn('quantity', function ($data) {
            return $data->quantity . " " . $data->product->unit->name;
        })->editColumn('created_at', function ($data) {
            return \Carbon\Carbon::parse($data->created_at)->format('d M Y, H:i');
        })->rawColumns(['action','purpose' ,'branch', 'product', 'created_at'])->make(true);
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
            // Cek stok terlebih dahulu
            $branchProductStock = BranchProductStock::where('branch_id', $request->branch_id)
                ->where('product_id', $request->product_id)
                ->first();

            if (!$branchProductStock || $branchProductStock->stock < $request->qty) {
                return response()->json([
                    'success' => false,
                    'status' => "Gagal",
                    'message' => 'Stok tidak mencukupi untuk pengeluaran. Stok saat ini: ' . ($branchProductStock->stock ?? 0),
                ], 400);
            }

            // Buat transaksi
            $transaction = Transaction::create([
                'branch_id' => $request->branch_id,
                'type' => 'out',
            ]);

            TransactionProduct::create([
                'transaction_id' => $transaction->id,
                'product_id' => $request->product_id,
                'quantity' => $request->qty,
            ]);

            // Kurangi stok produk di cabang
            $branchProductStock->stock -= $request->qty;
            $branchProductStock->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Data Pengeluaran Berhasil dibuat.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ]);
        }
    }


    public function show($id)
    {
        $transactionproduct = TransactionProduct::with('transaksi')
            ->whereHas('transaksi', function ($query) {
                $query->where('type', 'out');
            })
            ->where('id', $id)
            ->first();
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
            $transactionProduct = TransactionProduct::findOrFail($id);

            $branchProductStock = BranchProductStock::where('branch_id', $transactionProduct->transaksi->branch_id)
                ->where('product_id', $transactionProduct->product_id)
                ->first();

            if (!$branchProductStock) {
                throw new Exception('Stok awal tidak ditemukan.');
            }

            $branchProductStock->stock += $transactionProduct->quantity;
            $branchProductStock->save();

            $transactionProduct->update([
                'transaction_id' => $transactionProduct->transaction_id,
                'product_id' => $request->product_id,
                'quantity' => $request->qty,
            ]);

            $newBranchProductStock = BranchProductStock::where('branch_id', $request->branch_id)
                ->where('product_id', $request->product_id)
                ->first();

            if (!$newBranchProductStock || $newBranchProductStock->stock < $request->qty) {
                throw new Exception('Stok tidak mencukupi untuk pengeluaran.');
            }

            $newBranchProductStock->stock -= $request->qty;
            $newBranchProductStock->save();

            DB::commit();
            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Data Pengeluaran Berhasil diperbarui.',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ]);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $transactionProduct = TransactionProduct::findOrFail($id);

            $branchProductStock = BranchProductStock::where('branch_id', $transactionProduct->transaksi->branch_id)
                ->where('product_id', $transactionProduct->product_id)
                ->first();

            if (!$branchProductStock) {
                throw new Exception('Stok tidak ditemukan.');
            }

            $branchProductStock->stock += $transactionProduct->quantity;
            $branchProductStock->save();

            $transactionProduct->delete();

            $relatedProductsCount = TransactionProduct::where('transaction_id', $transactionProduct->transaction_id)->count();
            if ($relatedProductsCount == 0) {
                Transaction::findOrFail($transactionProduct->transaction_id)->delete();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Data Pengeluaran Berhasil dihapus.',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ]);
        }
    }
}

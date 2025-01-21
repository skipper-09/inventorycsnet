<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TransferProductController extends Controller
{
    /**
     * Display transfer list page
     */
    public function index()
    {
        $data = [
            'title' => 'Pemindahan Barang',
        ];

        return view('pages.transaction.transferproduct.index', $data);
    }

    /**
     * Get data for DataTables
     */
    public function getData()
    {
        $data = Transaction::with(['branch', 'tobranch', 'Transactionproduct.product'])
            ->where('purpose', 'transfer')
            ->where('type', 'out')
            ->orderByDesc('created_at')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('from_branch', function ($row) {
                return $row->branch->name ?? '-';
            })
            ->addColumn('to_branch', function ($row) {
                return $row->tobranch->name ?? '-';
            })
            ->addColumn('products', function ($row) {
                $products = [];
                foreach ($row->Transactionproduct as $tp) {
                    $unit = $tp->product->unit->name ??'';
                    $products[] = $tp->product->name . ' (' . $tp->quantity . ' ' . $unit . ')';
                }
                return implode('<br>', $products);
            })
            ->addColumn('date', function ($row) {
                return $row->created_at->format('d M Y H:i');
            })
            ->addColumn('action', function ($row) {
                $button = '';
                $button .= '<a href="' . route('transfer.details', ['id' => $row->id]) . '"
                          class="btn btn-sm btn-info" 
                          data-id="' . $row->id . '" 
                          data-type="details" 
                          data-toggle="tooltip" 
                          data-placement="bottom" 
                          title="Details">
                           <i class="fas fa-eye"></i>
                       </a>';
                $button .= '<a href="' . route('transfer.edit', ['id' => $row->id]) . '"
                          class="btn btn-sm btn-success" 
                          data-id="' . $row->id . '" 
                          data-type="edit" 
                          data-toggle="tooltip" 
                          data-placement="bottom" 
                          title="Edit Data">
                           <i class="fas fa-pen"></i>
                       </a>';
                $button .= ' <button class="btn btn-sm btn-danger action" 
                               data-id="' . $row->id . '" 
                               data-type="delete" 
                               data-route="' . route('transfer.delete', ['id' => $row->id]) . '" 
                               data-toggle="tooltip" 
                               data-placement="bottom" 
                               title="Delete Data">
                            <i class="fas fa-trash-alt"></i>
                        </button>';
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })
            ->rawColumns(['action', 'from_branch', 'to_branch', 'products', 'type', 'date'])
            ->make(true);
    }

    /**
     * Show transfer creation form
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Pemindahan Barang',
            'branch' => Branch::all(),
            'product' => Product::all(),
        ];

        return view('pages.transaction.transferproduct.add', $data);
    }

    /**
     * Store new transfer
     */
    public function store(Request $request)
    {
        $request->validate([
            'from_branch' => 'required|exists:branches,id',
            'to_branch' => 'required|exists:branches,id|different:from_branch',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            // Create transfer transaction (out)
            $transfer = Transaction::create([
                'branch_id' => $request->from_branch,
                'to_branch' => $request->to_branch,
                'type' => 'out',
                'purpose' => 'transfer'
            ]);

            // Add products to transfer
            foreach ($request->products as $product) {
                TransactionProduct::create([
                    'transaction_id' => $transfer->id,
                    'product_id' => $product['id'],
                    'quantity' => $product['quantity']
                ]);
            }

            DB::commit();
            return redirect()->route('transfer')->with([
                'status' => 'Success!',
                'message' => 'Berhasil Menambahkan Pemindahan Barang!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('transfer')->with(['status' => 'Error!', 'message' => 'Gagal Menambahkan Pemindahan Barang!']);
        }
    }

    /**
     * Show transfer details
     */
    public function details($id)
    {
        $transfer = Transaction::with(['branch', 'tobranch', 'Transactionproduct.product'])
            ->where('id', $id)
            ->where('purpose', 'transfer')
            ->where('type', 'out')
            ->firstOrFail();

        $data = [
            'title' => 'Pemindahan Barang',
            'transfer' => $transfer
        ];

        return view('pages.transaction.transferproduct.details', $data);
    }

    /**
     * Show edit transfer form
     */
    public function show($id)
    {
        $transfer = Transaction::with(['branch', 'tobranch', 'Transactionproduct.product'])
            ->where('id', $id)
            ->where('purpose', 'transfer')
            ->where('type', 'out')
            ->firstOrFail();

        $data = [
            'title' => 'Edit Pemindahan Barang',
            'transfer' => $transfer,
            'branch' => Branch::all(),
            'product' => Product::all(),
        ];

        return view('pages.transaction.transferproduct.edit', $data);
    }

    /**
     * Update transfer
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'from_branch' => 'required|exists:branches,id',
            'to_branch' => 'required|exists:branches,id|different:from_branch',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1'
        ]);

        try {
            DB::beginTransaction();

            // Find the outgoing transfer
            $transfer = Transaction::where('id', $id)
                ->where('purpose', 'transfer')
                ->where('type', 'out')
                ->firstOrFail();

            // Update transfer details
            $transfer->update([
                'branch_id' => $request->from_branch,
                'to_branch' => $request->to_branch
            ]);

            // Delete old products
            TransactionProduct::where('transaction_id', $transfer->id)->delete();

            // Add new products
            foreach ($request->products as $product) {
                TransactionProduct::create([
                    'transaction_id' => $transfer->id,
                    'product_id' => $product['id'],
                    'quantity' => $product['quantity']
                ]);
            }

            DB::commit();
            return redirect()->route('transfer')->with([
                'status' => 'Success!',
                'message' => 'Berhasil Mengupdate Pemindahan Barang!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('transfer')->with([
                'status' => 'Error!',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Find the outgoing transfer
            $transfer = Transaction::where('id', $id)
                ->where('purpose', 'transfer')
                ->where('type', 'out')
                ->firstOrFail();

            // Find and delete all related transaction products
            TransactionProduct::where('transaction_id', $id)->delete();

            // Delete the outgoing transfer
            $transfer->delete();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Pemindahan Barang Berhasil Dihapus!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'success' => false,
                'message' => 'Pemindahan Barang Gagal dihapus!',
                'error' => $e->getMessage()
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers\Report;

use App\Exports\TransactionProductExport;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class TransactionProductController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Laporan Penggunaan Barang',
            'transaksi' => Transaction::all(),
            'product' => Product::all(),
            'purposes' => Transaction::select('purpose')->distinct()->pluck('purpose'),
        ];

        return view('pages.report.transaction-product.index', $data);
    }

    public function getData(Request $request)
    {
        $query = TransactionProduct::with(['transaksi.WorkTransaction', 'product.unit'])
            ->whereHas('transaksi', function ($query) {
                $query->where('type', 'out')
                    ->where('purpose', '!=', 'stock_in');
            })
            ->orderByDesc('created_at'); // Moved the orderByDesc here



        // Apply transaction purpose filter
        if ($request->filled('transaksi')) {
            $query->whereHas('transaksi', function ($q) use ($request) {
                $q->where('purpose', $request->input('transaksi'))
                    ->where('purpose', '!=', 'stock_in');
            });
        }

        // Apply date filter
        if ($request->filled('created_at')) {
            $query->whereDate('created_at', $request->input('created_at'));
        }

        // Get and group the data
        $data = $query->get()->groupBy('transaksi.id');

        // Format the data for DataTables
        $formattedData = $data->map(function ($group) {
            $firstRow = $group->first();
            $products = $group->map(function ($item) {
                $unit = $item->product->unit->name ?? '';
                return "{$item->product->name} ({$item->quantity} {$unit})";
            })->implode('<br>');
            return [
                'created_at' => $firstRow->created_at->format('d-m-Y H:i'),
                'transaksi' => $firstRow->getTransactionPurpose(),
                'pelanggan' => (function () use ($firstRow) {
                    if (optional($firstRow->transaksi->WorkTransaction)->id === null) {
                        switch ($firstRow->transaksi->purpose) {
                            case 'psb':
                                return 'Pemasangan Customer ' . $firstRow->transaksi->customer->name;
                            case 'repair':
                                return 'Perbaikan Customer ' . $firstRow->transaksi->customer->name;
                            case 'transfer':
                                return 'Pindah Barang Dari ' . $firstRow->transaksi->branch->name . ' Ke ' . $firstRow->transaksi->tobranch->name;
                            default:
                                return '<span class="badge badge-label-success">Stok Masuk</span>';
                        }
                    } else {
                        return $firstRow->transaksi->WorkTransaction->name;
                    }
                })(),
                'products' => $products,
                'action' => $this->generateActionButtons($firstRow)
            ];
        })->values();

        return DataTables::of($formattedData)
            ->addIndexColumn()
            ->rawColumns(['action', 'created_at', 'transaksi', 'products', 'pelanggan'])
            ->make(true);
    }

    // Method to generate action buttons
    protected function generateActionButtons($row)
    {
        $button = '';

        $button .= '<a href="' . route('report.transaction-product.details', ['id' => $row->transaksi->id]) . '"
                          class="btn btn-sm btn-info" 
                          data-id="' . $row->id . '" 
                          data-type="details" 
                          data-toggle="tooltip" 
                          data-placement="bottom" 
                          title="Details">
                           <i class="fas fa-eye"></i>
                       </a>';
        // $button .= '<a href="' . route('report.transaction-product.edit', ['id' => $row->id]) . '"
        //           class="btn btn-sm btn-success" 
        //           data-id="' . $row->id . '" 
        //           data-type="edit" 
        //           data-toggle="tooltip" 
        //           data-placement="bottom" 
        //           title="Edit Data">
        //            <i class="fas fa-pen"></i>
        //        </a>';

        // $button .= ' <button class="btn btn-sm btn-danger action" 
        //            data-id="' . $row->id . '" 
        //            data-type="delete" 
        //            data-route="' . route('report.transaction-product.delete', ['id' => $row->id]) . '"
        //            data-toggle="tooltip" 
        //            data-placement="bottom" 
        //            title="Delete Data">
        //         <i class="fas fa-trash-alt"></i>
        //     </button>';

        return '<div class="d-flex gap-2">' . $button . '</div>';
    }

    public function details($id)
    {
        $transaction = Transaction::with(['branch', 'tobranch', 'transactionproduct.product.unit'])->where('type', 'out')->where('purpose', '!=', 'stock_in')
            ->findOrFail($id); // Cari transaksi berdasarkan ID, atau gagal jika tidak ditemukan

        $data = [
            'title' => 'Detail Transaksi',
            'transaction' => $transaction,
        ];

        return view('pages.report.transaction-product.details', $data);
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'required',
            'end_date' => 'required',
        ]);
        Log::info('Export Parameters:', $request->all()); // Logging untuk memverifikasi parameter
        return Excel::download(
            new TransactionProductExport($request),
            'laporan_transaksi_barang_' . $request->start_date . '_' . $request->end_date . '.xlsx'
        );
    }

    public function create()
    {
        $data = [
            'title' => 'Laporan Transaksi Barang',
            'transaksi' => Transaction::all(),
            'product' => Product::all(),
        ];

        return view('pages.report.transaction-product.create', $data);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'transaction_id' => 'required|exists:transactions,id',
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|numeric|min:1',
            ]);

            $transactionProduct = TransactionProduct::create([
                'transaction_id' => $request->transaction_id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);

            activity()
                ->causedBy(Auth::user())
                ->event('created')
                ->withProperties($transactionProduct->toArray())
                ->log("Data transaksi barang berhasil dibuat.");

            DB::commit();

            return redirect()
                ->route('report.transaction-product.index')
                ->with('success', 'Data transaksi barang berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $transactionProduct = TransactionProduct::findOrFail($id);

        $data = [
            'title' => 'Laporan Transaksi Barang',
            'transaksi' => Transaction::all(),
            'product' => Product::all(),
            'transactionProduct' => $transactionProduct,
        ];

        return view('pages.report.transaction-product.edit', $data);
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $transactionProduct = TransactionProduct::findOrFail($id);

            $oldTransactionProduct = $transactionProduct->toArray();

            $request->validate([
                'transaction_id' => 'required|exists:transactions,id',
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|numeric|min:1',
            ]);

            $transactionProduct->update([
                'transaction_id' => $request->transaction_id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);

            activity()
                ->causedBy(Auth::user())
                ->event('updated')
                ->withProperties([
                    'old' => $oldTransactionProduct,
                    'new' => $transactionProduct->toArray()
                ])
                ->log("Data transaksi barang berhasil diperbarui.");

            DB::commit();

            return redirect()
                ->route('report.transaction-product.index')
                ->with('success', 'Data transaksi barang berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $transactionProduct = TransactionProduct::findOrFail($id);

            activity()
                ->causedBy(Auth::user())
                ->event('deleted')
                ->withProperties($transactionProduct->toArray())
                ->log("Data transaksi barang berhasil dihapus.");

            $transactionProduct->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data transaksi barang berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}

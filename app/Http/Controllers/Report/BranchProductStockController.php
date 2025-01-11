<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchProductStock;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BranchProductStockController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Laporan Stok Barang',
            'branch' => Branch::all(),
            'product' => Product::all(),
        ];

        return view('pages.report.product-stock.index', $data);
    }

    public function getData(Request $request)
    {
        $query = BranchProductStock::with(['branch', 'product']);

        // Apply filter branch
        if ($request->has('filter') && !empty($request->input('filter'))) {
            $query->where('branch_id', $request->input('filter'));
        }

        // Apply filter product
        if ($request->has('product') && !empty($request->input('product'))) {
            $query->where('product_id', $request->input('product'));
        }

        $data = $query->orderByDesc('id')->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('branch', function ($data) {
                return $data->branch->name;
            })
            ->editColumn('product', function ($data) {
                return $data->product->name;
            })
            ->editColumn('stock', function ($data) {
                return $data->stock .' '. $data->product->unit->name;
            })
            ->rawColumns(['branch', 'product','stock'])
            ->make(true);
    }
}

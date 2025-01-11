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
            'title' => 'Laporan Stok Produk',
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
            // ->addColumn('action', function ($data) {
            //     $button = '';
            //     $button .= '<a href="' . route('product-stock.edit', ['id' => $data->id]) . '"
            //               class="btn btn-sm btn-success" 
            //               data-id="' . $data->id . '" 
            //               data-type="edit" 
            //               data-toggle="tooltip" 
            //               data-placement="bottom" 
            //               title="Edit Data">
            //                <i class="fas fa-pen"></i>
            //            </a>';

            //     $button .= ' <button class="btn btn-sm btn-danger action" 
            //                    data-id="' . $data->id . '" 
            //                    data-type="delete" 
            //                    data-route="' . route('product-stock.delete', ['id' => $data->id]) . '" 
            //                    data-toggle="tooltip" 
            //                    data-placement="bottom" 
            //                    title="Delete Data">
            //                 <i class="fas fa-trash-alt"></i>
            //             </button>';

            //     return '<div class="d-flex gap-2">' . $button . '</div>';
            // })
            ->editColumn('branch', function ($data) {
                return $data->branch->name;
            })
            ->editColumn('product', function ($data) {
                return $data->product->name;
            })
            ->rawColumns(['branch', 'product'])
            ->make(true);
    }
}

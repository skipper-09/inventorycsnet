<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Produk',
        ];
        return view('pages.master.product.index', $data);
    }

    public function getData()
    {
        $data = Product::orderByDesc('id')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            // $userauth = User::with('roles')->where('id', Auth::id())->first();
            $button = '';
            $button .= ' <a href="' . route('dashboard') . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                                        class="fas fa-pen "></i></a>';

            $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('produk.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                        class="fas fa-trash "></i></button>';
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->rawColumns(['action'])->make(true);
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            //return response
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Data Produk Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Menghapus Data Produk!',
                'trace' => $e->getTrace()
            ]);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'unit_id' => 'required|exists:unit_products,id',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->unit_id = $request->unit_id;
        $product->save();

        // Mengirim response JSON untuk memberikan feedback
        return response()->json(['success' => true]);
    }
}

<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\UnitProduct;
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
            'unit' => UnitProduct::all(),
        ];
        return view('pages.master.product.index', $data);
    }

    public function getData()
    {
        $data = Product::orderByDesc('id')->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('unit', function ($data) {
                return $data->unit->name;
            })
            ->addColumn('action', function ($data) {
                $userauth = User::with('roles')->where('id', Auth::id())->first();
                $button = '';
                if ($userauth->can('update-product')) {
                    $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('produk.edit', ['id' => $data->id]) . '" data-proses="' . route('produk.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
                            data-action="edit" data-title="Produk" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                                        class="fas fa-pen "></i></button>';
                }
                if ($userauth->can('delete-product')) {
                    $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('produk.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                        class="fas fa-trash "></i></button>';
                }
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })->rawColumns(['action'])->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'unit_id' => 'required|exists:unit_products,id',
        ], [
            'name.required' => 'Nama produk harus diisi.',
            'description.required' => 'Deskripsi produk harus diisi.',
            'unit_id.required' => 'Unit produk harus dipilih.',
        ]);

        try {
            $product = new Product();
            $product->name = $request->name;
            $product->description = $request->description;
            $product->unit_id = $request->unit_id;
            $product->save();

            activity()
                ->causedBy(Auth::user())
                ->event('created')
                ->withProperties($product->toArray())
                ->log("Data Produk berhasil dibuat.");

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Produk Berhasil dibuat.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json([
            'product' => $product,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'unit_id' => 'required|exists:unit_products,id',
        ], [
            'name.required' => 'Nama produk harus diisi.',
            'description.required' => 'Deskripsi produk harus diisi.',
            'unit_id.required' => 'Unit produk harus dipilih.',
        ]);

        try {
            $product = Product::findOrFail($id);
            $oldProduct = $product->toArray();

            $product->name = $request->name;
            $product->description = $request->description;
            $product->unit_id = $request->unit_id;
            $product->save();

            activity()
                ->causedBy(Auth::user())
                ->event('updated')
                ->withProperties([
                    'old' => $oldProduct,
                    'new' => $product->toArray()
                ])
                ->log("Data Produk berhasil diperbarui.");

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Produk Berhasil diupdate.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);

            activity()
                ->causedBy(Auth::user())
                ->event('deleted')
                ->withProperties($product->toArray())
                ->log("Data Produk berhasil dihapus.");

            $product->delete();

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
}

<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class ProductRoleController extends Controller
{
    public function index()
    {
        return view('pages.master.product_role.index', [
            'title' => 'Product Role',
        ]);
    }

    public function getData()
    {
        $data = Role::whereNotIn('name', ['Developer', 'Administrator'])
            ->orderByDesc('id')
            ->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $userauth = User::with('roles')->where('id', Auth::id())->first();
                $button = '';

                if ($userauth->can('update-product-role')) {
                    $button .= '<a href="' . route('productrole.edit', ['id' => $data->id]) . '"
                            class="btn btn-sm btn-success" 
                            title="Edit Data">
                            <i class="fas fa-pen"></i>
                        </a>';
                }
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function show($id)
    {
        $role = Role::findOrFail($id);
        $products = Product::orderBy('name')->get();
        $selectedProducts = ProductRole::where('role_id', $id)
            ->pluck('product_id')
            ->toArray();

        return view('pages.master.product_role.edit', [
            'title' => 'Edit Product Role',
            'role' => $role,
            'products' => $products,
            'selectedProducts' => $selectedProducts,
        ]);
    }

    public function update(Request $request, $roleId)
    {
        $request->validate([
            'product_id' => 'required|array',
            'product_id.*' => 'exists:products,id',
        ]);

        try {
            DB::beginTransaction();

            // Get old product roles before deletion for logging
            $oldProductRoles = ProductRole::where('role_id', $roleId)->get()->toArray();

            // Delete existing product roles
            ProductRole::where('role_id', $roleId)->delete();

            // Prepare new product roles data
            $productRoles = array_map(function ($productId) use ($roleId) {
                return [
                    'role_id' => $roleId,
                    'product_id' => $productId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $request->product_id);

            // Insert new product roles
            ProductRole::insert($productRoles);

            // Get new product roles for logging
            $newProductRoles = ProductRole::where('role_id', $roleId)->get()->toArray();

            // Log the activity
            activity()
                ->causedBy(Auth::user())
                ->event('updated')
                ->withProperties([
                    'old' => $oldProductRoles,
                    'new' => $newProductRoles
                ])
                ->log("Product Role berhasil diperbarui.");

            DB::commit();

            return redirect()
                ->route('productrole')
                ->with([
                    'status' => 'Success!',
                    'message' => 'Berhasil Mengubah Product Role!'
                ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('productrole')
                ->with([
                    'status' => 'Error!',
                    'message' => 'Gagal Mengubah Product Role!'
                ]);
        }
    }
}

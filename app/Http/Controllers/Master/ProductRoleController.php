<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductRole;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class ProductRoleController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Product Role',
            'product' => Product::all()
        ];
        return view('pages.master.product_role.index', $data);
    }

     public function getData()
     {
         $data = Role::whereNotIn('name',['Developer','Administrator'])->orderByDesc('id')->get();
         return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
             // $userauth = User::with('roles')->where('id', Auth::id())->first();
             $button = '';
             // $button .= ' <a href="' . route('dashboard') . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
             //                                             class="fas fa-pen "></i></a>';
 
             $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('productrole.edit', ['id' => $data->id]) . '" data-proses="' . route('productrole.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
                             data-action="edit" data-title="Product Role" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                                         class="fas fa-pen "></i></button>';
 
            //  $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('unitproduk.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
            //                                              class="fas fa-trash "></i></button>';
             return '<div class="d-flex gap-2">' . $button . '</div>';
         })->rawColumns(['action'])->make(true);
     }
}

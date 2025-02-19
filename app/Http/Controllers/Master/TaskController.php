<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class TaskController extends Controller
{
    public function getData()
    {
        $data = Task::orderByDesc('id')->get();
        return DataTables::of($data)
            ->addIndexColumn()
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
}

<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Exception;
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
                if ($userauth->can(abilities: 'update-product')) {
                    $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('produk.edit', ['id' => $data->id]) . '" data-proses="' . route('produk.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
                            data-action="edit" data-title="Produk" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                                        class="fas fa-pen "></i></button>';
                }
                if ($userauth->can('delete-product')) {
                    $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('task.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                        class="fas fa-trash "></i></button>';
                }
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })->editColumn('status',function($data){
                    return $data->status == 1 ? '<span class="badge badge-label-primary">Aktif</span>' : '<span class="badge badge-label-danger">Tidak Aktif</span>';
            })->rawColumns(['action','status'])->make(true);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ], [
            'name.required' => 'Nama Task harus diisi.',
            'description.required' => 'Deskripsi Task harus diisi.',
        ]);

        try {
            $task = new Task();
            $task->create($request->all());

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Task Berhasil dibuat.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }




     //destroy data
     public function destroy($id)
     {
         try {
             $task = Task::findOrFail($id);
             $task->delete();
             //return response
             return response()->json([
                 'status' => 'success',
                 'success' => true,
                 'message' => 'Task Berhasil Dihapus!.',
             ]);
         } catch (Exception $e) {
             return response()->json([
                 'message' => 'Gagal Menghapus Task!',
                 'trace' => $e->getTrace()
             ]);
         }
     }

}

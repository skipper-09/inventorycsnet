<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class BranchController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Cabang',
        ];
        return view('pages.master.branch.index', $data);
    }

    public function getData()
    {
        $data = Branch::orderByDesc('id')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $userauth = User::with('roles')->where('id', Auth::id())->first();
            $button = '';
            // $button .= ' <a href="' . route('dashboard') . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
            //                                             class="fas fa-pen "></i></a>';

            if ($userauth->can('update-branch')) {
                $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('branch.edit', ['id' => $data->id]) . '" data-proses="' . route('branch.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
                            data-action="edit" data-title="Unit Produk" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                                        class="fas fa-pen "></i></button>';
            }
            if ($userauth->can('delete-branch')) {
                $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('branch.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                        class="fas fa-trash "></i></button>';
            }
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->rawColumns(['action'])->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama cabang harus diisi.',
            'address.required' => 'Alamat harus diisi.',
        ]);

        try {
            $branch = new Branch();
            $branch->name = $request->name;
            $branch->address = $request->address;
            $branch->save();

            activity()
                ->causedBy(Auth::user())
                ->event('created')
                ->withProperties($branch->toArray())
                ->log("Data Cabang berhasil dibuat.");


            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Cabang Berhasil dibuat.'
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
        $branch = Branch::findOrFail($id);
        return response()->json([
            'branch' => $branch,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ], [
            'name.required' => 'Nama cabang harus diisi.',
            'address.required' => 'Alamat harus diisi.',
        ]);

        try {
            $branch = Branch::findOrFail($id);
            $oldBranch = $branch->toArray();
            $branch->name = $request->name;
            $branch->address = $request->address;
            $branch->save();

            activity()
                ->causedBy(Auth::user())
                ->event('updated')
                ->withProperties([
                    'old' => $oldBranch,
                    'new' => $branch->toArray()
                ])
                ->log("Data Cabang berhasil diperbarui.");

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Cabang Berhasil diupdate.'
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
            $brach = Branch::findOrFail($id);

            activity()
                ->causedBy(Auth::user())
                ->event('deleted')
                ->withProperties($brach->toArray())
                ->log("Data Cabang berhasil dihapus.");

            $brach->delete();

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Data Cabang Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Menghapus Data Cabang!',
                'trace' => $e->getTrace()
            ]);
        }
    }
}

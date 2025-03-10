<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class DepartmentController extends Controller
{
    public function index()
    {
        $data = [
            "title" => "Departemen",
        ];

        return view("pages.master.department.index", $data);
    }

    public function getData()
    {
        $data = Department::orderByDesc('id')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $userauth = User::with('roles')->where('id', Auth::id())->first();
            $button = '';
            if ($userauth->can('update-department')) {
                $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('department.edit', ['id' => $data->id]) . '" data-proses="' . route('department.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
                            data-action="edit" data-title="Jabatan" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                                        class="fas fa-pen "></i></button>';
            }
            if ($userauth->can('delete-department')) {
                $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('department.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                class="fas fa-trash "></i></button>';
            }
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->rawColumns(['action'])->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        try {
            $department = Department::create($validated);

            activity()
                ->causedBy(Auth::user())
                ->event('created')
                ->withProperties($department->toArray())
                ->log("Data Departemen berhasil dibuat.");

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Departemen Berhasil dibuat.',
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
        $department = Department::findOrFail($id);

        return response()->json([
            'department' => $department,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        try {
            $department = Department::findOrFail($id);
            $oldDepartment = $department->toArray();

            $department->update($validated);

            $department->refresh();

            activity()
                ->causedBy(Auth::user())
                ->event('updated')
                ->withProperties([
                    'old' => $oldDepartment,
                    'new' => $department->toArray()
                ])
                ->log("Data Departemen berhasil diperbarui.");

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Departemen Berhasil diupdate.',
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
            $department = Department::findOrFail($id);

            activity()
                ->causedBy(Auth::user())
                ->event('deleted')
                ->withProperties($department->toArray())
                ->log("Data Departemen berhasil dihapus.");

            $department->delete();

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Departemen Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Menghapus Data Departemen!',
                'trace' => $e->getTrace()
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\AllowanceType;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class AllowanceTypeController extends Controller
{
    public function index()
    {
        $data = [
            "title" => "Tipe Tunjangan",
        ];

        return view("pages.master.allowancetype.index", $data);
    }

    public function getData()
    {
        $data = AllowanceType::orderByDesc('id')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $userauth = User::with('roles')->where('id', Auth::id())->first();
            $button = '';
            if ($userauth->can('update-allowance-type')) {
                $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('allowancetype.edit', ['id' => $data->id]) . '" data-proses="' . route('allowancetype.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
                            data-action="edit" data-title="Tipe Tunjangan" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                                        class="fas fa-pen "></i></button>';
            }
            if ($userauth->can('delete-allowance-type')) {
                $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('allowancetype.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                class="fas fa-trash "></i></button>';
            }
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->rawColumns(['action'])->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $allowanceType = AllowanceType::create($validated);

            activity()
                ->causedBy(Auth::user())
                ->event('created')
                ->withProperties($allowanceType->toArray())
                ->log("Tipe Tunjangan berhasil dibuat.");


            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Tipe Tunjangan Berhasil dibuat.',
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
        $allowanceType = AllowanceType::findOrFail($id);
        return response()->json([
            'allowanceType' => $allowanceType,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            // Get the model before updating to store old values
            $allowanceType = AllowanceType::findOrFail($id);
            $oldAllowanceType = $allowanceType->toArray();

            // Update the model
            $allowanceType->update($validated);

            // Refresh the model to get the updated values
            $allowanceType->refresh();

            activity()
                ->causedBy(Auth::user())
                ->event('updated')
                ->withProperties([
                    'old' => $oldAllowanceType,
                    'new' => $allowanceType->toArray()
                ])
                ->log("Tipe Tunjangan berhasil diperbarui.");

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Tipe Tunjangan Berhasil diupdate.',
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
            $allowanceType = AllowanceType::findOrFail($id);
            
            activity()
                ->causedBy(Auth::user())
                ->event('deleted')
                ->withProperties($allowanceType->toArray())
                ->log("Tipe Tunjangan berhasil dihapus.");
            
            $allowanceType->delete();

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Tipe Tunjangan Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Menghapus Data Tipe Tunjangan!',
                'trace' => $e->getTrace()
            ]);
        }
    }
}

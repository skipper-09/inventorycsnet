<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\DeductionType;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class DeductionTypeController extends Controller
{
    public function index()
    {
        $data = [
            "title" => "Tipe Deduksi",
        ];

        return view("pages.master.deductiontype.index", $data);
    }

    public function getData()
    {
        $data = DeductionType::orderByDesc('id')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $userauth = User::with('roles')->where('id', Auth::id())->first();
            $button = '';
            if ($userauth->can('update-deduction-type')) {
                $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('deductiontype.edit', ['id' => $data->id]) . '" data-proses="' . route('deductiontype.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
                            data-action="edit" data-title="Tipe Deduksi" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                                        class="fas fa-pen "></i></button>';
            }
            if ($userauth->can('delete-deduction-type')) {
                $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('deductiontype.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
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
            $deductionType = DeductionType::create($validated);

            activity()
                ->causedBy(Auth::user())
                ->event('created')
                ->withProperties($deductionType->toArray())
                ->log("Tipe Deduksi berhasil dibuat.");

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Tipe Deduksi Berhasil dibuat.',
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
        $deductionType = DeductionType::findOrFail($id);
        return response()->json([
            'deductionType' => $deductionType,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            // Get the model before updating to store old values
            $deductionType = DeductionType::findOrFail($id);
            $oldDeductionType = $deductionType->toArray();

            // Update the model
            $deductionType->update($validated);

            // Refresh the model to get the updated values
            $deductionType->refresh();

            activity()
                ->causedBy(Auth::user())
                ->event('updated')
                ->withProperties([
                    'old' => $oldDeductionType,
                    'new' => $deductionType->toArray()
                ])
                ->log("Tipe Deduksi berhasil diperbarui.");

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Tipe Deduksi Berhasil diupdate.',
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
            $deductionType = DeductionType::findOrFail($id);

            activity()
                ->causedBy(Auth::user())
                ->event('deleted')
                ->withProperties($deductionType->toArray())
                ->log("Tipe Potongan berhasil dihapus.");

            $deductionType->delete();

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Tipe Deduksi Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Menghapus Data Tipe Deduksi!',
                'trace' => $e->getTrace()
            ]);
        }
    }
}

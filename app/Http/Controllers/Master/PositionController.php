<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class PositionController extends Controller
{
    public function index()
    {
        $data = [
            "title" => "Jabatan",
        ];

        return view("pages.master.position.index", $data);
    }

    public function getData()
    {
        $data = Position::orderByDesc('id')->get();
        return DataTables::of($data)->addIndexColumn()->addColumn('action', function ($data) {
            $userauth = User::with('roles')->where('id', Auth::id())->first();
            $button = '';
            if ($userauth->can('update-position')) {
                $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('position.edit', ['id' => $data->id]) . '" data-proses="' . route('position.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
                            data-action="edit" data-title="Jabatan" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                                        class="fas fa-pen "></i></button>';
            }
            if ($userauth->can('delete-position')) {
                $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('position.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
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
            $position = Position::create($validated);

            activity()
                ->causedBy(Auth::user())
                ->event('created')
                ->withProperties($position->toArray())
                ->log("Data Jabatan berhasil dibuat.");


            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Jabatan Berhasil dibuat.',
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
        $position = Position::findOrFail($id);

        return response()->json([
            'position' => $position,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $position = Position::findOrFail($id);
            $oldPosition = $position->toArray();

            $position->update($validated);

            $position->refresh();

            activity()
                ->causedBy(Auth::user())
                ->event('updated')
                ->withProperties([
                    'old' => $oldPosition,
                    'new' => $position->toArray()
                ])
                ->log("Data Jabatan berhasil diperbarui.");


            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Jabatan Berhasil diupdate.',
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
            $position = Position::findOrFail($id);

            activity()
                ->causedBy(Auth::user())
                ->event('deleted')
                ->withProperties($position->toArray())
                ->log("Data Jabatan berhasil dihapus.");

            $position->delete();

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Jabatan Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Menghapus Data Jabatan!',
                'trace' => $e->getTrace()
            ]);
        }
    }
}

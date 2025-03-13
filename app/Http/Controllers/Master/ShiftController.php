<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ShiftController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Shift',
        ];

        return view('pages.master.shift.index', $data);
    }

    public function getData()
    {
        $data = Shift::orderByDesc('id')->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $userauth = Auth::user();
                $button = '';
                if ($userauth->can('update-shift')) {
                    $button .= ' <button class="btn btn-sm btn-success" data-id="' . $data->id . '" data-type="edit" data-route="' . route('shift.edit', ['id' => $data->id]) . '" data-proses="' . route('shift.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
                            data-action="edit" data-title="Edit Shift" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                                        class="fas fa-pen "></i></button>';
                }
                if ($userauth->can('delete-shift')) {
                    $button .= ' <button class="btn btn-sm btn-danger action" data-id="' . $data->id . '" data-type="delete" data-route="' . route('shift.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                        class="fas fa-trash "></i></button>';
                }
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })->rawColumns(['action'])->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'shift_start' => 'required',
            'shift_end' => 'required',
            'status' => 'required|boolean',
        ], [
            'name.required' => 'Nama shift harus diisi.',
            'shift_start.required' => 'Jam mulai shift harus diisi.',
            'shift_end.required' => 'Jam selesai shift harus diisi.',
            'status.required' => 'Status shift harus dipilih.',
        ]);

        try {
            $shift = new Shift();
            $shift->name = $request->name;
            $shift->shift_start = $request->shift_start;
            $shift->shift_end = $request->shift_end;
            $shift->status = $request->status;

            $shift->save();

            activity()
                ->causedBy(Auth::user())
                ->event('created')
                ->withProperties($shift->toArray())
                ->log("Shift berhasil dibuat.");

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Data Shift Berhasil dibuat.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        $shift = Shift::findOrFail($id);
        return response()->json([
            'shift' => $shift,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'shift_start' => 'required',
            'shift_end' => 'required',
            'status' => 'required|boolean',
        ], [
            'name.required' => 'Nama shift harus diisi.',
            'shift_start.required' => 'Jam mulai shift harus diisi.',
            'shift_end.required' => 'Jam selesai shift harus diisi.',
            'status.required' => 'Status shift harus dipilih.',
        ]);

        try {
            $shift = Shift::findOrFail($id);
            $oldShift = $shift->toArray();

            $shift->name = $request->name;
            $shift->shift_start = $request->shift_start;
            $shift->shift_end = $request->shift_end;
            $shift->status = $request->status;

            $shift->save();

            activity()
                ->causedBy(Auth::user())
                ->event('updated')
                ->withProperties([
                    'old' => $oldShift,
                    'new' => $shift->toArray()
                ])
                ->log("Shift berhasil diperbarui.");

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Data Shift Berhasil diupdate.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $shift = Shift::findOrFail($id);

            activity()
                ->causedBy(Auth::user())
                ->event('deleted')
                ->withProperties($shift->toArray())
                ->log("Shift berhasil dihapus.");

            $shift->delete();

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Data Shift Berhasil Dihapus!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Menghapus Data Shift!',
                'trace' => $e->getTrace()
            ]);
        }
    }
}

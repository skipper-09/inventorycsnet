<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Deduction;
use App\Models\DeductionType;
use App\Models\Employee;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class DeductionController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Deduksi',
            'employees' => Employee::all(),
            'deductionTypes' => DeductionType::all(),
        ];
        return view('pages.master.deduction.index', $data);
    }

    public function getData()
    {
        $data = Deduction::with(['employee', 'deductionType'])->orderByDesc('id')->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('employee_name', function ($row) {
                return $row->employee->name ?? '-';
            })
            ->addColumn('deduction_type_name', function ($row) {
                return $row->deductionType->name ?? '-';
            })
            ->addColumn('formatted_amount', function ($row) {
                return 'Rp ' . number_format($row->amount, 0, ',', '.');
            })
            ->addColumn('action', function ($data) {
                $userauth = User::with('roles')->where('id', Auth::id())->first();
                $button = '';
                if ($userauth->can('update-deduction')) {
                    $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('deduction.edit', ['id' => $data->id]) . '" data-proses="' . route('deduction.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
                            data-action="edit" data-title="Deduksi" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                                        class="fas fa-pen "></i></button>';
                }
                if ($userauth->can('delete-deduction')) {
                    $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('deduction.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                        class="fas fa-trash "></i></button>';
                }
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })->rawColumns(['action'])->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'deduction_type_id' => 'required|exists:deduction_types,id',
            'amount' => 'required|numeric|min:1',
        ], [
            'employee_id.required' => 'Karyawan harus dipilih.',
            'deduction_type_id.required' => 'Jenis Deduksi harus dipilih.',
            'amount.required' => 'Jumlah Deduksi harus diisi.',
        ]);

        try {
            $deduction = new Deduction();
            $deduction->employee_id = $request->employee_id;
            $deduction->deduction_type_id = $request->deduction_type_id;
            $deduction->amount = $request->amount;
            $deduction->save();

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Deduksi Berhasil dibuat.'
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
        $deduction = Deduction::findOrFail($id);
        return response()->json([
            'deduction' => $deduction,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'deduction_type_id' => 'required|exists:deduction_types,id',
            'amount' => 'required|numeric|min:1',
        ], [
            'employee_id.required' => 'Karyawan harus dipilih.',
            'deduction_type_id.required' => 'Jenis Deduksi harus dipilih.',
            'amount.required' => 'Jumlah Deduksi harus diisi.',
        ]);

        try {
            $deduction = Deduction::findOrFail($id);
            $deduction->employee_id = $request->employee_id;
            $deduction->deduction_type_id = $request->deduction_type_id;
            $deduction->amount = $request->amount;
            $deduction->save();

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Deduksi Berhasil diupdate.'
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
            $deduction = Deduction::findOrFail($id);
            $deduction->delete();
            //return response
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Data Deduksi Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Menghapus Data Deduksi!',
                'trace' => $e->getTrace()
            ]);
        }
    }
}

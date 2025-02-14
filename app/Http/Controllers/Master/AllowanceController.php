<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Allowance;
use App\Models\AllowanceType;
use App\Models\Employee;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class AllowanceController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Tunjangan',
            'employees' => Employee::all(),
            'allowanceTypes' => AllowanceType::all(),
        ];
        return view('pages.master.allowance.index', $data);
    }

    public function getData()
    {
        $data = Allowance::with(['employee', 'allowanceType'])->orderByDesc('id')->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('employee_name', function ($row) {
                return $row->employee->name ?? '-';
            })
            ->addColumn('allowance_type_name', function ($row) {
                return $row->allowanceType->name ?? '-';
            })
            ->addColumn('formatted_amount', function ($row) {
                return 'Rp ' . number_format($row->amount, 0, ',', '.');
            })
            ->addColumn('action', function ($data) {
                $userauth = User::with('roles')->where('id', Auth::id())->first();
                $button = '';
                if ($userauth->can('update-allowance')) {
                    $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('allowance.edit', ['id' => $data->id]) . '" data-proses="' . route('allowance.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
                            data-action="edit" data-title="Deduksi" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                                        class="fas fa-pen "></i></button>';
                }
                if ($userauth->can('delete-allowance')) {
                    $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('allowance.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                        class="fas fa-trash "></i></button>';
                }
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })->rawColumns(['action'])->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'allowance_type_id' => 'required|exists:allowance_types,id',
            'amount' => 'required|numeric|min:1',
        ], [
            'employee_id.required' => 'Karyawan harus dipilih.',
            'allowance_type_id.required' => 'Jenis Tunjangan harus dipilih.',
            'amount.required' => 'Jumlah Tunjangan harus diisi.',
        ]);

        try {
            $allowance = new Allowance();
            $allowance->employee_id = $request->employee_id;
            $allowance->allowance_type_id = $request->allowance_type_id;
            $allowance->amount = $request->amount;
            $allowance->save();

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Data Tunjangan Berhasil dibuat.'
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
        $allowance = Allowance::findOrFail($id);
        return response()->json([
            'allowance' => $allowance,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'allowance_type_id' => 'required|exists:allowance_types,id',
            'amount' => 'required|numeric|min:1',
        ], [
            'employee_id.required' => 'Karyawan harus dipilih.',
            'allowance_type_id.required' => 'Jenis Tunjangan harus dipilih.',
            'amount.required' => 'Jumlah Tunjangan harus diisi.',
        ]);

        try {
            $allowance = Allowance::findOrFail($id);
            $allowance->employee_id = $request->employee_id;
            $allowance->allowance_type_id = $request->allowance_type_id;
            $allowance->amount = $request->amount;
            $allowance->save();

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Data Tunjangan Berhasil diupdate.'
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
            $allowance = Allowance::findOrFail($id);
            $allowance->delete();
            //return response
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Data Tunjangan Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Menghapus Data Tunjangan!',
                'trace' => $e->getTrace()
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Data Karyawan',
            'departments' => Department::all(),
            'positions' => Position::all(),
        ];
        return view('pages.master.employee.index', $data);
    }

    public function getData()
    {
        $data = Employee::with(['department', 'position'])->orderByDesc('id')->get();
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('department_name', function ($row) {
                return $row->department->name ?? '-';
            })
            ->addColumn('position_name', function ($row) {
                return $row->position->name ?? '-';
            })
            ->addColumn('action', function ($data) {
                $userauth = User::with('roles')->where('id', Auth::id())->first();
                $button = '';
                if ($userauth->can('update-employee')) {
                    $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('employee.edit', ['id' => $data->id]) . '" data-proses="' . route('employee.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
                            data-action="edit" data-title="Deduksi" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                                        class="fas fa-pen "></i></button>';
                }
                if ($userauth->can('delete-employee')) {
                    $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('employee.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                                        class="fas fa-trash "></i></button>';
                }
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })->rawColumns(['action'])->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:employees,email',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'nik' => 'required|string|unique:employees,nik',
            'identity_card' => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ], [
            "department_id.required" => "Departemen harus dipilih.",
            "position_id.required" => "Jabatan harus dipilih.",
            "name.required" => "Nama harus diisi.",
            "address.required" => "Alamat harus diisi.",
            "phone.required" => "Nomor telepon harus diisi.",
            "phone.max" => "Nomor telepon maksimal 20 karakter.",
            "email.unique" => "Email sudah digunakan.",
            "email.required" => "Email harus diisi.",
            "email.email" => "Format email tidak valid.",
            "date_of_birth.required" => "Tanggal lahir harus diisi.",
            "date_of_birth.date" => "Format tanggal lahir tidak valid.",
            "gender.required" => "Jenis kelamin harus dipilih.",
            "gender.in" => "Jenis kelamin tidak valid.",
            "nik.required" => "Nomor induk kependudukan harus diisi.",
            "nik.unique" => "Nomor induk kependudukan sudah digunakan.",
            "identity_card.required" => "Kartu identitas harus diupload.",
            "identity_card.file" => "Kartu identitas harus berupa file.",
            "identity_card.mimes" => "Format kartu identitas harus jpeg, png, jpg, atau pdf.",
            "identity_card.max" => "Ukuran kartu identitas maksimal 2MB.",
        ]);

        try {
            // Handle image upload
            $filename = '';
            if ($request->hasFile('identity_card')) {
                $file = $request->file('identity_card');
                $filename = 'identity_card_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/files/identity_card'), $filename);
            }

            Employee::create([
                'department_id' => $request->department_id,
                'position_id' => $request->position_id,
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'nik' => $request->nik,
                'identity_card' => $filename,
            ]);

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
        $employee = Employee::findOrFail($id);
        return response()->json([
            'employee' => $employee,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:employees,email,' . $id,
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'nik' => 'required|string|unique:employees,nik,' . $id,
            'identity_card' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ], [
            "department_id.required" => "Departemen harus dipilih.",
            "position_id.required" => "Jabatan harus dipilih.",
            "name.required" => "Nama harus diisi.",
            "address.required" => "Alamat harus diisi.",
            "phone.required" => "Nomor telepon harus diisi.",
            "phone.max" => "Nomor telepon maksimal 20 karakter.",
            "email.unique" => "Email sudah digunakan.",
            "email.required" => "Email harus diisi.",
            "email.email" => "Format email tidak valid.",
            "date_of_birth.required" => "Tanggal lahir harus diisi.",
            "date_of_birth.date" => "Format tanggal lahir tidak valid.",
            "gender.required" => "Jenis kelamin harus dipilih.",
            "gender.in" => "Jenis kelamin tidak valid.",
            "nik.required" => "Nomor induk kependudukan harus diisi.",
            "nik.unique" => "Nomor induk kependudukan sudah digunakan.",
            "identity_card.file" => "Kartu identitas harus berupa file.",
            "identity_card.mimes" => "Format kartu identitas harus jpeg, png, jpg, atau pdf.",
            "identity_card.max" => "Ukuran kartu identitas maksimal 2MB.",
        ]);

        try {
            // Find the employee to update
            $employee = Employee::findOrFail($id);

            // Handle image upload if new file is provided
            $filename = $employee->identity_card; // Retain existing image if no new file uploaded
            if ($request->hasFile('identity_card')) {
                $file = $request->file('identity_card');
                $filename = 'identity_card_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/files/identity_card'), $filename);
            }

            // Update the employee data
            $employee->update([
                'department_id' => $request->department_id,
                'position_id' => $request->position_id,
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'nik' => $request->nik,
                'identity_card' => $filename,
            ]);

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'Data Pegawai Berhasil diperbarui.'
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
            $employee = Employee::findOrFail($id);
            $employee->delete();
            //return response
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Data Pegawai Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Menghapus Data Pegawai!',
                'trace' => $e->getTrace()
            ]);
        }
    }
}

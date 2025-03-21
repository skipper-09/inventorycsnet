<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
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
        $data = Employee::with([
            'department',
            'position',
            'user.roles',
            'allowances',
            'deductions',
            'salaries',
            'leaves'
        ])->orderByDesc('id')->get();

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
                    $button .= ' <a href="' . route('employee.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i class="fas fa-pencil-alt"></i></a>';
                }
                // if ($userauth->can('read-employee')) {
                //     $button .= ' <a href="' . route('employee.details', ['id' => $data->id]) . '" class="btn btn-sm btn-info action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Detail Data"><i class="fas fa-eye"></i></a>';
                // }
                if ($userauth->can('delete-employee')) {
                    $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('employee.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                        class="fas fa-trash "></i></button>';
                }
                return '<div class="d-flex gap-2">' . $button . '</div>';
            })->rawColumns(['action'])->make(true);
    }

    public function create()
    {
        $data = [
            'title' => 'Karyawan',
            'departments' => Department::all(),
            'positions' => Position::all(),
            'company'=> Company::all(),
            'roles' => Role::where('name', '!=', 'Developer')->get(),
        ];
        return view('pages.master.employee.add', $data);
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
            'identity_card' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            // User credentials
            'picture' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,name',
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
            "identity_card.mimes" => "Format kartu identitas tidak valid.",
            "identity_card.max" => "Ukuran kartu identitas maksimal 2MB.",
            // "picture.required" => "Foto harus diupload.",
            "picture.image" => "Foto harus berupa gambar.",
            "picture.mimes" => "Format gambar tidak valid.",
            "picture.max" => "Ukuran gambar tidak boleh lebih dari 2MB.",
            "username.required" => "Username harus diisi.",
            "username.unique" => "Username sudah digunakan.",
            "password.required" => "Password harus diisi.",
            "password.min" => "Password minimal 8 karakter.",
            "roles.required" => "Role harus dipilih.",
            "roles.*.exists" => "Role yang dipilih tidak valid.",
        ]);

        try {
            DB::beginTransaction();

            // Handle identity card upload
            $identityCard = '';
            if ($request->hasFile('identity_card')) {
                $file = $request->file('identity_card');
                $identityCard = 'identity_card_' . time() . '_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/files/identity_card'), $identityCard);
            }

            // Handle picture upload
            $picture = '';
            if ($request->hasFile('picture')) {
                $file = $request->file('picture');
                $picture = 'picture_' . time() . '_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/files/picture'), $picture);
            }

            // Create employee record
            $employee = Employee::create([
                'department_id' => $request->department_id,
                'position_id' => $request->position_id,
                'company_id' => $request->company_id,
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'nik' => $request->nik,
                'identity_card' => $identityCard,
            ]);

            // Create associated user account
            $user = User::create([
                'employee_id' => $employee->id,
                'picture' => $picture,
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_block' => false,
            ]);

            // Assign roles to user
            $user->assignRole($request->role);

            activity()
                ->causedBy(Auth::user())
                ->event('created')
                ->withProperties($employee->toArray())
                ->log("Data Karyawan berhasil dibuat.");

            DB::commit();

            return redirect()->route('employee')->with([
                'status' => 'Success!',
                'message' => 'Berhasil Menambahkan Data!'
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            // Delete uploaded file if exists
            if (!empty($filename) && file_exists(public_path('storage/files/identity_card/' . $filename))) {
                unlink(public_path('storage/files/identity_card/' . $filename));
            } else if (!empty($picture) && file_exists(public_path('storage/files/picture/' . $picture))) {
                unlink(public_path('storage/files/picture/' . $picture));
            }

            Log::error($e);

            return redirect()->route('employee')->with([
                'status' => 'Error!',
                'message' => 'Gagal Menambahkan Data!'
            ]);
        }
    }

    public function show($id)
    {
        $data = [
            "title" => "Karyawan",
            "departments" => Department::all(),
            "positions" => Position::all(),
            "company"=> Company::all(),
            "roles" => Role::where('name', '!=', 'Developer')->get(),
            "employee" => Employee::with([
                'department',
                'position',
                'user.roles',
                'allowances',
                'deductions',
                'salaries',
                'leaves'
            ])->findOrFail($id)
        ];

        if (!$data['employee']->user) {
            return redirect()->route('employee')->with([
                'status' => 'Error!',
                'message' => 'Data user tidak ditemukan!'
            ]);
        }

        return view('pages.master.employee.edit', $data);
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
            'identity_card' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'username' => 'required|string|unique:users,username,' . $id . ',employee_id',
            'password' => 'nullable|string|min:8',
            'role' => 'required|exists:roles,name',
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
            "identity_card.mimes" => "Format kartu identitas tidak valid.",
            "identity_card.max" => "Ukuran kartu identitas maksimal 2MB.",
            "picture.image" => "Foto profil harus berupa gambar.",
            "picture.mimes" => "Format foto profil tidak valid.",
            "picture.max" => "Ukuran foto profil maksimal 2MB.",
            "username.required" => "Username harus diisi.",
            "username.unique" => "Username sudah digunakan.",
            "password.min" => "Password minimal 8 karakter.",
            "roles.required" => "Role harus dipilih.",
            "roles.exists" => "Role yang dipilih tidak valid.",
        ]);

        try {
            DB::beginTransaction();

            // Find the employee and associated user
            $employee = Employee::findOrFail($id);
            $user = User::where('employee_id', $id)->firstOrFail();

            // Handle identity card upload if new file is provided
            if ($request->hasFile('identity_card')) {
                // Delete old file if exists
                if ($employee->identity_card && file_exists(public_path('storage/files/identity_card/' . $employee->identity_card))) {
                    unlink(public_path('storage/files/identity_card/' . $employee->identity_card));
                }

                $file = $request->file('identity_card');
                $identityCard = 'identity_card_' . time() . '_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/files/identity_card'), $identityCard);
                $employee->identity_card = $identityCard;
            }

            // Handle profile picture upload if new file is provided
            if ($request->hasFile('picture')) {
                // Delete old file if exists
                if ($user->picture && file_exists(public_path('storage/files/picture/' . $user->picture))) {
                    unlink(public_path('storage/files/picture/' . $user->picture));
                }

                $file = $request->file('picture');
                $picture = 'picture_' . time() . '_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/files/picture'), $picture);
                $user->picture = $picture;
            }

            $oldEmployee = $employee->toArray();

            // Update employee data
            $employee->update([
                'department_id' => $request->department_id,
                'position_id' => $request->position_id,
                'company_id' => $request->company_id,
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'nik' => $request->nik,
            ]);

            // Update user data
            $userData = [
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            // Update user roles
            $user->roles()->detach();
            $user->assignRole($request->role);

            activity()
                ->causedBy(Auth::user())
                ->event('updated')
                ->withProperties([
                    'old' => $oldEmployee,
                    'new' => $employee->toArray()
                ])
                ->log("Data Karyawan berhasil diperbarui.");

            DB::commit();

            return redirect()->route('employee')->with([
                'status' => 'Success!',
                'message' => 'Berhasil Mengupdate Data!'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);

            return redirect()->route('employee')->with([
                'status' => 'Error!',
                'message' => 'Gagal Mengupdate Data!'
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            // Find the employee
            $employee = Employee::findOrFail($id);

            activity()
                ->causedBy(Auth::user())
                ->event('deleted')
                ->withProperties($employee->toArray())
                ->log("Data Karyawan berhasil dihapus.");

            // Delete associated user (if exists)
            if ($employee->user) {
                // Delete user profile picture if exists
                if ($employee->user->picture && file_exists(public_path('storage/files/picture/' . $employee->user->picture))) {
                    unlink(public_path('storage/files/picture/' . $employee->user->picture));
                }

                // Delete user record
                $employee->user->delete();
            }

            // Delete associated identity card if exists
            if ($employee->identity_card && file_exists(public_path('storage/files/identity_card/' . $employee->identity_card))) {
                unlink(public_path('storage/files/identity_card/' . $employee->identity_card));
            }

            // Delete associated allowances, salaries, deductions, and leaves if needed
            // Uncomment and adjust this block if you need to delete related records
            // $employee->allowances()->delete();
            // $employee->salaries()->delete();
            // $employee->deductions()->delete();
            // $employee->leaves()->delete();

            // Delete employee record
            $employee->delete();

            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Data Pegawai Berhasil Dihapus!'
            ]);

        } catch (Exception $e) {
            // Log the error for debugging purposes
            Log::error('Error deleting employee: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal Menghapus Data Pegawai!',
                'trace' => $e->getTrace() // Return trace for debugging purposes
            ]);
        }
    }
}

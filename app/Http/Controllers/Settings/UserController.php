<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {

        $data = [
            'title' => 'User',
            'roles' => Role::where('name', '!=', 'Developer')->get(),
            'employee' => Employee::whereDoesntHave('user')->get(),
        ];

        return view('pages.settings.user.index', $data);
    }

    public function getData()
    {
        $user = User::with(['roles', 'employee'])->whereNotIn('name', ['Developer'])->orderByDesc('id')->get();
        return DataTables::of($user)->addIndexColumn()->addColumn('action', function ($data) {
            $userauth = User::with('roles')->where('id', Auth::id())->first();
            $button = '';

            if ($userauth->can('update-user')) {
                $button .= ' <button class="btn btn-sm btn-success" data-id=' . $data->id . ' data-type="edit" data-route="' . route('user.edit', ['id' => $data->id]) . '" data-proses="' . route('user.update', ['id' => $data->id]) . '" data-bs-toggle="modal" data-bs-target="#modal8"
            data-action="edit" data-title="User" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                                        class="fas fa-pen "></i></button>';
            }
            if ($userauth->can('delete-user')) {
                $button .= ' <button class="btn btn-sm btn-danger action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('user.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                                        class="fas fa-trash "></i></button>';
            }

            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->addColumn('role', function ($data) {
            return $data->roles->isNotEmpty() ? $data->roles->pluck('name')->implode(', ') : '-';
        })->addColumn('employee', function ($data) {
            return $data->employee->name ?? '-';
        })->addColumn('status', function ($data) {
            return $data->is_block == 0 ? '<span class="badge badge-label-primary">Aktif</span>' : '<span class="badge badge-label-danger">Blokir</span>';
        })->editColumn('picture', function ($data) {
            return $data->picture == null ? '<img src="' . asset('assets/images/users/avatar-1.png') . '" alt="Profile Image" class="rounded-circle header-profile-user">' : '<img src="' . asset("storage/images/user/$data->picture") . '" alt="Profile Image" class="rounded-circle header-profile-user">';
        })->rawColumns(['action', 'role', 'picture', 'status', 'employee'])->make(true);
    }


    public function store(Request $request)
    {

        $request->validate([
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'username' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|unique:users,email|max:255',
            'password' => 'required|string|min:6|max:255|confirmed',
            'password_confirmation' => 'required|string|min:6|max:255',
            'is_block' => 'required|boolean',
            'role' => 'required'
        ], [
            'picture.image' => 'File harus berupa gambar.',
            'picture.mimes' => 'Format gambar tidak valid.',
            'picture.max' => 'Ukuran gambar tidak boleh lebih dari 2MB.',
            'username.required' => 'Username wajib diisi.',
            'username.string' => 'Username harus berupa teks.',
            'username.max' => 'Username maksimal 255 karakter.',
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.string' => 'Email harus berupa teks.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.string' => 'Password harus berupa teks.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.max' => 'Password maksimal 255 karakter.',
            'password_confirmation.required' => 'Konfirmasi password wajib diisi.',
            'password_confirmation.string' => 'Konfirmasi password harus berupa teks.',
            'password_confirmation.min' => 'Konfirmasi password minimal 6 karakter.',
            'password_confirmation.max' => 'Konfirmasi password maksimal 255 karakter.',
            'is_block.required' => 'Status wajib diisi.',
            'role.required' => 'Role wajib diisi.',
        ]);
        try {

            $filename = '';
            if ($request->hasFile('picture')) {
                $file = $request->file('picture');
                $filename = 'user_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/images/user/'), $filename);
            }

            $user = User::create([
                'picture' => $filename,
                'username' => $request->username,
                'employee_id' => $request->employee_id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_block' => $request->is_block,
            ]);

            foreach ($request->role as $role) {
                $user->assignRole($role);
            }

            activity()
                ->causedBy(Auth::user())
                ->event('created')
                ->withProperties($user->toArray())
                ->log("User {$user->name} berhasil dibuat.");

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'User Berhasil dibuat.'
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
        $user = User::with(['roles'])->findOrFail($id);
        return response()->json(['user' => $user, 'employee' => Employee::all()], 200);
    }

    public function update(Request $request, $id)
    {
        $validationRules = [
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'username' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users,email,' . $id,
            'is_block' => 'required|boolean',
            'role' => 'required'
        ];

        // Only validate password if it's being updated
        if ($request->filled('password')) {
            $validationRules['password'] = 'required|string|min:6|max:255|confirmed';
            $validationRules['password_confirmation'] = 'required|string|min:6|max:255';
        }

        $validationMessages = [
            'picture.image' => 'File harus berupa gambar.',
            'picture.mimes' => 'Format gambar tidak valid.',
            'picture.max' => 'Ukuran gambar tidak boleh lebih dari 2MB.',
            'username.required' => 'Username wajib diisi.',
            'username.string' => 'Username harus berupa teks.',
            'username.max' => 'Username maksimal 255 karakter.',
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.string' => 'Email harus berupa teks.',
            'email.max' => 'Email maksimal 255 karakter.',
            'email.unique' => 'Email sudah digunakan.',
            'is_block.required' => 'Status wajib diisi.',
            'role.required' => 'Role wajib diisi.',
        ];

        if ($request->filled('password')) {
            $validationMessages['password.required'] = 'Password wajib diisi.';
            $validationMessages['password.string'] = 'Password harus berupa teks.';
            $validationMessages['password.min'] = 'Password minimal 6 karakter.';
            $validationMessages['password.max'] = 'Password maksimal 255 karakter.';
            $validationMessages['password.confirmed'] = 'Konfirmasi password tidak cocok.';
            $validationMessages['password_confirmation.required'] = 'Konfirmasi password wajib diisi.';
            $validationMessages['password_confirmation.string'] = 'Konfirmasi password harus berupa teks.';
            $validationMessages['password_confirmation.min'] = 'Konfirmasi password minimal 6 karakter.';
            $validationMessages['password_confirmation.max'] = 'Konfirmasi password maksimal 255 karakter.';
        }

        $request->validate($validationRules, $validationMessages);
        try {
            $user = User::findOrFail($id);

            $oldUserData = $user->toArray();

            // Handle file upload
            if ($request->hasFile('picture')) {
                // Delete old picture if exists
                if ($user->picture && file_exists(public_path('storage/images/user/' . $user->picture))) {
                    unlink(public_path('storage/images/user/' . $user->picture));
                }

                $file = $request->file('picture');
                $filename = 'user_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/images/user/'), $filename);
                $user->picture = $filename;
            }

            // Update user data
            $user->username = $request->username;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->is_block = $request->is_block;
            $user->employee_id = $request->employee_id == null ? $user->employee_id : $request->employee_id;

            // Only update password if provided
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            DB::table('model_has_roles')->where('model_id', $id)->delete();
            foreach ($request->role as $role) {
                $user->assignRole($role);
            }

            activity()
                ->causedBy(Auth::user())
                ->event('updated')
                ->withProperties([
                    'old' => $oldUserData,
                    'new' => $user->toArray(),
                ])
                ->log("User {$user->name} berhasil diupdate.");

            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'User Berhasil diupdate.'
            ]);
        } catch (Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage()); // Log error
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
            $user = User::findOrFail($id);

            $oldUserData = $user->toArray();

            if (file_exists(public_path('storage/images/user/' . $user->picture))) {
                File::delete(public_path('storage/images/user/' . $user->picture));
            }

            // Log the activity
            activity()
                ->causedBy(Auth::user())
                ->event('deleted')
                ->withProperties($oldUserData)
                ->log("User {$user->name} berhasil dihapus.");

            $user->delete();

            //return response
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Data Produk Berhasil Dihapus!.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal Menghapus Data Produk!',
                'trace' => $e->getTrace()
            ]);
        }
    }
}

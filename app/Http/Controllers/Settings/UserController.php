<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'User',
            'roles' => Role::all(),
        ];

        return view('pages.settings.user.index', $data);
    }

    public function getData()
    {
        $user = User::orderByDesc('id')->get();

        return DataTables::of($user)->addIndexColumn()->addColumn('action', function ($data) {
            // $userauth = User::with('roles')->where('id', Auth::id())->first();
            $button = '';

            $button .= ' <a href="' . route('user.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-success action mr-1" data-id=' . $data->id . ' data-type="edit" data-toggle="tooltip" data-placement="bottom" title="Edit Data"><i
                class="fas fa-pencil-alt"></i></a>';

            $button .= ' <button  class="btn btn-sm btn-danger  action" data-id=' . $data->id . ' data-type="delete" data-route="' . route('user.delete', ['id' => $data->id]) . '" data-toggle="tooltip" data-placement="bottom" title="Delete Data"><i
                class="fas fa-trash-alt "></i></button>';
                
            return '<div class="d-flex gap-2">' . $button . '</div>';
        })->addColumn('role', function ($data) {
            return $data->roles[0]->name;
        })->editColumn('picture', function ($data) {
            return $data->picture == null ? '<img src="' . asset('assets/images/avataaars.png') . '" alt="Profile Image" class="rounded-circle header-profile-user">' : '<img src="' . asset("storage/images/user/$data->picture") . '" alt="Profile Image" class="rounded-circle header-profile-user">';
        })->rawColumns(['action', 'role', 'picture'])->make(true);
    }
    

    public function store(Request $request)
    {
        try {
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
    
            $filename = '';
            if ($request->hasFile('picture')) {
                $file = $request->file('picture');
                $filename = 'user_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/images/user/'), $filename);
            }
    
            $user = User::create([
                'picture' => $filename,
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_block' => $request->is_block,
            ]);
    
            $user->assignRole($request->role);
    
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
        $user = User::findOrFail($id);
        return response()->json([
            'user'  => $user,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        try {
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
    
            $user = User::findOrFail($id);
    
            $filename = $user->picture;
    
            if ($request->hasFile('picture')) {
                $file = $request->file('picture');
                $filename = 'user_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/images/user/'), $filename);
    
                // Hapus gambar lama jika ada
                if ($user->picture && File::exists(public_path('storage/images/user/' . $user->picture))) {
                    File::delete(public_path('storage/images/user/' . $user->picture));
                }
            }
    
            $user->update([
                'picture' => $filename,
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_block' => $request->is_block,
            ]);
    
            $user->syncRoles([$request->role]);
    
            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'User Berhasil diupdate.'
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
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json([
                'success' => true,
                'status' => "Berhasil",
                'message' => 'User Berhasil dihapus.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => "Gagal",
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }
}

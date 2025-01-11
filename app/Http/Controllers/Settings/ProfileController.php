<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index($id) {

        $data = [
            'title' => 'Profile',
            'profile' => User::find($id),
        ];

        return view('pages.settings.profile.index', $data);
    }

    public function update(Request $request, $id) {

        $request->validate([
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'nullable|string|max:255',
            'email' => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($id), // Abaikan pengguna saat ini berdasarkan ID
            ],
            'username' => [
                'nullable',
                'string',
                Rule::unique('users', 'username')->ignore($id), // Abaikan pengguna saat ini berdasarkan ID
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
            ],
        ], [
            'picture.image' => 'File harus berupa gambar.',
            'picture.mimes' => 'Format gambar tidak valid.',
            'picture.max' => 'Ukuran gambar maksimal 2MB.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Panjang nama maksimal 255 karakter.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'username.string' => 'Username harus berupa teks.',
            'username.unique' => 'Username sudah terdaftar.',
            'password.string' => 'Kata sandi harus berupa teks.',
            'password.min' => 'Panjang kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Kata sandi tidak cocok.',
            'password.regex' => 'Kata sandi harus mengandung huruf besar, huruf kecil, dan angka.',
        ]);

        $user = User::find($id);

        try {
            $filename = $user->picture;

            if ($request->hasFile('picture')) {
                $file = $request->file('picture');
                $filename = 'user_' . rand(0, 999999999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/images/user/'), $filename);

                if ($user->picture !== 'default.png' && file_exists(public_path('storage/images/user/' . $user->picture))) {
                    File::delete(public_path('storage/images/user/' . $user->picture));
                }
            }

            if ($request->filled('password')) {
                $user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            $user->update([
                'picture' => $filename,
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
            ]);

            return redirect()->back()->with(['status' => 'Success!', 'message' => 'Berhasil Update!']);
        } catch (Exception $e) {
            return redirect()->back()->with(['status' => 'Error!', 'message' => 'Gagal Update!']);
        }
    }
}

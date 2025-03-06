<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Salary;
use App\Models\User;
use App\Models\Employee;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index($id)
    {
        $user = User::with('employee.department', 'employee.position')->findOrFail($id);

        $salary = null;

        if ($user->hasRole('Employee') && $user->employee !== null) {
            $salary = Salary::where('employee_id', $user->employee->id)->latest()->first();
        }

        $data = [
            'title' => 'Profile',
            'profile' => $user,
            'isEmployee' => $user->hasRole('Employee') && $user->employee !== null,
            'salary' => $salary,
        ];

        return view('pages.settings.profile.index', $data);
    }

    public function update(Request $request, $id)
    {
        // Determine update type based on form data
        $updateType = 'profile'; // Default type
        
        if ($request->has('nik') || $request->has('phone') || $request->has('date_of_birth') || 
            $request->has('gender') || $request->has('address')) {
            $updateType = 'employee';
        } elseif ($request->has('password')) {
            $updateType = 'password';
        }
        
        $user = User::findOrFail($id);
        
        // Set up validation rules based on update type
        $validationRules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($id),
            ],
            'username' => [
                'required',
                'string',
                Rule::unique('users', 'username')->ignore($id),
            ],
        ];
        
        // Add specific validation rules based on update type
        if ($updateType === 'profile') {
            $validationRules['picture'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
        } elseif ($updateType === 'employee') {
            $validationRules['address'] = 'nullable|string|max:255';
            $validationRules['phone'] = 'nullable|string|max:20';
            $validationRules['date_of_birth'] = 'nullable|date';
            $validationRules['gender'] = 'nullable|in:male,female';
            $validationRules['nik'] = 'nullable|string|max:20';
        } elseif ($updateType === 'password') {
            $validationRules['password'] = [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
            ];
        }

        // Validation messages
        $validationMessages = [
            'picture.image' => 'File harus berupa gambar.',
            'picture.mimes' => 'Format gambar tidak valid.',
            'picture.max' => 'Ukuran gambar maksimal 2MB.',
            'name.required' => 'Nama tidak boleh kosong.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Panjang nama maksimal 255 karakter.',
            'email.required' => 'Email tidak boleh kosong.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'username.required' => 'Username tidak boleh kosong.',
            'username.string' => 'Username harus berupa teks.',
            'username.unique' => 'Username sudah terdaftar.',
            'password.required' => 'Kata sandi tidak boleh kosong.',
            'password.string' => 'Kata sandi harus berupa teks.',
            'password.min' => 'Panjang kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Kata sandi tidak cocok.',
            'password.regex' => 'Kata sandi harus mengandung huruf besar, huruf kecil, dan angka.',
        ];

        // Validate the request
        $request->validate($validationRules, $validationMessages);

        DB::beginTransaction();

        try {
            // Update user basic information regardless of update type
            $userData = [
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
            ];

            // Handle specific update types
            if ($updateType === 'profile' && $request->hasFile('picture')) {
                $file = $request->file('picture');
                $filename = 'user_' . time() . '_' . rand(0, 999999) . '.' . $file->getClientOriginalExtension();

                $file->move(public_path('storage/images/user/'), $filename);

                // Delete old picture if not default
                if ($user->picture && $user->picture !== 'default.png' && file_exists(public_path('storage/images/user/' . $user->picture))) {
                    File::delete(public_path('storage/images/user/' . $user->picture));
                }
                
                $userData['picture'] = $filename;
            }
            
            // Handle password update
            if ($updateType === 'password' && $request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            // Update the user
            $user->update($userData);

            // Update employee details if applicable
            if ($updateType === 'employee' && $user->hasRole('Employee') && $user->employee) {
                $user->employee->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'address' => $request->address,
                    'phone' => $request->phone,
                    'date_of_birth' => $request->date_of_birth,
                    'gender' => $request->gender,
                    'nik' => $request->nik,
                ]);
            }

            DB::commit();

            return redirect()->back()->with([
                'status' => 'Success!',
                'message' => 'Profile berhasil diperbarui!'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Profile update error: ' . $e->getMessage());

            return redirect()->back()->with([
                'status' => 'Error!',
                'message' => 'Gagal memperbarui profile: ' . $e->getMessage()
            ]);
        }
    }
}
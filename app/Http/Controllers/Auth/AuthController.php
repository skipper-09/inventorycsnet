<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Login'
        ];

        return view('pages.auth.login', $data);
    }

    public function signin(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        $user = User::where(['username' => $request->username]);
        if ($user->count() > 0) {
            $user = $user->first();

            if ($user->is_block == 1) {
                return redirect()->back()->withErrors([
                    'message' => 'Akun ini telah di kunci, silahkan hubungi admin untuk membuka akun.'
                ]);
            } else {
                if (Auth::guard('web')->attempt(['username' => $request->username, 'password' => $request->password])) {
                    $request->session()->regenerate();
                    if ($request->remember_me == 'Y') {
                        $time = 60 * 60 * 24 * 365;
                        Cookie::queue('username', $request->username, $time);
                        Cookie::queue('password', $request->password, $time);
                    }
                    return redirect()->route('dashboard')->with(['status' => 'Success!', 'message' => 'Berhasil Login!']);
                } else {
                    return
                        redirect()->back()->withErrors([
                            'message' => 'Wrong username or password'
                        ]);
                }
            }
        } else {
            return redirect()->back()->withErrors([
                'message' => 'Username dan password tidak terdaftar'
            ]);
        }
    }

    public function signout()
    {
        Auth::logout();

        return redirect()->route('login');
    }

    public function ResetPassword(){
        $data = [
            'title'=>'Reset Password',
        ];
        return view('pages.auth.reset-password',$data);
    }
}

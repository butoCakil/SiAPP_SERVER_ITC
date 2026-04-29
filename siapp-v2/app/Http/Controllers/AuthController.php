<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('admin_id')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $admin = Admin::where('username', $request->username)
            ->where('status', 'login')
            ->first();

        if (!$admin || !$admin->verifyPassword($request->password)) {
            return back()->withErrors(['login' => 'Username atau password salah.'])->withInput();
        }

        session([
            'admin_id'   => $admin->id,
            'admin_nama' => $admin->username,
            'admin_foto' => $admin->foto,
            'admin_email'=> $admin->email,
        ]);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        // bersihkan session dengan benar
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
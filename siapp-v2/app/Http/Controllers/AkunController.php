<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AkunController extends Controller
{
    public function index()
    {
        $akuns = DB::table('admin')->orderBy('id')->get();
        return view('akun.index', compact('akuns'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'email'    => 'required|email|unique:admin,email',
            'password' => 'required|min:6',
        ]);

        DB::table('admin')->insert([
            'username'  => $request->username,
            'email'     => $request->email,
            'password'  => md5($request->password),
            'status'    => 'login',
            'wa'        => $request->wa ?? '',
            'foto'      => 'default.jpg',
            'timestamp' => now(),
        ]);

        return redirect()->route('akun')->with('success', 'Akun berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        DB::table('admin')->where('id', $id)->update([
            'username' => $request->username,
            'email'    => $request->email,
            'wa'       => $request->wa ?? '',
        ]);

        return redirect()->route('akun')->with('success', 'Akun berhasil diupdate.');
    }

    public function resetPassword(Request $request, int $id)
    {
        $request->validate(['password' => 'required|min:6']);

        DB::table('admin')->where('id', $id)->update([
            'password' => md5($request->password),
        ]);

        return redirect()->route('akun')->with('success', 'Password berhasil direset.');
    }

    public function destroy(int $id)
    {
        $currentId = session('admin_id');
        if ($id == $currentId) {
            return redirect()->route('akun')->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        DB::table('admin')->where('id', $id)->delete();
        return redirect()->route('akun')->with('success', 'Akun berhasil dihapus.');
    }
}
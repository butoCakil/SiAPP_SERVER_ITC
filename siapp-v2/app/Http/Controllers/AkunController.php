<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AkunController extends Controller
{
    private function currentUsername(): string
    {
        return (string) session('admin_nama', '');
    }

    private function isPengembang(): bool
    {
        return $this->currentUsername() === 'Pengembang';
    }

    private function isSuperAdmin(): bool
    {
        return $this->currentUsername() === 'Super Admin';
    }

    public function index()
    {
        $akuns = DB::table('admin')->orderBy('id')->get();

        // Filter: Super Admin tidak melihat akun Pengembang
        if ($this->isSuperAdmin()) {
            $akuns = $akuns->filter(fn($a) => $a->username !== 'Pengembang')->values();
        }

        // Selain Pengembang & Super Admin: sembunyikan email Pengembang (handle di blade)
        return view('akun.index', compact('akuns'));
    }

    public function store(Request $request)
    {
        if (!$this->isPengembang() && !$this->isSuperAdmin()) {
            return redirect()->route('akun')->with('error', 'Tidak memiliki akses.');
        }

        $request->validate([
            'username' => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        $usernameAda = DB::table('admin')->whereRaw('LOWER(username) = ?', [strtolower($request->username)])->exists();
        if ($usernameAda) {
            return redirect()->route('akun')->with('error', 'Username "' . $request->username . '" sudah digunakan.');
        }

        $emailAda = DB::table('admin')->whereRaw('LOWER(email) = ?', [strtolower($request->email)])->exists();
        if ($emailAda) {
            return redirect()->route('akun')->with('error', 'Email "' . $request->email . '" sudah digunakan.');
        }

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
        if (!$this->isPengembang() && !$this->isSuperAdmin()) {
            return redirect()->route('akun')->with('error', 'Tidak memiliki akses.');
        }

        // Super Admin tidak bisa edit akun Pengembang
        $target = DB::table('admin')->where('id', $id)->first();
        if ($this->isSuperAdmin() && $target && $target->username === 'Pengembang') {
            return redirect()->route('akun')->with('error', 'Tidak bisa mengelola akun Pengembang.');
        }

        DB::table('admin')->where('id', $id)->update([
            'username' => $request->username,
            'email'    => $request->email,
            'wa'       => $request->wa ?? '',
        ]);

        return redirect()->route('akun')->with('success', 'Akun berhasil diupdate.');
    }

    public function resetPassword(Request $request, int $id)
    {
        if (!$this->isPengembang() && !$this->isSuperAdmin()) {
            return redirect()->route('akun')->with('error', 'Tidak memiliki akses.');
        }

        // Super Admin tidak bisa reset password Pengembang
        $target = DB::table('admin')->where('id', $id)->first();
        if ($this->isSuperAdmin() && $target && $target->username === 'Pengembang') {
            return redirect()->route('akun')->with('error', 'Tidak bisa mengelola akun Pengembang.');
        }

        $request->validate(['password' => 'required|min:6']);

        DB::table('admin')->where('id', $id)->update([
            'password' => md5($request->password),
        ]);

        return redirect()->route('akun')->with('success', 'Password berhasil direset.');
    }

    public function destroy(int $id)
    {
        if (!$this->isPengembang() && !$this->isSuperAdmin()) {
            return redirect()->route('akun')->with('error', 'Tidak memiliki akses.');
        }

        $currentId = session('admin_id');
        if ($id == $currentId) {
            return redirect()->route('akun')->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        // Super Admin tidak bisa hapus akun Pengembang
        $target = DB::table('admin')->where('id', $id)->first();
        if ($this->isSuperAdmin() && $target && $target->username === 'Pengembang') {
            return redirect()->route('akun')->with('error', 'Tidak bisa mengelola akun Pengembang.');
        }

        DB::table('admin')->where('id', $id)->delete();
        return redirect()->route('akun')->with('success', 'Akun berhasil dihapus.');
    }
}

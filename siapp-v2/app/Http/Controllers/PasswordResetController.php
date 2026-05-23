<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    // ── Halaman lupa password (dari login) ──
    public function showForgot()
    {
        return view('auth.forgot');
    }

    // ── Proses request reset (dari login) ──
    public function requestReset(Request $request)
    {
        $request->validate([
            'identity' => 'required',
            'metode'   => 'required|in:email,wa',
        ]);

        $admin = DB::table('admin')
            ->where(function ($q) use ($request) {
                $q->whereRaw('LOWER(username) = ?', [strtolower($request->identity)])
                  ->orWhereRaw('LOWER(email) = ?', [strtolower($request->identity)]);
            })
            ->where('status', 'login')
            ->first();

        if (!$admin) {
            return back()->with('error', 'Akun tidak ditemukan.');
        }

        // Cek metode tersedia
        if ($request->metode === 'wa' && empty($admin->wa)) {
            return back()->with('error', 'Akun ini tidak memiliki nomor WhatsApp.');
        }
        if ($request->metode === 'email' && empty($admin->email)) {
            return back()->with('error', 'Akun ini tidak memiliki email.');
        }

        return $this->kirimReset($admin, $request->metode);
    }

    // ── Kirim reset dari menu Akun (oleh Pengembang/Super Admin) ──
    public function kirimDariAkun(Request $request, int $id)
    {
        $loginUsername = session('admin_nama', '');
        if (!in_array($loginUsername, ['Pengembang', 'Super Admin'])) {
            return redirect()->route('akun')->with('error', 'Tidak memiliki akses.');
        }

        $request->validate(['metode' => 'required|in:email,wa']);

        $admin = DB::table('admin')->where('id', $id)->first();
        if (!$admin) {
            return redirect()->route('akun')->with('error', 'Akun tidak ditemukan.');
        }

        // Super Admin tidak bisa reset Pengembang
        if ($loginUsername === 'Super Admin' && $admin->username === 'Pengembang') {
            return redirect()->route('akun')->with('error', 'Tidak bisa mengelola akun Pengembang.');
        }

        if ($request->metode === 'wa' && empty($admin->wa)) {
            return redirect()->route('akun')->with('error', 'Akun ' . $admin->username . ' tidak memiliki nomor WhatsApp.');
        }
        if ($request->metode === 'email' && empty($admin->email)) {
            return redirect()->route('akun')->with('error', 'Akun ' . $admin->username . ' tidak memiliki email.');
        }

        return $this->kirimReset($admin, $request->metode, true);
    }

    // ── Core: generate token/OTP dan kirim ──
    private function kirimReset(object $admin, string $metode, bool $dariAkun = false): \Illuminate\Http\RedirectResponse
    {
        // Hapus token lama yang belum dipakai
        DB::table('password_resets')
            ->where('admin_id', $admin->id)
            ->whereNull('used_at')
            ->delete();

        $token     = Str::random(48);
        $otp       = (string) random_int(100000, 999999);
        $expiredAt = now()->addMinutes(30);

        DB::table('password_resets')->insert([
            'admin_id'   => $admin->id,
            'token'      => $token,
            'otp'        => $otp,
            'metode'     => $metode,
            'expired_at' => $expiredAt,
            'created_at' => now(),
        ]);

        $resetUrl = url('/reset-password/' . $token);

        if ($metode === 'email') {
            $this->kirimEmail($admin, $resetUrl, $otp);
        } else {
            $this->kirimWA($admin, $resetUrl, $otp);
        }

        // Notifikasi ke Pengembang via WA
        $this->notifikasiPengembang($admin, $metode);

        $redirect = $dariAkun
            ? redirect()->route('akun')->with('success', 'Link/OTP reset password berhasil dikirim ke ' . $admin->username . '.')
            : redirect()->route('password.otp', ['token' => $token])->with('info', 'Kode OTP atau link reset telah dikirim.');

        return $redirect;
    }

    // ── Halaman input OTP ──
    public function showOtp(string $token)
    {
        $reset = DB::table('password_resets')
            ->where('token', $token)
            ->whereNull('used_at')
            ->where('expired_at', '>', now())
            ->first();

        if (!$reset) {
            return redirect()->route('login')->with('error', 'Token tidak valid atau sudah kedaluwarsa.');
        }

        return view('auth.otp', compact('token'));
    }

    // ── Verifikasi OTP ──
    public function verifyOtp(Request $request, string $token)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $reset = DB::table('password_resets')
            ->where('token', $token)
            ->where('otp', $request->otp)
            ->whereNull('used_at')
            ->where('expired_at', '>', now())
            ->first();

        if (!$reset) {
            return back()->with('error', 'Kode OTP salah atau sudah kedaluwarsa.');
        }

        return redirect()->route('password.reset.form', ['token' => $token]);
    }

    // ── Halaman form reset password (via link atau setelah OTP) ──
    public function showReset(string $token)
    {
        $reset = DB::table('password_resets')
            ->where('token', $token)
            ->whereNull('used_at')
            ->where('expired_at', '>', now())
            ->first();

        if (!$reset) {
            return redirect()->route('login')->with('error', 'Token tidak valid atau sudah kedaluwarsa.');
        }

        return view('auth.reset', compact('token'));
    }

    // ── Proses reset password ──
    public function doReset(Request $request, string $token)
    {
        $request->validate([
            'password'              => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ]);

        $reset = DB::table('password_resets')
            ->where('token', $token)
            ->whereNull('used_at')
            ->where('expired_at', '>', now())
            ->first();

        if (!$reset) {
            return redirect()->route('login')->with('error', 'Token tidak valid atau sudah kedaluwarsa.');
        }

        DB::table('admin')->where('id', $reset->admin_id)->update([
            'password' => md5($request->password),
        ]);

        DB::table('password_resets')->where('token', $token)->update([
            'used_at' => now(),
        ]);

        return redirect()->route('login')->with('success', 'Password berhasil direset. Silakan login.');
    }

    // ── Kirim email ──
    private function kirimEmail(object $admin, string $url, string $otp): void
    {
        try {
            Mail::send([], [], function ($m) use ($admin, $url, $otp) {
                $m->to($admin->email)
                  ->subject('[SiAPP] Reset Password')
                  ->html("
                    <div style='font-family:sans-serif;max-width:500px;margin:auto;'>
                        <h2 style='color:#007bff;'>🔐 Reset Password SiAPP</h2>
                        <p>Halo <strong>{$admin->username}</strong>,</p>
                        <p>Permintaan reset password telah diterima.</p>
                        <p><strong>Kode OTP:</strong> <span style='font-size:24px;font-weight:bold;color:#dc3545;letter-spacing:4px;'>{$otp}</span></p>
                        <p>Atau klik link berikut:</p>
                        <a href='{$url}' style='display:inline-block;padding:10px 20px;background:#007bff;color:#fff;border-radius:6px;text-decoration:none;'>Reset Password</a>
                        <hr>
                        <small style='color:#999;'>Link & OTP berlaku 30 menit. Abaikan jika tidak merasa meminta reset.</small>
                    </div>
                  ");
            });
        } catch (\Throwable $e) {
            \Log::error('Gagal kirim email reset: ' . $e->getMessage());
        }
    }

    // ── Kirim WA ──
    private function kirimWA(object $admin, string $url, string $otp): void
    {
        $setting  = DB::table('statusnya')->first();
        $deviceId = $setting->wa_device_id ?? '';

        if (!$deviceId || !$admin->wa) return;

        $pesan = "🔐 *Reset Password SiAPP*\n\n"
               . "Halo *{$admin->username}*,\n\n"
               . "Kode OTP Anda: *{$otp}*\n\n"
               . "Atau buka link:\n{$url}\n\n"
               . "_Berlaku 30 menit. Abaikan jika tidak merasa meminta reset._";

        try {
            \Illuminate\Support\Facades\Http::post('https://api.whacenter.com/api/send', [
                'device_id' => $deviceId,
                'number'    => $admin->wa,
                'message'   => $pesan,
                'file'      => null,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Gagal kirim WA reset: ' . $e->getMessage());
        }
    }

    // ── Notifikasi Pengembang ──
    private function notifikasiPengembang(object $admin, string $metode): void
    {
        $pengembang = DB::table('admin')->where('username', 'Pengembang')->first();
        if (!$pengembang || !$pengembang->wa) return;

        $setting  = DB::table('statusnya')->first();
        $deviceId = $setting->wa_device_id ?? '';
        if (!$deviceId) return;

        $pesan = "ℹ️ *SiAPP - Info Reset Password*\n\n"
               . "Akun *{$admin->username}* ({$admin->email})\n"
               . "melakukan reset password via *" . strtoupper($metode) . "*\n"
               . "Waktu: " . now()->format('d/m/Y H:i:s');

        try {
            \Illuminate\Support\Facades\Http::post('https://api.whacenter.com/api/send', [
                'device_id' => $deviceId,
                'number'    => $pengembang->wa,
                'message'   => $pesan,
                'file'      => null,
            ]);
        } catch (\Throwable $e) {
            \Log::error('Gagal kirim notif Pengembang: ' . $e->getMessage());
        }
    }
}

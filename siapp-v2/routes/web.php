<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\DeviceViewController;
use App\Http\Controllers\PresensiViewController;
use App\Http\Controllers\SiswaViewController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ApiKeyController;
use App\Http\Controllers\AkunController;

/*
|--------------------------------------------------------------------------
| ROOT DECISION
|--------------------------------------------------------------------------
| Tidak ada logic lain di sini selain routing keputusan
*/

Route::get('/', function () {
    return Auth::check()
        ? redirect('/dashboard')
        : redirect('/home');
});

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (TANPA AUTH)
|--------------------------------------------------------------------------
*/
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/home/poll', [HomeController::class, 'poll'])->name('home.poll');

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (PER ROUTE, BUKAN GROUP ROOT)
|--------------------------------------------------------------------------
*/

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth.admin')
    ->name('dashboard');

// Setting
Route::get('/setting', [SettingController::class, 'index'])
    ->middleware('auth.admin')
    ->name('setting');

Route::post('/setting', [SettingController::class, 'update'])
    ->middleware('auth.admin')
    ->name('setting.update');

Route::post('/setting/retry-push', [SettingController::class, 'retryPush'])
    ->middleware('auth.admin')
    ->name('setting.retry-push');

// Device
Route::get('/device', [DeviceViewController::class, 'index'])
    ->middleware('auth.admin')
    ->name('device');

Route::get('/device/registrasi', [DeviceViewController::class, 'registrasi'])
    ->middleware('auth.admin')
    ->name('device.registrasi');

Route::post('/device/registrasi', [DeviceViewController::class, 'storeReg'])
    ->middleware('auth.admin')
    ->name('device.registrasi.store');

Route::put('/device/registrasi/{id}', [DeviceViewController::class, 'updateReg'])
    ->middleware('auth.admin')
    ->name('device.registrasi.update');

Route::delete('/device/registrasi/{id}', [DeviceViewController::class, 'destroyReg'])
    ->middleware('auth.admin')
    ->name('device.registrasi.destroy');

// Device AJAX
Route::get('/device/cards', [DeviceViewController::class, 'cards'])
    ->middleware('auth.admin')
    ->name('device.cards');

Route::get('/device/{id}/log', [DeviceViewController::class, 'logViewer'])
    ->middleware('auth.admin')
    ->name('device.log');

Route::get('/device/{id}', [DeviceViewController::class, 'detail'])
    ->middleware('auth.admin')
    ->name('device.detail');

Route::delete('/device/{id}', [DeviceViewController::class, 'destroy'])
    ->middleware('auth.admin')
    ->name('device.destroy');

// Presensi
Route::get('/presensi', [PresensiViewController::class, 'index'])
    ->middleware('auth.admin')
    ->name('presensi');

Route::get('/presensi/create', [PresensiViewController::class, 'create'])
    ->middleware('auth.admin')
    ->name('presensi.create');

Route::post('/presensi', [PresensiViewController::class, 'store'])
    ->middleware('auth.admin')
    ->name('presensi.store');

Route::get('/presensi/{id}/edit', [PresensiViewController::class, 'edit'])
    ->middleware('auth.admin')
    ->name('presensi.edit');

Route::put('/presensi/{id}', [PresensiViewController::class, 'update'])
    ->middleware('auth.admin')
    ->name('presensi.update');

Route::delete('/presensi/{id}', [PresensiViewController::class, 'destroy'])
    ->middleware('auth.admin')
    ->name('presensi.destroy');

// Event Presensi
Route::post('/presensi/event', [PresensiViewController::class, 'storeEvent'])
    ->middleware('auth.admin')
    ->name('presensi.event.store');

Route::put('/presensi/event/{id}', [PresensiViewController::class, 'updateEvent'])
    ->middleware('auth.admin')
    ->name('presensi.event.update');

Route::delete('/presensi/event/{id}', [PresensiViewController::class, 'destroyEvent'])
    ->middleware('auth.admin')
    ->name('presensi.event.destroy');

Route::get('/presensi/event', [PresensiViewController::class, 'event'])
    ->middleware('auth.admin')
    ->name('presensi.event');

// Rekap Bulanan Per Siswa
Route::get('/presensi/rekap', [PresensiViewController::class, 'rekap'])
    ->middleware('auth.admin')->name('presensi.rekap');
Route::get('/presensi/rekap/semester', [PresensiViewController::class, 'rekapSemester'])
    ->middleware('auth.admin')->name('presensi.rekap.semester');
Route::get('/presensi/rekap/semester/{nis}', [PresensiViewController::class, 'rekapSemesterDetail'])
    ->middleware('auth.admin')->name('presensi.rekap.semester.detail');
Route::get('/presensi/rekap/{nis}', [PresensiViewController::class, 'rekapDetail'])
    ->middleware('auth.admin')->name('presensi.rekap.detail');

// Izin Keluar
Route::get('/presensi/ijin', [PresensiViewController::class, 'ijin'])
    ->middleware('auth.admin')->name('presensi.ijin');
Route::post('/presensi/ijin', [PresensiViewController::class, 'storeIjin'])
    ->middleware('auth.admin')->name('presensi.ijin.store');
Route::put('/presensi/ijin/{id}', [PresensiViewController::class, 'updateIjin'])
    ->middleware('auth.admin')->name('presensi.ijin.update');
Route::delete('/presensi/ijin/{id}', [PresensiViewController::class, 'destroyIjin'])
    ->middleware('auth.admin')->name('presensi.ijin.destroy');

// Siswa
Route::get('/siswa', [SiswaViewController::class, 'index'])
    ->middleware('auth.admin')
    ->name('siswa');

Route::get('/siswa/create', [SiswaViewController::class, 'create'])
    ->middleware('auth.admin')
    ->name('siswa.create');

Route::post('/siswa', [SiswaViewController::class, 'store'])
    ->middleware('auth.admin')
    ->name('siswa.store');

Route::get('/siswa/{id}/edit', [SiswaViewController::class, 'editSiswa'])
    ->middleware('auth.admin')
    ->name('siswa.edit');

Route::put('/siswa/{id}', [SiswaViewController::class, 'updateSiswa'])
    ->middleware('auth.admin')
    ->name('siswa.update');

Route::delete('/siswa/{id}', [SiswaViewController::class, 'destroySiswa'])
    ->middleware('auth.admin')
    ->name('siswa.destroy');

Route::post('/siswa/kartu', [SiswaViewController::class, 'updateKartu'])
    ->middleware('auth.admin')
    ->name('siswa.kartu');

Route::get('/siswa/tmprfid', [SiswaViewController::class, 'tmprfid'])
    ->middleware('auth.admin')
    ->name('siswa.tmprfid');

// Log Management
Route::get('/log', [LogController::class, 'index'])
    ->middleware('auth.admin')
    ->name('log');

Route::delete('/log/tempreq', [LogController::class, 'clearTempreq'])
    ->middleware('auth.admin')
    ->name('log.tempreq.clear');

Route::delete('/log/device', [LogController::class, 'clearDevice'])
    ->middleware('auth.admin')
    ->name('log.device.clear');

Route::get('/log/sidebar', [LogController::class, 'sidebar'])
    ->middleware('auth.admin')->name('log.sidebar');
Route::get('/log/ajax/mqtt', [LogController::class, 'ajaxMqtt'])
    ->middleware('auth.admin')->name('log.ajax.mqtt');
Route::get('/log/ajax/request', [LogController::class, 'ajaxRequest'])
    ->middleware('auth.admin')->name('log.ajax.request');
Route::get('/log/ajax/server', [LogController::class, 'ajaxServer'])
    ->middleware('auth.admin')->name('log.ajax.server');

/*
|--------------------------------------------------------------------------
| PUBLIC API / DEVICE
|--------------------------------------------------------------------------
*/
Route::get('/tag', [SiswaViewController::class, 'tagKartu']);

// API Key Management
Route::get('/apikey', [ApiKeyController::class, 'index'])
    ->middleware('auth.admin')
    ->name('apikey');

Route::post('/apikey', [ApiKeyController::class, 'store'])
    ->middleware('auth.admin')
    ->name('apikey.store');

Route::put('/apikey/{id}', [ApiKeyController::class, 'update'])
    ->middleware('auth.admin')
    ->name('apikey.update');

Route::delete('/apikey/{id}', [ApiKeyController::class, 'destroy'])
    ->middleware('auth.admin')
    ->name('apikey.destroy');

/*
|--------------------------------------------------------------------------
| INTERNAL UPLOAD API (dari legacy PHP forwarder)
|--------------------------------------------------------------------------
*/
Route::middleware(['local.only'])->prefix('api/upload')->group(function () {
    Route::post('/presensi',   [\App\Http\Controllers\UploadController::class, 'presensi']);
    Route::post('/sholat',     [\App\Http\Controllers\UploadController::class, 'sholat']);
    Route::post('/izinsholat', [\App\Http\Controllers\UploadController::class, 'izinSholat']);
    Route::post('/file',       [\App\Http\Controllers\UploadController::class, 'file']);
});

// Akun Management
Route::get('/akun', [AkunController::class, 'index'])
    ->middleware('auth.admin')->name('akun');
Route::post('/akun', [AkunController::class, 'store'])
    ->middleware('auth.admin')->name('akun.store');
Route::put('/akun/{id}', [AkunController::class, 'update'])
    ->middleware('auth.admin')->name('akun.update');
Route::delete('/akun/{id}', [AkunController::class, 'destroy'])
    ->middleware('auth.admin')->name('akun.destroy');
Route::put('/akun/{id}/password', [AkunController::class, 'resetPassword'])
    ->middleware('auth.admin')->name('akun.password');
/*
|--------------------------------------------------------------------------
| INTERNAL API (AUTH)
|--------------------------------------------------------------------------
*/
Route::get('/api-internal/device-online', function () {
    $online  = DB::table('devices')->where('hidden', 0)->where('online', 1)->count();
    $offline = DB::table('devices')->where('hidden', 0)->where('online', 0)->count();
    return response()->json([
        'online'  => $online,
        'offline' => $offline,
    ]);
})->middleware('auth.admin');

Route::get('/log/file', function (\Illuminate\Http\Request $request) {
    $filename = basename($request->input('f', ''));
    $path     = '/opt/lampp/htdocs/data/uploads/' . $filename;
    if (!$filename || !file_exists($path) || !str_ends_with($filename, '.txt')) {
        abort(404);
    }
    return response(file_get_contents($path), 200, ['Content-Type' => 'text/plain; charset=utf-8']);
})->middleware('auth.admin')->name('log.file.read');

Route::get('/api-internal/device-metrics/{id}', function (string $id) {
    $metrics = DB::table('device_metrics')
        ->where('device_id', $id)
        ->orderBy('recorded_at', 'asc')
        ->get(['ram', 'rssi', 'ping', 'buffer', 'recorded_at']);
    return response()->json($metrics);
})->middleware('auth.admin');

Route::post('/device/{id}/koneksi', [DeviceViewController::class, 'kirimKoneksi'])
    ->middleware('auth.admin')
    ->name('device.koneksi');

Route::post('/device/{id}/listdir', [DeviceViewController::class, 'listDir'])
    ->middleware('auth.admin')->name('device.listdir');
Route::post('/device/{id}/uploadfile', [DeviceViewController::class, 'uploadFileSd'])
    ->middleware('auth.admin')->name('device.uploadfile');
Route::get('/api-internal/device-dirlist/{id}', [DeviceViewController::class, 'getDirList'])
    ->middleware('auth.admin')->name('device.dirlist');

Route::post('/device/{id}/label', [DeviceViewController::class, 'updateLabel'])
    ->middleware('auth.admin')
    ->name('device.label');

// ── Kaldik ──
Route::get('/kaldik', [App\Http\Controllers\KaldikController::class, 'index'])
    ->middleware('auth.admin')->name('kaldik.index');
Route::post('/kaldik', [App\Http\Controllers\KaldikController::class, 'store'])
    ->middleware('auth.admin')->name('kaldik.store');
Route::put('/kaldik/{id}', [App\Http\Controllers\KaldikController::class, 'update'])
    ->middleware('auth.admin')->name('kaldik.update');
Route::delete('/kaldik/{id}', [App\Http\Controllers\KaldikController::class, 'destroy'])
    ->middleware('auth.admin')->name('kaldik.destroy');
Route::get('/kaldik/api/events', [App\Http\Controllers\KaldikController::class, 'apiEvents'])
    ->middleware('auth.admin')->name('kaldik.api.events');
Route::get('/kaldik/template', [App\Http\Controllers\KaldikController::class, 'downloadTemplate'])
    ->middleware('auth.admin')->name('kaldik.template');
Route::post('/kaldik/upload', [App\Http\Controllers\KaldikController::class, 'upload'])
    ->middleware('auth.admin')->name('kaldik.upload');

Route::get('/presensi/siswa/cari', [PresensiViewController::class, 'cariSiswa'])
    ->name('presensi.siswa.cari');

/*
|--------------------------------------------------------------------------
| PASSWORD RESET
|--------------------------------------------------------------------------
*/
Route::get('/forgot-password', [App\Http\Controllers\PasswordResetController::class, 'showForgot'])->name('password.forgot');
Route::post('/forgot-password', [App\Http\Controllers\PasswordResetController::class, 'requestReset'])->name('password.request');
Route::get('/reset-password/{token}', [App\Http\Controllers\PasswordResetController::class, 'showReset'])->name('password.reset.form');
Route::post('/reset-password/{token}', [App\Http\Controllers\PasswordResetController::class, 'doReset'])->name('password.reset.do');
Route::get('/reset-password/{token}/otp', [App\Http\Controllers\PasswordResetController::class, 'showOtp'])->name('password.otp');
Route::post('/reset-password/{token}/otp', [App\Http\Controllers\PasswordResetController::class, 'verifyOtp'])->name('password.otp.verify');
Route::post('/akun/{id}/reset-link', [App\Http\Controllers\PasswordResetController::class, 'kirimDariAkun'])->middleware('auth.admin')->name('akun.reset.link');

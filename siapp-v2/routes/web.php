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

/*
|--------------------------------------------------------------------------
| PUBLIC API / DEVICE
|--------------------------------------------------------------------------
*/
Route::get('/tag', [SiswaViewController::class, 'tagKartu']);

/*
|--------------------------------------------------------------------------
| INTERNAL API (AUTH)
|--------------------------------------------------------------------------
*/
Route::get('/api-internal/device-online', function () {
    return response()->json([
        'online' => DB::table('devices')->where('online', 1)->count()
    ]);
})->middleware('auth.admin');
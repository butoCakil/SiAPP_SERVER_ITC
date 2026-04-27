<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\DeviceViewController;
use App\Http\Controllers\PresensiViewController;
use App\Http\Controllers\SiswaViewController;
use Illuminate\Support\Facades\Route;

// ── Auth ──
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ── Protected Routes ──
Route::middleware(['auth.admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Setting
    Route::get('/setting', [SettingController::class, 'index'])->name('setting');
    Route::post('/setting', [SettingController::class, 'update'])->name('setting.update');

    // Device
    Route::get('/device', [DeviceViewController::class, 'index'])->name('device');

    // Presensi
    Route::get('/presensi', [PresensiViewController::class, 'index'])->name('presensi');
    Route::get('/presensi/event', [PresensiViewController::class, 'event'])->name('presensi.event');

    // Siswa
    Route::get('/siswa', [SiswaViewController::class, 'index'])->name('siswa');
    Route::post('/siswa/kartu', [SiswaViewController::class, 'updateKartu'])->name('siswa.kartu');
    Route::get('/siswa/tmprfid', [SiswaViewController::class, 'tmprfid'])->name('siswa.tmprfid');

    // Device AJAX
    Route::get("/device/cards", [DeviceViewController::class, "cards"])->name("device.cards");
    Route::delete("/device/{id}", [DeviceViewController::class, "destroy"])->name("device.destroy");

});
// ESP Admin - tanpa auth
Route::get('/tag', [App\Http\Controllers\SiswaViewController::class, 'tagKartu']);

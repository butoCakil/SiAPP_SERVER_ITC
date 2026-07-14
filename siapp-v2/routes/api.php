<?php

use App\Http\Controllers\Api\PresensiController;
use App\Http\Controllers\Api\SimController;
use Illuminate\Support\Facades\Route;

// ── Lapis 1: Device → Server ──
// Diakses dari MQTT subscriber (internal), dilindungi device_token
Route::middleware(['device.key', 'log.req'])->group(function () {
    Route::post('/tag', [PresensiController::class, 'tag']);
});

// ── Lapis 2: Server → TIM IT (SIM) ──
// Diakses TIM IT, dilindungi sim_token
Route::middleware(['sim.token'])->prefix('sim')->group(function () {
    Route::get('/presensi',       [SimController::class, 'presensi']);
    Route::get('/presensi/range', [SimController::class, 'presensiRange']);
    Route::get('/siswa',          [SimController::class, 'siswa']);
});

use App\Http\Controllers\Api\DeviceController;

// ── Device Management ──
Route::middleware(["device.key"])->group(function () {
    Route::post("/device/perintah", [DeviceController::class, "kirimPerintah"]);
});

// ── REST API untuk TIM IT (pull data) ──
Route::middleware('sim.token')->prefix('sim')->group(function () {
    Route::get('/presensi',   [App\Http\Controllers\Api\SimController::class, 'presensi']);
    Route::get('/sholat',     [App\Http\Controllers\Api\SimController::class, 'sholat']);
    Route::get('/izin-mens',  [App\Http\Controllers\Api\SimController::class, 'izinMens']);
    Route::get('/ijin',       [App\Http\Controllers\Api\SimController::class, 'ijin']);
});

// DB API (device sync / dummy data testing hardware baru)
Route::middleware(['db.token'])->prefix('db')->group(function () {
    Route::get('/fake',     [\App\Http\Controllers\DbController::class, 'fake']);
    Route::get('/fake-mid', [\App\Http\Controllers\DbController::class, 'fakeMid']);
    Route::get('/query',    [\App\Http\Controllers\DbController::class, 'query']);
});

Route::get('/test-open', function () {
    return response()->json(['ok' => true]);
});

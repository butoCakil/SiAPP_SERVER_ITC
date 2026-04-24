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

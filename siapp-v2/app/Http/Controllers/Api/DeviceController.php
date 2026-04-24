<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DeviceService;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function __construct(private DeviceService $service) {}

    public function kirimPerintah(Request $request)
    {
        $deviceId = $request->input('device_id');

        if (!$deviceId || !preg_match('/^[A-Za-z0-9_\-]+$/', $deviceId)) {
            return response()->json(['status' => 'error', 'message' => 'device_id tidak valid'], 400);
        }

        $reboot       = (bool) $request->input('reboot', false);
        $sync         = (bool) $request->input('sync', false);
        $upload       = (bool) $request->input('upload', false);
        $setSetting   = (bool) $request->input('setSetting', false);
        $toggleSerial = $request->input('toggleSerial') !== null
            ? (int) $request->input('toggleSerial')
            : null;

        $perintahAktif  = array_filter([$reboot, $sync, $upload, $setSetting, $toggleSerial]);
        $jumlahPerintah = count($perintahAktif);

        if ($jumlahPerintah === 0) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada perintah'], 400);
        }

        if ($jumlahPerintah > 1) {
            return response()->json(['status' => 'error', 'message' => 'Terlalu banyak perintah'], 400);
        }

        if ($setSetting) {
            $result = $this->service->kirimSetting($deviceId);
            return response()->json($result);
        }

        $command = array_filter([
            'reboot'       => $reboot       ?: null,
            'sync'         => $sync         ?: null,
            'upload'       => $upload       ?: null,
            'toggleSerial' => $toggleSerial,
        ]);

        $result = $this->service->kirimCommand($deviceId, $command);
        return response()->json($result);
    }
}
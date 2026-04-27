<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DeviceViewController extends Controller
{
    public function index()
    {
        [$devices, $regDevices, $onlineCount, $offlineCount] = $this->getData();
        return view('device.index', compact('devices', 'regDevices', 'onlineCount', 'offlineCount'));
    }

    public function cards()
    {
        [$devices, $regDevices] = $this->getData();
        return view('device._cards', compact('devices', 'regDevices'));
    }

    public function destroy(Request $request, string $id)
    {
        DB::table('devices')->where('device_id', $id)->delete();
        return response()->json(['status' => 'ok']);
    }

    private function getData(): array
    {
        $devices = DB::table('devices')
            ->orderByRaw('online DESC, last_seen DESC')
            ->get();
        $regDevices   = DB::table('reg_device')->get()->keyBy('no_device');
        $onlineCount  = $devices->where('online', 1)->count();
        $offlineCount = $devices->where('online', 0)->count();
        return [$devices, $regDevices, $onlineCount, $offlineCount];
    }
}
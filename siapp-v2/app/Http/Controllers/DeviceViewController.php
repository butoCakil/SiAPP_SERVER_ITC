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

    // ── Registrasi Device ──
    public function registrasi()
    {
        $regDevices = DB::table('reg_device')->orderBy('kode')->orderBy('no_device')->get();
        $kodeList   = ['GATE', 'MASJID', 'EVENT', 'GATETL'];
        return view('device.registrasi', compact('regDevices', 'kodeList'));
    }

    public function storeReg(Request $request)
    {
        $request->validate([
            'chip_id'    => 'required',
            'no_device'  => 'required|unique:reg_device,no_device',
            'kode'       => 'required',
            'info_device'=> 'required',
            'status'     => 'required',
        ]);

        DB::table('reg_device')->insert([
            'chip_id'    => trim($request->chip_id),
            'no_device'  => strtoupper(trim($request->no_device)),
            'kode'       => strtoupper(trim($request->kode)),
            'info_device'=> $request->info_device,
            'status'     => $request->status,
        ]);

        return redirect()->route('device.registrasi')
            ->with('success', 'Device berhasil didaftarkan.');
    }

    public function updateReg(Request $request, int $id)
    {
        DB::table('reg_device')->where('id', $id)->update([
            'chip_id'    => trim($request->chip_id),
            'kode'       => strtoupper(trim($request->kode)),
            'info_device'=> $request->info_device,
            'status'     => $request->status,
        ]);

        return redirect()->route('device.registrasi')
            ->with('success', 'Data device berhasil diupdate.');
    }

    public function destroyReg(int $id)
    {
        DB::table('reg_device')->where('id', $id)->delete();
        return redirect()->route('device.registrasi')
            ->with('success', 'Device berhasil dihapus dari registrasi.');
    }

    private function getData(): array
    {
        $devices      = DB::table('devices')->orderByRaw('online DESC, device_id ASC')->get();
        $regDevices   = DB::table('reg_device')->get()->keyBy('no_device');
        $onlineCount  = $devices->where('online', 1)->count();
        $offlineCount = $devices->where('online', 0)->count();
        return [$devices, $regDevices, $onlineCount, $offlineCount];
    }
}
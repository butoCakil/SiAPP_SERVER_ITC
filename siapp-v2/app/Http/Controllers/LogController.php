<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $tab      = $request->input('tab', 'tempreq');
        $perPage  = 50;

        // ── Tab tempreq ──
        $filterTanggal = $request->input('tanggal', date('Y-m-d'));
        $filterIp      = $request->input('ip', '');
        $filterInfo    = $request->input('info', '');
        $filterSearch  = $request->input('q', '');

        $queryTempreq = DB::table('tempreq')
            ->when($filterTanggal, fn($q) => $q->whereDate('timestamp', $filterTanggal))
            ->when($filterIp,      fn($q) => $q->where('ip', 'like', "%{$filterIp}%"))
            ->when($filterInfo,    fn($q) => $q->where('info', $filterInfo))
            ->when($filterSearch,  fn($q) => $q->where('detail', 'like', "%{$filterSearch}%"))
            ->orderBy('timestamp', 'desc');

        $tempreqLogs  = $queryTempreq->paginate($perPage)->withQueryString();
        $tempreqTotal = DB::table('tempreq')->count();
        $infoList     = DB::table('tempreq')->distinct()->orderBy('info')->pluck('info');

        // ── Tab device_logs ──
        $filterDevice  = $request->input('device', '');
        $filterTanggal2= $request->input('tanggal2', date('Y-m-d'));

        $queryDevice = DB::table('device_logs')
            ->when($filterDevice,   fn($q) => $q->where('device_id', $filterDevice))
            ->when($filterTanggal2, fn($q) => $q->whereDate('received_at', $filterTanggal2))
            ->orderBy('received_at', 'desc');

        $deviceLogs   = $queryDevice->paginate($perPage)->withQueryString();
        $deviceTotal  = DB::table('device_logs')->count();
        $deviceList   = DB::table('device_logs')->distinct()->orderBy('device_id')->pluck('device_id');

        return view('log.index', compact(
            'tab',
            'tempreqLogs', 'tempreqTotal', 'infoList',
            'filterTanggal', 'filterIp', 'filterInfo', 'filterSearch',
            'deviceLogs', 'deviceTotal', 'deviceList',
            'filterDevice', 'filterTanggal2'
        ));
    }

    public function clearTempreq(Request $request)
    {
        $tanggal  = $request->input('tanggal');
        $all      = $request->input('all');
        $keepDays = $request->input('keep_days'); // 7 atau 30

        if ($all) {
            $deleted = DB::table('tempreq')->delete();
            return back()->with('success', "Semua request log berhasil dihapus ({$deleted} records).");
        }

        if ($request->input('before_today')) {
            $deleted = DB::table('tempreq')->whereDate('timestamp', '<', date('Y-m-d'))->delete();
            return back()->with('success', "Request log sebelum hari ini berhasil dihapus ({$deleted} records).");
        }

        if ($keepDays) {
            $batas   = now()->subDays((int)$keepDays)->toDateString();
            $deleted = DB::table('tempreq')->whereDate('timestamp', '<', $batas)->delete();
            return back()->with('success', "Request log lebih dari {$keepDays} hari berhasil dihapus ({$deleted} records).");
        }

        if ($tanggal) {
            $deleted = DB::table('tempreq')->whereDate('timestamp', $tanggal)->delete();
            return back()->with('success', "Request log tanggal {$tanggal} berhasil dihapus ({$deleted} records).");
        }

        return back()->with('error', 'Pilih opsi hapus.');
    }

    public function clearDevice(Request $request)
    {
        $device   = $request->input('device');
        $tanggal  = $request->input('tanggal');
        $all      = $request->input('all');
        $keepDays = $request->input('keep_days');

        if ($all) {
            $deleted = DB::table('device_logs')->delete();
            return back()->with('success', "Semua device log berhasil dihapus ({$deleted} records).");
        }

        if ($request->input('before_today')) {
            $deleted = DB::table('device_logs')->whereDate('received_at', '<', date('Y-m-d'))->delete();
            return back()->with('success', "Device log sebelum hari ini berhasil dihapus ({$deleted} records).");
        }

        if ($keepDays) {
            $batas   = now()->subDays((int)$keepDays)->toDateString();
            $deleted = DB::table('device_logs')->whereDate('received_at', '<', $batas)->delete();
            return back()->with('success', "Device log lebih dari {$keepDays} hari berhasil dihapus ({$deleted} records).");
        }

        $query = DB::table('device_logs');
        if ($device)  $query->where('device_id', $device);
        if ($tanggal) $query->whereDate('received_at', $tanggal);

        $deleted = $query->delete();
        return back()->with('success', "Device log berhasil dihapus ({$deleted} records).");
    }
}
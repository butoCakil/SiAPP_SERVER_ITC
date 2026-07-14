<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PresensiViewController extends Controller
{
    // ══════════════════════════════════════════
    // PRESENSI HARIAN
    // ══════════════════════════════════════════

    public function index(Request $request)
    {
        $tanggal   = $request->input('tanggal', date('Y-m-d'));
        $kelas     = $request->input('kelas', '');
        $filterKet = $request->input('ket', '');

        $setting      = DB::table('statusnya')->first();
        $tingkatAktif = json_decode($setting->tingkat_aktif ?? '["X","XI","XII"]', true);

        $query = DB::table('datapresensi as dp')
            ->leftJoin('datasiswa as ds', 'ds.nokartu', '=', 'dp.nokartu')
            ->where('dp.tanggal', $tanggal)
            ->whereIn('ds.tingkat', $tingkatAktif)
            ->orderBy('dp.waktumasuk')
            ->select('dp.*');

        if ($kelas) $query->where('dp.info', $kelas);

        if ($filterKet === 'terlambat')       $query->whereIn('dp.ketmasuk', ['T', 'TL', 'TLT']);
        elseif ($filterKet === 'tepat')       $query->whereIn('dp.ketmasuk', ['M', 'TW']);
        elseif ($filterKet === 'pulang_awal') $query->where('dp.ketpulang', 'PA');

        $presensi    = $query->get();
        $total       = $presensi->count();
        $tepat       = $presensi->where('ketmasuk', 'TW')->count();
        $telat       = $presensi->whereIn('ketmasuk', ['TL', 'TLT', 'T'])->count();
        $sudahPulang = $presensi->filter(fn($p) => $p->waktupulang && $p->waktupulang !== '00:00:00')->count();

        $kelasList = DB::table('datapresensi')
            ->where('tanggal', $tanggal)->whereNotNull('info')
            ->distinct()->orderBy('info')->pluck('info');

        return view('presensi.index', compact(
            'presensi',
            'tanggal',
            'total',
            'tepat',
            'telat',
            'sudahPulang',
            'kelasList',
            'kelas',
            'filterKet'
        ));
    }

    public function create()
    {
        $siswaList = DB::table('datasiswa')->where('status', 'aktif')->orderBy('kelas')->orderBy('nama')->get();
        return view('presensi.create', compact('siswaList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nokartu'    => 'required',
            'nomorinduk' => 'required',
            'nama'       => 'required',
            'tanggal'    => 'required|date',
            'waktumasuk' => 'required',
        ]);

        // Cek duplikat
        $ada = DB::table('datapresensi')
            ->where('nokartu', $request->nokartu)
            ->where('tanggal', $request->tanggal)
            ->exists();

        if ($ada) {
            return back()->with('error', 'Data presensi sudah ada untuk siswa ini pada tanggal tersebut.')->withInput();
        }

        DB::table('datapresensi')->insert([
            'nokartu'    => strtoupper($request->nokartu),
            'nomorinduk' => $request->nomorinduk,
            'nama'       => $request->nama,
            'info'       => $request->info ?? '',
            'tanggal'    => $request->tanggal,
            'waktumasuk' => $request->waktumasuk,
            'ketmasuk'   => $request->ketmasuk ?? 'TW',
            'waktupulang' => $request->waktupulang ?? null,
            'ketpulang'  => $request->ketpulang ?? null,
            'keterangan' => $request->keterangan ?? null,
            'infodevice2' => 'Manual',
            'updated_at' => now(),
        ]);

        return redirect()->route('presensi', ['tanggal' => $request->tanggal])
            ->with('success', 'Data presensi berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $presensi = DB::table('datapresensi')->where('id', $id)->first();
        if (!$presensi) abort(404);
        return view('presensi.edit', compact('presensi'));
    }

    public function update(Request $request, int $id)
    {
        DB::table('datapresensi')->where('id', $id)->update([
            'waktumasuk'  => $request->waktumasuk,
            'ketmasuk'    => $request->ketmasuk,
            'waktupulang' => $request->waktupulang ?: null,
            'ketpulang'   => $request->ketpulang ?: null,
            'keterangan'  => $request->keterangan ?: null,
            'updated_at'  => now(),
        ]);

        return redirect()->route('presensi', ['tanggal' => $request->tanggal])
            ->with('success', 'Data presensi berhasil diupdate.');
    }

    public function destroy(int $id)
    {
        $p = DB::table('datapresensi')->where('id', $id)->first();
        DB::table('datapresensi')->where('id', $id)->delete();
        return redirect()->route('presensi', ['tanggal' => $p->tanggal ?? date('Y-m-d')])
            ->with('success', 'Data presensi berhasil dihapus.');
    }

    // ══════════════════════════════════════════
    // REKAP BULANAN — LIST SEMUA SISWA
    // ══════════════════════════════════════════

    public function rekap(Request $request)
    {
        $bulan       = $request->input('bulan', date('Y-m'));
        $filterKelas = $request->input('kelas', '');

        if ($bulan > date('Y-m')) $bulan = date('Y-m');

        $tahun = date('Y', strtotime($bulan . '-01'));
        $bln   = date('m', strtotime($bulan . '-01'));

        $bulanSebelum = date('Y-m', strtotime($bulan . '-01 -1 month'));
        $bulanBerikut = date('Y-m', strtotime($bulan . '-01 +1 month'));
        $bulanBerikut = $bulanBerikut > date('Y-m') ? null : $bulanBerikut;

        $queryS = DB::table('datasiswa')->where('status', 'aktif')->orderBy('kelas')->orderBy('nama');
        if ($filterKelas) $queryS->where('kelas', $filterKelas);
        $siswaList = $queryS->get();
        $kelasList = DB::table('datasiswa')->where('status', 'aktif')->distinct()->orderBy('kelas')->pluck('kelas');

        // ── Bulk query ──
        $nokartuList = $siswaList->pluck('nokartu')->toArray();
        $nisList     = $siswaList->pluck('nis')->toArray();

        $presensiRaw = DB::table('datapresensi')
            ->whereIn('nokartu', $nokartuList)
            ->where('tanggal', 'like', $bulan . '%')
            ->select('nokartu', 'ketmasuk', 'waktupulang')
            ->get();

        $izinRaw = DB::table('daftarijin')
            ->whereIn('nis', $nisList)
            ->where('tanggalijin', 'like', $bulan . '%')
            ->whereIn('kode', ['IJIN', 'IZIN'])
            ->select('nis')
            ->get();

        $eventRaw = DB::table('presensiEvent')
            ->whereIn('nis', $nisList)
            ->where('tanggal', 'like', $bulan . '%')
            ->select('nis', 'keterangan', 'ruang')
            ->get();

        // ── Index ──
        $presensiIdx = [];
        foreach ($presensiRaw as $p) {
            if (!isset($presensiIdx[$p->nokartu])) {
                $presensiIdx[$p->nokartu] = ['masuk' => 0, 'terlambat' => 0, 'pulang' => 0];
            }
            $presensiIdx[$p->nokartu]['masuk']++;
            if (in_array($p->ketmasuk, ['T', 'TL', 'TLT'])) {
                $presensiIdx[$p->nokartu]['terlambat']++;
            }
            if ($p->waktupulang && $p->waktupulang !== '00:00:00') {
                $presensiIdx[$p->nokartu]['pulang']++;
            }
        }

        $izinIdx = [];
        foreach ($izinRaw as $i) {
            $izinIdx[$i->nis] = ($izinIdx[$i->nis] ?? 0) + 1;
        }

        $eventIdx = [];
        foreach ($eventRaw as $e) {
            if (!isset($eventIdx[$e->nis])) {
                $eventIdx[$e->nis] = ['dhuha' => 0, 'dhuhur' => 0, 'ashar' => 0, 'izinMens' => 0];
            }
            if ($e->ruang === 'Izin Mens') {
                $eventIdx[$e->nis]['izinMens']++;
            } elseif ($e->keterangan === 'DHUHA') {
                $eventIdx[$e->nis]['dhuha']++;
            } elseif ($e->keterangan === 'DZUHUR') {
                $eventIdx[$e->nis]['dhuhur']++;
            } elseif ($e->keterangan === 'ASHAR') {
                $eventIdx[$e->nis]['ashar']++;
            }
        }

        // ── Susun data ──
        $siswaData = $siswaList->map(function ($s) use ($presensiIdx, $izinIdx, $eventIdx) {
            $p = $presensiIdx[$s->nokartu] ?? [];
            $e = $eventIdx[$s->nis]       ?? [];
            return (object) array_merge((array) $s, [
                'masuk'     => $p['masuk']     ?? 0,
                'terlambat' => $p['terlambat'] ?? 0,
                'pulang'    => $p['pulang']    ?? 0,
                'izin'      => $izinIdx[$s->nis] ?? 0,
                'dhuha'     => $e['dhuha']     ?? 0,
                'dhuhur'    => $e['dhuhur']    ?? 0,
                'ashar'     => $e['ashar']     ?? 0,
                'izinMens'  => $e['izinMens']  ?? 0,
            ]);
        });

        return view('presensi.rekap', compact(
            'bulan',
            'tahun',
            'bln',
            'siswaData',
            'kelasList',
            'filterKelas',
            'bulanSebelum',
            'bulanBerikut'
        ));
    }

    // ══════════════════════════════════════════
    // REKAP DETAIL — KALENDER PER SISWA
    // ══════════════════════════════════════════

    public function rekapDetail(Request $request, string $nis)
    {
        $bulan = $request->input('bulan', date('Y-m'));
        if ($bulan > date('Y-m')) $bulan = date('Y-m');

        $tahun      = date('Y', strtotime($bulan . '-01'));
        $bln        = date('m', strtotime($bulan . '-01'));
        $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bln, $tahun);

        $setting   = DB::table('statusnya')->first();
        $hariKerja = $setting->info ?? '5';

        $siswa = DB::table('datasiswa')->where('nis', $nis)->first();
        if (!$siswa) abort(404);

        // Presensi bulan ini
        $presensiList = DB::table('datapresensi')
            ->where('nokartu', $siswa->nokartu)
            ->where('tanggal', 'like', $bulan . '%')
            ->get()->keyBy('tanggal');

        // Event sholat bulan ini
        $eventList = DB::table('presensiEvent')
            ->where('nis', $nis)
            ->where('tanggal', 'like', $bulan . '%')
            ->get()->groupBy('tanggal');

        // Izin keluar bulan ini
        $izinList = DB::table('daftarijin')
            ->where('nis', $nis)
            ->where('tanggalijin', 'like', $bulan . '%')
            ->get()->groupBy('tanggalijin');

        // Kaldik libur bulan ini
        $kaldikLibur = DB::table('kaldik')
            ->where('tanggal', 'like', $bulan . '%')
            ->whereIn('tipe', ['libur_nasional', 'cuti_bersama', 'libur_semester', 'daring', 'force_majeure'])
            ->pluck('judul', 'tanggal');

        $kalender = [];
        $summary  = [
            'masuk' => 0,
            'terlambat' => 0,
            'pulang' => 0,
            'izin' => 0,
            'dhuha' => 0,
            'dhuhur' => 0,
            'ashar' => 0,
            'izin_mens' => 0,
            'libur' => 0,
            'tanpa_ket' => 0,
        ];

        for ($hari = 1; $hari <= $jumlahHari; $hari++) {
            $tgl     = $tahun . '-' . sprintf('%02d', $bln) . '-' . sprintf('%02d', $hari);
            $dayName = date('l', strtotime($tgl));

            $isLibur = ($hariKerja == '5')
                ? in_array($dayName, ['Saturday', 'Sunday'])
                : $dayName === 'Sunday';
            $isLibur = $isLibur || isset($kaldikLibur[$tgl]);

            $presensi = $presensiList[$tgl] ?? null;
            $events   = $eventList[$tgl] ?? collect();
            $izins    = $izinList[$tgl] ?? collect();

            $dhuha    = $events->firstWhere('keterangan', 'DHUHA');
            $dhuhur   = $events->firstWhere('keterangan', 'DZUHUR');
            $ashar    = $events->firstWhere('keterangan', 'ASHAR');
            $izinMens = $events->first(fn($e) => $e->ruang === 'Izin Mens');

            if ($isLibur) {
                $tipe = 'libur';
                $summary['libur']++;
            } elseif ($presensi) {
                if (in_array($presensi->ketmasuk, ['T', 'TL', 'TLT'])) {
                    $tipe = 'terlambat';
                    $summary['terlambat']++;
                } else {
                    $tipe = 'hadir';
                    $summary['masuk']++;
                }
                if ($presensi->waktupulang && $presensi->waktupulang !== '00:00:00') {
                    $summary['pulang']++;
                }
            } else {
                $tipe = 'tanpa_ket';
                $summary['tanpa_ket']++;
            }

            if ($dhuha && $dhuha->ruang !== 'Izin Mens') $summary['dhuha']++;
            if ($dhuhur && $dhuhur->ruang !== 'Izin Mens') $summary['dhuhur']++;
            if ($ashar  && $ashar->ruang  !== 'Izin Mens') $summary['ashar']++;
            if ($izinMens) $summary['izin_mens']++;
            $summary['izin'] += $izins->count();

            $kalender[] = [
                'tgl'      => $tgl,
                'hari'     => $hari,
                'day'      => $dayName,
                'is_libur'     => $isLibur,
                'kaldik_judul' => $kaldikLibur[$tgl] ?? null,
                'tipe'         => $tipe,
                'presensi' => $presensi,
                'dhuha'    => $dhuha,
                'dhuhur'   => $dhuhur,
                'ashar'    => $ashar,
                'izin_mens' => $izinMens,
                'izins'    => $izins,
            ];
        }

        $bulanSebelum = date('Y-m', strtotime($bulan . '-01 -1 month'));
        $bulanBerikut = date('Y-m', strtotime($bulan . '-01 +1 month'));
        $bulanBerikut = $bulanBerikut > date('Y-m') ? null : $bulanBerikut;

        return view('presensi.rekap-detail', compact(
            'bulan',
            'tahun',
            'bln',
            'jumlahHari',
            'siswa',
            'nis',
            'kalender',
            'summary',
            'bulanSebelum',
            'bulanBerikut',
            'kaldikLibur'
        ));
    }

    // ══════════════════════════════════════════
    // REKAP SEMESTER — TABEL SEMUA SISWA
    // ══════════════════════════════════════════

    public function rekapSemester(Request $request)
    {
        $tahun       = $request->input('tahun', date('Y'));
        $semester    = $request->input('semester', date('m') >= 7 ? 'gasal' : 'genap');
        $filterKelas = $request->input('kelas', '');

        if ($semester === 'gasal') {
            $bulanList = ['07', '08', '09', '10', '11', '12'];
        } else {
            $bulanList = ['01', '02', '03', '04', '05', '06'];
        }

        $periodeList = array_map(fn($b) => $tahun . '-' . $b, $bulanList);
        $tglMulai    = $periodeList[0] . '-01';
        $tglAkhir    = $periodeList[5] . '-31';

        $queryS = DB::table('datasiswa')->where('status', 'aktif')->orderBy('kelas')->orderBy('nama');
        if ($filterKelas) $queryS->where('kelas', $filterKelas);
        $siswaList = $queryS->get();
        $kelasList = DB::table('datasiswa')->where('status', 'aktif')->distinct()->orderBy('kelas')->pluck('kelas');

        // ── Bulk query semua data semester sekaligus ──
        $nokartuList = $siswaList->pluck('nokartu')->toArray();
        $nisList     = $siswaList->pluck('nis')->toArray();

        // Presensi — group by nokartu + bulan
        $presensiRaw = DB::table('datapresensi')
            ->whereIn('nokartu', $nokartuList)
            ->whereBetween('tanggal', [$tglMulai, $tglAkhir])
            ->select('nokartu', 'tanggal', 'ketmasuk', 'waktupulang')
            ->get();

        // Izin — group by nis + bulan
        $izinRaw = DB::table('daftarijin')
            ->whereIn('nis', $nisList)
            ->whereBetween('tanggalijin', [$tglMulai, $tglAkhir])
            ->whereIn('kode', ['IJIN', 'IZIN'])
            ->select('nis', 'tanggalijin')
            ->get();

        // Event sholat — group by nis + bulan
        $eventRaw = DB::table('presensiEvent')
            ->whereIn('nis', $nisList)
            ->whereBetween('tanggal', [$tglMulai, $tglAkhir])
            ->select('nis', 'tanggal', 'keterangan', 'ruang')
            ->get();

        // ── Index data ke array [nokartu/nis][bulan] ──
        $presensiIdx = [];
        foreach ($presensiRaw as $p) {
            $bln = substr($p->tanggal, 0, 7);
            if (!isset($presensiIdx[$p->nokartu][$bln])) {
                $presensiIdx[$p->nokartu][$bln] = ['masuk' => 0, 'terlambat' => 0, 'pulang' => 0];
            }
            $presensiIdx[$p->nokartu][$bln]['masuk']++;
            if (in_array($p->ketmasuk, ['T', 'TL', 'TLT'])) {
                $presensiIdx[$p->nokartu][$bln]['terlambat']++;
            }
            if ($p->waktupulang && $p->waktupulang !== '00:00:00') {
                $presensiIdx[$p->nokartu][$bln]['pulang']++;
            }
        }

        $izinIdx = [];
        foreach ($izinRaw as $i) {
            $bln = substr($i->tanggalijin, 0, 7);
            $izinIdx[$i->nis][$bln] = ($izinIdx[$i->nis][$bln] ?? 0) + 1;
        }

        $eventIdx = [];
        foreach ($eventRaw as $e) {
            $bln = substr($e->tanggal, 0, 7);
            if (!isset($eventIdx[$e->nis][$bln])) {
                $eventIdx[$e->nis][$bln] = ['dhuha' => 0, 'dhuhur' => 0, 'ashar' => 0, 'izinMens' => 0];
            }
            if ($e->ruang === 'Izin Mens') {
                $eventIdx[$e->nis][$bln]['izinMens']++;
            } elseif ($e->keterangan === 'DHUHA') {
                $eventIdx[$e->nis][$bln]['dhuha']++;
            } elseif ($e->keterangan === 'DZUHUR') {
                $eventIdx[$e->nis][$bln]['dhuhur']++;
            } elseif ($e->keterangan === 'ASHAR') {
                $eventIdx[$e->nis][$bln]['ashar']++;
            }
        }

        // ── Susun data per siswa ──
        $siswaData = $siswaList->map(function ($s) use ($periodeList, $presensiIdx, $izinIdx, $eventIdx) {
            $bulanData = [];
            foreach ($periodeList as $bulan) {
                $p = $presensiIdx[$s->nokartu][$bulan] ?? [];
                $i = $eventIdx[$s->nis][$bulan] ?? [];
                $bulanData[$bulan] = [
                    'masuk'     => $p['masuk']     ?? 0,
                    'terlambat' => $p['terlambat'] ?? 0,
                    'pulang'    => $p['pulang']    ?? 0,
                    'izin'      => $izinIdx[$s->nis][$bulan] ?? 0,
                    'dhuha'     => $i['dhuha']     ?? 0,
                    'dhuhur'    => $i['dhuhur']    ?? 0,
                    'ashar'     => $i['ashar']     ?? 0,
                    'izinMens'  => $i['izinMens']  ?? 0,
                ];
            }
            return (object) array_merge((array) $s, ['bulanData' => $bulanData]);
        });

        return view('presensi.rekap-semester', compact(
            'tahun',
            'semester',
            'filterKelas',
            'periodeList',
            'bulanList',
            'siswaData',
            'kelasList'
        ));
    }

    // ══════════════════════════════════════════
    // REKAP SEMESTER DETAIL — KALENDER PER SISWA
    // ══════════════════════════════════════════

    public function rekapSemesterDetail(Request $request, string $nis)
    {
        $tahun    = $request->input('tahun', date('Y'));
        $semester = $request->input('semester', date('m') >= 7 ? 'gasal' : 'genap');

        if ($semester === 'gasal') {
            $bulanList = ['07', '08', '09', '10', '11', '12'];
        } else {
            $bulanList = ['01', '02', '03', '04', '05', '06'];
        }

        $periodeList = array_map(fn($b) => $tahun . '-' . $b, $bulanList);

        $setting   = DB::table('statusnya')->first();
        $hariKerja = $setting->info ?? '5';

        $siswa = DB::table('datasiswa')->where('nis', $nis)->first();
        if (!$siswa) abort(404);

        $summaryTotal = [
            'masuk' => 0,
            'terlambat' => 0,
            'pulang' => 0,
            'izin' => 0,
            'dhuha' => 0,
            'dhuhur' => 0,
            'ashar' => 0,
            'izin_mens' => 0,
            'libur' => 0,
            'tanpa_ket' => 0,
        ];

        $kalenderPerBulan = [];

        foreach ($periodeList as $bulan) {
            $bln        = date('m', strtotime($bulan . '-01'));
            $thn        = date('Y', strtotime($bulan . '-01'));
            $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bln, $thn);

            $presensiList = DB::table('datapresensi')
                ->where('nokartu', $siswa->nokartu)
                ->where('tanggal', 'like', $bulan . '%')
                ->get()->keyBy('tanggal');

            $eventList = DB::table('presensiEvent')
                ->where('nis', $nis)
                ->where('tanggal', 'like', $bulan . '%')
                ->get()->groupBy('tanggal');

            $izinList = DB::table('daftarijin')
                ->where('nis', $nis)
                ->where('tanggalijin', 'like', $bulan . '%')
                ->get()->groupBy('tanggalijin');

            $kaldikLibur = DB::table('kaldik')
                ->where('tanggal', 'like', $bulan . '%')
                ->whereIn('tipe', ['libur_nasional', 'cuti_bersama', 'libur_semester', 'daring', 'force_majeure'])
                ->pluck('judul', 'tanggal');

            $kalender = [];
            for ($hari = 1; $hari <= $jumlahHari; $hari++) {
                $tgl     = $thn . '-' . sprintf('%02d', $bln) . '-' . sprintf('%02d', $hari);
                $dayName = date('l', strtotime($tgl));

                $isLibur = ($hariKerja == '5')
                    ? in_array($dayName, ['Saturday', 'Sunday'])
                    : $dayName === 'Sunday';
                $isLibur = $isLibur || isset($kaldikLibur[$tgl]);

                $presensi = $presensiList[$tgl] ?? null;
                $events   = $eventList[$tgl] ?? collect();
                $izins    = $izinList[$tgl] ?? collect();

                $dhuha    = $events->firstWhere('keterangan', 'DHUHA');
                $dhuhur   = $events->firstWhere('keterangan', 'DZUHUR');
                $ashar    = $events->firstWhere('keterangan', 'ASHAR');
                $izinMens = $events->first(fn($e) => $e->ruang === 'Izin Mens');

                if ($isLibur) {
                    $tipe = 'libur';
                    $summaryTotal['libur']++;
                } elseif ($presensi) {
                    if (in_array($presensi->ketmasuk, ['T', 'TL', 'TLT'])) {
                        $tipe = 'terlambat';
                        $summaryTotal['terlambat']++;
                    } else {
                        $tipe = 'hadir';
                        $summaryTotal['masuk']++;
                    }
                    if ($presensi->waktupulang && $presensi->waktupulang !== '00:00:00') {
                        $summaryTotal['pulang']++;
                    }
                } else {
                    $tipe = 'tanpa_ket';
                    $summaryTotal['tanpa_ket']++;
                }

                if ($dhuha && $dhuha->ruang !== 'Izin Mens') $summaryTotal['dhuha']++;
                if ($dhuhur && $dhuhur->ruang !== 'Izin Mens') $summaryTotal['dhuhur']++;
                if ($ashar  && $ashar->ruang  !== 'Izin Mens') $summaryTotal['ashar']++;
                if ($izinMens) $summaryTotal['izin_mens']++;
                $summaryTotal['izin'] += $izins->count();

                $kalender[] = [
                    'tgl'         => $tgl,
                    'hari'        => $hari,
                    'day'         => $dayName,
                    'is_libur'    => $isLibur,
                    'kaldik_judul' => $kaldikLibur[$tgl] ?? null,
                    'tipe'        => $tipe,
                    'presensi' => $presensi,
                    'dhuha'    => $dhuha,
                    'dhuhur'   => $dhuhur,
                    'ashar'    => $ashar,
                    'izin_mens' => $izinMens,
                    'izins'    => $izins,
                ];
            }

            $kalenderPerBulan[$bulan] = [
                'bulan'      => $bulan,
                'bln'        => $bln,
                'thn'        => $thn,
                'jumlahHari' => $jumlahHari,
                'kalender'   => $kalender,
            ];
        }

        // Navigasi tahun
        $tahunSebelum = $tahun - 1;
        $tahunBerikut = $tahun + 1;

        return view('presensi.rekap-semester-detail', compact(
            'tahun',
            'semester',
            'nis',
            'siswa',
            'periodeList',
            'bulanList',
            'kalenderPerBulan',
            'summaryTotal',
            'tahunSebelum',
            'tahunBerikut'
        ));
    }

    // ══════════════════════════════════════════
    // PEMBIASAAN SHOLAT
    // ══════════════════════════════════════════

    public function event(Request $request)
    {
        $tanggal     = $request->input('tanggal', date('Y-m-d'));
        $filterKelas = $request->input('kelas', '');

        $query = DB::table('presensiEvent as pe')
            ->leftJoin('datasiswa as ds', 'ds.nis', '=', 'pe.nis')
            ->where('pe.tanggal', $tanggal);

        if ($filterKelas) $query->where('ds.kelas', $filterKelas);

        $events = $query->select(
            'pe.id',
            'pe.nis',
            'ds.nama',
            'ds.kelas',
            'pe.keterangan',
            'pe.ruang',
            'pe.mulai',
            'pe.jam',
        )
            ->orderBy('pe.nis')->orderBy('pe.jam')->get();

        $siswaMap = [];
        foreach ($events as $e) {
            $nis = $e->nis;
            if (!isset($siswaMap[$nis])) {
                $siswaMap[$nis] = [
                    'nis'         => $nis,
                    'nama'        => $e->nama ?? '-',
                    'kelas'       => $e->kelas ?? '-',
                    'dhuha'       => null,
                    'dhuha_id'    => null,
                    'dhuha_izin'  => false,
                    'dzuhur'      => null,
                    'dzuhur_id'   => null,
                    'dzuhur_izin' => false,
                    'ashar'       => null,
                    'ashar_id'    => null,
                    'ashar_izin'  => false,
                ];
            }
            if ($e->keterangan === 'DHUHA') {
                $siswaMap[$nis]['dhuha']      = $e->mulai;
                $siswaMap[$nis]['dhuha_id']   = $e->id;
                $siswaMap[$nis]['dhuha_izin'] = $e->ruang === 'Izin Mens';
            } elseif ($e->keterangan === 'DZUHUR') {
                $siswaMap[$nis]['dzuhur']      = $e->mulai;
                $siswaMap[$nis]['dzuhur_id']   = $e->id;
                $siswaMap[$nis]['dzuhur_izin'] = $e->ruang === 'Izin Mens';
            } elseif ($e->keterangan === 'ASHAR') {
                $siswaMap[$nis]['ashar']      = $e->mulai;
                $siswaMap[$nis]['ashar_id']   = $e->id;
                $siswaMap[$nis]['ashar_izin'] = $e->ruang === 'Izin Mens';
            }
        }

        $siswaList          = collect(array_values($siswaMap));
        $totalDhuha         = $siswaList->filter(fn($s) => $s['dhuha'] && !$s['dhuha_izin'])->count();
        $totalDzuhur        = $siswaList->filter(fn($s) => $s['dzuhur'] && !$s['dzuhur_izin'])->count();
        $totalAshar         = $siswaList->filter(fn($s) => $s['ashar'] && !$s['ashar_izin'])->count();
        $totalIzin          = $siswaList->filter(fn($s) => $s['dzuhur_izin'] || $s['ashar_izin'] || $s['dhuha_izin'])->count();
        $totalKeduanya      = $siswaList->filter(fn($s) => ($s['dzuhur'] && !$s['dzuhur_izin']) && ($s['ashar'] && !$s['ashar_izin']))->count();
        $totalTidakKeduanya = $siswaList->filter(function ($s) {
            if ($s['dzuhur_izin'] || $s['ashar_izin'] || $s['dhuha_izin']) return false; // izin mens, dikecualikan
            $dzuhurOk = $s['dzuhur'] && !$s['dzuhur_izin'];
            $asharOk  = $s['ashar']  && !$s['ashar_izin'];
            return !$dzuhurOk || !$asharOk; // belum keduanya murni
        })->count();
        $kelasList          = DB::table('datasiswa')->where('status', 'aktif')->distinct()->orderBy('kelas')->pluck('kelas');

        return view('presensi.event', compact(
            'siswaList',
            'tanggal',
            'filterKelas',
            'kelasList',
            'totalDhuha',
            'totalDzuhur',
            'totalAshar',
            'totalIzin',
            'totalKeduanya',
            'totalTidakKeduanya'
        ));
    }

    public function cariSiswa(Request $request)
    {
        $q = trim($request->input('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $siswa = DB::table('datasiswa')
            ->where(function ($w) use ($q) {
                $w->where('nis', 'like', "%{$q}%")
                    ->orWhere('nama', 'like', "%{$q}%");
            })
            ->orderBy('nama')
            ->limit(15)
            ->get(['nis', 'nama', 'kelas']);

        return response()->json($siswa);
    }

    public function storeEvent(Request $request)
    {
        $request->validate([
            'nis'         => 'required',
            'tanggal'     => 'required|date',
            'keterangan'  => 'required|in:DHUHA,DZUHUR,ASHAR',
            'jam'         => 'required',
        ]);

        $siswa = DB::table('datasiswa')->where('nis', $request->nis)->first();
        if (!$siswa) return back()->with('error', 'Siswa tidak ditemukan.');

        $ada = DB::table('presensiEvent')
            ->where('nis', $request->nis)
            ->where('tanggal', $request->tanggal)
            ->where('keterangan', $request->keterangan)
            ->exists();

        if ($ada) return back()->with('error', 'Data sudah ada untuk sholat ini.');

        DB::table('presensiEvent')->insert([
            'nokartu'    => $siswa->nokartu,
            'nis'        => $request->nis,
            'ruang'      => $request->ruang ?? 'Manual',
            'mulai'      => $request->jam,
            'jam'        => $request->jam,
            'tanggal'    => $request->tanggal,
            'keterangan' => $request->keterangan,
            'timestamp'  => now(),
        ]);

        return redirect()->route('presensi.event', ['tanggal' => $request->tanggal])
            ->with('success', 'Data sholat berhasil ditambahkan.');
    }

    public function updateEvent(Request $request, int $id)
    {
        DB::table('presensiEvent')->where('id', $id)->update([
            'jam'        => $request->jam,
            'mulai'      => $request->jam,
            'keterangan' => $request->keterangan,
            'ruang'      => $request->ruang,
        ]);

        return response()->json(['status' => 'ok']);
    }

    public function destroyEvent(int $id)
    {
        $e = DB::table('presensiEvent')->where('id', $id)->first();
        DB::table('presensiEvent')->where('id', $id)->delete();
        return redirect()->route('presensi.event', ['tanggal' => $e->tanggal ?? date('Y-m-d')])
            ->with('success', 'Data sholat berhasil dihapus.');
    }

    // ══════════════════════════════════════════
    // IZIN KELUAR
    // ══════════════════════════════════════════

    public function ijin(Request $request)
    {
        $tanggal     = $request->input('tanggal', date('Y-m-d'));
        $filterKelas = $request->input('kelas', '');
        $filterStatus = $request->input('status', '');

        $query = DB::table('daftarijin as di')
            ->leftJoin('datasiswa as ds', DB::raw('ds.nis COLLATE utf8_general_ci'), '=', DB::raw('di.nis COLLATE utf8_general_ci'))
            ->where('di.tanggalijin', $tanggal)
            ->select('di.*', 'ds.kelas')
            ->orderBy('di.timestamp', 'desc');

        if ($filterKelas) $query->where('ds.kelas', $filterKelas);
        if ($filterStatus === 'belum') $query->whereNull('di.jam_kembali');
        if ($filterStatus === 'sudah') $query->whereNotNull('di.jam_kembali');

        $ijinList  = $query->get();
        $kelasList = DB::table('datasiswa')->where('status', 'aktif')->distinct()->orderBy('kelas')->pluck('kelas');
        $totalBelumKembali = DB::table('daftarijin')->where('tanggalijin', $tanggal)->whereNull('jam_kembali')->count();
        $totalSudahKembali = DB::table('daftarijin')->where('tanggalijin', $tanggal)->whereNotNull('jam_kembali')->count();

        return view('presensi.ijin', compact(
            'ijinList',
            'tanggal',
            'kelasList',
            'filterKelas',
            'filterStatus',
            'totalBelumKembali',
            'totalSudahKembali'
        ));
    }

    public function storeIjin(Request $request)
    {
        $request->validate([
            'nis'        => 'required',
            'tanggal'    => 'required|date',
            'jam_keluar' => 'required',
        ]);

        $siswa = DB::table('datasiswa')->where('nis', $request->nis)->first();
        if (!$siswa) return back()->with('error', 'Siswa tidak ditemukan.');

        DB::table('daftarijin')->insert([
            'nokartu'    => $siswa->nokartu,
            'nis'        => $request->nis,
            'nama'       => $siswa->nama,
            'info'       => $request->info ?? 'Manual',
            'jam_keluar' => $request->jam_keluar,
            'jam_kembali' => $request->jam_kembali ?: null,
            'tanggalijin' => $request->tanggal,
            'kode'       => 'IJIN',
            'timestamp'  => now(),
        ]);

        return redirect()->route('presensi.ijin', ['tanggal' => $request->tanggal])
            ->with('success', 'Data izin berhasil ditambahkan.');
    }

    public function updateIjin(Request $request, int $id)
    {
        DB::table('daftarijin')->where('id', $id)->update([
            'jam_keluar'  => $request->jam_keluar,
            'jam_kembali' => $request->jam_kembali ?: null,
            'info'        => $request->info,
        ]);

        return redirect()->route('presensi.ijin', ['tanggal' => $request->tanggal])
            ->with('success', 'Data izin berhasil diupdate.');
    }

    public function destroyIjin(int $id)
    {
        $ijin = DB::table('daftarijin')->where('id', $id)->first();
        DB::table('daftarijin')->where('id', $id)->delete();
        return redirect()->route('presensi.ijin', ['tanggal' => $ijin->tanggalijin ?? date('Y-m-d')])
            ->with('success', 'Data izin berhasil dihapus.');
    }
}

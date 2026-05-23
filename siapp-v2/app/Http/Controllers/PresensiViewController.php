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
        $siswaList = DB::table('datasiswa')->orderBy('kelas')->orderBy('nama')->get();
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
        $bulan      = $request->input('bulan', date('Y-m'));
        $filterKelas = $request->input('kelas', '');

        if ($bulan > date('Y-m')) $bulan = date('Y-m');

        $tahun = date('Y', strtotime($bulan . '-01'));
        $bln   = date('m', strtotime($bulan . '-01'));

        // Navigasi bulan
        $bulanSebelum = date('Y-m', strtotime($bulan . '-01 -1 month'));
        $bulanBerikut = date('Y-m', strtotime($bulan . '-01 +1 month'));
        $bulanBerikut = $bulanBerikut > date('Y-m') ? null : $bulanBerikut;

        // List siswa
        $queryS = DB::table('datasiswa')->orderBy('kelas')->orderBy('nama');
        if ($filterKelas) $queryS->where('kelas', $filterKelas);
        $siswaList = $queryS->get();

        $kelasList = DB::table('datasiswa')->distinct()->orderBy('kelas')->pluck('kelas');

        // Hitung summary per siswa untuk bulan ini
        $siswaData = $siswaList->map(function ($s) use ($bulan) {

            $masuk = DB::table('datapresensi')
                ->where('nokartu', $s->nokartu)
                ->where('tanggal', 'like', $bulan . '%')
                ->count();

            $terlambat = DB::table('datapresensi')
                ->where('nokartu', $s->nokartu)
                ->where('tanggal', 'like', $bulan . '%')
                ->whereIn('ketmasuk', ['T', 'TL', 'TLT'])
                ->count();

            $pulang = DB::table('datapresensi')
                ->where('nokartu', $s->nokartu)
                ->where('tanggal', 'like', $bulan . '%')
                ->whereNotNull('waktupulang')
                ->where('waktupulang', '!=', '00:00:00')
                ->count();

            $izin = DB::table('daftarijin')
                ->where('nis', $s->nis)
                ->where('tanggalijin', 'like', $bulan . '%')
                ->whereIn('kode', ['IJIN', 'IZIN'])
                ->count();

            $dhuhur = DB::table('presensiEvent')
                ->where('nis', $s->nis)
                ->where('tanggal', 'like', $bulan . '%')
                ->where('keterangan', 'DZUHUR')
                ->where('ruang', '!=', 'Izin Mens')
                ->count();

            $ashar = DB::table('presensiEvent')
                ->where('nis', $s->nis)
                ->where('tanggal', 'like', $bulan . '%')
                ->where('keterangan', 'ASHAR')
                ->where('ruang', '!=', 'Izin Mens')
                ->count();

            $izinMens = DB::table('presensiEvent')
                ->where('nis', $s->nis)
                ->where('tanggal', 'like', $bulan . '%')
                ->where('ruang', 'Izin Mens')
                ->count();

            return (object) array_merge((array) $s, compact(
                'masuk',
                'terlambat',
                'pulang',
                'izin',
                'dhuhur',
                'ashar',
                'izinMens'
            ));
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

        $kalender = [];
        $summary  = [
            'masuk' => 0,
            'terlambat' => 0,
            'pulang' => 0,
            'izin' => 0,
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

            $presensi = $presensiList[$tgl] ?? null;
            $events   = $eventList[$tgl] ?? collect();
            $izins    = $izinList[$tgl] ?? collect();

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

            if ($dhuhur && $dhuhur->ruang !== 'Izin Mens') $summary['dhuhur']++;
            if ($ashar  && $ashar->ruang  !== 'Izin Mens') $summary['ashar']++;
            if ($izinMens) $summary['izin_mens']++;
            $summary['izin'] += $izins->count();

            $kalender[] = [
                'tgl'      => $tgl,
                'hari'     => $hari,
                'day'      => $dayName,
                'is_libur' => $isLibur,
                'tipe'     => $tipe,
                'presensi' => $presensi,
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
            'bulanBerikut'
        ));
    }

    // ══════════════════════════════════════════
    // REKAP SEMESTER — TABEL SEMUA SISWA
    // ══════════════════════════════════════════

    public function rekapSemester(Request $request)
    {
        $tahun     = $request->input('tahun', date('Y'));
        $semester  = $request->input('semester', date('m') >= 7 ? 'gasal' : 'genap');
        $filterKelas = $request->input('kelas', '');

        // Tentukan range bulan
        if ($semester === 'gasal') {
            $bulanList = ['07', '08', '09', '10', '11', '12'];
            $tahunList = array_fill(0, 6, $tahun);
        } else {
            $bulanList = ['01', '02', '03', '04', '05', '06'];
            $tahunList = array_fill(0, 6, $tahun);
        }

        $periodeList = array_map(fn($b, $t) => $t . '-' . $b, $bulanList, $tahunList);

        $queryS = DB::table('datasiswa')->orderBy('kelas')->orderBy('nama');
        if ($filterKelas) $queryS->where('kelas', $filterKelas);
        $siswaList = $queryS->get();

        $kelasList = DB::table('datasiswa')->distinct()->orderBy('kelas')->pluck('kelas');

        // Hitung summary per siswa per bulan
        $siswaData = $siswaList->map(function ($s) use ($periodeList) {
            $bulanData = [];
            foreach ($periodeList as $bulan) {
                $masuk = DB::table('datapresensi')
                    ->where('nokartu', $s->nokartu)
                    ->where('tanggal', 'like', $bulan . '%')
                    ->count();

                $terlambat = DB::table('datapresensi')
                    ->where('nokartu', $s->nokartu)
                    ->where('tanggal', 'like', $bulan . '%')
                    ->whereIn('ketmasuk', ['T', 'TL', 'TLT'])
                    ->count();

                $pulang = DB::table('datapresensi')
                    ->where('nokartu', $s->nokartu)
                    ->where('tanggal', 'like', $bulan . '%')
                    ->whereNotNull('waktupulang')
                    ->where('waktupulang', '!=', '00:00:00')
                    ->count();

                $izin = DB::table('daftarijin')
                    ->where('nis', $s->nis)
                    ->where('tanggalijin', 'like', $bulan . '%')
                    ->whereIn('kode', ['IJIN', 'IZIN'])
                    ->count();

                $dhuhur = DB::table('presensiEvent')
                    ->where('nis', $s->nis)
                    ->where('tanggal', 'like', $bulan . '%')
                    ->where('keterangan', 'DZUHUR')
                    ->where('ruang', '!=', 'Izin Mens')
                    ->count();

                $ashar = DB::table('presensiEvent')
                    ->where('nis', $s->nis)
                    ->where('tanggal', 'like', $bulan . '%')
                    ->where('keterangan', 'ASHAR')
                    ->where('ruang', '!=', 'Izin Mens')
                    ->count();

                $izinMens = DB::table('presensiEvent')
                    ->where('nis', $s->nis)
                    ->where('tanggal', 'like', $bulan . '%')
                    ->where('ruang', 'Izin Mens')
                    ->count();

                $bulanData[$bulan] = compact(
                    'masuk',
                    'terlambat',
                    'pulang',
                    'izin',
                    'dhuhur',
                    'ashar',
                    'izinMens'
                );
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

            $kalender = [];
            for ($hari = 1; $hari <= $jumlahHari; $hari++) {
                $tgl     = $thn . '-' . sprintf('%02d', $bln) . '-' . sprintf('%02d', $hari);
                $dayName = date('l', strtotime($tgl));

                $isLibur = ($hariKerja == '5')
                    ? in_array($dayName, ['Saturday', 'Sunday'])
                    : $dayName === 'Sunday';

                $presensi = $presensiList[$tgl] ?? null;
                $events   = $eventList[$tgl] ?? collect();
                $izins    = $izinList[$tgl] ?? collect();

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

                if ($dhuhur && $dhuhur->ruang !== 'Izin Mens') $summaryTotal['dhuhur']++;
                if ($ashar  && $ashar->ruang  !== 'Izin Mens') $summaryTotal['ashar']++;
                if ($izinMens) $summaryTotal['izin_mens']++;
                $summaryTotal['izin'] += $izins->count();

                $kalender[] = [
                    'tgl'      => $tgl,
                    'hari'     => $hari,
                    'day'      => $dayName,
                    'is_libur' => $isLibur,
                    'tipe'     => $tipe,
                    'presensi' => $presensi,
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
                    'dzuhur'      => null,
                    'dzuhur_id' => null,
                    'ashar'       => null,
                    'ashar_id'  => null,
                    'dzuhur_izin' => false,
                    'ashar_izin'  => false,
                ];
            }
            if ($e->keterangan === 'DZUHUR') {
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
        $totalDzuhur        = $siswaList->filter(fn($s) => $s['dzuhur'])->count();
        $totalAshar         = $siswaList->filter(fn($s) => $s['ashar'])->count();
        $totalIzin          = $siswaList->filter(fn($s) => $s['dzuhur_izin'] || $s['ashar_izin'])->count();
        $totalKeduanya      = $siswaList->filter(fn($s) => $s['dzuhur'] && $s['ashar'])->count();
        $totalTidakKeduanya = $siswaList->filter(fn($s) => !$s['dzuhur'] || !$s['ashar'])->count();
        $kelasList          = DB::table('datasiswa')->distinct()->orderBy('kelas')->pluck('kelas');

        return view('presensi.event', compact(
            'siswaList',
            'tanggal',
            'filterKelas',
            'kelasList',
            'totalDzuhur',
            'totalAshar',
            'totalIzin',
            'totalKeduanya',
            'totalTidakKeduanya'
        ));
    }

    public function storeEvent(Request $request)
    {
        $request->validate([
            'nis'         => 'required',
            'tanggal'     => 'required|date',
            'keterangan'  => 'required|in:DZUHUR,ASHAR',
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
        $kelasList = DB::table('datasiswa')->distinct()->orderBy('kelas')->pluck('kelas');
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

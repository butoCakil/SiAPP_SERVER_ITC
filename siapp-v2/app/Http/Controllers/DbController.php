<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Faker\Factory as FakerFactory;

class DbController extends Controller
{
    // ── GET /api/db/fake ──
    public function fake()
    {
        $data = $this->buatDummySiswa(30)->map(fn($s) => [
            'nokartu' => $s['nokartu'],
            'nis'     => $s['nis'],
        ]);

        return response()->json([
            'metadata' => [
                'jumlah_data' => $data->count(),
                'timestamp'   => now()->toIso8601String(),
                'versi'       => 'v1.0-dummy',
            ],
            'data' => $data,
        ]);
    }

    public function fakeMid()
    {
        $data = $this->buatDummySiswa(100);

        return response()->json([
            'metadata' => [
                'jumlah_data' => $data->count(),
                'timestamp'   => now()->toIso8601String(),
                'versi'       => 'v1.0-dummy',
            ],
            'data' => $data,
        ]);
    }

    // ── Generator data dummy untuk uji firmware/hardware baru ──
    // Seed tetap (12345) supaya hasilnya konsisten setiap dipanggil,
    // memudahkan pengujian yang butuh data yang bisa direproduksi.
    private function buatDummySiswa(int $jumlah)
    {
        $faker = FakerFactory::create('id_ID');
        $faker->seed(12345);

        $jurusan = ['AT', 'DKV', 'TE'];
        $tingkat = ['X', 'XI', 'XII'];

        return collect(range(1, $jumlah))->map(function ($i) use ($faker, $jurusan, $tingkat) {
            return [
                'nokartu' => $faker->regexify('[0-9A-F]{8}'),
                'nis'     => (string) (90000 + $i),
                'nama'    => strtoupper($faker->name()),
                'kelas'   => $faker->randomElement($tingkat) . ' TEST-' . $faker->randomElement($jurusan) . ' ' . $faker->numberBetween(1, 3),
            ];
        });
    }

    // ── GET /api/db/query?db=datasiswa&akses=mid ──
    public function query(Request $request)
    {
        $db_tbl = $request->input('db');
        $akses  = $request->input('akses');

        if (!$db_tbl) {
            return response()->json(['status' => '404', 'message' => 'Permintaan tidak lengkap'], 404);
        }

        $today = now()->toDateString();

        switch ($db_tbl) {
            case 'datasiswa':
                if ($akses === 'lite')
                    $query = DB::table('datasiswa')->where('status', 'aktif')->select('nokartu', 'nis');
                elseif ($akses === 'mid')
                    $query = DB::table('datasiswa')->where('status', 'aktif')->select('nokartu', 'nis', 'nama', 'kelas');
                else
                    $query = DB::table('datasiswa')->where('status', 'aktif')->select('nis', 'nama', 'kelas', 'poin', 'tingkat', 'email');
                break;

            case 'datagtk':
            case 'dataguru':
                $query = DB::table('dataguru')->select('nip', 'nama', 'nick', 'info', 'jabatan', 'email');
                break;

            case 'daftarijin':
                $query = $akses === 'hariini'
                    ? DB::table('daftarijin')->whereDate('tanggalijin', $today)->orderByDesc('timestamp')
                    : DB::table('daftarijin')->orderByDesc('timestamp');
                break;

            case 'datapresensi':
                $cols = ['nama', 'nis', 'info', 'waktumasuk', 'ketmasuk', 'a_time', 'waktupulang', 'ketpulang', 'b_time', 'updated_at', 'infodevice', 'infodevice2'];
                $query = $akses === 'hariini'
                    ? DB::table('datapresensi')->select($cols)->whereDate('tanggal', $today)->orderByDesc('updated_at')
                    : DB::table('datapresensi')->select($cols);
                break;

            case 'presensiEvent':
                $cols = ['nis', 'ruang', 'mulai', 'selesai', 'jam', 'tanggal', 'timestamp', 'keterangan'];
                $query = $akses === 'hariini'
                    ? DB::table('presensiEvent')->select($cols)->whereDate('tanggal', $today)->orderByDesc('timestamp')
                    : DB::table('presensiEvent')->select($cols);
                break;

            case 'kalender':
                $query = DB::table('kalender')->select('*');
                break;

            case 'daftarruang':
                $query = DB::table('daftarruang')->select('*');
                break;

            case 'daftarketerlambatan':
                $query = DB::table('daftarketerlambatan')->select('*');
                break;

            default:
                return response()->json(['status' => '404', 'message' => "Akses ke tabel `$db_tbl` tidak diijinkan"], 404);
        }

        $data = $query->get();

        if (in_array($akses, ['lite', 'mid'])) {
            return response()->json([
                'metadata' => [
                    'jumlah_data' => $data->count(),
                    'timestamp'   => now()->toIso8601String(),
                    'versi'       => 'v1.2',
                ],
                'data' => $data,
            ]);
        }

        return response()->json($data);
    }
}

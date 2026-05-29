<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushPresensi extends Command
{
    protected $signature   = 'push:presensi {--tanggal= : Tanggal spesifik Y-m-d, default hari ini} {--force : Kirim ulang semua data meski sudah pernah dikirim}';
    protected $description = 'Push data presensi ke 4 endpoint TIM IT';

    private string $tanggal;
    private bool   $force;

    public function handle(): void
    {
        $this->tanggal = $this->option('tanggal') ?? date('Y-m-d');
        $this->force   = (bool) $this->option('force');

        $timid       = DB::table('statusnya')->first();
        $urlPresensi = $timid->timid_presensi_url ?? '';
        $urlSholat   = $timid->timid_sholat_url ?? '';
        $urlIzinMens = $timid->timid_izin_mens_url ?? '';
        $urlIjin     = $timid->timid_ijin_url ?? '';

        $adaUrl = $urlPresensi || $urlSholat || $urlIzinMens || $urlIjin;
        if (!$adaUrl) {
            $this->warn('[' . now() . '] URL TIM IT belum dikonfigurasi. Skip.');
            return;
        }

        $this->info('[' . now() . '] Push tanggal: ' . $this->tanggal . ($this->force ? ' (FORCE)' : ''));

        $totalKirim = 0;
        if ($urlPresensi)  $totalKirim += $this->pushPresensiHarian($urlPresensi);
        if ($urlSholat)    $totalKirim += $this->pushSholat($urlSholat);
        if ($urlIzinMens)  $totalKirim += $this->pushIzinMens($urlIzinMens);
        if ($urlIjin)      $totalKirim += $this->pushIjin($urlIjin);

        if ($totalKirim === 0) {
            $this->line('[' . now() . '] Tidak ada data baru. Skip.');
        } else {
            $this->info('[' . now() . '] Selesai. Total dikirim: ' . $totalKirim . ' endpoint.');
        }
    }

    // ── 1. Presensi Harian ──
    private function pushPresensiHarian(string $url): int
    {
        $query = DB::table('datapresensi as dp')
            ->leftJoin('datasiswa as ds', 'ds.nokartu', '=', 'dp.nokartu')
            ->where('dp.tanggal', $this->tanggal)
            ->whereNull('dp.pushed_at');

        $data = $query->select(
            'dp.id',
            'dp.nomorinduk as nis',
            'dp.nama',
            'dp.info as kelas',
            'dp.waktumasuk',
            'dp.ketmasuk',
            'dp.waktupulang',
            'dp.ketpulang',
            'dp.infodevice2 as device'
        )
            ->get();

        if ($data->isEmpty()) return 0;

        $payload = [
            'type'      => 'presensi_harian',
            'timestamp' => now()->toIso8601String(),
            'tanggal'   => $this->tanggal,
            'total'     => $data->count(),
            'data'      => $data->map(fn($p) => [
                'nis'          => $p->nis,
                'nama'         => $p->nama,
                'kelas'        => $p->kelas,
                'waktu_masuk'  => $p->waktumasuk,
                'ket_masuk'    => $p->ketmasuk,
                'waktu_pulang' => ($p->waktupulang && $p->waktupulang !== '00:00:00') ? $p->waktupulang : null,
                'ket_pulang'   => $p->ketpulang ?: null,
                'device'       => $p->device,
            ])->values(),
        ];

        $batches = $data->chunk(10);
        $allOk   = true;
        $sentIds = collect();

        foreach ($batches as $batch) {
            $batchPayload = [
                'type'      => 'presensi_harian',
                'timestamp' => now()->toIso8601String(),
                'tanggal'   => $this->tanggal,
                'total'     => $batch->count(),
                'data'      => $batch->map(fn($p) => [
                    'nis'          => $p->nis,
                    'nama'         => $p->nama,
                    'kelas'        => $p->kelas,
                    'waktu_masuk'  => $p->waktumasuk,
                    'ket_masuk'    => $p->ketmasuk,
                    'waktu_pulang' => ($p->waktupulang && $p->waktupulang !== '00:00:00') ? $p->waktupulang : null,
                    'ket_pulang'   => $p->ketpulang ?: null,
                    'device'       => $p->device,
                ])->values(),
            ];
            $ok = $this->kirim($url, $batchPayload, 'Presensi Harian');
            if ($ok) $sentIds = $sentIds->concat($batch->pluck('id'));
            else $allOk = false;
        }

        if ($sentIds->isNotEmpty()) {
            DB::table('datapresensi')->whereIn('id', $sentIds)->update(['pushed_at' => now()]);
        }

        return $allOk ? 1 : 0;
    }

    // ── 2. Sholat ──
    private function pushSholat(string $url): int
    {
        $query = DB::table('presensiEvent as pe')
            ->leftJoin('datasiswa as ds', 'ds.nis', '=', 'pe.nis')
            ->where('pe.tanggal', $this->tanggal)
            ->where('pe.ruang', '!=', 'Izin Mens')
            ->whereNull('pe.pushed_at');

        $events = $query->select(
            'pe.id',
            'pe.nis',
            'ds.nama',
            'ds.kelas',
            'pe.keterangan',
            'pe.ruang',
            'pe.mulai'
        )->get();

        if ($events->isEmpty()) return 0;

        // Pivot per siswa
        $siswaMap = [];
        foreach ($events as $e) {
            $nis = $e->nis;
            if (!isset($siswaMap[$nis])) {
                $siswaMap[$nis] = [
                    'ids'          => [],
                    'nis'          => $nis,
                    'nama'         => $e->nama ?? '-',
                    'kelas'        => $e->kelas ?? '-',
                    'dzuhur'       => null,
                    'ashar'        => null,
                    'device_dzuhur' => null,
                    'device_ashar' => null,
                ];
            }
            $siswaMap[$nis]['ids'][] = $e->id;
            if ($e->keterangan === 'DZUHUR') {
                $siswaMap[$nis]['dzuhur']        = $e->mulai;
                $siswaMap[$nis]['device_dzuhur'] = $e->ruang;
            } elseif ($e->keterangan === 'ASHAR') {
                $siswaMap[$nis]['ashar']        = $e->mulai;
                $siswaMap[$nis]['device_ashar'] = $e->ruang;
            }
        }

        $dataList = collect(array_values($siswaMap));
        $payload = [
            'type'      => 'presensi_sholat',
            'timestamp' => now()->toIso8601String(),
            'tanggal'   => $this->tanggal,
            'total'     => $dataList->count(),
            'data'      => $dataList->map(fn($s) => [
                'nis'          => $s['nis'],
                'nama'         => $s['nama'],
                'kelas'        => $s['kelas'],
                'dzuhur'       => $s['dzuhur'],
                'ashar'        => $s['ashar'],
                'device_dzuhur' => $s['device_dzuhur'],
                'device_ashar' => $s['device_ashar'],
            ])->values(),
        ];

        // Kirim per batch 50 records
        $batches   = $dataList->chunk(10);
        $allOk     = true;
        $sentIds   = collect();

        foreach ($batches as $batch) {
            $batchPayload = [
                'type'      => 'presensi_sholat',
                'timestamp' => now()->toIso8601String(),
                'tanggal'   => $this->tanggal,
                'total'     => $batch->count(),
                'data'      => $batch->map(fn($s) => [
                    'nis'           => $s['nis'],
                    'nama'          => $s['nama'],
                    'kelas'         => $s['kelas'],
                    'dzuhur'        => $s['dzuhur'],
                    'ashar'         => $s['ashar'],
                    'device_dzuhur' => $s['device_dzuhur'],
                    'device_ashar'  => $s['device_ashar'],
                ])->values(),
            ];

            $ok = $this->kirim($url, $batchPayload, 'Presensi Sholat');

            if ($ok) {
                // Kumpulkan ID dari siswa di batch ini
                foreach ($batch as $s) {
                    foreach ($s['ids'] as $id) {
                        $sentIds->push($id);
                    }
                }
            } else {
                $allOk = false;
            }
        }

        if ($sentIds->isNotEmpty()) {
            DB::table('presensiEvent')->whereIn('id', $sentIds)->update(['pushed_at' => now()]);
        }

        return $allOk ? 1 : 0;
    }

    // ── 3. Izin Menstruasi ──
    private function pushIzinMens(string $url): int
    {
        $query = DB::table('presensiEvent as pe')
            ->leftJoin('datasiswa as ds', 'ds.nis', '=', 'pe.nis')
            ->where('pe.tanggal', $this->tanggal)
            ->where('pe.ruang', 'Izin Mens')
            ->whereNull('pe.pushed_at');

        $data = $query->select(
            'pe.id',
            'pe.nis',
            'ds.nama',
            'ds.kelas',
            'pe.mulai',
            'pe.keterangan'
        )->get();

        if ($data->isEmpty()) return 0;

        $siswaMap = [];
        foreach ($data as $e) {
            $nis = $e->nis;
            if (!isset($siswaMap[$nis])) {
                $siswaMap[$nis] = [
                    'ids'          => [],
                    'nis'          => $nis,
                    'nama'         => $e->nama ?? '-',
                    'kelas'        => $e->kelas ?? '-',
                    'waktu_dzuhur' => null,
                    'waktu_ashar'  => null,
                ];
            }
            $siswaMap[$nis]['ids'][] = $e->id;
            if ($e->keterangan === 'DZUHUR') {
                $siswaMap[$nis]['waktu_dzuhur'] = $e->mulai;
            } elseif ($e->keterangan === 'ASHAR') {
                $siswaMap[$nis]['waktu_ashar'] = $e->mulai;
            }
        }

        $dataList = collect(array_values($siswaMap));
        $payload = [
            'type'      => 'izin_mens',
            'timestamp' => now()->toIso8601String(),
            'tanggal'   => $this->tanggal,
            'total'     => $dataList->count(),
            'data'      => $dataList->map(fn($s) => [
                'nis'          => $s['nis'],
                'nama'         => $s['nama'],
                'kelas'        => $s['kelas'],
                'waktu_dzuhur' => $s['waktu_dzuhur'],
                'waktu_ashar'  => $s['waktu_ashar'],
            ])->values(),
        ];

        $batches = $dataList->chunk(10);
        $allOk   = true;
        $sentIds = collect();

        foreach ($batches as $batch) {
            $batchPayload = [
                'type'      => 'izin_mens',
                'timestamp' => now()->toIso8601String(),
                'tanggal'   => $this->tanggal,
                'total'     => $batch->count(),
                'data'      => $batch->map(fn($s) => [
                    'nis'          => $s['nis'],
                    'nama'         => $s['nama'],
                    'kelas'        => $s['kelas'],
                    'waktu_dzuhur' => $s['waktu_dzuhur'],
                    'waktu_ashar'  => $s['waktu_ashar'],
                ])->values(),
            ];
            $ok = $this->kirim($url, $batchPayload, 'Izin Menstruasi');
            if ($ok) {
                foreach ($batch as $s) {
                    foreach ($s['ids'] as $id) $sentIds->push($id);
                }
            } else $allOk = false;
        }

        if ($sentIds->isNotEmpty()) {
            DB::table('presensiEvent')->whereIn('id', $sentIds)->update(['pushed_at' => now()]);
        }

        return $allOk ? 1 : 0;
    }

    // ── 4. Izin Keluar/Pulang ──
    private function pushIjin(string $url): int
    {
        // Data baru (belum pernah dikirim)
        $queryBaru = DB::table('daftarijin as di')
            ->leftJoin('datasiswa as ds', 'ds.nis', '=', 'di.nis')
            ->where('di.tanggalijin', $this->tanggal)
            ->whereNull('di.pushed_at')
            ->select(
                'di.id',
                'di.nis',
                'di.nama',
                'ds.kelas',
                'di.jam_keluar',
                'di.jam_kembali',
                'di.info'
            )->get();

        // Data yang sudah kembali tapi belum dikonfirmasi kembalinya
        $queryKembali = DB::table('daftarijin as di')
            ->leftJoin('datasiswa as ds', 'ds.nis', '=', 'di.nis')
            ->where('di.tanggalijin', $this->tanggal)
            ->whereNotNull('di.jam_kembali')
            ->whereNull('di.kembali_pushed_at')
            ->select(
                'di.id',
                'di.nis',
                'di.nama',
                'ds.kelas',
                'di.jam_keluar',
                'di.jam_kembali',
                'di.info'
            )->get();

        $gabungan = $queryBaru->concat($queryKembali)->unique('id');

        if ($gabungan->isEmpty()) return 0;

        $payload = [
            'type'      => 'izin_keluar',
            'timestamp' => now()->toIso8601String(),
            'tanggal'   => $this->tanggal,
            'total'     => $gabungan->count(),
            'data'      => $gabungan->map(fn($p) => [
                'nis'         => $p->nis,
                'nama'        => $p->nama,
                'kelas'       => $p->kelas ?? '-',
                'jam_keluar'  => $p->jam_keluar,
                'jam_kembali' => $p->jam_kembali,
                'keterangan'  => $p->info,
                'status'      => $p->jam_kembali ? 'kembali' : 'belum_kembali',
            ])->values(),
        ];

        $batches  = $gabungan->chunk(10);
        $allOk    = true;
        $sentIds  = collect();

        foreach ($batches as $batch) {
            $batchPayload = [
                'type'      => 'izin_keluar',
                'timestamp' => now()->toIso8601String(),
                'tanggal'   => $this->tanggal,
                'total'     => $batch->count(),
                'data'      => $batch->map(fn($p) => [
                    'nis'         => $p->nis,
                    'nama'        => $p->nama,
                    'kelas'       => $p->kelas ?? '-',
                    'jam_keluar'  => $p->jam_keluar,
                    'jam_kembali' => $p->jam_kembali,
                    'keterangan'  => $p->info,
                    'status'      => $p->jam_kembali ? 'kembali' : 'belum_kembali',
                ])->values(),
            ];
            $ok = $this->kirim($url, $batchPayload, 'Izin Keluar/Pulang');
            if ($ok) $sentIds = $sentIds->concat($batch->pluck('id'));
            else $allOk = false;
        }

        if ($sentIds->isNotEmpty()) {
            $idsBaru = $queryBaru->pluck('id')->intersect($sentIds);
            if ($idsBaru->isNotEmpty())
                DB::table('daftarijin')->whereIn('id', $idsBaru)->update(['pushed_at' => now()]);
            $idsKembali = $queryKembali->pluck('id')->intersect($sentIds);
            if ($idsKembali->isNotEmpty())
                DB::table('daftarijin')->whereIn('id', $idsKembali)->update(['kembali_pushed_at' => now()]);
        }

        return $allOk ? 1 : 0;
    }

    // ── Helper: kirim HTTP POST ──
    private function kirim(string $url, array $payload, string $label): bool
    {
        $endpoint = $payload['type'] ?? $label;
        $tanggal  = $payload['tanggal'] ?? $this->tanggal;
        $total    = $payload['total'] ?? 0;

        try {
            $response = Http::timeout(15)
                ->withHeaders(['X-Api-Key' => DB::table('statusnya')->value('timid_api_key') ?? ''])
                // ->post($url, $payload);
                ->get($url, ['payloadjson' => json_encode($payload, JSON_UNESCAPED_UNICODE)]);

            if ($response->successful()) {
                $this->info('[' . now() . '] ✅ ' . $label . ' terkirim (' . $total . ' records)');
                Log::info('push:presensi ' . $label . ' OK', [
                    'total'  => $total,
                    'status' => $response->status(),
                ]);
                DB::table('push_log')->insert([
                    'endpoint'    => $endpoint,
                    'tanggal'     => $tanggal,
                    'status'      => 1,
                    'total'       => $total,
                    'http_status' => $response->status(),
                    'pesan'       => $this->ringkasRespon($response->body(), $response->status()),
                    'created_at'  => now(),
                ]);
                return true;
            } else {
                $this->error('[' . now() . '] ❌ ' . $label . ' gagal. Status: ' . $response->status());
                Log::error('push:presensi ' . $label . ' GAGAL', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                DB::table('push_log')->insert([
                    'endpoint'    => $endpoint,
                    'tanggal'     => $tanggal,
                    'status'      => 0,
                    'total'       => $total,
                    'http_status' => $response->status(),
                    'pesan'       => $this->ringkasRespon($response->body(), $response->status()),
                    'created_at'  => now(),
                ]);
                return false;
            }
        } catch (\Exception $e) {
            $this->error('[' . now() . '] ❌ ' . $label . ' error: ' . $e->getMessage());
            Log::error('push:presensi exception', ['label' => $label, 'message' => $e->getMessage()]);
            DB::table('push_log')->insert([
                'endpoint'    => $endpoint,
                'tanggal'     => $tanggal,
                'status'      => 0,
                'total'       => $total,
                'http_status' => null,
                'pesan'       => substr($e->getMessage(), 0, 500),
                'created_at'  => now(),
            ]);
            return false;
        }
    }

    private function ringkasRespon(string $body, int $httpStatus): string
    {
        $trimmed = trim($body);

        // Coba parse JSON
        $json = json_decode($trimmed, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return substr(json_encode($json, JSON_UNESCAPED_UNICODE), 0, 300);
        }

        // Jika HTML, anggap terkirim tapi response non-JSON
        if (stripos($trimmed, '<!DOCTYPE') !== false || stripos($trimmed, '<html') !== false) {
            return 'Terkirim (response non-JSON)';
        }

        // Plain text
        return substr($trimmed, 0, 300);
    }
}

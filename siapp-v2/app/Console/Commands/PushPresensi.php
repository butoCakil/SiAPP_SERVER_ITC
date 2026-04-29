<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushPresensi extends Command
{
    protected $signature   = 'push:presensi {--tanggal= : Tanggal spesifik (Y-m-d), default hari ini}';
    protected $description = 'Push data presensi ke ekosistem SIM TIM IT';

    public function handle(): void
    {
        $tanggal     = $this->option('tanggal') ?? date('Y-m-d');
        $urlPresensi = config('timid.presensi_url');
        $urlSholat   = config('timid.sholat_url');

        if (!$urlPresensi && !$urlSholat) {
            $this->warn('[' . now() . '] URL TIM IT belum dikonfigurasi. Skip.');
            return;
        }

        $this->info('[' . now() . '] Mulai push data tanggal: ' . $tanggal);

        if ($urlPresensi) {
            $this->pushPresensiHarian($tanggal, $urlPresensi);
        }

        if ($urlSholat) {
            $this->pushSholat($tanggal, $urlSholat);
        }

        $this->info('[' . now() . '] Selesai.');
    }

    private function pushPresensiHarian(string $tanggal, string $url): void
    {
        $data = DB::table('datapresensi as dp')
            ->leftJoin('datasiswa as ds', 'ds.nokartu', '=', 'dp.nokartu')
            ->where('dp.tanggal', $tanggal)
            ->select(
                'ds.nis', 'dp.nama', 'dp.info as kelas',
                'dp.waktumasuk', 'dp.ketmasuk',
                'dp.waktupulang', 'dp.ketpulang'
            )
            ->orderBy('dp.waktumasuk')
            ->get()
            ->map(fn($p) => [
                'nis'          => $p->nis ?? '-',
                'nama'         => $p->nama,
                'kelas'        => $p->kelas,
                'waktu_masuk'  => $p->waktumasuk,
                'ket_masuk'    => $p->ketmasuk,
                'waktu_pulang' => ($p->waktupulang && $p->waktupulang !== '00:00:00') ? $p->waktupulang : null,
                'ket_pulang'   => $p->ketpulang ?: null,
            ]);

        $payload = [
            'type'      => 'presensi_harian',
            'timestamp' => now()->toIso8601String(),
            'tanggal'   => $tanggal,
            'total'     => $data->count(),
            'data'      => $data->values(),
        ];

        $this->kirim($url, $payload, 'Presensi Harian');
    }

    private function pushSholat(string $tanggal, string $url): void
    {
        $events = DB::table('presensiEvent as pe')
            ->leftJoin('datasiswa as ds', 'ds.nis', '=', 'pe.nis')
            ->where('pe.tanggal', $tanggal)
            ->select('pe.nis', 'ds.nama', 'ds.kelas', 'pe.keterangan', 'pe.ruang', 'pe.mulai')
            ->orderBy('pe.nis')
            ->get();

        // Pivot per siswa
        $siswaMap = [];
        foreach ($events as $e) {
            $nis = $e->nis;
            if (!isset($siswaMap[$nis])) {
                $siswaMap[$nis] = [
                    'nis'       => $nis,
                    'nama'      => $e->nama ?? '-',
                    'kelas'     => $e->kelas ?? '-',
                    'dzuhur'    => null,
                    'ashar'     => null,
                    'izin_mens' => false,
                ];
            }
            if ($e->keterangan === 'DZUHUR') {
                $siswaMap[$nis]['dzuhur'] = $e->mulai;
            } elseif ($e->keterangan === 'ASHAR') {
                $siswaMap[$nis]['ashar'] = $e->mulai;
            }
            if ($e->ruang === 'Izin Mens') {
                $siswaMap[$nis]['izin_mens'] = true;
            }
        }

        $payload = [
            'type'      => 'presensi_sholat',
            'timestamp' => now()->toIso8601String(),
            'tanggal'   => $tanggal,
            'total'     => count($siswaMap),
            'data'      => array_values($siswaMap),
        ];

        $this->kirim($url, $payload, 'Pembiasaan Sholat');
    }

    private function kirim(string $url, array $payload, string $label): void
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['X-Api-Key' => config('timid.api_key')])
                ->post($url, $payload);

            if ($response->successful()) {
                $this->info('[' . now() . '] ✅ ' . $label . ' terkirim. Response: ' . $response->status());
                Log::info('push:presensi ' . $label . ' OK', ['status' => $response->status()]);
            } else {
                $this->error('[' . now() . '] ❌ ' . $label . ' gagal. Status: ' . $response->status());
                Log::error('push:presensi ' . $label . ' GAGAL', ['status' => $response->status(), 'body' => $response->body()]);
            }
        } catch (\Exception $e) {
            $this->error('[' . now() . '] ❌ Error: ' . $e->getMessage());
            Log::error('push:presensi exception', ['message' => $e->getMessage()]);
        }
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NaikKelas extends Command
{
    protected $signature   = 'siswa:naik-kelas {--dry-run : Tampilkan rencana tanpa eksekusi}';
    protected $description = 'Kenaikan kelas (X->XI, XI->XII) dan kelulusan (XII->lulus) untuk siswa aktif';

    public function handle(): void
    {
        $dryRun = (bool) $this->option('dry-run');

        $siswaAktif = DB::table('datasiswa')->where('status', 'aktif')->get();

        if ($siswaAktif->isEmpty()) {
            $this->warn('Tidak ada siswa dengan status aktif.');
            return;
        }

        $rencana = [];
        $tingkatTakDikenal = [];

        foreach ($siswaAktif as $s) {
            if ($s->tingkat === 'XII') {
                $rencana[] = [
                    'aksi'        => 'LULUS',
                    'nis'         => $s->nis,
                    'nama'        => $s->nama,
                    'kelas_lama'  => $s->kelas,
                    'kelas_baru'  => $s->kelas . ' (tetap, jejak histori)',
                ];
            } elseif ($s->tingkat === 'XI') {
                $kelasBaru = preg_replace('/^XI\b/', 'XII', $s->kelas);
                $rencana[] = [
                    'aksi'        => 'NAIK',
                    'nis'         => $s->nis,
                    'nama'        => $s->nama,
                    'kelas_lama'  => $s->kelas,
                    'kelas_baru'  => $kelasBaru,
                ];
            } elseif ($s->tingkat === 'X') {
                $kelasBaru = preg_replace('/^X\b/', 'XI', $s->kelas);
                $rencana[] = [
                    'aksi'        => 'NAIK',
                    'nis'         => $s->nis,
                    'nama'        => $s->nama,
                    'kelas_lama'  => $s->kelas,
                    'kelas_baru'  => $kelasBaru,
                ];
            } else {
                $tingkatTakDikenal[] = $s;
            }
        }

        if (!empty($tingkatTakDikenal)) {
            $this->error('Ditemukan ' . count($tingkatTakDikenal) . ' siswa dengan tingkat tidak dikenal (bukan X/XI/XII) — DILEWATI, tidak diapa-apakan:');
            foreach ($tingkatTakDikenal as $s) {
                $this->line("  - NIS {$s->nis} ({$s->nama}) tingkat='{$s->tingkat}' kelas='{$s->kelas}'");
            }
        }

        // Ringkasan per transisi kelas
        $ringkasan = collect($rencana)
            ->groupBy(fn($r) => $r['aksi'] . ': ' . $r['kelas_lama'] . ' -> ' . $r['kelas_baru'])
            ->map(fn($g) => count($g));

        $this->info('=== RENCANA PERUBAHAN ===');
        foreach ($ringkasan as $label => $jumlah) {
            $this->line("  {$label}  ({$jumlah} siswa)");
        }
        $this->info('Total siswa terdampak: ' . count($rencana));

        if ($dryRun) {
            $this->warn('--dry-run aktif, tidak ada perubahan yang disimpan.');
            return;
        }

        if (!$this->confirm('Lanjutkan eksekusi perubahan ke database?', false)) {
            $this->warn('Dibatalkan.');
            return;
        }

        DB::transaction(function () use ($rencana) {
            foreach ($rencana as $r) {
                if ($r['aksi'] === 'LULUS') {
                    DB::table('datasiswa')->where('nis', $r['nis'])->update([
                        'status'     => 'lulus',
                        'updated_at' => now(),
                    ]);
                } else {
                    $tingkatBaru = str_starts_with($r['kelas_baru'], 'XII') ? 'XII' : 'XI';
                    DB::table('datasiswa')->where('nis', $r['nis'])->update([
                        'kelas'      => $r['kelas_baru'],
                        'tingkat'    => $tingkatBaru,
                        'updated_at' => now(),
                    ]);
                }
            }
        });

        $this->info('Selesai. ' . count($rencana) . ' siswa diproses.');
    }
}

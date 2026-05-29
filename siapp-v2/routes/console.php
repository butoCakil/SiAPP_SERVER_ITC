<?php

use Illuminate\Support\Facades\Schedule;

// Presensi otomatis buka/tutup
Schedule::command('presensi:scheduler')->everyMinute();

// Push data ke TIM IT — interval dari config
$pushInterval = \Illuminate\Support\Facades\DB::table('statusnya')->value('push_interval') ?? 5;
Schedule::command('push:presensi')->cron('*/' . $pushInterval . ' * * * *');

// Auto cleanup log — setiap hari jam 02:00
$logRetention = \Illuminate\Support\Facades\DB::table('statusnya')->value('log_retention') ?? 30;
Schedule::command("log:clean --days={$logRetention}")->dailyAt('02:00');

// Push otomatis ke TIM IT — hanya jika push_auto aktif
$pushAuto = \Illuminate\Support\Facades\DB::table('statusnya')->value('push_auto') ?? 1;
if ($pushAuto) {
    // Push harian jam 22:00 — pastikan semua data hari ini terkirim
    Schedule::command('push:presensi')->dailyAt('22:00');

    // Push mingguan Sabtu jam 23:00 — re-check 7 hari terakhir
    foreach (range(1, 7) as $i) {
        $menit = ($i - 1) * 5;
        $jam   = '23:' . str_pad($menit, 2, '0', STR_PAD_LEFT);
        $tgl   = date('Y-m-d', strtotime("-{$i} day"));
        Schedule::command("push:presensi --tanggal={$tgl}")->weeklyOn(6, $jam);
    }
}

Schedule::command('device:offline-check')->everyMinute();

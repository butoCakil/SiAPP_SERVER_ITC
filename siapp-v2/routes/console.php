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

Schedule::command('device:offline-check')->everyMinute();

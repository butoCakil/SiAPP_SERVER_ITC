<?php

use Illuminate\Support\Facades\Schedule;

// Presensi otomatis buka/tutup
Schedule::command('presensi:scheduler')->everyMinute();

// Push data ke TIM IT — interval dari config
Schedule::command('push:presensi')->cron('*/' . config('timid.interval', 5) . ' * * * *');

// Auto cleanup log — setiap hari jam 02:00
$logRetention = \Illuminate\Support\Facades\DB::table('statusnya')->value('log_retention') ?? 30;
Schedule::command("log:clean --days={$logRetention}")->dailyAt('02:00');
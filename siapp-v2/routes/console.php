<?php

use Illuminate\Support\Facades\Schedule;

// Presensi otomatis buka/tutup
Schedule::command('presensi:scheduler')->everyMinute();

// Push data ke TIM IT — interval dari config
Schedule::command('push:presensi')->cron('*/' . config('timid.interval', 5) . ' * * * *');
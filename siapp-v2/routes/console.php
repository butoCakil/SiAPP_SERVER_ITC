<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('presensi:scheduler')->everyMinute();
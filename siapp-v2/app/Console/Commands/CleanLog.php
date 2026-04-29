<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanLog extends Command
{
    protected $signature   = 'log:clean {--days=30 : Hapus log lebih dari X hari}';
    protected $description = 'Auto-cleanup log tempreq dan device_logs';

    public function handle(): void
    {
        $days  = (int) $this->option('days');
        $batas = now()->subDays($days)->toDateString();

        $deletedReq    = DB::table('tempreq')->whereDate('timestamp', '<', $batas)->delete();
        $deletedDevice = DB::table('device_logs')->whereDate('received_at', '<', $batas)->delete();

        $msg = "log:clean — hapus >{$days} hari: tempreq={$deletedReq}, device_logs={$deletedDevice}";
        $this->info('[' . now() . '] ' . $msg);
        Log::info($msg);
    }
}
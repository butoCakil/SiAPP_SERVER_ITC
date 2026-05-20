<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DeviceOfflineChecker extends Command
{
    protected $signature   = 'device:offline-check';
    protected $description = 'Cek device offline dan kirim notifikasi WA';

    public function handle(): void
    {
        $setting = DB::table('statusnya')->first();
        if (!$setting) {
            $this->error('Setting tidak ditemukan.');
            return;
        }

        $offlineAfter      = $setting->offline_after      ?? 120;
        $escalationAfter   = $setting->escalation_after   ?? 300;
        $quietStart        = $setting->notif_quiet_start  ?? 18;
        $quietEnd          = $setting->notif_quiet_end    ?? 6;
        $escalationStart   = $setting->notif_escalation_start ?? 10;
        $escalationEnd     = $setting->notif_escalation_end   ?? 16;
        $waNumber          = $setting->wa_number          ?? '082241863393';
        $waDeviceId        = $setting->wa_device_id       ?? '';

        $now = now()->toDateTimeString();

        /* ── 1. SET OFFLINE ── */
        $toOffline = DB::table('devices')
            ->where('online', 1)
            ->where('hidden', 0)
            ->whereRaw("TIMESTAMPDIFF(SECOND, last_seen, NOW()) >= ?", [$offlineAfter])
            ->get();

        foreach ($toOffline as $device) {
            $status = json_decode($device->last_status, true) ?? [];
            $status['status'] = 'offline';

            DB::table('devices')->where('id', $device->id)->update([
                'online'       => 0,
                'offline_since' => $now,
                'last_status'  => json_encode($status, JSON_UNESCAPED_UNICODE),
            ]);
        }

        /* ── 2. SNAPSHOT ── */
        $devices = DB::table('devices as d')
            ->leftJoin('reg_device as r', 'r.no_device', '=', 'd.device_id')
            ->where('d.hidden', 0)
            ->select(
                'd.device_id',
                'd.online',
                'd.online_since',
                'd.offline_since',
                DB::raw("COALESCE(r.info_device, d.info) as info_device")
            )
            ->orderBy('d.device_id')
            ->get();

        $currentStatus = [];
        $deviceInfo    = [];
        foreach ($devices as $d) {
            $currentStatus[$d->device_id] = (int) $d->online;
            $deviceInfo[$d->device_id]    = [
                'online_since'  => $d->online_since,
                'offline_since' => $d->offline_since,
                'info'          => $d->info_device,
            ];
        }

        $totalDevices = count($currentStatus);
        $totalOnline  = array_sum($currentStatus);
        $totalOffline = $totalDevices - $totalOnline;
        $offlineRatio = $totalDevices > 0 ? $totalOffline / $totalDevices : 0;

        /* ── 3. LOAD SNAPSHOT LAMA ── */
        $statusFile = storage_path('app/last_device_status.json');
        $lastStatus = [];
        if (file_exists($statusFile)) {
            $lastStatus = json_decode(file_get_contents($statusFile), true) ?? [];
        }

        /* ── 4. DELTA ── */
        $changes      = [];
        $recoveryList = [];

        foreach ($currentStatus as $id => $online) {
            $prev = $lastStatus[$id] ?? null;
            if ($prev !== null && $prev !== $online) {
                $duration = 0;
                if ($online == 0) {
                    $duration = time() - strtotime($deviceInfo[$id]['online_since'] ?? 'now');
                } else {
                    $duration = time() - strtotime($deviceInfo[$id]['offline_since'] ?? 'now');
                    $recoveryList[] = $id;
                }
                $changes[$id] = [
                    'from'     => $prev,
                    'to'       => $online,
                    'duration' => $this->formatDuration($duration),
                ];
            }
        }

        /* ── 5. SEVERITY ── */
        $severity = 'ℹ️ *INFO*';
        if ($totalOffline >= 10 || $offlineRatio > 0.2) {
            $severity = '🚨 *CRITICAL*';
        } elseif ($offlineRatio > 0) {
            $severity = '⚠️ *WARNING*';
        }

        $massOutage = ($offlineRatio >= 0.8);
        $totalDown  = ($totalOnline === 0);

        /* ── 6. DELTA REPORT ── */
        if (count($changes) > 0 && $this->isWeekday()) {
            $msg  = "📢 STATUS UPDATE\n";
            $msg .= "⏱ {$now}\n";
            $msg .= "Severity: {$severity}\n";
            $msg .= "────────────────\n\n";

            foreach ($changes as $id => $c) {
                $info = $deviceInfo[$id]['info'] ? ("_(" . $deviceInfo[$id]['info'] . ")_") : "";
                if ($c['to'] == 0) {
                    $msg .= "🔴 {$id} {$info}\n🟢 Online → 🔴 Offline\n⏳ Online sebelumnya: {$c['duration']}\n\n";
                } else {
                    $msg .= "🟢 {$id} {$info}\n🔴 Offline → 🟢 Online\n⏳ Offline sebelumnya: {$c['duration']}\n\n";
                }
            }

            $msg .= "📊 Online: {$totalOnline} | Offline: {$totalOffline} | Total: {$totalDevices}\n";
            $msg .= $this->buildSnapshot($currentStatus, $deviceInfo);

            if (!$this->isQuietHours($quietStart, $quietEnd)) {
                $this->kirimWA($waNumber, $waDeviceId, $msg);
                $this->line('[' . now() . '] Delta report sent.');
            }
        }

        /* ── 7. ESCALATION ── */
        $escalationFile  = storage_path('app/escalation_state.json');
        $escalationState = [];
        if (file_exists($escalationFile)) {
            $escalationState = json_decode(file_get_contents($escalationFile), true) ?? [];
        }

        $today        = now()->format('Y-m-d');
        $escalateList = [];

        foreach ($currentStatus as $id => $online) {
            if ($online == 0) {
                $since = $deviceInfo[$id]['offline_since'];
                if ($since && (time() - strtotime($since)) >= $escalationAfter) {
                    if (!isset($escalationState[$id]) || $escalationState[$id] !== $today) {
                        $escalateList[] = $id;
                    }
                }
            }
        }

        if (
            $severity === '🚨 *CRITICAL*' &&
            count($escalateList) > 0 &&
            $this->isHourInRange($escalationStart, $escalationEnd) &&
            $this->isWeekday()
        ) {
            if ($massOutage) {
                $msg  = "🚨 MASS OUTAGE\n{$totalOffline} dari {$totalDevices} device offline\n⏱ {$now}\nKemungkinan gangguan jaringan utama.";
            } elseif ($totalDown) {
                $msg  = "🚨 TOTAL SYSTEM DOWN\nSemua device offline\n⏱ {$now}";
            } else {
                $msg  = "⏰ ESCALATION (CRITICAL)\n⏱ {$now}\n\nTotal Offline: {$totalOffline} dari {$totalDevices}\n\n";
                $countShown = 0;
                foreach ($escalateList as $id) {
                    if ($countShown >= 20) {
                        $remaining = count($escalateList) - 20;
                        $msg .= "(+{$remaining} device lainnya)\n";
                        break;
                    }
                    $since    = $deviceInfo[$id]['offline_since'];
                    $duration = $this->formatDuration(time() - strtotime($since));
                    $info     = $deviceInfo[$id]['info'] ?? '-';
                    $msg .= "🔴 {$id} ({$info}) – {$duration}\n";
                    $countShown++;
                }
            }

            $msg .= "\n" . $this->buildSnapshot($currentStatus, $deviceInfo);

            if (!$this->isQuietHours($quietStart, $quietEnd)) {
                $this->kirimWA($waNumber, $waDeviceId, $msg);

                foreach ($escalateList as $id) {
                    $escalationState[$id] = $today;
                }
                file_put_contents($escalationFile, json_encode($escalationState, JSON_PRETTY_PRINT));
                $this->line('[' . now() . '] Escalation sent.');
            }
        }

        /* ── 8. RECOVERY RESET ── */
        foreach ($recoveryList as $id) {
            unset($escalationState[$id]);
        }
        file_put_contents($escalationFile, json_encode($escalationState, JSON_PRETTY_PRINT));

        /* ── 9. SAVE SNAPSHOT ── */
        file_put_contents($statusFile, json_encode($currentStatus, JSON_PRETTY_PRINT));
    }

    private function kirimWA(string $number, string $deviceId, string $message): void
    {
        try {
            Http::post('https://api.whacenter.com/api/send', [
                'device_id' => $deviceId,
                'number'    => $number,
                'message'   => $message,
                'file'      => null,
            ]);
        } catch (\Throwable $e) {
            $this->error('Gagal kirim WA: ' . $e->getMessage());
        }
    }

    private function buildSnapshot(array $currentStatus, array $deviceInfo): string
    {
        $output = "────────────────\n📋 STATUS SEMUA DEVICE\n\n";
        foreach ($currentStatus as $id => $online) {
            $info = $deviceInfo[$id]['info'] ? ("_(" . $deviceInfo[$id]['info'] . ")_") : "";
            if ($online == 1) {
                $duration = $this->formatDuration(time() - strtotime($deviceInfo[$id]['online_since'] ?? 'now'));
                $output .= "🟢 *{$id}* {$info} ⏳ {$duration}\n";
            } else {
                $duration = $this->formatDuration(time() - strtotime($deviceInfo[$id]['offline_since'] ?? 'now'));
                $output .= "🔴 *{$id}* {$info} ⏳ {$duration}\n";
            }
        }
        return $output;
    }

    private function formatDuration(int $seconds): string
    {
        if ($seconds < 0) $seconds = 0;
        $days    = floor($seconds / 86400);
        $hours   = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        if ($days > 0)  return sprintf("%d h %02d:%02d", $days, $hours, $minutes);
        if ($hours > 0) return sprintf("%02d:%02d", $hours, $minutes);
        return $minutes . " m";
    }

    private function isQuietHours(int $start, int $end): bool
    {
        $hour = (int) now()->format('G');
        if ($start < $end) return ($hour >= $start && $hour < $end);
        return ($hour >= $start || $hour < $end);
    }

    private function isHourInRange(int $start, int $end): bool
    {
        $hour = (int) now()->format('G');
        return ($hour >= $start && $hour < $end);
    }

    private function isWeekday(): bool
    {
        return (int) now()->format('N') <= 5;
    }
}

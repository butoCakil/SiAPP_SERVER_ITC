<?php

namespace App\Services;

use Bluerhinos\phpMQTT;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeviceService
{
    private string $host;
    private int    $port;
    private string $username;
    private string $password;
    public function __construct()
    {
        $this->host     = (string) config('mqtt.host', 'localhost');
        $this->port     = (int)    config('mqtt.port', 1883);
        $this->username = (string) config('mqtt.username', '');
        $this->password = (string) config('mqtt.password', '');
    }

    // ── Publish setting ke device ──
    public function kirimSetting(string $deviceId): array
    {
        $setting = DB::table('statusnya')->first();

        if (!$setting) {
            return ['status' => 'error', 'message' => 'Data setting tidak tersedia'];
        }

        $payload = json_encode([
            'device_id' => $deviceId,
            'settings'  => [
                'mode'        => (int) $setting->mode,
                'wa'          => $setting->wa,
                'wta'         => $setting->wta,
                'wtp'         => $setting->wtp,
                'wtp_jumat'   => $setting->wtp_jumat,
                'wp'          => $setting->wp,
                'wp_jumat'    => $setting->wp_jumat,
                'hari_kerja'  => (int) $setting->hari_kerja,
                'waktumasuk'  => $setting->waktumasuk,
                'waktupulang' => $setting->waktupulang,
                'info'        => $setting->info,
                'dhuha_start'  => $setting->dhuha_start  ?? '07:00:00',
                'dhuha_end'    => $setting->dhuha_end    ?? '11:00:00',
                'dzuhur_start' => $setting->dzuhur_start ?? '11:30:00',
                'dzuhur_end'   => $setting->dzuhur_end   ?? '13:30:00',
                'ashar_start'  => $setting->ashar_start  ?? '15:00:00',
                'ashar_end'    => $setting->ashar_end    ?? '16:30:00',
                'up1'          => $setting->upload1      ?? '07:30:00',
                'up2'          => $setting->upload2      ?? '13:00:00',
                'rs1'          => $setting->restart1     ?? '05:00:00',
                'rs2'          => $setting->restart2     ?? '17:00:00',
            ],
        ], JSON_UNESCAPED_UNICODE);

        return $this->publish("devices/{$deviceId}/settings", $payload);
    }

    // ── Publish koneksi ke device ──
    public function kirimKoneksi(string $deviceId, array $koneksi): array
    {
        $payload = json_encode([
            'device_id' => $deviceId,
            'koneksi'   => $koneksi,
        ], JSON_UNESCAPED_UNICODE);

        return $this->publish("devices/{$deviceId}/settings", $payload);
    }

    // ── Publish command ke device ──
    public function kirimCommand(string $deviceId, array $command): array
    {
        $payload = json_encode([
            'device_id' => $deviceId,
            'command'   => $command,
        ], JSON_UNESCAPED_UNICODE);

        return $this->publish("devices/{$deviceId}/settings", $payload);
    }

    // ── Update status device di DB ──
    public function updateStatus(string $deviceId, array $data, int $online): void
    {
        $existing = DB::table('devices')->where('device_id', $deviceId)->first();

        if ($existing && $existing->online == 0 && $online == 1) {
            DB::table('devices')
                ->where('device_id', $deviceId)
                ->update(['online_since' => now()]);
        }

        $lastStatus = json_encode([
            'status'  => $data['status']  ?? 'unknown',
            'ram'     => $data['ram']     ?? null,
            'ssid'    => $data['ssid']    ?? null,
            'rssi'    => $data['rssi']    ?? null,
            'latency' => $data['latency'] ?? null,
            'count'   => $data['count']   ?? null,
            'serial'  => $data['serial']  ?? null,
            'version' => $data['version'] ?? null,
        ], JSON_UNESCAPED_UNICODE);

        $upsert = [
            'last_status' => $lastStatus,
            'last_seen'   => now(),
            'online'      => $online,
            'updated_at'  => now(),
        ];

        if (!empty($data['version'])) {
            $upsert['fw_version'] = $data['version'];
        }

        if ($existing) {
            if ($online == 1) {
                $upsert['hidden'] = 0;
            }
            DB::table('devices')->where('device_id', $deviceId)->update($upsert);
        } else {
            DB::table('devices')->insert(array_merge($upsert, [
                'device_id'  => $deviceId,
                'hidden'     => 0,
                'created_at' => now(),
            ]));
        }

        // ── Rekam metrik untuk sparkline ──
        if ($online == 1) {
            DB::table('device_metrics')->insert([
                'device_id'   => $deviceId,
                'ram'         => (int) ($data['ram']     ?? 0),
                'rssi'        => (int) ($data['rssi']    ?? -100),
                'ping'        => (int) ($data['latency'] ?? 0),
                'buffer'      => (int) ($data['count']   ?? 0),
                'recorded_at' => now(),
            ]);

            // Hapus data lebih dari 24 jam
            // DB::table('device_metrics')
            //     ->where('device_id', $deviceId)
            //     ->where('recorded_at', '<', now()->subHours(24))
            //     ->delete();
        }
    }
    // ── Update info device di DB ──
    public function updateInfo(string $deviceId, array $data): void
    {
        $info = json_encode([
            'ssid'    => $data['ssid']    ?? null,
            'serial'  => $data['serial']  ?? null,
            'version' => $data['version'] ?? null,
        ], JSON_UNESCAPED_UNICODE);

        $exists = DB::table('devices')->where('device_id', $deviceId)->exists();

        if ($exists) {
            DB::table('devices')->where('device_id', $deviceId)->update([
                'info'       => $info,
                'fw_version' => $data['version'] ?? null,
                'updated_at' => now(),
            ]);
        } else {
            DB::table('devices')->insert([
                'device_id'  => $deviceId,
                'info'       => $info,
                'fw_version' => $data['version'] ?? null,
                'hidden'     => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    // ── Update feedback device di DB ──
    public function updateFeedback(string $deviceId, array $data): void
    {
        // ── Handle dir_list (payload langsung dari firmware, tidak pakai mode) ──
        if (isset($data['path']) && isset($data['files'])) {
            DB::table('devices')
                ->where('device_id', $deviceId)
                ->update([
                    'last_dirlist' => json_encode($data, JSON_UNESCAPED_UNICODE),
                    'last_seen'    => now(),
                    'updated_at'   => now(),
                ]);
            return;
        }

        $mode    = (int) ($data['mode']    ?? 0);
        $version = $data['version'] ?? null;

        $detail = $data['detail'] ?? '';
        $decoded = json_decode($detail, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $detail = $decoded;
        }

        $detailJson = json_encode([
            'status'    => $data['status']    ?? null,
            'detail'    => $detail,
            'device_id' => $deviceId,
            'version'   => $version,
            'timestamp' => $data['timestamp'] ?? null,
        ], JSON_UNESCAPED_UNICODE);

        $column = match ($mode) {
            1       => 'last_command',
            2       => 'last_setting',
            default => 'last_status',
        };

        $updateData = [
            $column      => $detailJson,
            'last_seen'  => now(),
            'updated_at' => now(),
        ];

        if (!empty($version)) {
            $updateData['fw_version'] = $version;
        }

        DB::table('devices')
            ->where('device_id', $deviceId)
            ->update($updateData);
    }

    // ── Simpan log device ──
    public function simpanLog(string $deviceId, string $topic, string $payload): void
    {
        DB::table('device_logs')->insert([
            'device_id'   => $deviceId,
            'topic'       => $topic,
            'payload'     => $payload,
            'received_at' => now(),
        ]);
    }

    // ── Helper: publish ke MQTT ──
    private function publish(string $topic, string $payload): array
    {
        $clientId = 'siapp_pub_dev_' . substr(md5(microtime()), 0, 6);
        $mqtt     = new phpMQTT($this->host, $this->port, $clientId);

        for ($i = 1; $i <= 2; $i++) {
            if ($mqtt->connect(true, null, $this->username, $this->password)) {
                $mqtt->publish($topic, $payload, 0);
                $mqtt->proc();
                $mqtt->close();
                return ['status' => 'ok', 'message' => "Terkirim ke {$topic} (attempt {$i})", 'data' => $payload];
            }
            usleep(100000);
        }

        return ['status' => 'error', 'message' => 'Gagal terhubung ke broker MQTT'];
    }
}

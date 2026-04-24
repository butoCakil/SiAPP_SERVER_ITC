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
        $this->host     = config('mqtt.host');
        $this->port     = config('mqtt.port');
        $this->username = config('mqtt.username');
        $this->password = config('mqtt.password');
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
                'wp'          => $setting->wp,
                'waktumasuk'  => $setting->waktumasuk,
                'waktupulang' => $setting->waktupulang,
                'info'        => $setting->info,
            ],
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

        DB::table('devices')->updateOrInsert(
            ['device_id' => $deviceId],
            array_merge($upsert, ['created_at' => now()])
        );
    }

    // ── Update info device di DB ──
    public function updateInfo(string $deviceId, array $data): void
    {
        $info = json_encode([
            'ssid'    => $data['ssid']    ?? null,
            'serial'  => $data['serial']  ?? null,
            'version' => $data['version'] ?? null,
        ], JSON_UNESCAPED_UNICODE);

        DB::table('devices')->updateOrInsert(
            ['device_id' => $deviceId],
            [
                'info'       => $info,
                'fw_version' => $data['version'] ?? null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    // ── Update feedback device di DB ──
    public function updateFeedback(string $deviceId, array $data): void
    {
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

        $column = match($mode) {
            1       => 'last_command',
            2       => 'last_setting',
            default => 'last_status',
        };

        DB::table('devices')
            ->where('device_id', $deviceId)
            ->update([
                'fw_version' => $version,
                $column      => $detailJson,
                'last_seen'  => now(),
                'updated_at' => now(),
            ]);
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
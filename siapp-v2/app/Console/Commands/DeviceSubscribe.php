<?php

namespace App\Console\Commands;

use App\Services\DeviceService;
use Bluerhinos\phpMQTT;
use Illuminate\Console\Command;

class DeviceSubscribe extends Command
{
    protected $signature   = 'mqtt:device';
    protected $description = 'Jalankan MQTT subscriber untuk manajemen device';

    public function handle(DeviceService $service): void
    {
        $host     = config('mqtt.host');
        $port     = config('mqtt.port');
        $username = config('mqtt.username');
        $password = config('mqtt.password');
        $clientId = 'siapp_device_' . gethostname() . '_' . getmypid();

        $this->info('[' . now() . '] Memulai Device subscriber | Client: ' . $clientId);

        $topics = [
            'devices/+/status'   => ['qos' => 0, 'function' => function ($topic, $msg) use ($service) {
                $this->prosesMsg($topic, $msg, $service);
            }],
            'devices/+/update'   => ['qos' => 0, 'function' => function ($topic, $msg) use ($service) {
                $this->prosesMsg($topic, $msg, $service);
            }],
            'devices/+/info'     => ['qos' => 0, 'function' => function ($topic, $msg) use ($service) {
                $this->prosesMsg($topic, $msg, $service);
            }],
            'devices/+/feedback' => ['qos' => 0, 'function' => function ($topic, $msg) use ($service) {
                $this->prosesMsg($topic, $msg, $service);
            }],
            'devices/+/reqset'   => ['qos' => 0, 'function' => function ($topic, $msg) use ($service) {
                $this->prosesMsg($topic, $msg, $service);
            }],
        ];

        $mqtt = new phpMQTT($host, $port, $clientId);

        while (true) {
            if (!$mqtt->connect(true, null, $username, $password)) {
                $this->error('[' . now() . '] Koneksi broker gagal. Retry 5 detik...');
                sleep(5);
                continue;
            }

            $this->info('[' . now() . '] Terhubung ke broker ' . $host . ':' . $port);
            $mqtt->subscribe($topics, 0);
            $this->info('[' . now() . '] Subscribe ke 5 device management topic.');

            while ($mqtt->proc()) {
                usleep(50000);
            }

            $this->warn('[' . now() . '] Koneksi terputus. Reconnect...');
            $mqtt->close();
            sleep(3);
        }
    }

    private function prosesMsg(string $topic, string $msg, DeviceService $service): void
    {
        $parts      = explode('/', $topic);
        $topicType  = $parts[2] ?? '';

        $data = json_decode($msg, true);

        if (!is_array($data)) {
            $this->error('[' . now() . '] JSON tidak valid dari topic: ' . $topic);
            return;
        }

        $deviceId = $data['device_id'] ?? ($parts[1] ?? '');
        $status   = $data['status'] ?? 'unknown';
        $online   = ($status === 'online') ? 1 : 0;

        $this->line('[' . now() . '] ' . $topicType . ' dari ' . $deviceId);

        match ($topicType) {
            'status'   => $service->updateStatus($deviceId, $data, $online),
            'update'   => $service->updateStatus($deviceId, $data, $online),
            'info'     => $service->updateInfo($deviceId, $data),
            'feedback' => $service->updateFeedback($deviceId, $data),
            'reqset'   => $service->kirimSetting($deviceId),
            default    => $this->warn('Topic tidak dikenal: ' . $topicType),
        };

        $service->simpanLog($deviceId, $topic, $msg);
    }
}
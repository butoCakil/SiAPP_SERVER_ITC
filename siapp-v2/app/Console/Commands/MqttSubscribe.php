<?php

namespace App\Console\Commands;

use App\Services\PresensiService;
use Bluerhinos\phpMQTT;
use Illuminate\Console\Command;

class MqttSubscribe extends Command
{
    protected $signature   = 'mqtt:subscribe';
    protected $description = 'Jalankan MQTT subscriber untuk presensi RFID';

    public function handle(PresensiService $service): void
    {
        $host     = config('mqtt.host');
        $port     = config('mqtt.port');
        $username = config('mqtt.username');
        $password = config('mqtt.password');
        $clientId = 'siapp_sub_' . gethostname() . '_' . getmypid();

        $this->info('[' . now() . '] Memulai MQTT subscriber | Client: ' . $clientId);

        $mqtt = new phpMQTT($host, $port, $clientId);

        while (true) {
            if (!$mqtt->connect(true, null, $username, $password)) {
                $this->error('[' . now() . '] Koneksi broker gagal. Retry 5 detik...');
                sleep(5);
                continue;
            }

            $this->info('[' . now() . '] Terhubung ke broker ' . $host . ':' . $port);

            $devices = \App\Models\RegDevice::where('status', 'terdaftar')->pluck('no_device')->toArray();

            if (empty($devices)) {
                $this->warn('[' . now() . '] Tidak ada device terdaftar.');
                sleep(10);
                continue;
            }

            $topics = [];
            foreach ($devices as $nodevice) {
                $topics['dariMCU/' . $nodevice] = [
                    'qos'      => 0,
                    'function' => function ($topic, $msg) use ($service, $mqtt) {
                        $this->prosesMsg($topic, $msg, $service, $mqtt);
                    },
                ];
            }

            $mqtt->subscribe($topics, 0);
            $this->info('[' . now() . '] Subscribe ke ' . count($devices) . ' device topic.');

            while ($mqtt->proc()) {
                // loop utama
            }

            $this->warn('[' . now() . '] Koneksi terputus. Reconnect...');
            sleep(2);
        }
    }

    private function prosesMsg(string $topic, string $msg, PresensiService $service, phpMQTT $mqtt): void
    {
        $timestamp = now()->toDateTimeString();
        $this->line('[' . $timestamp . '] Pesan dari: ' . $topic . ' => ' . $msg);

        $json = json_decode($msg, true);

        if (!is_array($json)) {
            $this->error('[' . $timestamp . '] Pesan tidak valid (bukan JSON)');
            $nodevice = '';
            $this->kirimRespon($mqtt, $nodevice, $this->errorRespon('505', 'Pesan tidak valid'));
            return;
        }

        $nodevice = $json['nodevice'] ?? '';

        if (!$nodevice) {
            $this->error('[' . $timestamp . '] nodevice kosong');
            $this->kirimRespon($mqtt, '', $this->errorRespon('407', 'nodevice kosong'));
            return;
        }

        $result   = $service->prosesTag($json);
        $response = json_encode($result);

        $this->kirimRespon($mqtt, $nodevice, $response);
        $this->info('[' . $timestamp . '] Respon ke ' . $nodevice . ': ' . $response);
    }

    private function kirimRespon(phpMQTT $mqtt, string $nodevice, string $payload): void
    {
        $topic    = 'responServer/' . $nodevice;
        $clientId = 'siapp_pub_' . $nodevice . '_' . substr(md5(microtime()), 0, 6);
        $pub      = new phpMQTT(config('mqtt.host'), config('mqtt.port'), $clientId);

        for ($i = 1; $i <= 2; $i++) {
            if ($pub->connect(true, null, config('mqtt.username'), config('mqtt.password'))) {
                $pub->publish($topic, $payload, 0);
                $pub->proc();
                $pub->close();
                $this->line('Terkirim ke ' . $topic . ' (attempt ' . $i . ')');
                return;
            }
            usleep(100000);
        }

        $this->error('Gagal kirim ke ' . $topic . ' setelah 2 percobaan');
    }

    private function errorRespon(string $code, string $info): string
    {
        return json_encode(['respon' => [['message' => $code, 'info' => $info]]]);
    }
}
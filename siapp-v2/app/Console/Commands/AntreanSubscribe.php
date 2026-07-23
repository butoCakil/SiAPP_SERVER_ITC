<?php

namespace App\Console\Commands;

use App\Services\BatchUploadService;
use Bluerhinos\phpMQTT;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AntreanSubscribe extends Command
{
    protected $signature   = 'mqtt:antrean';
    protected $description = 'Jalankan MQTT subscriber untuk antrean presensi (transport baru, migrasi bertahap dari HTTP)';

    public function handle(BatchUploadService $service): void
    {
        $host     = config('mqtt.host');
        $port     = config('mqtt.port');
        $username = config('mqtt.username');
        $password = config('mqtt.password');

        // Client ID TETAP (bukan random per proses) -- supaya persistent session
        // (clean_session=false) konsisten dikenali broker antar restart command ini.
        $clientId = 'siapp_antrean_sub';

        $this->info('[' . now() . '] Memulai Antrean subscriber | Client: ' . $clientId);

        $mqtt = new phpMQTT($host, $port, $clientId);

        while (true) {
            // clean_session = false: beda dari mqtt:subscribe/mqtt:device yang sengaja
            // ephemeral. Lihat catatan desain -- manfaat utamanya baru terasa kalau
            // suatu saat firmware upgrade ke library MQTT yang mendukung publish QoS 1.
            if (!$mqtt->connect(false, null, $username, $password)) {
                $this->error('[' . now() . '] Koneksi broker gagal. Retry 5 detik...');
                sleep(5);
                continue;
            }

            $this->info('[' . now() . '] Terhubung ke broker ' . $host . ':' . $port);

            $topics = [
                'antrean/+/data' => [
                    'qos'      => 0,
                    'function' => function ($topic, $msg) use ($service) {
                        $this->prosesBatch($topic, $msg, $service);
                    },
                ],
            ];

            $mqtt->subscribe($topics, 0);
            $this->info('[' . now() . '] Subscribe ke antrean/+/data');

            while ($mqtt->proc()) {
                usleep(50000);
            }

            $this->warn('[' . now() . '] Koneksi terputus. Reconnect...');
            $mqtt->close();
            sleep(3);
        }
    }

    private function prosesBatch(string $topic, string $msg, BatchUploadService $service): void
    {
        $timestamp = now()->toDateTimeString();
        $this->line('[' . $timestamp . '] Pesan dari: ' . $topic);

        $json = json_decode($msg, true);

        if (!is_array($json)) {
            $this->error('[' . $timestamp . '] Payload tidak valid (bukan JSON), diabaikan');
            return;  // tidak bisa ack -- tidak tahu nodevice/batch_id tujuan
        }

        $nodevice = $json['nodevice'] ?? '';
        $batchId  = $json['batch_id'] ?? '';
        $items    = $json['data'] ?? [];

        if (!$nodevice || !$batchId || !is_array($items)) {
            $this->error('[' . $timestamp . '] nodevice/batch_id/data kosong, diabaikan');
            return;
        }

        // === BARU: routing berdasarkan prefix nodevice ===
        // IM.../IZ... belum ada handler (firmware belum punya fungsi Izin/Izin Mens
        // sama sekali -- lihat catatan audit sebelumnya). G... -> GATE. Selain itu -> sholat.
        $macAddress = $json['macAddress'] ?? '';
        $timestamp  = $json['timestamp'] ?? now()->format('Y-m-d H:i:s');

        if (preg_match('/^G/i', $nodevice)) {
            $r = $service->prosesPresensi($items, $nodevice, $macAddress, $timestamp);
            $result = [
                'inserted' => $r['inserted'],
                'updated'  => $r['updated'],
                'skipped'  => $r['skipped'],
                'errors'   => $r['errors'],
            ];
        } else {
            $r = $service->prosesSholat($items, $nodevice);
            $result = [
                'inserted' => $r['inserted'],
                'updated'  => 0,  // sholat tidak ada konsep 'updated'
                'skipped'  => $r['skipped'],
                'errors'   => $r['errors'],
            ];
        }

        // === Log ke tempreq (pengganti backup JSON untuk jalur ini, aman utk banyak penulis) ===
        try {
            DB::table('tempreq')->insert([
                'ip'     => $nodevice,
                'req'    => json_encode($json),
                'info'   => "antrean: inserted={$result['inserted']} updated={$result['updated']} skipped={$result['skipped']}",
                'detail' => json_encode($result),
            ]);
        } catch (\Throwable $e) {
            $this->warn('Gagal log tempreq: ' . $e->getMessage());
        }

        // === Kirim ack balik ke device ===
        $ackPayload = json_encode([
            'batch_id' => $batchId,
            'status'   => 'ok',
            'inserted' => $result['inserted'],
            'updated'  => $result['updated'],
            'skipped'  => $result['skipped'],
        ]);

        $this->kirimAck($nodevice, $ackPayload);
        $this->info('[' . $timestamp . '] Ack ke antrean/' . $nodevice . '/ack (batch ' . $batchId . '): ' . $ackPayload);
    }

    // Koneksi PUBLISH terpisah dari koneksi SUBSCRIBE -- meniru pola
    // MqttSubscribe::kirimRespon(), bukan reuse koneksi yang sedang proc().
    private function kirimAck(string $nodevice, string $payload): void
    {
        $topic    = 'antrean/' . $nodevice . '/ack';
        $clientId = 'siapp_antrean_pub_' . $nodevice . '_' . substr(md5(microtime()), 0, 6);
        $pub      = new phpMQTT(config('mqtt.host'), config('mqtt.port'), $clientId);

        for ($i = 1; $i <= 2; $i++) {
            if ($pub->connect(true, null, config('mqtt.username'), config('mqtt.password'))) {
                $pub->publish($topic, $payload, 0);
                $pub->proc();
                $pub->close();
                $this->line('Ack terkirim ke ' . $topic . ' (attempt ' . $i . ')');
                return;
            }
            usleep(100000);
        }

        $this->error('Gagal kirim ack ke ' . $topic . ' setelah 2 percobaan');
    }
}

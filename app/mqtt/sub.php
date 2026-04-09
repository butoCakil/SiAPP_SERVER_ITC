<?php
date_default_timezone_set('Asia/Jakarta');
require "vendor/autoload.php";

$host = "localhost";
$port = 1883;
$username = "ben";
$password = "1234";

// ── Client ID stabil: hostname + PID ──
// Berbeda antar proses (tidak saling kick), tapi konsisten dalam satu proses
// Format: sub_tag_<hostname>_<pid>
$client_id = "sub_tag_" . gethostname() . "_" . getmypid();
// $client_id = "php_subscriber_tag"; // FIXED (bukan rand)

echo "Client ID: $client_id\n";

$mqtt = new Bluerhinos\phpMQTT($host, $port, $client_id);

while (true) {

    if ($mqtt->connect(true, NULL, $username, $password)) {

        echo "Terhubung ke broker MQTT.\n";
        echo "Mulai MQTT: " . date('Y-m-d H:i:s') . "\n";

        $topics = [
            'dariMCU/+' => ["qos" => 0, "function" => "procmsg"]
        ];

        $mqtt->subscribe($topics, 0);

        while ($mqtt->proc()) {
            // ── 10ms loop (dari 50ms) ──
            // 50ms terlalu lambat: jika ESP32 publish dan langsung
            // subscribe menunggu, 50ms delay di sini = 50ms tambahan
            // sebelum procmsg() dipanggil
            usleep(10000); // 10ms
            // usleep(50000); // 50ms agar tidak CPU spike
        }

        echo "[" . date('Y-m-d H:i:s') . "] Reconnecting MQTT...\n";
        $mqtt->close(); // pastikan close sebelum reconnect
        sleep(2);
    } else {
        echo "Koneksi ke broker gagal. Retry 5 detik.\n";
        sleep(5);
    }
}

function procmsg($topic, $msg)
{
    try {
        global $mqtt;
        global $jsonString; // tetap pakai global seperti awal
        global $text_log;
        global $nodevice;

        $text_log = "";
        $timestamp = date('Y-m-d H:i:s');

        $text_log .= "[$timestamp]\nMenerima pesan dari: $topic - Pesan: $msg\n";

        // ── Ekstrak nodevice dari topic SEBELUM validasi ──
        // Format topic: dariMCU/{nodevice}
        $parts    = explode('/', $topic);
        $nodevice = isset($parts[1]) ? $parts[1] : '';

        if (!$nodevice) {
            simpanLogTag("[$timestamp] Tidak bisa ekstrak nodevice dari topic: $topic");
            $jsonString = buatErrorResponse("407", "nodevice kosong dari topic");
            include "pub.php";
            return;
        }

        if (strlen($msg) > 10000) {
            simpanLogTag("[$timestamp] Payload terlalu besar-> $topic: $msg");
            $jsonString = buatErrorResponse("404", "Payload terlalu besar");
            include "pub.php";
            return;
        }

        $json = json_decode($msg, true);

        if (!is_array($json)) {
            simpanLogTag("[$timestamp] Format JSON tidak valid-> $topic: $msg");
            $jsonString = buatErrorResponse("405", "Format JSON tidak valid");
            include "pub.php";
            return;
        }

        $nokartu   = $json['nokartu']   ?? '';
        $idchip    = $json['idchip']    ?? '';
        $nodevice  = $json['nodevice'] ?? $nodevice;
        $key       = $json['key']       ?? '';
        $ip_a      = $json['ipa']       ?? '';

        if (!$nodevice) {
            simpanLogTag("[$timestamp] nodevice kosong-> $topic: $msg");
            $jsonString = buatErrorResponse("407", "nodevice kosong");
            include "pub.php";
            return;
        }

        $url = 'http://localhost/app/directagJSON.php';

        $urlWithParams = $url . '?' . http_build_query([
            'nokartu'  => $nokartu,
            'idchip'   => $idchip,
            'nodevice' => $nodevice,
            'key'      => $key,
            'ipa'      => $ip_a
        ]);

        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'follow_location' => 0
            ]
        ]);

        $response = @file_get_contents($urlWithParams, false, $context);

        if (!$response) {

            $jsonString = buatErrorResponse("505", "Server tidak merespon");

            include "pub.php";

            simpanLogTag("[$timestamp] HTTP gagal untuk $nodevice");

            return; // jangan close MQTT
        }

        if (!is_array(json_decode($response, true))) {
            simpanLogTag("[$timestamp] Respon server tidak valid-> $topic: $response");
            $jsonString = buatErrorResponse("505", "Respon server tidak valid");
            include "pub.php";
            return;
        }

        $text_log .= "[$timestamp]\nBerhasil merespon \"$topic\" : $response\n";

        $jsonString = $response; // PENTING: tetap sama seperti awal

        include "pub.php";

        unset($json);
        unset($response);
    } catch (Throwable $e) {
        $timestamp = date('Y-m-d H:i:s');
        $error = "[$timestamp] ERROR CALLBACK: " . $e->getMessage() . "\n";

        echo $error;
        file_put_contents(
            "/opt/lampp/htdocs/app/mqtt/log/tag_" . date("Y-m-d") . ".log",
            $error,
            FILE_APPEND | LOCK_EX
        );
    }
}

function simpanLogTag($text_log)
{
    $tanggal = date("Y-m-d");
    $lokasi_penyimpanan = "/opt/lampp/htdocs/app/mqtt/log/";
    $nama_file_log = $lokasi_penyimpanan . "tag_" . $tanggal . ".log";

    file_put_contents($nama_file_log, $text_log, FILE_APPEND | LOCK_EX);
}

function buatErrorResponse($message, $info)
{
    $array = array(
        "0" => array(
            "message" => $message,
            "info"    => $info
        )
    );

    return json_encode(array('respon' => $array));
}

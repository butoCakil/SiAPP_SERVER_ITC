<?php
mysqli_report(MYSQLI_REPORT_OFF);

require "vendor/autoload.php"; // pastikan composer autoload

use Bluerhinos\phpMQTT;

// --- Konfigurasi MQTT ---
$server   = "localhost";
$port     = 1883;
$username = "ben";
$password = "1234";
$client_id = "php_subscriber_device";

// --- Aktifkan log ke file ---
$log_file = __DIR__ . "/device_error.log";
ini_set("log_errors", true);
ini_set("error_log", $log_file);

// Tambahkan hanya error echo ke log
function writeLog($text, $isError = false)
{
    global $log_file;
    $timestamp = "[" . date("Y-m-d H:i:s") . "] ";

    // Tampilkan semua ke terminal
    echo $timestamp . $text . "\n";

    // Tapi simpan ke file hanya jika error
    if ($isError) {
        file_put_contents($log_file, $timestamp . $text . "\n", FILE_APPEND);
    }
}

writeLog("=== MULAI SUBSCRIBER ===");
writeLog("Subscriber MQTT aktif... menunggu pesan...");

// --- Subscribe ke semua status device ---
// $topics['devices/+/status'] = ["qos" => 0, "function" => "procMsg"];
$topics = [
    'devices/+/status'   => ["qos" => 0, "function" => "procMsg"],
    'devices/+/update'   => ["qos" => 0, "function" => "procMsg"],
    'devices/+/info'   => ["qos" => 0, "function" => "procMsg"],
    'devices/+/feedback' => ["qos" => 0, "function" => "procMsg"],
    'devices/+/reqset' => ["qos" => 0, "function" => "procMsg"]
];


// $mqtt->subscribe($topics, 0);

writeLog("=== MULAI SUBSCRIBER ===");

$mqtt = new phpMQTT($server, $port, $client_id);


// --- Loop ---
while (true) {
    if (!$mqtt->connect(true, NULL, $username, $password)) {
        writeLog("Gagal konek broker. Retry 5 detik...");
        sleep(5);
        continue;
    }

    writeLog("Connected ke broker.");

    $mqtt->subscribe($topics, 0);

    while ($mqtt->proc()) {
        usleep(50000); // lebih responsif, CPU tetap aman
    }

    writeLog("Koneksi MQTT terputus.");
    $mqtt->close();
    sleep(3);
}

// while ($mqtt->proc()) {
// akan memanggil procMsg() setiap ada pesan
// }

$mqtt->close();

function dbConnect()
{
    $host = "localhost";
    $user = "siap";
    $pass = "skanebaSMKBOS$";
    $dbs  = "siap";

    $konek = mysqli_connect($host, $user, $pass, $dbs);

    if (!$konek) {
        writeLog("Gagal konek DB: " . mysqli_connect_error(), true);
        return false;
    }

    return $konek;
}

// --- Callback ketika pesan diterima ---
function procMsg($topic, $msg)
{
    // writeLog("=== CALLBACK DIPANGGIL ===");
    // writeLog("Pesan diterima dari topic: {$topic} | isi: {$msg}");

    static $konek = null;

    // --- VALIDASI JSON (tidak ubah perilaku)
    $data = json_decode($msg, true);
    if (!is_array($data)) {
        writeLog("JSON tidak valid dari topic $topic", true);
        return;
    }

    // --- Ambil sekali saja
    // --- Deteksi jenis topik ---
    $topic_parts = explode('/', $topic);
    $topic_type  = $topic_parts[2] ?? '';
    $device_id   = $data['device_id'] ?? ($topic_parts[1] ?? '');
    
    $status = $data['status'] ?? 'unknown';
    $online = ($status == 'online') ? 1 : 0;

    // --- Koneksi ke database ---
    $konek = dbConnect();
    if (!$konek) {
        return;
    }

    // ============================================================
    // PENANGANAN devices/+/status
    // ============================================================
    if ($topic_type === "status") {

        // --- Simpan/update devices ---
        $status = $data['status'] ?? 'unknown';
        $ram = $data['ram'] ?? null;
        $ssid = $data['ssid'] ?? null;
        $rssi = $data['rssi'] ?? null;
        $latency = $data['latency'] ?? null;
        $serial = $data['serial'] ?? null;
        $version = $data['version'] ?? null;
        $last_status_json = json_encode([
            'status' => $status,
            'ram' => $ram,
            'ssid' => $ssid,
            'rssi' => $rssi,
            'latency' => $latency,
            'serial' => $serial,
            'version' => $version
        ], JSON_UNESCAPED_UNICODE);

        $cekSql = "SELECT online FROM devices WHERE device_id = '$device_id' LIMIT 1";
        $result = mysqli_query($konek, $cekSql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if ($row['online'] == 1) {
            } else {
                // Device sebelumnya offline → update waktu online_since
                $updateTimeSql = "
                UPDATE devices 
                SET online_since = NOW()
                WHERE device_id = '$device_id'
            ";
                mysqli_query($konek, $updateTimeSql);
            }
        }

        if ($version !== null && $version !== '') {
            $sql = "
                INSERT INTO devices (device_id, last_status, last_seen, online, fw_version)
                VALUES ('$device_id', '$last_status_json', NOW(), $online, '$version')
                ON DUPLICATE KEY UPDATE
                last_status = VALUES(last_status),
                last_seen = NOW(),
                online = VALUES(online),
                fw_version = VALUES(fw_version)
            ";
        } else {
            $sql = "
                INSERT INTO devices (device_id, last_status, last_seen, online)
                VALUES ('$device_id', '$last_status_json', NOW(), $online)
                ON DUPLICATE KEY UPDATE
                last_status = VALUES(last_status),
                last_seen = NOW(),
                online = VALUES(online)
            ";
        }

        if (mysqli_query($konek, $sql)) {
            // writeLog("Status $device_id diupdate jadi $status");
        } else {
            writeLog("Gagal update devices: " . mysqli_error($konek), true);
            writeLog("SQL: " . $sql, true);
        }
    } elseif ($topic_type === "update") {

        $status  = $data['status'] ?? 'unknown';
        $ram     = $data['ram'] ?? null;
        $rssi    = $data['rssi'] ?? null;
        $latency = $data['latency'] ?? null;

        $last_status_json = json_encode([
            'status'  => $status,
            'ram'     => $ram,
            'rssi'    => $rssi,
            'latency' => $latency
        ], JSON_UNESCAPED_UNICODE);

        // cek status sebelumnya
        $cekSql = "SELECT online FROM devices WHERE device_id = '$device_id' LIMIT 1";
        $result = mysqli_query($konek, $cekSql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if ((int)$row['online'] === 0 && $online === 1) {
                // OFFLINE → ONLINE
                mysqli_query(
                    $konek,
                    "UPDATE devices SET online_since = NOW() WHERE device_id = '$device_id'"
                );
            }
        }

        $sql = "
        INSERT INTO devices (device_id, last_status, last_seen, online)
        VALUES ('$device_id', '$last_status_json', NOW(), $online)
        ON DUPLICATE KEY UPDATE
            last_status = VALUES(last_status),
            last_seen   = NOW(),
            online      = VALUES(online)
    ";

        if (mysqli_query($konek, $sql)) {
            // writeLog("STATUS $device_id updated ($status)");
        } else {
            writeLog("STATUS update failed: " . mysqli_error($konek), true);
        }
    } elseif ($topic_type === "info") {

        $ssid    = $data['ssid'] ?? null;
        $serial  = $data['serial'] ?? null;
        $version = $data['version'] ?? null;

        $info_json = json_encode([
            'ssid'    => $ssid,
            'serial'  => $serial,
            'version' => $version
        ], JSON_UNESCAPED_UNICODE);

        $sql = "
        INSERT INTO devices (device_id, info, fw_version)
        VALUES ('$device_id', '$info_json', '$version')
        ON DUPLICATE KEY UPDATE
            info       = VALUES(info),
            fw_version = VALUES(fw_version)
    ";

        if (mysqli_query($konek, $sql)) {
            // writeLog("INFO $device_id updated");
        } else {
            writeLog("INFO update failed: " . mysqli_error($konek), true);
        }
    }

    // ============================================================
    // PENANGANAN devices/+/feedback 
    // ============================================================
    elseif ($topic_type === "feedback") {
        $mode   = $data['mode'] ?? 0;
        $device_id   = $data['device_id'] ?? '';
        $status = $data['status'] ?? '';
        $detail = $data['detail'] ?? '';
        $timestamp = $data['timestamp'] ?? '';
        $version = $data['version'] ?? '';

        $device_id_esc = mysqli_real_escape_string($konek, $device_id);
        $version_esc   = mysqli_real_escape_string($konek, $version);
        
        // Jika detail berupa string JSON, decode dulu agar bisa disimpan sebagai objek
        $decoded_detail = json_decode($detail, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $detail = $decoded_detail;
        }

        // Susun JSON final untuk disimpan (tanpa double-encode)
        $detail_json = json_encode([
            'status' => $status,
            'detail' => $detail,
            'device_id' => $device_id,
            'version' => $version,
            'timestamp'   => $timestamp,
        ], JSON_UNESCAPED_UNICODE);

        // Escape sebelum masuk SQL
        $detail_json = mysqli_real_escape_string($konek, $detail_json);

        // Tentukan kolom berdasarkan mode
        if ($mode == 1) {
            $sql = "UPDATE devices
            SET fw_version = '$version_esc', last_command = '$detail_json',
                last_seen = NOW()
            WHERE device_id = '$device_id_esc'";
        } elseif ($mode == 2) {
            $sql = "UPDATE devices
            SET fw_version = '$version_esc', last_setting = '$detail_json',
                last_seen = NOW()
            WHERE device_id = '$device_id_esc'";
        } else {
            // mode lain bisa disimpan ke last_status sebagai fallback
            $sql = "UPDATE devices
            SET fw_version = '$version_esc', last_status = '$detail_json',
                last_seen = NOW()
            WHERE device_id = '$device_id_esc'";
        }

        if (!mysqli_query($konek, $sql)) {
            writeLog("Gagal update feedback untuk $device_id: " . mysqli_error($konek), true);
        } else {
            // writeLog("Feedback $device_id (mode $mode) disimpan ke tabel devices");
        }
    } elseif ($topic_type === "reqset") {
        // writeLog("📩 Permintaan setting diterima dari $device_id");

        if (!$device_id) {
            writeLog("REQSET gagal: device_id kosong", true);
            return;
        }

        // Siapkan URL endpoint yang akan dipanggil
        $base_url = "http://172.16.80.123/app/mqtt/send_setting.php";
        $params = http_build_query([
            'device_id'   => $device_id,
            'setSetting'  => 1,
            'reboot'      => '',
            'sync'        => '',
            'upload'      => '',
            'token'       => 'e807f1fcf82d132f9bb018ca6738a19f'
        ]);
        $url = $base_url . '?' . $params;

        writeLog("🔗 Mengirim request ke: $url");

        // Eksekusi URL (tanpa blocking lama)
        // $response = @file_get_contents($url);

        $context = stream_context_create([
            'http' => [
                'timeout' => 2,
                'ignore_errors' => true
            ]
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === FALSE) {
            writeLog("❌ Gagal memanggil $url", true);
        } else {
            writeLog("✅ Respon send_setting.php: " . substr($response, 0, 120) . "...");
        }
    }


    // --- Simpan ke device_logs ---
    $topic_esc = mysqli_real_escape_string($konek, $topic);
    $msg_esc = mysqli_real_escape_string($konek, $msg);

    $sql2 = "
        INSERT INTO device_logs (device_id, topic, payload, received_at)
        VALUES ('$device_id', '$topic_esc', '$msg_esc', NOW())
    ";

    if (mysqli_query($konek, $sql2)) {
        // writeLog("Log device $device_id disimpan");
    } else {
        writeLog("Gagal simpan log: " . mysqli_error($konek), true);
    }

    mysqli_close($konek);
}

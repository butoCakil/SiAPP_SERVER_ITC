<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require "vendor/autoload.php";

use Bluerhinos\phpMQTT;

/* =========================================================
   KONFIGURASI
========================================================= */

$server    = "localhost";
$port      = 1883;
$username  = "ben";
$password  = "1234";
$client_id = "php_db_publisher_" . uniqid();

$db_host = "localhost";
$db_user = "siap";
$db_pass = "skanebaSMKBOS$";
$db_name = "siap";


/* =========================================================
   HELPER FUNCTIONS
========================================================= */

function getStatusCode($type)
{
    switch ($type) {
        case 'info':
            return 200;
        case 'error':
            return 400;
        case 'unauthorized':
            return 401;
        case 'forbidden':
            return 403;
        default:
            return 503;
    }
}

function logMsg($msg, $type = 'info')
{
    static $lastLogs = [];

    $timestamp = date("Y-m-d H:i:s");
    $formatted = "[$timestamp][$type] $msg";
    $logFile = __DIR__ . '/send_setting.log';

    // Hindari spam log yang sama dalam 2 detik
    $hash = md5($formatted);
    $now = time();
    if (isset($lastLogs[$hash]) && ($now - $lastLogs[$hash] < 2)) {
        return;
    }
    $lastLogs[$hash] = $now;

    // Rotasi jika > 5MB
    if (file_exists($logFile) && filesize($logFile) > 5 * 1024 * 1024) {
        rename($logFile, $logFile . '.' . time() . '.bak');
    }

    // Buat file jika belum ada
    if (!file_exists($logFile)) {
        $header = "=== Log File Created at $timestamp ===" . PHP_EOL;
        file_put_contents($logFile, $header);
        chmod($logFile, 0644);
    }

    file_put_contents($logFile, $formatted . PHP_EOL, FILE_APPEND | LOCK_EX);
}

function respondJSON($statusCode, $status, $msg, $data = null)
{
    header('Content-Type: application/json');
    http_response_code($statusCode);

    logMsg($msg, $status);

    $response = [
        'status' => $status
    ];

    if ($data !== null) {
        $response['data'] = $data;
    }

    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}


/* =========================================================
   AMBIL PARAMETER
========================================================= */

$device_id   = $_GET['device_id'] ?? null;
$token       = $_GET['token'] ?? null;
$reboot      = isset($_GET['reboot']) ? boolval($_GET['reboot']) : false;
$sync        = isset($_GET['sync']) ? boolval($_GET['sync']) : false;
$upload      = isset($_GET['upload']) ? boolval($_GET['upload']) : false;
$setSetting  = isset($_GET['setSetting']) ? boolval($_GET['setSetting']) : false;
$toggleSerial = isset($_GET['toggleSerial']) ? intval($_GET['toggleSerial']) : null;


/* =========================================================
   VALIDASI device_id (MASALAH #5)
========================================================= */

if (!$device_id) {
    respondJSON(400, 'error', 'device_id tidak ditemukan');
}

if (!preg_match('/^[A-Za-z0-9_\-]+$/', $device_id)) {
    respondJSON(400, 'error', 'device_id tidak valid');
}


/* =========================================================
   VALIDASI JUMLAH PERINTAH
========================================================= */

$perintahAktif = [$reboot, $sync, $upload, $setSetting, $toggleSerial];
$jumlahPerintah = count(array_filter($perintahAktif));

if ($jumlahPerintah > 1) {
    respondJSON(400, 'error', "Terlalu banyak perintah => " . print_r($_GET, true));
}

if ($jumlahPerintah === 0) {
    respondJSON(400, 'error', "Tidak ada perintah => " . print_r($_GET, true));
}


/* =========================================================
   VALIDASI TOKEN
========================================================= */

if (!$token) {
    respondJSON(401, 'unauthorized', "Token tidak ditemukan => " . print_r($_GET, true));
}


/* =========================================================
   KONEKSI DATABASE
========================================================= */

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    respondJSON(500, 'error', "Koneksi database gagal: " . $conn->connect_error);
}


/* =========================================================
   VALIDASI TOKEN DARI DB
========================================================= */

$sql = "SELECT kode_api FROM api LIMIT 1";
$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    respondJSON(503, 'error', "Layanan tidak tersedia");
}

$row = $result->fetch_assoc();
$kode_api = $row['kode_api'];

if (!hash_equals($kode_api, $token)) {
    respondJSON(403, 'forbidden', "Token tidak valid => " . print_r($_GET, true));
}


/* =========================================================
   AMBIL DATA SETTING JIKA DIMINTA
========================================================= */

$settings = [];

if ($setSetting) {

    $sql = "SELECT mode, wa, wta, wtp, wp, waktumasuk, waktupulang, info FROM statusnya LIMIT 1";
    $result = $conn->query($sql);

    if (!$result || $result->num_rows === 0) {
        respondJSON(400, 'error', "Tidak ada data setting tersedia");
    }

    $row = $result->fetch_assoc();

    $settings = [
        "mode"         => (int)$row["mode"],
        "wa"           => $row["wa"],
        "wta"          => $row["wta"],
        "wtp"          => $row["wtp"],
        "wp"           => $row["wp"],
        "waktumasuk"   => $row["waktumasuk"],
        "waktupulang"  => $row["waktupulang"],
        "info"         => $row["info"]
    ];
}


/* =========================================================
   SIAPKAN COMMAND
========================================================= */

$commands = [];

if ($reboot || $sync || $upload || $toggleSerial) {
    $commands = [
        "reboot" => $reboot,
        "sync"   => $sync,
        "upload" => $upload,
        "toggleSerial" => $toggleSerial
    ];
}


/* =========================================================
   SUSUN PAYLOAD MQTT
========================================================= */

$payloadData = [
    "device_id" => $device_id
];

if (!empty($settings)) $payloadData["settings"] = $settings;
if (!empty($commands)) $payloadData["command"] = $commands;

$payload = json_encode($payloadData, JSON_UNESCAPED_UNICODE);


/* =========================================================
   PUBLISH KE MQTT
========================================================= */

$mqtt = new phpMQTT($server, $port, $client_id);

if (!$mqtt->connect(true, NULL, $username, $password, 5)) {
    respondJSON(500, 'error', "Gagal terhubung ke broker MQTT");
}

$topic = "devices/{$device_id}/settings";

try {
    $mqtt->publish($topic, $payload, 0);
    $mqtt->close();
    $conn->close();

    respondJSON(200, 'info', "Payload berhasil dikirim ke {$topic}", $payload);
} catch (Exception $e) {
    $mqtt->close();
    $conn->close();
    respondJSON(500, 'error', "Gagal publish: " . $e->getMessage());
}

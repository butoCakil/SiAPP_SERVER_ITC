<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Jakarta');
header('Content-Type: application/json');

if (!file_exists(__DIR__ . "/../config/konesi.php")) {
    echo json_encode(["status" => "error", "message" => "File koneksi tidak ditemukan"]);
    exit;
}
require_once "../config/konesi.php";

if (!isset($konek) || !$konek instanceof mysqli) {
    echo json_encode(["status" => "error", "message" => "Koneksi database tidak valid"]);
    exit;
}

$json = file_get_contents("php://input");
$data = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["status" => "error", "message" => "Format JSON rusak: " . json_last_error_msg()]);
    exit;
}

if (!$data || !isset($data['data']) || !is_array($data['data'])) {
    echo json_encode(["status" => "error", "message" => "Data JSON tidak valid"]);
    exit;
}

// --- Meta Data ---
$meta_nodevice   = $data['nodevice']   ?? '0';
$meta_chipID     = $data['chipID']     ?? '';
$meta_macAddress = $data['macAddress'] ?? ($_SERVER['HTTP_X_MAC'] ?? 'unknown');
$meta_ipAddress  = $data['ipAddress']  ?? ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
$meta_timestamp  = $data['timestamp']  ?? date('Y-m-d H:i:s');

// --- Lokasi File JSON Backup ---
$folder = __DIR__ . "/jsonIzinSholat";
if (!is_dir($folder) && !mkdir($folder, 0777, true)) {
    echo json_encode(["status" => "error", "message" => "Gagal membuat folder penyimpanan"]);
    exit;
}
$today     = date("Y-m-d");
$json_file = "$folder/izinmens-$today.json";

if (file_exists($json_file)) {
    $raw = file_get_contents($json_file);
    $json_content = json_decode($raw, true);
    if (!is_array($json_content)) $json_content = [];
} else {
    $json_content = [];
}
if (!isset($json_content['data']) || !is_array($json_content['data'])) {
    $json_content['data'] = [];
}

$inserted = 0;
$skipped  = 0;
$errors   = [];

$RUANG = "Izin Mens";

$stmt = $konek->prepare("
    INSERT INTO presensiEvent
        (nokartu, nis, ruang, mulai, selesai, jam, tanggal, keterangan)
    VALUES
        (?, ?, ?, ?, NULL, ?, ?, ?)
");

if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Gagal prepare statement: " . $konek->error]);
    exit;
}

$stmt_check = $konek->prepare("
    SELECT id FROM presensiEvent
    WHERE nokartu = ? AND tanggal = ? AND keterangan = ? AND ruang = 'Izin Mens'
    LIMIT 1
");

if (!$stmt_check) {
    echo json_encode(["status" => "error", "message" => "Gagal prepare check: " . $konek->error]);
    exit;
}

$konek->begin_transaction();

foreach ($data['data'] as $item) {
    if (!is_array($item)) continue;

    $nokartu = trim($item['i'] ?? '');
    $nis     = trim($item['n'] ?? '');
    $sesi    = strtoupper(trim($item['s'] ?? ''));
    $waktu   = trim($item['t'] ?? '');

    if ($nokartu === '' || $nis === '' || $sesi === '' || $waktu === '') {
        $errors[] = "Data tidak lengkap untuk NIS: $nis";
        continue;
    }

    $tanggal = substr($waktu, 0, 10);
    $jam     = substr($waktu, 11, 8);

    $map = [];
    if (str_contains($sesi, 'H')) $map[] = ['DHUHA', $tanggal, $jam];
    if (str_contains($sesi, 'D')) $map[] = ['DZUHUR', $tanggal, $jam];
    if (str_contains($sesi, 'A')) $map[] = ['ASHAR',  $tanggal, $jam];

    if (empty($map)) {
        $errors[] = "Sesi tidak dikenal ($sesi) untuk NIS: $nis";
        continue;
    }

    foreach ($map as [$ket, $tgl, $j]) {
        $stmt_check->bind_param("sss", $nokartu, $tgl, $ket);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $skipped++;
            $stmt_check->free_result();
            continue;
        }
        $stmt_check->free_result();

        $stmt->bind_param("sssssss", $nokartu, $nis, $RUANG, $j, $j, $tgl, $ket);

        if ($stmt->execute()) {
            $inserted++;
            $json_entry = [
                "nokartu"    => $nokartu,
                "nis"        => $nis,
                "tanggal"    => $tgl,
                "jam"        => $j,
                "keterangan" => $ket,
                "nodevice"   => $meta_nodevice,
            ];
            $found = false;
            foreach ($json_content['data'] as &$existing) {
                if (
                    $existing['nokartu'] === $nokartu
                    && $existing['keterangan'] === $ket
                    && $existing['tanggal'] === $tgl
                ) {
                    $existing = $json_entry;
                    $found = true;
                    break;
                }
            }
            unset($existing);
            if (!$found) $json_content['data'][] = $json_entry;
        } else {
            $errors[] = "Gagal insert $nis ($ket): " . $stmt->error;
        }
    }
}

$konek->commit();
$stmt->close();
$stmt_check->close();
$konek->close();

file_put_contents(
    $json_file,
    json_encode($json_content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
);

echo json_encode([
    "status"    => $inserted > 0 ? "success" : "noChanges",
    "inserted"  => $inserted,
    "skipped"   => $skipped,
    "errors"    => $errors,
    "json_file" => basename($json_file),
    "timestamp" => date("Y-m-d H:i:s")
], JSON_PRETTY_PRINT);

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Jakarta');
header('Content-Type: application/json');

// ✅ safety check koneksi
if (!file_exists(__DIR__ . "/../config/konesi.php")) {
    echo json_encode(["status" => "error", "message" => "File koneksi tidak ditemukan"]);
    exit;
}
require_once "../config/konesi.php";

if (!isset($konek) || !$konek instanceof mysqli) {
    echo json_encode(["status" => "error", "message" => "Koneksi database tidak valid"]);
    exit;
}

if (isset($_FILES['file'])) {
    $json = file_get_contents($_FILES['file']['tmp_name']);
} else {
    $json = file_get_contents("php://input");
}
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
$meta_nodevice   = $data['nodevice']    ?? '0';
$meta_chipID     = $data['chipID']      ?? '';
$meta_macAddress = $data['macAddress']  ?? ($_SERVER['HTTP_X_MAC'] ?? 'unknown');
$meta_ipAddress  = $data['ipAddress']   ?? ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
$meta_timestamp  = $data['timestamp']   ?? date('Y-m-d H:i:s');

// --- Lokasi File JSON Backup ---
$folder = __DIR__ . "/jsonSholat";
if (!is_dir($folder) && !mkdir($folder, 0777, true)) {
    echo json_encode(["status" => "error", "message" => "Gagal membuat folder penyimpanan"]);
    exit;
}
$today     = date("Y-m-d");
$json_file = "$folder/sholat-$today.json";

// --- Load atau inisialisasi JSON backup ---
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

$RUANG = "Masjid 3";

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

// Prepared statement cek duplikat (nokartu + tanggal + keterangan)
$stmt_check = $konek->prepare("
    SELECT id FROM presensiEvent
    WHERE nokartu = ? AND tanggal = ? AND keterangan = ?
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

    // Validasi field wajib
    if ($nokartu === '' || $nis === '' || $sesi === '' || $waktu === '') {
        $errors[] = "Data tidak lengkap untuk NIS: $nis";
        continue;
    }

    $tanggal = substr($waktu, 0, 10);
    $jam     = substr($waktu, 11, 8);

    // Mapping sesi → keterangan
    // s='H'  → DHUHA
    // s='D'  → DZUHUR
    // s='A'  → ASHAR
    // s='DA' → DZUHUR + ASHAR (dua baris)
    $map = [];

    if (str_contains($sesi, 'H')) {
        $map[] = ['DHUHA', $waktu, $tanggal, $jam];
    }

    if (str_contains($sesi, 'D')) {
        $map[] = ['DZUHUR', $waktu, $tanggal, $jam];
    }

    if (str_contains($sesi, 'A')) {
        $map[] = ['ASHAR', $waktu, $tanggal, $jam];
    }

    if (empty($map)) {
        $errors[] = "Sesi tidak dikenal ($sesi) untuk NIS: $nis";
        continue;
    }

    foreach ($map as [$ket, $wkt, $tgl, $j]) {
        // Cek duplikat
        $stmt_check->bind_param("sss", $nokartu, $tgl, $ket);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $skipped++;
            $stmt_check->free_result();
            continue;
        }
        $stmt_check->free_result();

        $stmt->bind_param(
            "sssssss",
            $nokartu,
            $nis,
            $RUANG,
            $j,
            $j,
            $tgl,
            $ket
        );

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
                if (($existing['nokartu'] ?? '') === $nokartu
                    && ($existing['keterangan'] ?? '') === $ket
                    && ($existing['tanggal'] ?? '') === $tgl
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

// --- Simpan JSON backup harian ---
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

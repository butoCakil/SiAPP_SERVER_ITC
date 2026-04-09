<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Jakarta');
header('Content-Type: application/json');

require_once "../config/konesi.php";

$json = file_get_contents("php://input");
$data = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
    echo json_encode(["status" => "error", "message" => "JSON tidak valid"]);
    exit;
}

$inserted = 0;
$errors = [];

$stmt = $konek->prepare("
    INSERT INTO presensiEvent
    (nokartu, nis, ruang, mulai, selesai, jam, tanggal, keterangan)
    VALUES (?, ?, ?, ?, NULL, ?, ?, ?)
");

$RUANG = "Masjid 3";

foreach ($data as $item) {

    $nokartu = trim($item['i'] ?? '');
    $nis     = trim($item['n'] ?? '');
    $sesi    = strtoupper(trim($item['s'] ?? ''));

    if ($nokartu === '' || $nis === '' || $sesi === '') {
        $errors[] = "Data tidak lengkap";
        continue;
    }

    // mapping sesi → waktu
    $map = [];

    if (str_contains($sesi, 'D') && !empty($item['td'])) {
        $map[] = ['DZUHUR', $item['td']];
    }

    if (str_contains($sesi, 'A') && !empty($item['ta'])) {
        $map[] = ['ASHAR', $item['ta']];
    }

    foreach ($map as [$ket, $waktu]) {

        $tanggal = substr($waktu, 0, 10);
        $jam     = substr($waktu, 11, 8);

        $stmt->bind_param(
            "sssssss",
            $nokartu,
            $nis,
            $RUANG,
            $waktu,
            $jam,
            $tanggal,
            $ket
        );

        if ($stmt->execute()) {
            $inserted++;
        } else {
            $errors[] = $stmt->error;
        }
    }
}

$stmt->close();
$konek->close();

echo json_encode([
    "status"   => $inserted > 0 ? "success" : "noChanges",
    "inserted" => $inserted,
    "errors"  => $errors,
    "timestamp" => date("Y-m-d H:i:s")
], JSON_PRETTY_PRINT);

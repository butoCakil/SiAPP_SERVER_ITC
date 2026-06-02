<?php
date_default_timezone_set('Asia/Jakarta');
header('Content-Type: application/json; charset=utf-8');

$json = isset($_FILES['file'])
    ? file_get_contents($_FILES['file']['tmp_name'])
    : file_get_contents('php://input');

$ch = curl_init('http://127.0.0.1:8080/api/upload/presensi');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $json,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 30,
]);
$response = curl_exec($ch);
$err      = curl_error($ch);
curl_close($ch);

echo $response ?: json_encode(['status' => 'error', 'message' => 'Forward gagal: ' . $err]);

<?php
header('Content-Type: application/json; charset=utf-8');
$key = $_GET['key'] ?? '';
$ch  = curl_init('http://127.0.0.1:8080/api/db/fake?key=' . urlencode($key));
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10]);
$response = curl_exec($ch);
$err      = curl_error($ch);
curl_close($ch);
echo $response ?: json_encode(['status' => 'error', 'message' => 'Forward gagal: ' . $err]);

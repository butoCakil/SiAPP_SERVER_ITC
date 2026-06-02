<?php
date_default_timezone_set('Asia/Jakarta');

$ch = curl_init('http://127.0.0.1:8080/api/upload/file');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $_FILES ? ['file' => new CURLFile($_FILES['file']['tmp_name'], $_FILES['file']['type'], $_FILES['file']['name'])] : [],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 30,
]);
$response = curl_exec($ch);
$err      = curl_error($ch);
curl_close($ch);

echo $response ?: 'Forward gagal: ' . $err;

<?php
$nokartu = $_GET['nokartu'] ?? '';
$ipa     = $_GET['ipa'] ?? '';
$ch = curl_init('http://127.0.0.1:8080/tag?nokartu=' . urlencode($nokartu) . '&ipa=' . urlencode($ipa));
curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10]);
$response = curl_exec($ch);
$err      = curl_error($ch);
curl_close($ch);
echo $response ?: 'Forward gagal: ' . $err;

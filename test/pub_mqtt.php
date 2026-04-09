<?php

require "vendor/autoload.php";

$host = "localhost"; // Ganti dengan alamat broker MQTT Anda
$port = 1883; // Ganti dengan port broker MQTT (biasanya 1883)
$username = "ben"; // Ganti dengan username Anda (jika diperlukan)
$password = "1234"; // Ganti dengan password Anda (jika diperlukan)
$topic = "test"; // Ganti dengan topik yang diinginkan
$message = "Hello, MQTT!"; // Ganti dengan pesan yang ingin Anda kirim

$client_id = "php_publisher_" . rand(); // ID pelanggan MQTT

$mqtt = new Bluerhinos\phpMQTT($host, $port, $client_id);

if ($mqtt->connect(true, NULL, $username, $password)) {
    echo "Terhubung ke broker MQTT.\n";

    $mqtt->publish($topic, $message, 0);

    $mqtt->close();
} else {
    echo "Koneksi ke broker MQTT gagal. Coba lagi nanti.\n";
}

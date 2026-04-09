<?php

require "vendor/autoload.php";

$host = "localhost"; // Ganti dengan alamat broker MQTT Anda
$port = 1883; // Ganti dengan port broker MQTT (biasanya 1883)
$username = "ben"; // Ganti dengan username Anda (jika diperlukan)
$password = "1234"; // Ganti dengan password Anda (jika diperlukan)
$topic = "test"; // Ganti dengan topik yang diinginkan

$client_id = "php_subscriber_test"; // ID pelanggan MQTT

$mqtt = new Bluerhinos\phpMQTT($host, $port, $client_id);

if ($mqtt->connect(true, NULL, $username, $password)) {
    echo "Terhubung ke broker MQTT.\n";

    $topics[$topic] = ["qos" => 0, "function" => "procmsg"];
    $mqtt->subscribe($topics, 0);

    while ($mqtt->proc()) {
        // Loop ini akan menjalankan proses MQTT secara berkelanjutan
        // Anda dapat menambahkan logika di sini untuk menangani pesan yang diterima
    }
} else {
    echo "Koneksi ke broker MQTT gagal. Coba lagi nanti.\n";
}

function procmsg($topic, $msg)
{
    echo "Menerima pesan dari: $topic - Pesan: $msg\n";
}

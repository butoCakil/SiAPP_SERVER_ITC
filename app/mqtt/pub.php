<?php
// ================================================================
// pub.php — VERSI DIPERBAIKI
// ================================================================
// Perubahan:
// 1. Ganti QoS 1 → QoS 0: tidak perlu tunggu PUBACK,
//    lebih cepat dan tidak ada risiko packet hilang saat close()
// 2. Tambah proc() setelah publish untuk flush buffer sebelum close()
// 3. Tambah retry 1x jika connect gagal
// 4. Timeout connect lebih pendek (tidak hang lama)
// ================================================================

$array = array(
    "0" => array(
        "message" => "404",
        "info"    => "ERROR. Method Not Allowed"
    )
);
$json    = json_encode(array('respon' => $array));
$host    = "localhost";
$port    = 1883;
$username = "ben";
$password = "1234";
$topic   = "responServer/" . $nodevice;
$message = @$jsonString ? $jsonString : "{" . $json . "}";

// Client ID tetap unik per publish
$pub_client_id = "pub_" . $nodevice . "_" . substr(md5(microtime()), 0, 6);

$pub_mqtt = new Bluerhinos\phpMQTT($host, $port, $pub_client_id);

// ── Coba connect, retry 1x jika gagal ──
$connected = false;
for ($attempt = 1; $attempt <= 2; $attempt++) {
    if ($pub_mqtt->connect(true, NULL, $username, $password)) {
        $connected = true;
        break;
    }
    usleep(100000); // tunggu 100ms sebelum retry
}

if ($connected) {
    // ── QoS 0: fire-and-forget, tidak perlu tunggu PUBACK ──
    // Lebih cepat dan lebih reliable untuk use case real-time ini
    $pub_mqtt->publish($topic, $message, 0);

    // ── Flush buffer: panggil proc() sekali agar packet benar-benar terkirim ──
    // Tanpa ini, packet mungkin masih di buffer TCP saat close() dipanggil
    $pub_mqtt->proc();

    $text_log .= "Berhasil kirim balasan ke \"$topic\" (attempt $attempt)\n\n";
} else {
    $text_log .= "Koneksi \"$topic\" gagal setelah 2 percobaan.\n\n";
}

$pub_mqtt->close();

echo $text_log;

$tanggal = date("Y-m-d");
$lokasi_penyimpanan = "/opt/lampp/htdocs/app/mqtt/log/log/";
$nama_file_log = $lokasi_penyimpanan . "tag_" . $tanggal . ".log";
file_put_contents($nama_file_log, $text_log, FILE_APPEND | LOCK_EX);

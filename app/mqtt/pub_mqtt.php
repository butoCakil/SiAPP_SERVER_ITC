<?php
// beri feedback
$array = array(
    "0" => array(
        "message" => "404",
        "info" => "ERROR. Method Not Allowed"
    )
);

// encode array to json
$json = json_encode(array('respon' => $array));

require("vendor/autoload.php"); // Memuat pustaka phpMQTT

$host = "localhost";
$port = 1883;
$username = "ben";
$password = "1234";
// $topic = "responServer";
$topic = "responServer_" . $nodevice; // Menggunakan topik "responServer" dengan tambahan ID perangkat
$message = @$jsonString ? $jsonString : "{" . $json . "}";

// $infoValue = $message['respon']['info'];

if ($mqtt->connect(true, NULL, $username, $password)) {
    $text_log .= "Berhasil kirim balasan ke \"$topic\"";
    $text_log .= "\n";
    // $text_log .= $infoValue;
    // $text_log .= "\n";
    $text_log .= "\n";
    $mqtt->publish($topic, $message, 1);
} else {
    $text_log .= "Koneksi \"$topic\" gagal.\n";
    $text_log .= "\n";
}

$mqtt->close();

echo $text_log;

// Mendapatkan tanggal hari ini
$tanggal = date("Y-m-d");

// Direktori penyimpanan file log (ganti dengan direktori yang diinginkan)
$lokasi_penyimpanan = "/opt/lampp/htdocs/app/mqtt/log/log/";

// Nama file log
$nama_file_log = $lokasi_penyimpanan . "tag_" . $tanggal . ".log";

// Mengecek apakah file sudah ada atau belum
if (!file_exists($nama_file_log)) {
    // Jika file belum ada, maka membuat file baru dan menuliskan data
    echo "create $nama_file_log:";
    $ok = file_put_contents($nama_file_log, $text_log);

    if ($ok) {
        echo "OK";
    } else {
        echo "FAIL";
    }
} else {
    echo "ready $nama_file_log:";
    // Jika file sudah ada, maka menambahkan data ke dalam file
    $ok = file_put_contents($nama_file_log, $text_log, FILE_APPEND);

    if ($ok) {
        echo "OK";
    } else {
        echo "FAIL";
    }
}

echo "\n\n";

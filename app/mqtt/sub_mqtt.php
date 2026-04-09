<?php
global $text_log;
$text_log = "";
date_default_timezone_set('Asia/Jakarta');
$timestamp = date('Y-m-d H:i:s');
$tanggal = date('Y-m-d');

require "vendor/autoload.php"; // Memuat pustaka phpMQTT yang telah diinstal melalui Composer

$devices = [
    "2409TL001",
    "2409TL002",
    "2409TL003",
    "2409TL004",
    "2309G001",
    "2309G002",
    "2309G003",
    "2309G004",
    "2309G005",
    "2309G006",
    "2309G007",
    "2309G008",
    "2309G009",
    "2309G010",
    "2309MAS001",
    "2309MAS002",
    "2309MAS003",
    "2309MAS004",
    "2309MAS005",
    "2310AT001",
    "2310AT002",
    "2310AT003",
    "2310AT004",
    "2310AT005",
    "2310AT006",
    "2310AT007",
    "2310AT008",
    "2310AT009",
    "2310AT010",
    "2310DKV001",
    "2310DKV002",
    "2310DKV003",
    "2310DKV004",
    "2310DKV005",
    "2310DKV006",
    "2310DKV007",
    "2310DKV008",
    "2310DKV009",
    "2310DKV010",
    "2310TE001",
    "2310TE002",
    "2310TE003",
    "2310TE004",
    "2310TE005",
    "2310TE006",
    "2310TE007",
    "2310TE008",
    "2310TE009",
    "2310TE010",
    "2310NA001",
    "2310NA002",
    "2310NA003",
    "2310NA004",
    "2310NA005",
    "2310NA006",
    "2310NA007",
    "2310NA008",
    "2310NA009",
    "2310NA010",
    "2310NA011",
    "2310NA012",
    "2310NA013",
    "2310NA014",
    "2310NA015",
    "2310NA016",
    "2310NA017",
    "2310NA018",
    "2310NA019",
    "2310NA020",
    "2309IZ001",
    "2309IZ002",
    "2309IZ003",
    "2309IZ004",
    "2309IZ005"
];

$host = "localhost"; // Ganti dengan alamat broker MQTT Anda
$port = 1883; // Port broker MQTT (biasanya 1883)
$username = "ben"; // Ganti dengan username Anda (jika diperlukan)
$password = "1234"; // Ganti dengan password Anda (jika diperlukan)
$topic = "dariMCU"; // Ganti dengan topik yang Anda ingin subscribec

$client_id = "php_subscriber_tag"; // ID pelanggan MQTT

global $mqtt;
$mqtt = new Bluerhinos\phpMQTT($host, $port, $client_id);

if ($mqtt->connect(true, NULL, $username, $password)) {
    echo "Terhubung ke broker MQTT.\n";

    // 1 TOPIK UNTUK SEMUA DEVICE
    // $topics[$topic] = ["qos" => 0, "function" => "procmsg"];
    // $mqtt->subscribe($topics, 0); // Subscribe ke topik yang diinginkan

    // AKTIFKAN ini UNTUK 1 TOPIK per DEVICE
    foreach ($devices as $device) {
        $topic = "dariMCU_" . $device;
        $topics[$topic] = ["qos" => 0, "function" => "procmsg"];
    }
    $mqtt->subscribe($topics, 0);

    if ($mqtt) {
        echo "Mulai MQTT: $timestamp\n";
    }

    while ($mqtt->proc()) {
        // Loop ini akan menjalankan proses MQTT secara berkelanjutan
        // Anda dapat menambahkan logika di sini untuk menangani pesan yang diterima
    }
} else {
    $text_log .= "Koneksi ke broker MQTT gagal. Coba lagi nanti.\n";
}

function procmsg($topic, $msg)
{
    global $mqtt;
    global $text_log;
    $text_log = "";

    date_default_timezone_set('Asia/Jakarta');
    $timestamp = date('Y-m-d H:i:s');

    // Fungsi ini akan dipanggil ketika pesan diterima pada topik yang di-subscribe
    // Anda dapat menambahkan logika di sini untuk menangani pesan yang diterima
    $text_log .= "[$timestamp]\nMenerima pesan dari: $topic - Pesan: $msg\n";

    $json = (array)json_decode($msg, true);

    $nokartu = @$json['nokartu'];
    $idchip = @$json['idchip'];
    $nodevice = @$json['nodevice'];
    $key = @$json['key'];
    $ip_a = @$json['ipa'];

    // URL target yang ingin Anda panggil dengan parameter GET
    $url = 'http://localhost/app/directagJSON.php';

    // Membuat URL lengkap dengan parameter GET
    $urlWithParams = $url . '?nokartu=' . urlencode($nokartu) . '&idchip=' . urlencode($idchip) . '&nodevice=' . urlencode($nodevice) . '&key=' . urlencode($key) . '&ipa=' . urlencode($ip_a);

    // Melakukan panggilan menggunakan file_get_contents()
    $response = file_get_contents($urlWithParams);

    if (!$response) {
        $text_log .= "Tidak ada Respon dari \"directagJSON\".\n\n";

        echo $text_log;

        // Mendapatkan tanggal hari ini
        $tanggal = date("Y-m-d");

        // Direktori penyimpanan file log (ganti dengan direktori yang diinginkan)
        $lokasi_penyimpanan = "/opt/lampp/htdocs/app/mqtt/log/";

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

        $mqtt->close();
    } else {
        // Respons berhasil, Anda dapat mengolah respons sesuai kebutuhan
        $text_log .= "[$timestamp]\nBerhasil merespon \"$topic\" : " . $response . "\n";

        $jsonString = json_encode($response);
        // echo $text_log;
        include "pub_mqtt.php";
    }
}

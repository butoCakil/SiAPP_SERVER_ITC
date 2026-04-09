<?php
include "../config/db.php";
$db = koneksi();

$status = "diijinkan";
$query = "SELECT * FROM exportdb WHERE status = :status ORDER BY id ASC";
$stmt = $db->prepare($query);
$stmt->bindParam(":status", $status);
$stmt->execute();
// $result = $stmt->fetch(PDO::FETCH_ASSOC);

while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data_db[] = $result['db'];
    $link_db[] = $result['link'];
    $keyapi_db[] = $result['keyapi'];
}

// print_r($data_db);
$jumlah_db = count($data_db);

for ($i = 0; $i < $jumlah_db; $i++) {
    // Mengambil data dari database dan mengonversinya ke JSON
    $query = "SELECT * FROM `$data_db[$i]`";
    $checkTableQuery = "SHOW TABLES LIKE '$data_db[$i]'";
    $checkTableStmt = $db->prepare($checkTableQuery);
    $checkTableStmt->execute();

    if ($checkTableStmt->rowCount() > 0) {
        // echo "Data tabel: $data_db[$i]";
        // echo "<br>";
        // echo "Data link_db: $link_db[$i]";
        // echo "<br>========================================<br>";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $data = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        $json_data = json_encode($data);

        // Menampilkan data JSON sebagai respons
        // echo $json_data;
        e_POST($json_data, $link_db[$i], $keyapi_db[$i]);
        // echo "<br>========================================<br>";
        // echo "<br>";
        // echo "<br>";
        // echo "<br>";
        
    } else {
        http_response_code(404);
        $message = "tabel tidak ditemmukan";
        echo json_encode(array("message" => "$message"));
    }
}

function e_POST($data_string, $script_url, $api_key)
{
    $ch = curl_init($script_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string),
        'Authorization: Bearer ' . $api_key
    ));

    $result = curl_exec($ch);
    curl_close($ch);

    // Hasil dari permintaan POST akan dikembalikan oleh skrip Google Apps Script
    echo $result;
}

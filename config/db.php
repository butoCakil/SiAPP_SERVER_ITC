<?php
function koneksi()
{
    // $user = "u0360177_esepro";
    // $pass = "zg+dHrx69o8R";
    // $dbs = "u0360177_ci4presensi";
    // $cfg['Servers'][$i]['user'] = 'siap';
    // $cfg['Servers'][$i]['password'] = 'skanebaSMKBOS$';

    $user = "siap";
    $pass = "skanebaSMKBOS$";
    // $user = "root";
    // $pass = "";
    $dbs = "siap";
    try {
        $db = new PDO("mysql:host=localhost;dbname=$dbs", "$user", "$pass");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $db;
    } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(array("message" => "Koneksi ke database gagal: " . $e->getMessage()));
        exit;
    }
}

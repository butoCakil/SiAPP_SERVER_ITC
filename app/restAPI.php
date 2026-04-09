<?php
date_default_timezone_set('Asia/Jakarta');
$tanggal = (date('Y-m-d'));

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$db_tbl = @$_GET['db'];
$akses = @$_GET['akses'];
$token = @$_GET['key'];
$fulltabel = @$_GET['tabel'];

$redemcode = "db_tbl_all";
// bf84b03e04fca268e50fc7698e8d673e

$message = "";
$allowed = false;

if (!empty($token)) {
    // Koneksi ke database
    $db = koneksi();

    // cek key [token]
    // Query untuk mengecek apakah data ada dalam tabel dan masih berlaku
    $query = "SELECT COUNT(*) as count, masaberlaku FROM api WHERE kode_api = :key";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":key", $token);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] > 0) {
        // Data ditemukan dalam tabel, periksa tanggal expire
        $tanggal_expire = strtotime($result['masaberlaku']);
        $tanggal_sekarang = strtotime(date('Y-m-d'));

        if ($tanggal_sekarang <= $tanggal_expire) {
            $key = true; // Token masih berlaku
            $message = "[OK][key] Token valid dan masih berlaku.";
        } else {
            $key = false; // Token telah kadaluarsa
            $message = "[ERROR][key] Token tidak valid atau sudah kadaluarsa (exp. " . date("d-m-Y", $tanggal_expire) . ").";
        }
    } else {
        $key = false; // Data tidak ditemukan dalam tabel
        if ($token) {
            $message = "[ERROR][Key] Token tidak terdaftar.";
        } else {
            $message = "[ERROR][Key] Token yang valid diperlukan, untuk mendapatkan akses.";
        }
    }

    // Route the request to the appropriate endpoint
    if ($token && $key) {
        if ($akses == "list") {
            if ($fulltabel == "bosmintasemuatabel") {
                // Query untuk mendapatkan daftar tabel
                $query = "SHOW TABLES";
                $stmt = $db->query($query);
                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            } else {
                // Daftar tabel yang ingin Anda tampilkan
                $tablesToDisplay = array(
                    "daftarruang",
                    "daftarijin",
                    "dataguru",
                    "datapresensi",
                    "datasiswa",
                    "jadwalgurujur",
                    "jadwalkbm",
                    "jampelajaran",
                    "jurnalguru",
                    "kalender",
                    "kelompokkelas",
                    "kodeinfo",
                    "pengumuman",
                    "presensiEvent",
                    "presensikelas",
                    "daftarketerlambatan"
                );

                // Query untuk mendapatkan daftar tabel yang sesuai dengan kriteria
                $query = "SHOW TABLES LIKE ?";
                $stmt = $db->prepare($query);

                foreach ($tablesToDisplay as $table) {
                    $stmt->execute([$table]);
                    $result = $stmt->fetch(PDO::FETCH_COLUMN);

                    if ($result) {
                        $tables[] = $result;
                    }
                }
            }

            // Membuat respons JSON
            $response = array(
                "tables" => $tables
            );

            // Mengirim respons JSON
            http_response_code(200); // OK
            echo json_encode($response);
        } else {
            if ($db_tbl) {
                switch ($db_tbl) {
                    case 'dataguru':
                        $query = "SELECT nip, nama, nick, info, jabatan, email, level_login FROM $db_tbl";
                        $query = fullakses($akses, $redemcode, $db_tbl, $query);
                        $allowed = true;
                        break;
                    case 'datasiswa':
                        if($akses == "lite")
                            $query = "SELECT nokartu, nis FROM datasiswa";
                        else if($akses == "mid")
                            $query = "SELECT nokartu, nis, nama, kelas FROM datasiswa";
                        else
                            $query = "SELECT nis, nama, kelas, t_waktu_telat, poin, tingkat, jur, email FROM $db_tbl";
                        
                        $query = fullakses($akses, $redemcode, $db_tbl, $query);
                        $allowed = true;
                        break;
                    case 'daftarijin':
                        if($akses == 'hariini'){
                            $query = "SELECT * FROM $db_tbl WHERE tanggalijin LIKE '%$tanggal%' ORDER BY timestamp DESC";
                        } else {
                            $query = "SELECT * FROM $db_tbl ORDER BY timestamp DESC";
                        }
                        $query = fullakses($akses, $redemcode, $db_tbl, $query);
                        $allowed = true;
                        break;
                    case 'datapresensi':
                        if($akses == 'hariini'){
                           $query = "SELECT nama, nomorinduk, info, waktumasuk, ketmasuk, a_time, waktupulang, ketpulang, b_time, updated_at, infodevice, infodevice2 FROM $db_tbl WHERE tanggal LIKE '%$tanggal%' ORDER BY updated_at DESC";
                        } else {
                            $query = "SELECT nama, nomorinduk, info, waktumasuk, ketmasuk, a_time, waktupulang, ketpulang, b_time, updated_at, infodevice, infodevice2 FROM $db_tbl";
                        }
                        $query = fullakses($akses, $redemcode, $db_tbl, $query);
                        $allowed = true;
                        break;
                    case 'presensikelas':
                        $query = "SELECT nis, nama, ruangan, kelas, mulai_jamke, sampai_jamke, status, catatan, nick_guru, nama_guru, timestamp FROM $db_tbl";
                        $query = fullakses($akses, $redemcode, $db_tbl, $query);
                        $allowed = true;
                        break;
                    case 'presensiEvent':
                        if($akses == 'hariini'){
                            $query = "SELECT `nis`, `ruang`, `mulai`, `selesai`, `jam`, `tanggal`, `timestamp`, `keterangan` FROM $db_tbl WHERE tanggal LIKE '%$tanggal%' ORDER BY timestamp DESC";
                        } else {
                            $query = "SELECT `nis`, `ruang`, `mulai`, `selesai`, `jam`, `tanggal`, `timestamp`, `keterangan` FROM $db_tbl ORDER BY timestamp DESC";
                        }
                        $query = fullakses($akses, $redemcode, $db_tbl, $query);
                        $allowed = true;
                        break;
                    case 'jadwalkbm':
                        $query = "SELECT * FROM $db_tbl";
                        $query = fullakses($akses, $redemcode, $db_tbl, $query);
                        $allowed = true;
                        break;
                    case 'jurnalguru':
                        $query = "SELECT * FROM $db_tbl";
                        $query = fullakses($akses, $redemcode, $db_tbl, $query);
                        $allowed = true;
                        break;
                    case 'riwayat_topup':
                        $query = "SELECT * FROM $db_tbl";
                        $query = fullakses($akses, $redemcode, $db_tbl, $query);
                        $allowed = true;
                        break;
                    case 'riwayat_transaksi':
                        $query = "SELECT kode_item, nama_item, nominal, user, timestamp, akses FROM $db_tbl";
                        $query = fullakses($akses, $redemcode, $db_tbl, $query);
                        $allowed = true;
                        break;
                    case 'reg_device':
                        // memerlukan akses super admin untuk mengetahui detail device
                        if (@$_GET['superadmin'] == "({[!234]})") {
                            $query = "SELECT * FROM $db_tbl";
                        } else {
                            $query = "SELECT no_device, info_device, status FROM $db_tbl";
                        }
                        $query = fullakses($akses, $redemcode, $db_tbl, $query);
                        $allowed = true;
                        break;
                    case 'kalender':
                        $query = "SELECT * FROM $db_tbl";
                        $query = fullakses($akses, $redemcode, $db_tbl, $query);
                        $allowed = true;
                        break;
                    case 'daftarruang':
                        $query = "SELECT * FROM $db_tbl";
                        $query = fullakses($akses, $redemcode, $db_tbl, $query);
                        $allowed = true;
                        break;
                    case 'daftarketerlambatan':
                        $query = "SELECT * FROM $db_tbl";
                        $query = fullakses($akses, $redemcode, $db_tbl, $query);
                        $allowed = true;
                        break;
                        // Add more endpoints as needed
                    default:
                        http_response_code(404);
                        $message = "Akses ke Tabel `$db_tbl` tidak di-ijinkan";
                        $allowed = false;
                        // echo json_encode(array("message" => "tabel tidak ditemmukan atau tabel tidak diallowedkan"));
                        break;
                }

                // Mengambil data dari database dan mengonversinya ke JSON
                $checkTableQuery = "SHOW TABLES LIKE '$db_tbl'";
                $checkTableStmt = $db->prepare($checkTableQuery);
                $checkTableStmt->execute();

                if ($checkTableStmt->rowCount() > 0) {
                    if ($allowed == true) {

                        $stmt = $db->prepare($query);
                        $stmt->execute();
                        $data = array();
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $data[] = $row;
                        }

                        if($akses == "lite" || $akses == "mid"){
                           // Buat metadata
                            $metadata = array(
                                "jumlah_data" => count($data),
                                "timestamp"   => date("c"), // ISO 8601 format, contoh: 2025-08-28T14:30:00+07:00
                                "versi"       => "v1.2"
                            );

                            // Gabungkan metadata + data
                            $response = array(
                                "metadata" => $metadata,
                                "data"     => $data
                            ); 

                             // Encode jadi JSON
                            $json_data = json_encode($response, JSON_UNESCAPED_UNICODE);
                        } else {
                            $json_data = json_encode($data);
                        }

                        // Menampilkan data JSON sebagai respons
                        echo $json_data;
                    } else {
                        http_response_code(404);
                        echo json_encode(array(
                            "status" => "404",
                            "message" => "$message"
                        ));
                    }
                } else {
                    http_response_code(404);
                    $message = "tabel tidak ditemmukan";
                    echo json_encode(array(
                        "status" => "404",
                        "message" => "$message"
                    ));
                }
            } else {
                http_response_code(404);
                echo json_encode(array(
                    "status" => "404",
                    "message" => "Permintaan tidak lengkap"
                ));
            }
        }
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "$message"));
    }
} else {
    http_response_code(404);
    echo json_encode(array(
        "status" => "404",
        "message" => "Permintaan tidak diterima"
    ));
}

function fullakses($_akses, $_redemcode, $_db_tbl, $_query)
{
    if ($_akses == $_redemcode) {
        $query = "SELECT * FROM $_db_tbl";

        return $query;
    } else {
        return $_query;
    }
}

function koneksi()
{
    $user = "u0360177_esepro";
    // $pass = "zg+dHrx69o8R";
    // $dbs = "u0360177_ci4presensi";
    // $cfg['Servers'][$i]['user'] = 'siap';
    // $cfg['Servers'][$i]['password'] = 'skanebaSMKBOS$';

    $user = "siap";
    $pass = "skanebaSMKBOS$";
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

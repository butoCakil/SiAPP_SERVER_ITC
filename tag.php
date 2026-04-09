<?php
ini_set('display_errors', 1);
if (@$_GET['nokartu']) {
    date_default_timezone_set('Asia/Jakarta');
    $date = date("Y-m-d H:i:s");
    $pesan = "Someting Wrong";
    $akses = "SUPERADMIN";
    $nokartu = $_GET['nokartu'];
    
    if (strlen($nokartu) != 8) {
        $logMessage = "$date - Panjang karakter tidak valid: `$nokartu`.\n";
        // Nama file log
        $logFileName = "suspicious_input.log";
        // Tulis pesan log ke file
        file_put_contents($logFileName, $logMessage, FILE_APPEND);

        die("Value tidak diijinkan Bos.\n");
    }
    // Hapus karakter selain huruf dan angka
    $nokartu = preg_replace("/[^a-fA-F0-9]/", "", $nokartu);
    $nokartu ? "Ada Kartu\n" : "";
    // Pengecekan panjang karakter

    // Periksa jika ada karakter yang dihapus
    if ($_GET['nokartu'] !== $nokartu) {
        // Input mengandung karakter yang tidak valid
        $logMessage = $date . " - Input karakter tidak valid: `" . $_GET['nokartu'] . "`, " . $clientIP = $_SERVER['REMOTE_ADDR'] . "\n";

        // Nama file log
        $logFileName = "suspicious_input.log";

        // Tulis pesan log ke file
        file_put_contents($logFileName, $logMessage, FILE_APPEND);

        die("Input mengandung karakter yang tidak valid.\n");
    } else {
        include "config/konesi.php";

        // Hapus semua data dari tabel 'tmprfid'
        $sql_del = mysqli_query($konek, "DELETE FROM tmprfid");

        if ($sql_del) {
            // Inisialisasi koneksi database
            $konek = mysqli_connect("localhost", "siap", "skanebaSMKBOS$", "siap");

            if (!$konek) {
                die("Koneksi database gagal: " . mysqli_connect_error());
            }

            $nokartu_clean = mysqli_real_escape_string($konek, $nokartu);

            // Prepare the INSERT statement to insert data into the 'tmprfid' table
            $query_insert_tmprfid = "INSERT INTO tmprfid (nokartu, nokartu_admin) VALUES (?, ?)";
            $stmt_insert_tmprfid = mysqli_stmt_init($konek);
            mysqli_stmt_prepare($stmt_insert_tmprfid, $query_insert_tmprfid);
            mysqli_stmt_bind_param(
                $stmt_insert_tmprfid,
                "ss",
                $nokartu_clean,
                $nokartu_clean
            );
            $sql_tag = mysqli_stmt_execute($stmt_insert_tmprfid);

            // Tutup prepared statement
            mysqli_stmt_close($stmt_insert_tmprfid);

            if ($sql_tag) {
                echo "Berhasil\n";
                $replacement = str_repeat('*', 5); // Membuat string "*****"
                $result = $replacement . substr($nokartu_clean, 5);
                $pesan = "Berhasil tag Kartu: $result";
            } else {
                echo "Gagal Masukkan kartu ke tag";
                $pesan = "Gagal tag Kartu";
            }

            $ip_a = isset($_GET['ipa']) ? $_GET['ipa'] : null;

            $suspiciousChars = findSuspiciousChars($ip_a);

            if (empty($suspiciousChars)) {
                echo "uye. ";
            } else {
                // Input mengandung karakter yang tidak valid
                $date = date("Y-m-d H:i:s");
                $logMessage = $date . " - Karakter mencurigakan: " . $suspiciousChars . ", " .  $_GET['nokartu'] . ", " . $clientIP = $_SERVER['REMOTE_ADDR'] . "\n";

                // Nama file log
                $logFileName = "suspicious_input.log";

                // Tulis pesan log ke file
                file_put_contents($logFileName, $logMessage, FILE_APPEND);
                die("Karakter mencurigakan: " . $suspiciousChars);
            }

            // if (!findSuspiciousChars($ip_a)) {
            //     die("Program dihentikan karena karakter mencurigakan ditemukan.");
            // } else {
            //     echo "ip OK. ";
            // }

            if (!$ip_a) {
                $clientIP = $_SERVER['REMOTE_ADDR'];
            } else {
                $clientIP = $ip_a;
            }

            $requestUrl = $_SERVER['REQUEST_URI'];

            // Prepare the INSERT statement to insert data into the 'tempreq' table
            $timestamp = date('Y-m-d H:i:s');
            // cek tabel data yang sama
            $stmt = mysqli_stmt_init($konek);
            if (mysqli_stmt_prepare($stmt, "SELECT * FROM tempreq WHERE timestamp = ? AND ip = ? AND req = ? AND info = ?")) {
                mysqli_stmt_bind_param(
                    $stmt,
                    "ssss",
                    $timestamp,
                    $clientIP,
                    $requestUrl,
                    $akses
                );
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                echo "woyo";
                // Memeriksa apakah ada data yang cocok
                if ($row = mysqli_fetch_assoc($result)) {
                } else {
                    $query_insert_tempreq = "INSERT INTO tempreq (ip, req, info, detail) VALUES (?, ?, ?, ?)";
                    $stmt_insert_tempreq = mysqli_stmt_init($konek);
                    mysqli_stmt_prepare($stmt_insert_tempreq, $query_insert_tempreq);
                    mysqli_stmt_bind_param(
                        $stmt_insert_tempreq,
                        "ssss",
                        $clientIP,
                        $requestUrl,
                        $akses,
                        $pesan
                    );
                    // $detail = "[$hasil_kode_device] - " . $info_kode_array[$pesan];
                    $insert = mysqli_stmt_execute($stmt_insert_tempreq);

                    // Tutup prepared statement
                    mysqli_stmt_close($stmt_insert_tempreq);
                }

                // Tutup prepared statement
                mysqli_stmt_close($stmt);
            }
        }

        // Tutup koneksi database
        mysqli_close($konek);
    }
} else {
    echo "Permintaan tidak lengkap";
}

function findSuspiciousChars($input)
{
    // Buat pola regex untuk karakter yang dianggap aman
    $safePattern = "/^[0-9\s.]+$/";

    // Inisialisasi string untuk menyimpan karakter mencurigakan
    $suspiciousChars = '';

    // Loop melalui setiap karakter dalam input
    for ($i = 0; $i < strlen($input); $i++) {
        $char = $input[$i];

        // Periksa apakah karakter tidak sesuai dengan pola aman
        if (!preg_match($safePattern, $char)) {
            $suspiciousChars .= $char;
        }
    }

    return $suspiciousChars;
}

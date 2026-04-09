<?php
session_start();

if (isset($_POST['setip'])) {
    echo "masuk hapus data";

    $datab = $_POST['datab'];
    $nick = $_POST['nick'];
    $nama = $_POST['nama'];

    include("../config/konesi.php");

    // Persiapan prepared statement
    $stmt = mysqli_prepare($konek, "DELETE FROM `$datab` WHERE nick = ?");

    if ($stmt) {
        // Binding parameter
        mysqli_stmt_bind_param($stmt, "s", $nick);

        // Eksekusi prepared statement
        $result = mysqli_stmt_execute($stmt);
        
        if ($result) {
            echo "berhasil hapus";
            $pesan = 'Berhasil hapus data "' . $nama . '", dari tabel "' . $datab . '"!';
            $_SESSION['pesan'] = $pesan;
            if ($datab == "dataguru") {
                $url = "../datagtk.php";
            } else {
                $url = "../datasiswa.php";
            }
            header('location: ' . $url);
        } else {
            echo "gagal hapus";
            $pesan = 'Gagal hapus data"' . $nama . '", dari tabel "' . $datab . '"!';
            $_SESSION['pesan'] = $pesan;
            $url = isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : '';
            header('location: ' . $url);
        }

        // Tutup prepared statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Gagal mempersiapkan prepared statement.";
    }

    mysqli_close($konek);

} else {
    include('error404_2.php');
}

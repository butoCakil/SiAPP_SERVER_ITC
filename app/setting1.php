<?php
session_start();

include("../config/konesi.php");

$info = $_POST['pilihhari'];
$waktumasuk = $_POST['waktumasuk'];
$waktupulang = $_POST['waktupulang'];

// Persiapan prepared statement
$sql = "UPDATE statusnya SET waktumasuk=?, waktupulang=?, info=?";
$stmt = mysqli_prepare($konek, $sql);

if ($stmt) {
    // Binding parameter
    mysqli_stmt_bind_param($stmt, "sss", $waktumasuk, $waktupulang, $info);

    // Eksekusi prepared statement
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        $pesan = "Berhasil update pengaturan waktu masuk dan waktu pulang";
        // echo '<script type ="text/JavaScript">';
        // echo 'alert("' . $pesan . '")';
        // echo '</script>';
    } else {
        $pesan = "Error: " . $sql . "<br>" . mysqli_error($konek);
        // echo '<script type ="text/JavaScript">';
        // echo 'alert("' . $pesan . '")';
        // echo '</script>';
    }

    // Tutup prepared statement
    mysqli_stmt_close($stmt);
} else {
    $pesan = "Gagal mempersiapkan prepared statement.";
}

mysqli_close($konek);

$username['key'] = $_SESSION['username'];
$pesan_admin['key'] = $_SESSION['pesan_admin'];
$_POST['pesan'] = $pesan;
$_SESSION = $_POST;
$_SESSION['username'] = $username['key'];
$_SESSION['pesan_admin'] = $pesan_admin['key'];

header("location: " . $_SERVER['HTTP_REFERER']);

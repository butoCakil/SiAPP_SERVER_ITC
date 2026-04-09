<?php
session_start();

include("../config/konesi.php");

$wa = $_POST['bukamasuk'];
$wta = $_POST['tutupmasuk'];
$wtp = $_POST['bukapulang'];
$wp = $_POST['tutuppulang'];

// Persiapan prepared statement
$sql = "UPDATE statusnya SET wa=?, wta=?, wtp=?, wp=?";
$stmt = mysqli_prepare($konek, $sql);

if ($stmt) {
    // Binding parameter
    mysqli_stmt_bind_param($stmt, "ssss", $wa, $wta, $wtp, $wp);

    // Eksekusi prepared statement
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        $pesan = "Berhasil update pengaturan Batas waktu akses";
    } else {
        $pesan = "Error: " . $sql . "<br>" . mysqli_error($konek);
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

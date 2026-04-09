<?php

$host = "localhost";
// $user = "u0360177_esepro";
// $pass = "zg+dHrx69o8R";
// $dbs = "u0360177_ci4presensi";
// $cfg['Servers'][$i]['user'] = 'siap';
// $cfg['Servers'][$i]['password'] = 'skanebaSMKBOS$';

$user = "siap";
$pass = "skanebaSMKBOS$";
$dbs = "siap";

$konek = mysqli_connect($host, $user, $pass, $dbs);

if (!$konek) {
    echo ("Gagal Konek database bossku!");
    $pesan = 'Gagal Konek database bossku!';
    die;
    header("Location: error404_4.php");
    // include('error404_4.php');
} else {
    // echo ("Berhasil Konek database bossku!");
}

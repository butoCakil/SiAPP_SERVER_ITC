<?php
include('app/get_user.php');
$tahun = date('Y');

if (@$_GET['tahun']) {
    $tahun_pilih = $_GET['tahun'];
    $tahun_pilih = mysqli_real_escape_string($konek, $tahun_pilih);
} else {
    $tahun_pilih = date('Y');
}

$tahun_pilih_plus = (int)$tahun_pilih + 1;
$tahun_pilih_min = (int)$tahun_pilih - 1;

// echo 'tahun pilih : ' . $tahun_pilih . '<br>';
// echo 'tahun pilih plus : ' . $tahun_pilih_plus . '<br>';
// echo 'tahun pilih min : ' . $tahun_pilih_min . '<br>';


if ($tahun_pilih_plus == ($tahun + 1)) {
    $tahun_pilih_plus = '#';
}

if ($tahun_pilih_plus == '#') {
    $tahun_pilih_plus = '#';
    $disable = ' disabled btn btn-secondary';
} else {
    $tahun_pilih_plus = 'rekaptahun.php?tahun=' . $tahun_pilih_plus;
    $disable = '';
}


$title = 'Rekap Presensi Tahun ' . $tahun_pilih;
$navlink = 'Rekap';
$navlink_sub = 'tahunan';
include('views/_rekaptahun.php');
// include('../config/konesi.php');
$q = mysqli_query($konek, "SELECT info FROM statusnya");
$status = mysqli_fetch_assoc($q);
$harikerja = $status['info'];

mysqli_close($konek);

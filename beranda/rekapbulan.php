<?php
include('app/get_user.php');

$tanggal = date('Y-m-d');
$tahun = date('Y');
$tanggal_ = date('d-m-Y');
$bulan_tahun_pilih = date('Y-m');
$bulan_tahun_ = date('m-Y');
$tanggal_indo = date('d') . " " . $bulanBahasaIndonesia . " " . date('Y');

if (@$_GET['bulan']) {
    $bulan_get = $_GET['bulan'];
    $bulan_pilih = date('m', strtotime($bulan_get));
    $tahun_pilih = date('Y', strtotime($bulan_get));
    $bulan_tahun_pilih = $tahun_pilih . "-" . $bulan_pilih;
    $nama_bulan_indo_pilih = bulanIndo(date('F', strtotime($bulan_get)));

    if ($bulan_tahun_pilih > date('Y-m')) {
        $bulan_tahun_pilih = date('Y-m');
        // $bulan_pilih = date('m');
        // $tahun_pilih = date('Y');
        // $nama_bulan_indo_pilih = bulanIndo(date('F'));
        $pesan = 'Perlu mesin waktu untuk melihat masa depan...';
        header("Location: rekapbulan.php?bulan=$bulan_tahun_pilih");
    }
} else {
    $tahun_pilih = date('Y', strtotime($tanggal));
    $bulan_pilih = date('m', strtotime($tanggal));
    $nama_bulan_indo_pilih = $bulanBahasaIndonesia;
}

$bulan_pilih_plus = $bulan_pilih + 1;
if ($bulan_pilih_plus > 12) {
    $bulan_pilih_plus = 1;
    $tahun_pilih_plus = $tahun_pilih + 1;
    $bulan_tahun_pilihplus = $tahun_pilih_plus . "-" . sprintf('%02d', $bulan_pilih_plus);
} else {
    $bulan_tahun_pilihplus = $tahun_pilih . "-" . sprintf('%02d', $bulan_pilih_plus);
}


$bulan_pilih_minus = $bulan_pilih - 1;
if ($bulan_pilih_minus < 1) {
    $bulan_pilih_minus = 12;
    $tahun_pilih_min = $tahun_pilih - 1;
    $bulan_tahun_pilihmin = $tahun_pilih_min . "-" . sprintf('%02d', $bulan_pilih_minus);
} else {
    $bulan_tahun_pilihmin = $tahun_pilih . "-" . sprintf('%02d', $bulan_pilih_minus);
}

if (sprintf('%02d', $bulan_pilih_plus) == sprintf('%02d', (date('m', strtotime($tanggal)) + 1))) {
    $bulan_tahun_pilihplus = '#';

    // echo '<script> alert("Tidak bisa memilih bulan ini"); </script>';
}

include('../config/konesi.php');

$q = mysqli_query($konek, "SELECT info FROM statusnya");
$status = mysqli_fetch_assoc($q);
$harikerja = $status['info'];

$title = 'Rekap Presensi ';
$navlink = 'Wali';
$navlink_sub = 'bulanan';

include('views/_rekapbulan.php');

mysqli_close($konek);

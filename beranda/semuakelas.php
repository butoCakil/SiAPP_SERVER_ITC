<?php
include('app/get_user.php');
$tanggal = date('Y-m-d');
$tanggal_dmY = date('d-m-Y');
$tahun = date('Y');

if (@$_GET['tanggal']) {
    $tanggal_get = $_GET['tanggal'];

    if ($tanggal_get > $tanggal) {
        $tanggal_get = date('Y-m-d', strtotime($tanggal));
    }

    $tanggal_pilih = date('Y-m-d', strtotime($tanggal_get));
    $tanggal_pilih_dmY = date('d-m-Y', strtotime($tanggal_get));
    $nama_hari_pilih = hariIndo(date('l', strtotime($tanggal_get)));
    $nama_hari_singkat_pilih = hariSingkatIndo(date('l', strtotime($tanggal_get)));
    $tahun_bulan_pilih = date('Y-m', strtotime($tanggal_get));
    $bulan_tahun_pilih = date('m-Y', strtotime($tanggal_get));
    $bulanIndoPilih = bulanIndo(date('F', strtotime($tanggal_get)));
} else {
    $tanggal_pilih = date('Y-m-d');
    $tanggal_pilih_dmY = date('d-m-Y');
    $nama_hari_pilih = hariIndo(date('l', strtotime($tanggal_pilih)));
    $nama_hari_singkat_pilih = hariSingkatIndo(date('l', strtotime($tanggal_pilih)));
    $tahun_bulan_pilih = date('Y-m');
    $bulan_tahun_pilih = date('m-Y');
    $bulanIndoPilih = bulanIndo(date('F', strtotime($tanggal_pilih)));
}

$tanggal_indo_pilih = date('d', strtotime($tanggal_pilih)) . " " . $bulanIndoPilih . " " . date('Y', strtotime($tanggal_pilih));
$hari_indo = hariIndo(date('l', strtotime($tanggal_pilih)));


if (@$_GET['kelas']) {
    $kelas_get = $_GET['kelas'];
    $kelas_get = mysqli_real_escape_string($konek, $kelas_get);
    $kelas_pilih = $kelas_get;
    $link_kelas_pilih = '&kelas=' . $kelas_pilih;
} else {
    $kelas_pilih = "";
    $link_kelas_pilih = "";
}

if (@$_GET['jur']) {
    $jur_get = $_GET['jur'];
    $jur_get = mysqli_real_escape_string($konek, $jur_get);
    $jur_pilih = $jur_get;
    $link_jur_pilih = '&jur=' . $jur_pilih;
} else {
    $jur_pilih = "";
    $link_jur_pilih = "";
}

$link_pilih = $link_kelas_pilih . $link_jur_pilih;

if ($jur_pilih && $kelas_pilih) {
    $sortir = " kode = '" . $kelas_pilih . $jur_pilih . "1'";
    $sortir = $sortir . " OR kode = '" . $kelas_pilih . $jur_pilih . "2'";
    $sortir = $sortir . " OR kode = '" . $kelas_pilih . $jur_pilih . "3'";
    $query_sortir = " WHERE (" . $sortir . ")";
} elseif ($jur_pilih && !$kelas_pilih) {
    $sortir = " kode = 'X" . $jur_pilih . "1'";
    $sortir = $sortir . " OR kode = 'X" . $jur_pilih . "2'";
    $sortir = $sortir . " OR kode = 'X" . $jur_pilih . "3'";
    $sortir = $sortir . " OR kode = 'XI" . $jur_pilih . "1'";
    $sortir = $sortir . " OR kode = 'XI" . $jur_pilih . "2'";
    $sortir = $sortir . " OR kode = 'XI" . $jur_pilih . "3'";
    $sortir = $sortir . " OR kode = 'XII" . $jur_pilih . "1'";
    $sortir = $sortir . " OR kode = 'XII" . $jur_pilih . "2'";
    $sortir = $sortir . " OR kode = 'XII" . $jur_pilih . "3'";
    $query_sortir = " WHERE (" . $sortir . ")";
} else if (@$_GET['kelasjur']) {
    $kelasjur = $_GET['kelasjur'];
    $kelasjur = mysqli_real_escape_string($konek, $kelasjur);
    $link_pilih = '&kelasjur=' . $kelasjur;

    $query_sortir = " WHERE kode = '" . $kelasjur . "'";
} else {
    $query_sortir = "";
}

// echo "<br>";
// echo "<pre>";
// print_r($query_sortir);
// echo "</pre>";

// die;

include("../config/konesi.php");

$sql2 = "SELECT * FROM statusnya";
$query2 = mysqli_query($konek, $sql2);
$status = mysqli_fetch_assoc($query2);
$harikerja = $status['info'];

$tanggal_pilih_plus = date('Y-m-d', strtotime($tanggal_pilih . ' +1 day'));

$cek_libur_tanggal_plus = limaharikerja($tanggal_pilih_plus);
if ($cek_libur_tanggal_plus == true) {
    $tanggal_pilih_plus = date('Y-m-d', strtotime($tanggal_pilih_plus . ' +1 day'));
    $cek_libur_tanggal_plus = limaharikerja($tanggal_pilih_plus);
    if ($cek_libur_tanggal_plus == true) {
        $tanggal_pilih_plus = date('Y-m-d', strtotime($tanggal_pilih_plus . ' +1 day'));
    }
}

$tanggal_pilih_min = date('Y-m-d', strtotime($tanggal_pilih . ' -1 day'));

$cek_libur_tanggal_min = limaharikerja($tanggal_pilih_min);
if ($cek_libur_tanggal_min == true) {
    $tanggal_pilih_min = date('Y-m-d', strtotime($tanggal_pilih_min . ' -1 day'));
    $cek_libur_tanggal_min = limaharikerja($tanggal_pilih_min);
    if ($cek_libur_tanggal_min == true) {
        $tanggal_pilih_min = date('Y-m-d', strtotime($tanggal_pilih_min . ' -1 day'));
    }
}

if ($tanggal_pilih_plus >= (date('Y-m-d', strtotime($tanggal . ' +1 day')))) {
    $tanggal_pilih_plus = '#';
    $disabled = ' disabled btn btn-secondary';
} else {
    $tanggal_pilih_plus = 'semuakelas.php?tanggal=' . $tanggal_pilih_plus . $link_pilih;
    $disabled = '';
}

$cek_libur_tanggal = limaharikerja($tanggal_pilih);
if ($cek_libur_tanggal == true) {
    $tanggal_kurangi_1 = date('Y-m-d', strtotime('-1 day', strtotime($tanggal)));
    $cek_libur_tanggal = limaharikerja($tanggal_kurangi_1);
    if ($cek_libur_tanggal == true) {
        $tanggal_kurangi_2 = date('Y-m-d', strtotime('-1 day', strtotime($tanggal_kurangi_1)));
        $cek_libur_tanggal = limaharikerja($tanggal_kurangi_2);
        if ($cek_libur_tanggal == true) {
            $tanggal_kurangi_3 = date('Y-m-d', strtotime('-1 day', strtotime($tanggal_kurangi_2)));
            $cek_libur_tanggal = limaharikerja($tanggal_kurangi_3);
            if ($cek_libur_tanggal == false) {
                $pesan = 'Hari ini Libur ya...';
                header('location: semuakelas.php?tanggal=' . $tanggal_kurangi_3);
            }
        } else {
            $pesan = 'Hari ini Libur ya...';
            header('location: semuakelas.php?tanggal=' . $tanggal_kurangi_2);
        }
    } else {
        $pesan = 'Hari ini Libur ya...';
        header('location: semuakelas.php?tanggal=' . $tanggal_kurangi_1);
    }
}

$title = 'Semua Kelas';
$kelasjur = $_GET['kelasjur'];

if(isset($kelasjur)){
    $title = "Rekap Presensi Kelas $kelasjur";
}

$navlink = 'Wali';
$navlink_sub = 'semuakelas';
include('views/_semuakelas.php');

mysqli_close($konek);

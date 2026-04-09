<?php
include('app/get_user.php');
$tahun = date('Y');
$tahun_pilih = date('Y');
$tanggal = date('Y-m-d');
$tanggal_pilih = date('Y-m-d');
$tahun_bulan = date('Y-m');
$tahun_bulan_pilih = date('Y-m');
$bulan_tahun = date('m-Y');
$bulan_indo = date('F', strtotime($tanggal));
$bulan_indo_pilih = bulanIndo(date('F', strtotime($tanggal_pilih)));
$bulan_tahun_indo = bulanIndo(date('F', strtotime($tanggal))) . ' ' . date('Y', strtotime($tanggal));
$bulan_tahun_indo_pilih = bulanIndo(date('F', strtotime($tanggal_pilih))) . ' ' . date('Y', strtotime($tanggal_pilih));

include('../config/konesi.php');

if(@$_GET['tahun']){
    $tahun_pilih = $_GET['tahun'];
    $tahun_pilih = mysqli_real_escape_string($konek, $tahun_pilih);
} else {
    $tahun_pilih = date('Y');
}

if (@$_GET['datab']) {
    $datab = $_GET['datab'];
    $datab = mysqli_real_escape_string($konek, $datab);
    if ($datab == 'siswa') {
        $title = 'Rekap Tahunan Wali Kelas ' . $ket_akses_login;
        $navlink = 'Wali';
        $navlink_sub = 'tahunan';

        if ($akses_login != 'Wali Kelas') {
            header('location: semuakelas.php');
        } else {
            // echo 'S = 1';
            // $databasis = 'datasiswa';
            // $where_sql = "WHERE kelas = '" . $ket_akses_login . "'";
            // $where_sql_2 = "WHERE info = '" . $ket_akses_login . "'";
            $link_datab = 'tahunan.php?datab=siswa&tahun=';
            $sql1 = "SELECT * FROM datasiswa WHERE kelas='" . $ket_akses_login . "' ORDER BY nama ASC";
            $sql2 = "SELECT * FROM datapresensi WHERE info='" . $ket_akses_login . "' AND YEAR(tanggal) = '$tahun_pilih' ORDER BY nama ASC";
        }
    } else if ($datab == 'GTK') {
        $title = 'Rekap Presensi Tahunan GTK ';
        $navlink = 'Data Guru';
        $navlink_sub = 'tahunan';
        $ket_akses_login = 'Guru dan Tenaga Pendidikan';

        // echo 'S = 2';
        if ($level_login == 'admin' || $level_login == 'superadmin') {

            // $databasis = 'dataguru';
            // $where_sql = '';
            // $where_sql_2 = "WHERE (info = 'GR' OR info = 'KR')";
            $link_datab = 'tahunan.php?datab=GTK&tahun=';
            $sql1 = "SELECT * FROM dataguru ORDER BY nama ASC";
            $sql2 = "SELECT * FROM datapresensi WHERE (kode = 'GR' OR kode = 'KR') AND YEAR(tanggal) = '$tahun_pilih'";
        } else {
            header('location: semuakelas.php');
        }
    } else {
        // echo 'S = 3';
        if ($level_login == 'admin' || $level_login == 'superadmin') {
            header('location:tahunan.php?datab=GTK');
        } else if ($level_login == 'user_gtk') {
            header('location:tahunan.php?datab=siswa');
        } else {
            $_SESSION['pesan'] = 'Anda tidak memiliki akses ke halaman ini. Maaf! <br> Silakan kembali ke Home / Beranda. <i>(Kode: #TA002)</i>';
            header('location: 404.php');
        }
    }
} else {
    // echo 'S = 4';
    if ($level_login == 'admin' || $level_login == 'superadmin') {
        // echo 'S = 5';
        header('location:tahunan.php?datab=GTK');
    } else if ($level_login == 'user_gtk') {
        // echo 'S = 6';
        header('location:tahunan.php?datab=siswa');
    } else {
        // echo 'S = 7';
        $_SESSION['pesan'] = 'Anda tidak memiliki akses ke halaman ini. Maaf! <br> Silakan kembali ke Home / Beranda. <i>(Kode: #TA001)</i>';
        header('location: 404.php');
    }
}


$tanggal_pilih_min = (int)$tahun_pilih - 1;
// $tanggal_pilih_plus = 'tahunan.php?tahun=' . (int)$tahun_pilih + 1;

if($tahun_pilih == date('Y')){
    $tanggal_pilih_plus = 'tahunan.php?#';
    $disabled_plus = 'disabled';
} else {
    $tanggal_pilih_plus = $link_datab . ((int)$tahun_pilih + 1);
    $disabled_plus = '';
}

// $sql1 = "SELECT * FROM datasiswa WHERE kelas = '$ket_akses_login' ORDER BY nama ASC";
$query1 = mysqli_query($konek, $sql1);
$hit_data = mysqli_num_rows($query1);

$hasil_data = array();
while ($data = mysqli_fetch_array($query1)) {
    $hasil_data[] = $data;
}

// $sql2 = "SELECT * FROM datapresensi WHERE info = '$ket_akses_login' AND YEAR(tanggal) = '$tahun_pilih'";
$query2 = mysqli_query($konek, $sql2);
$hit_datapresensi = mysqli_num_rows($query2);


$hasil_datapresensi = array();
while ($datapresensi = mysqli_fetch_array($query2)) {
    $hasil_datapresensi[] = $datapresensi;
}

include('views/_tahunan.php');
mysqli_close($konek);
include('views/footer.php');

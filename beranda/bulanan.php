<?php
include('app/get_user.php');
$tahun = date('Y');
$tanggal = date('Y-m-d');
$tahun_bulan = date('Y-m');
$bulan_tahun = date('m-Y');
$bulan_indo = date('F', strtotime($tanggal));
$bulan_tahun_indo = bulanIndo(date('F', strtotime($tanggal))) . ' ' . date('Y', strtotime($tanggal));

if (@$_GET['bulan']) {
    $tahun_bulan_get = $_GET['bulan'];
    $tahun_pilih = date('Y', strtotime($tahun_bulan_get));
    $tanggal_pilih = date('Y-m-d', strtotime($tahun_bulan_get));
    $tahun_bulan_pilih = date('Y-m', strtotime($tahun_bulan_get));
    $bulan_indo_pilih = bulanIndo(date('F', strtotime($tahun_bulan_get)));
    $bulan_tahun_indo_pilih = bulanIndo(date('F', strtotime($tahun_bulan_get))) . ' ' . date('Y', strtotime($tahun_bulan_get));
} else {
    $tahun_pilih = date('Y');
    $tanggal_pilih = date('Y-m-d');
    $tahun_bulan_pilih = date('Y-m');
    $bulan_indo_pilih = bulanIndo(date('F', strtotime($tanggal_pilih)));
    $bulan_tahun_indo_pilih = bulanIndo(date('F', strtotime($tanggal_pilih))) . ' ' . date('Y', strtotime($tanggal_pilih));
}

if (@$_GET['datab']) {
    $datab = $_GET['datab'];
    if ($datab == 'siswa') {
        $title = 'Wali Kelas ' . $ket_akses_login . ' - ' . $bulan_tahun_indo_pilih;
        $navlink = 'Wali';
        $navlink_sub = 'bulanan';

        if ($akses_login != 'Wali Kelas') {
            header('location: semuakelas.php');
        } else {
            // echo 'S = 1';
            // $databasis = 'datasiswa';
            // $where_sql = "WHERE kelas = '" . $ket_akses_login . "'";
            // $where_sql_2 = "WHERE info = '" . $ket_akses_login . "'";
            $link_datab = 'bulanan.php?datab=siswa&bulan=';
            $sql1 = "SELECT * FROM datasiswa WHERE kelas='" . $ket_akses_login . "' ORDER BY nama ASC";
            $sql2 = "SELECT * FROM datapresensi WHERE info='" . $ket_akses_login . "' ORDER BY nama ASC";
        }
    } else if ($datab == 'GTK') {
        $title = 'Data Guru dan Tendik - ' . $bulan_tahun_indo_pilih;
        $navlink = 'Data Guru';
        $navlink_sub = 'bulanan';
        $ket_akses_login = 'Guru dan Tenaga Pendidikan';

        // echo 'S = 2';
        if ($level_login == 'admin' || $level_login == 'superadmin') {

            // $databasis = 'dataguru';
            // $where_sql = '';
            // $where_sql_2 = "WHERE (info = 'GR' OR info = 'KR')";
            $link_datab = 'bulanan.php?datab=GTK&bulan=';
            $sql1 = "SELECT * FROM dataguru ORDER BY nama ASC";
            $sql2 = "SELECT * FROM datapresensi WHERE (kode = 'GR' OR kode = 'KR')";
        } else {
            header('location: semuakelas.php');
        }
    } else {
        // echo 'S = 3';
        if ($level_login == 'admin' || $level_login == 'superadmin') {
            header('location:bulanan.php?datab=GTK');
        } else if ($level_login =='user_gtk') {
            header('location:bulanan.php?datab=siswa');
        } else {
            $_SESSION['pesan'] = 'Anda tidak memiliki akses ke halaman ini. Maaf! <br> Silakan kembali ke Home / Beranda. <i>(Kode: #BU002)</i>';
            header('location: 404.php');
        }
    }
} else {
    // echo 'S = 4';
    if ($level_login == 'admin' || $level_login == 'superadmin') {
        // echo 'S = 5';
        header('location:bulanan.php?datab=GTK');
    } else if ($level_login == 'user_gtk') {
        // echo 'S = 6';
        header('location:bulanan.php?datab=siswa');
    } else {
        $_SESSION['pesan'] = 'Anda tidak memiliki akses ke halaman ini. Maaf! <br> Silakan kembali ke Home / Beranda. <i>(Kode: #BU001)</i>';
        // echo 'S = 7';
        header('location: 404.php');
    }
}

include('../config/konesi.php');

// $sql = "SELECT * FROM datasiswa WHERE kelas = '$ket_akses_login' ORDER BY nama ASC";
// $sql2 = "SELECT * FROM datapresensi WHERE info = '$ket_akses_login'";
// $sql2 = "SELECT * FROM datapresensi WHERE (kode = 'GR' OR kode = 'KR')";
$query1 = mysqli_query($konek, $sql1);
$query2 = mysqli_query($konek, $sql2);
$hit_data = mysqli_num_rows($query1);


$hasil_data = array();
while ($datasiswa = mysqli_fetch_array($query1)) {
    $hasil_data[] = $datasiswa;
}

$hasil_datapresensi = array();
while ($datapresensi = mysqli_fetch_array($query2)) {
    $hasil_datapresensi[] = $datapresensi;
}

$tahun_bulan_pilih_plus = date('Y-m', strtotime($tahun_bulan_pilih . ' +1 month'));
$tahun_bulan_pilih_min = date('Y-m', strtotime($tahun_bulan_pilih . ' -1 month'));

if ($tahun_bulan_pilih_plus == date('Y-m', strtotime($tahun_bulan . ' +1 month'))) {
    $tahun_bulan_pilih_plus = '#';
    $disabled_plus = 'disabled';
} else {
    $tahun_bulan_pilih_plus = $link_datab . $tahun_bulan_pilih_plus;
    $disabled_plus = '';
}

// // cari data di array
// $nokartu = $hasil_data[5]['nokartu'];
// $nama = $hasil_data[5]['nama'];
// $tanggal_pilih_ini = '2022-02-10';
// $hasil_cari_presensi = cari_data_presensi($nokartu, $hasil_datapresensi);
// $hasil_cari_tanggal = cari_tanggal($tanggal_pilih_ini, $hasil_cari_presensi);

// $nokartu_presensi = $hasil_cari_tanggal[0]['nokartu'];
// $tanggal_presensi = $hasil_cari_tanggal[0]['tanggal'];
// $waktumasuk_presensi = $hasil_cari_tanggal[0]['waktumasuk'];
// $waktukeluar_presensi = @$hasil_cari_tanggal[0]['waktukeluar'];
// $keterangan_presensi = $hasil_cari_tanggal[0]['keterangan'];

// print_r('<br>');
// print_r('nokartu_presensi : ' . $nokartu_presensi);
// print_r('<br>');
// print_r('tanggal_presensi : ' . $tanggal_presensi);
// print_r('<br>');
// print_r('waktumasuk_presensi : ' . $waktumasuk_presensi);
// print_r('<br>');
// print_r('waktukeluar_presensi : ' . $waktukeluar_presensi);
// print_r('<br>');
// print_r('keterangan_presensi : ' . $keterangan_presensi);
// print_r('<br>');

// print_r('no kartu siswa: ' . $nokartu);
// print_r('<br>');
// print_r('nama siswa: ' . $nama);
// print_r('<br>');
// print_r('<br>');
// print_r('hasil_cari_tanggal : ');
// print_r('<br>');
// printf('<pre>%s</pre>', print_r($hasil_cari_tanggal, true));
// print_r('<br>');
// print_r('<br>');
// print_r('hasil_cari_presensi : ');
// print_r('<br>');
// printf('<pre>%s</pre>', print_r($hasil_cari_presensi, true));
// print_r('<br>');

// print_r('<br>');
// print_r('hasil_data : ');
// printf("<pre> %s </pre>", print_r($hasil_data, true));
// print_r('<br>');
// print_r('hasil_datapresensi : ');
// printf("<pre> %s </pre>", print_r($hasil_datapresensi, true));

// function cari_data_presensi($nokartu, $hasil_datapresensi)
// {
//     $hasil_cari_presensi = array();
//     foreach ($hasil_datapresensi as $dtp) {
//         if ($dtp['nokartu'] == $nokartu) {
//             $hasil_cari_presensi[] = $dtp;
//         }
//     }

//     return $hasil_cari_presensi;
// }

// // // cari berdasarkan tanggal di hasil cari presensi
// function cari_tanggal($tanggal_pilih, $hasil_cari_presensi)
// {
//     $hasil_cari_tanggal = array();
//     foreach ($hasil_cari_presensi as $dtp) {
//         if ($dtp['tanggal'] == $tanggal_pilih) {
//             $hasil_cari_tanggal[] = $dtp;
//         }
//     }
//     return $hasil_cari_tanggal;
// }

// die;

include('views/header.php');
include('views/_bulanan.php');

mysqli_close($konek);

include('views/footer.php');

?>


<?php
function cari_data_presensi($nokartu_siswa, $hasil_datapresensi)
{
    $hasil_cari_presensi = array();
    foreach ($hasil_datapresensi as $dtp) {
        if ($dtp['nokartu'] == $nokartu_siswa) {
            $hasil_cari_presensi[] = $dtp;
        }
    }

    return $hasil_cari_presensi;
}

// // cari berdasarkan tanggal di hasil cari presensi
function cari_tanggal($tanggal_pilih, $hasil_cari_presensi)
{
    $hasil_cari_tanggal = array();
    foreach ($hasil_cari_presensi as $dtp) {
        if ($dtp['tanggal'] == $tanggal_pilih) {
            $hasil_cari_tanggal[] = $dtp;
        }
    }
    return $hasil_cari_tanggal;
}
?>
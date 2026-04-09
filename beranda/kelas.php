<?php
include('app/get_user.php');
$tanggal = date('Y-m-d');
$tanggal_dmY = date('d-m-Y');
$tahun = date('Y');

include("../config/konesi.php");

if (@$_POST) {
    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";
    // die;

    $p_nick = mysqli_real_escape_string($konek, @$_POST['nick']);
    $p_nama_guru = mysqli_real_escape_string($konek, @$_POST['nama_guru']);
    $p_ruangan = mysqli_real_escape_string($konek, @$_POST['ruangan']);
    $p_info_ruangan = mysqli_real_escape_string($konek, @$_POST['info_ruangan']);
    $p_kelas = mysqli_real_escape_string($konek, @$_POST['kelas']);
    $p_kelompok = mysqli_real_escape_string($konek, @$_POST['kelompok']);
    $p_jur = mysqli_real_escape_string($konek, @$_POST['jur']);
    $p_tingkat = mysqli_real_escape_string($konek, @$_POST['tingkat']);
    $p_jamke = mysqli_real_escape_string($konek, @$_POST['jamke']);
    $p_sampai_jamke = mysqli_real_escape_string($konek, @$_POST['sampai_jamke']);
    $p_tanggal = mysqli_real_escape_string($konek, @$_POST['tanggal']);
    $p_jurnal = mysqli_real_escape_string($konek, @$_POST['jurnal']);

    // echo "<br>nick : " . $p_nick;
    // echo "<br>nama_guru : " . $p_nama_guru;
    // echo "<br>ruangan : " . $p_ruangan;
    // echo "<br>info_ruangan : " . $p_info_ruangan;
    // echo "<br>kelas : " . $p_kelas;
    // echo "<br>kelompok : " . $p_kelompok;
    // echo "<br>jur : " . $p_jur;
    // echo "<br>tingkat : " . $p_tingkat;
    // echo "<br>jamke : " . $p_jamke;
    // echo "<br>sampai_jamke : " . $p_sampai_jamke;
    // echo "<br>tanggal : " . $p_tanggal;
    // echo "<br>jurnal : " . $p_jurnal;

    // cek eksistansi jurnal
    $cek_jurnal = mysqli_query($konek, "SELECT * FROM jurnalguru WHERE tanggal = '$p_tanggal' AND jamke = '$p_jamke' AND kelas = '$p_kelas' AND ruangan = '$p_ruangan' AND nick = '$p_nick'");
    $hasil_cek_jurnal = mysqli_num_rows($cek_jurnal);

    if ($hasil_cek_jurnal == 0) {
        $qu_jurnal = mysqli_query($konek, "INSERT INTO `jurnalguru`
    (`nick`, `nama`, `jurnal`, `ruangan`, `info`, `kelas`, `kelompok`, `jur`, `tingkat`, `jamke`, `sampai_jamke`, `tanggal`) VALUES ('$p_nick','$p_nama_guru','$p_jurnal','$p_ruangan','$p_info_ruangan','$p_kelas','$p_kelompok','$p_jur','$p_tingkat','$p_jamke','$p_sampai_jamke','$p_tanggal')");

        if ($qu_jurnal) {
            echo "<br>Berhasil tambahkan jurnal";
        } else {
            echo "<br>Gagal tambahkan jurnal<br>error : " . mysqli_error($konek);
        }
    } else {
        $update_jurnal = mysqli_query($konek, "UPDATE jurnalguru SET jurnal = '$p_jurnal' WHERE tanggal = '$p_tanggal' AND jamke = '$p_jamke' AND kelas = '$p_kelas' AND ruangan = '$p_ruangan' AND nick = '$p_nick'");

        if ($update_jurnal) {
            echo "<br>Berhasil update jurnal";
        } else {
            echo "<br>Gagal update jurnal<br>error : " . mysqli_error($konek);
        }
    }

    $url_ini = $_SERVER['REQUEST_URI'];
    header("location: " . $url_ini);
    // echo "url_ini: " . $url_ini;
    // die;
}

if (@$_GET['tanggal']) {
    $tanggal_get = mysqli_real_escape_string($konek, $_GET['tanggal']);

    if ($tanggal_get > $tanggal) {
        $tanggal_get = date('Y-m-d', strtotime($tanggal));
    }

    $tanggal_pilih = date('Y-m-d', strtotime($tanggal_get));
    $tanggal_pilih_dmY = date('d-m-Y', strtotime($tanggal_get));
    $tanggal_pilih_Ymd = date('Ymd', strtotime($tanggal_get));
    $nama_hari_pilih = hariIndo(date('l', strtotime($tanggal_get)));
    $nama_hari_singkat_pilih = hariSingkatIndo(date('l', strtotime($tanggal_get)));
    $tahun_bulan_pilih = date('Y-m', strtotime($tanggal_get));
    $bulan_tahun_pilih = date('m-Y', strtotime($tanggal_get));
    $bulanIndoPilih = bulanIndo(date('F', strtotime($tanggal_get)));
} else {
    $tanggal_pilih = date('Y-m-d');
    $tanggal_pilih_dmY = date('d-m-Y');
    $tanggal_pilih_Ymd = date('Ymd');
    $nama_hari_pilih = hariIndo(date('l', strtotime($tanggal_pilih)));
    $nama_hari_singkat_pilih = hariSingkatIndo(date('l', strtotime($tanggal_pilih)));
    $tahun_bulan_pilih = date('Y-m');
    $bulan_tahun_pilih = date('m-Y');
    $bulanIndoPilih = bulanIndo(date('F', strtotime($tanggal_pilih)));
}

$tanggal_indo_pilih = date('d', strtotime($tanggal_pilih)) . " " . $bulanIndoPilih . " " . date('Y', strtotime($tanggal_pilih));
$hari_indo = hariIndo(date('l', strtotime($tanggal_pilih)));

$hari_ing = date('l', strtotime($tanggal_pilih));
$hari_ini_s = strtolower(hariSingkatIndo($hari_ing));

$jur_get = mysqli_real_escape_string($konek, @$_GET['jur']);
$kelas_get = mysqli_real_escape_string($konek, @$_GET['kelas']);
$tingkat_get = mysqli_real_escape_string($konek, @$_GET['tingkat']);
$kelasjur_get = mysqli_real_escape_string($konek, @$_GET['kelasjur']);
$kelompokjur_get = mysqli_real_escape_string($konek, @$_GET['kelompokjur']);

// START: cek Guru dan jadwal sesuai tanggal pilih
$cek_guru_jadwal = mysqli_query($konek, "SELECT * FROM jadwalkbm WHERE nick = '$nick_login'");
$hasil_cek_guru_jadwal = mysqli_fetch_array($cek_guru_jadwal);
$ruangan = @$hasil_cek_guru_jadwal['ruangan'];
$jur_ = @$hasil_cek_guru_jadwal['jur'];


$cek_guru_jadwal = mysqli_query($konek, "SELECT * FROM dataguru WHERE nick = '$nick_login'");
$hasil_cek_guru_jadwal = mysqli_fetch_array($cek_guru_jadwal);
$nama_guru = @$hasil_cek_guru_jadwal['nama'];

if (!$jur_get) {
    $jur_pilih = @$jur_;
} else {
    $jur_pilih = $jur_get;
}

// cek ruangan guru di jadwal kbm
$hit_datasiswa = 0;
$hit_jadwal_guru = 0;
$hasil_cek_ruanganguru_jadwalkbm = array();


// $tanggal_pilih = mysqli_real_escape_string($konek, $tanggal_pilih);
$cek_ruanganguru_jadwalkbm = mysqli_query($konek, "SELECT * FROM jadwalkbm WHERE nick = '$nick_login' AND tanggal = '$tanggal_pilih' ORDER BY mulai_jamke ASC");
$hit_jadwal_kbm = mysqli_num_rows($cek_ruanganguru_jadwalkbm);
while ($_hasil_cek_ruanganguru_jadwalkbm = mysqli_fetch_array($cek_ruanganguru_jadwalkbm)) {
    $hasil_cek_ruanganguru_jadwalkbm[] = $_hasil_cek_ruanganguru_jadwalkbm;
    $hit_jadwal_guru++;
}

// echo "<br>hasil_cek_ruanganguru_jadwalkbm<pre>";
// print_r($hasil_cek_ruanganguru_jadwalkbm);
// echo "</pre>";
// echo "<br>";
// die;

// START: link tombol 'selanjutnya' dan 'berikutnya'
$link_pilih = @$_GET['jur'] ? '&jur=' . mysqli_real_escape_string($konek, $_GET['jur']) : '';

// END: link tombol 'selanjutnya' dan 'berikutnya'

$idjdwl = mysqli_real_escape_string($konek, @$_GET['idjdwl']);

if (!$idjdwl) {
    $idjdwl = 0;
}

if (!$kelas_get) {
    $kelas_kbm = @$hasil_cek_ruanganguru_jadwalkbm[$idjdwl]['kelas'];
} else {
    $kelas_kbm = $kelas_get;
}

if (!$tingkat_get) {
    $tingkat_kbm = @$hasil_cek_ruanganguru_jadwalkbm[$idjdwl]['tingkat'];
} else {
    $tingkat_kbm = $tingkat_get;
}

// echo "<br>";
// echo "Kelas : " . $kelas_kbm;
// die;

if ($hit_jadwal_kbm > 0 && (@$_GET['p'] != 'y')) {
    // echo "<br>";
    // echo "Ada KBM";
    // die;
    // ambil data jadwal yang dipilih

    $kelompok_kbm = @$hasil_cek_ruanganguru_jadwalkbm[$idjdwl]['kelompok'];
    $ruangan = @$hasil_cek_ruanganguru_jadwalkbm[$idjdwl]['ruangan'];
    $info_ruangan = @$hasil_cek_ruanganguru_jadwalkbm[$idjdwl]['info'];
    $jur_kbm = @$hasil_cek_ruanganguru_jadwalkbm[$idjdwl]['jur'];
    $mulai_kbm = @$hasil_cek_ruanganguru_jadwalkbm[$idjdwl]['mulai_jamke'];
    $sampai_kbm = @$hasil_cek_ruanganguru_jadwalkbm[$idjdwl]['sampai_jamke'];

    if ($kelompok_kbm) {
        $sortir_siswa = "kelompok = '" . $kelompok_kbm . "' AND tingkat = '" . $tingkat_kbm . "'";
        $list_jadwal_guru = 'kelompok';
        $text_jadwal_guru = $tingkat_kbm . " " . $jur_kbm . " " . "Kelompok : ";

        $sortir_siswa = $sortir_siswa . " AND (kode = '" . $tingkat_kbm . $jur_pilih . "1'";
        $sortir_siswa = $sortir_siswa . " OR kode = '" . $tingkat_kbm . $jur_pilih . "2'";
        $sortir_siswa = $sortir_siswa . " OR kode = '" . $tingkat_kbm . $jur_pilih . "3')";
    } else {
        $sortir_siswa = "kode = '" . $kelas_kbm . "' AND tingkat = '" . $tingkat_kbm . "'";
        $list_jadwal_guru = 'kelas';
        $text_jadwal_guru = 'Kelas : ';
    }

    // START: ambil data siswa sesuai kelompok / kelas
    $hasil_datasiswa = array();
    // $sortir_siswa = mysqli_real_escape_string($konek, $sortir_siswa);

    // echo "<br>";
    // echo "Sortir : " . $sortir_siswa;
    // die;

    $qu_kelompok_kelas = mysqli_query($konek, "SELECT * FROM datasiswa WHERE " . $sortir_siswa);


    while ($_hasil_datasiswa = mysqli_fetch_array($qu_kelompok_kelas)) {
        $hit_datasiswa++;
        $hasil_datasiswa[] = $_hasil_datasiswa;
    }

    // echo "<pre>";
    // print_r($hasil_datasiswa);
    // echo "</pre>";
    // die;

    // END: ambil data siswa sesuai kelompok / kelas
} else {
    // echo "Tidak ada KBM";
    // die;

    if (@$_GET['p'] == 'y') {
        if ($kelasjur_get) {
            $kelasjur = $kelasjur_get;
            $sortir_siswa = " kode = '" . $kelasjur . "'";
        } else {

            if ($jur_pilih && $tingkat_kbm) {
                $sortir_siswa = " kode = '" . $tingkat_kbm . $jur_pilih . "1'";
                $sortir_siswa = $sortir_siswa . " OR kode = '" . $tingkat_kbm . $jur_pilih . "2'";
                $sortir_siswa = $sortir_siswa . " OR kode = '" . $tingkat_kbm . $jur_pilih . "3'";
                $query_sortir_siswa = " WHERE (" . $sortir_siswa . ")";
            } elseif ($jur_pilih && !$tingkat_kbm) {
                if (@$_GET['tingkat']) {
                    if ($_GET['tingkat'] == 'X') {
                        $sortir_siswa = " kode = 'X" . $jur_pilih . "1'";
                        $sortir_siswa = $sortir_siswa . " OR kode = 'X" . $jur_pilih . "2'";
                        $sortir_siswa = $sortir_siswa . " OR kode = 'X" . $jur_pilih . "3'";
                    } elseif ($_GET['tingkat'] == 'XI') {
                        $sortir_siswa = " kode = 'XI" . $jur_pilih . "1'";
                        $sortir_siswa = $sortir_siswa . " OR kode = 'XI" . $jur_pilih . "2'";
                        $sortir_siswa = $sortir_siswa . " OR kode = 'XI" . $jur_pilih . "3'";
                    } elseif ($_GET['tingkat'] == 'XII') {
                        $sortir_siswa = " kode = 'XII" . $jur_pilih . "1'";
                        $sortir_siswa = $sortir_siswa . " OR kode = 'XII" . $jur_pilih . "2'";
                        $sortir_siswa = $sortir_siswa . " OR kode = 'XII" . $jur_pilih . "3'";
                    }
                } else {
                    $sortir_siswa = " kode = 'X" . $jur_pilih . "1'";
                    $sortir_siswa = $sortir_siswa . " OR kode = 'X" . $jur_pilih . "2'";
                    $sortir_siswa = $sortir_siswa . " OR kode = 'X" . $jur_pilih . "3'";
                    $sortir_siswa = $sortir_siswa . " OR kode = 'XI" . $jur_pilih . "1'";
                    $sortir_siswa = $sortir_siswa . " OR kode = 'XI" . $jur_pilih . "2'";
                    $sortir_siswa = $sortir_siswa . " OR kode = 'XI" . $jur_pilih . "3'";
                    $sortir_siswa = $sortir_siswa . " OR kode = 'XII" . $jur_pilih . "1'";
                    $sortir_siswa = $sortir_siswa . " OR kode = 'XII" . $jur_pilih . "2'";
                    $sortir_siswa = $sortir_siswa . " OR kode = 'XII" . $jur_pilih . "3'";
                }
            }
        }

        if ($kelompokjur_get) {
            $sortir_siswa = "(" . $sortir_siswa . ") AND kelompok = '" . $kelompokjur_get . "'";
        }

        // $sortir_siswa = "kode = '" . $kelas_kbm . "' AND tingkat = '" . $tingkat_kbm . "' AND jur = '" . $jur_pilih . "'";
        $list_jadwal_guru = 'kelas';
        $text_jadwal_guru = 'Kelas : ';

        // START: ambil data siswa sesuai kelompok / kelas
        $hasil_datasiswa = array();
        // $sortir_siswa = mysqli_real_escape_string($konek, $sortir_siswa);
        $qu_kelompok_kelas = mysqli_query($konek, "SELECT * FROM datasiswa WHERE " . $sortir_siswa . " ORDER BY kelas ASC");
        while ($_hasil_datasiswa = mysqli_fetch_array($qu_kelompok_kelas)) {
            $hit_datasiswa++;
            $hasil_datasiswa[] = $_hasil_datasiswa;
        }
        // END: ambil data siswa sesuai kelompok / kelas
    }
}
// END: cek Guru dan jadwal sesuai tanggal pilih

// echo "<br>";
// echo "<pre>";
// print_r($sortir_siswa);
// echo "</pre>";
// echo "<br>";
// echo "<br>hasil_cek_ruanganguru_jadwalkbm : <pre>";
// print_r($hasil_datasiswa);
// echo "</pre>";
// echo "<br>Kelas : " . $kelas_kbm;
// echo "<br>jur : " . $jur_pilih;
// die;
// START: memilih tampilan hari kerja saja
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
    $tanggal_pilih_plus = 'kelas.php?jur=' . $jur_pilih . '&tanggal=' . $tanggal_pilih_plus . $link_pilih;
    $disabled = '';
}

$cek_libur_tanggal = limaharikerja($tanggal_pilih);
if ($cek_libur_tanggal == true) {
    $tanggal_kurangi_1 = date('Y-m-d', strtotime('-1 day', strtotime($tanggal_pilih)));
    $tanggal_pilih = $tanggal_kurangi_1;
    $cek_libur_tanggal = limaharikerja($tanggal_kurangi_1);
    if ($cek_libur_tanggal == true) {
        $tanggal_kurangi_2 = date('Y-m-d', strtotime('-1 day', strtotime($tanggal_kurangi_1)));
        $tanggal_pilih = $tanggal_kurangi_2;
        $cek_libur_tanggal = limaharikerja($tanggal_kurangi_2);
        if ($cek_libur_tanggal == true) {
            $tanggal_kurangi_3 = date('Y-m-d', strtotime('-1 day', strtotime($tanggal_kurangi_2)));
            $tanggal_pilih = $tanggal_kurangi_3;
            $cek_libur_tanggal = limaharikerja($tanggal_kurangi_3);
            if ($cek_libur_tanggal == false) {
                $pesan = 'Hari ini Libur ya...';
                header('location: kelas.php?tanggal=' . $tanggal_kurangi_3 . $link_pilih);
            }
        } else {
            $pesan = 'Hari ini Libur ya...';
            header('location: kelas.php?tanggal=' . $tanggal_kurangi_2 . $link_pilih);
        }
    } else {
        $pesan = 'Hari ini Libur ya...';
        header('location: kelas.php?tanggal=' . $tanggal_kurangi_1 . $link_pilih);
    }
}
// END: memilih tampilan hari kerja saja

$title = 'Kelas Saya';
$navlink = 'Kelas';

include('views/_kelas.php');
mysqli_close($konek);

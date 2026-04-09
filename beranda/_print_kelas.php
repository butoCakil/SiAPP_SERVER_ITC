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

    $p_nick = @$_POST['nick'];
    $p_nama_guru = @$_POST['nama_guru'];
    $p_ruangan = @$_POST['ruangan'];
    $p_info_ruangan = @$_POST['info_ruangan'];
    $p_kelas = @$_POST['kelas'];
    $p_kelompok = @$_POST['kelompok'];
    $p_jur = @$_POST['jur'];
    $p_tingkat = @$_POST['tingkat'];
    $p_jamke = @$_POST['jamke'];
    $p_sampai_jamke = @$_POST['sampai_jamke'];
    $p_tanggal = @$_POST['tanggal'];
    $p_jurnal = @$_POST['jurnal'];

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
    $tanggal_get = $_GET['tanggal'];
    $tanggal_get = mysqli_real_escape_string($konek, $tanggal_get);

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

$jur_get = @$_GET['jur'];
$jur_get = mysqli_real_escape_string($konek, $jur_get);
$kelas_get = @$_GET['kelas'];
$kelas_get = mysqli_real_escape_string($konek, $kelas_get);
$tingkat_get = @$_GET['tingkat'];
$tingkat_get = mysqli_real_escape_string($konek, $tingkat_get);
$kelasjur_get = @$_GET['kelasjur'];
$kelas_get = mysqli_real_escape_string($konek, $kelas_get);
$kelompokjur_get = @$_GET['kelompokjur'];
$kelompokjur_get = mysqli_real_escape_string($konek, $kelompokjur_get);

// START: cek Guru dan jadwal sesuai tanggal pilih
$cek_guru_jadwal = mysqli_query($konek, "SELECT * FROM jadwalkbm WHERE nick = '$nick_login'");
$hasil_cek_guru_jadwal = mysqli_fetch_array($cek_guru_jadwal);

$ruangan = @$hasil_cek_guru_jadwal['ruangan'];


$cek_guru_jadwal = mysqli_query($konek, "SELECT * FROM dataguru WHERE nick = '$nick_login'");
$hasil_cek_guru_jadwal = mysqli_fetch_array($cek_guru_jadwal);
$nama_guru = @$hasil_cek_guru_jadwal['nama'];

if (!$jur_get) {
    $jur_pilih = @$hasil_cek_guru_jadwal['jur'];
} else {
    $jur_pilih = $jur_get;
}

// cek ruangan guru di jadwal kbm
$hit_datasiswa = 0;
$hit_jadwal_guru = 0;
$hasil_cek_ruanganguru_jadwalkbm = array();
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
$link_pilih = '';

// END: link tombol 'selanjutnya' dan 'berikutnya'

$idjdwl = @$_GET['idjdwl'];
$idjdwl = mysqli_real_escape_string($konek, $idjdwl);

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

if ($hit_jadwal_kbm > 0 && (@$_GET['p'] != 'y')) {
    // echo "<br>";
    // echo "Ada KBM";
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

    // echo "<br>";
    // echo "Sortir : " . $sortir_siswa;
    // START: ambil data siswa sesuai kelompok / kelas
    $hasil_datasiswa = array();
    $qu_kelompok_kelas = mysqli_query($konek, "SELECT * FROM datasiswa WHERE " . $sortir_siswa);
    while ($_hasil_datasiswa = mysqli_fetch_array($qu_kelompok_kelas)) {
        $hit_datasiswa++;
        $hasil_datasiswa[] = $_hasil_datasiswa;
    }
    // END: ambil data siswa sesuai kelompok / kelas
} else {
    // echo "Tidak ada KBM";

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
    $tanggal_pilih_plus = 'print_kelas.php?jur=' . $jur_pilih . '&tanggal=' . $tanggal_pilih_plus . $link_pilih;
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
                header('location: print_kelas.php?tanggal=' . $tanggal_kurangi_3 . $link_pilih);
            }
        } else {
            $pesan = 'Hari ini Libur ya...';
            header('location: print_kelas.php?tanggal=' . $tanggal_kurangi_2 . $link_pilih);
        }
    } else {
        $pesan = 'Hari ini Libur ya...';
        header('location: print_kelas.php?tanggal=' . $tanggal_kurangi_1 . $link_pilih);
    }
}

$title = 'File Kelas Saya';
$navlink = 'Kelas';
include "views/header.php";
include "views/navbar.php";
?>

<section class="content">
    <div class="container-fluid">
        <div class="card bg-primary bg-gradient-primary elevation-3" style="border: none; z-index: 1;">
            <div id="header_rekap" class="card-body">
                <div style="display: flex; justify-content: baseline; justify-content: space-between;">
                    <div>
                        <a class="nav-link bg-light elevation-3" style="border-radius: 5px;" href="print_kelas.php?&jur=<?= @$jur_pilih; ?>&tanggal=<?= @$tanggal_pilih_min; ?><?= @$link_pilih_p; ?>">
                            <div style="display: flex; gap: 10px;">
                                <i class="fas fa-angle-double-left"></i>
                                <span>Sebelumnya</span>
                            </div>
                        </a>
                    </div>
                    <div style="display: flex; flex-direction: column; text-align: center;">
                        <div>
                            <h4 class="mt-2"><b><?= $hari_indo; ?></b>, </h4>
                        </div>
                        <div>
                            <h4><?= $tanggal_indo_pilih; ?></h4>
                        </div>
                    </div>
                    <div>
                        <a class="border-0 nav-link elevation-3 bg-light<?= $disabled; ?>" style="border-radius: 5px;" href="<?= $tanggal_pilih_plus; ?>">
                            <div style="display: flex; gap: 10px;">
                                <span>Berikutnya</span>
                                <i class="fas fa-angle-double-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12" style="margin-top: -20px;">
            <div class="card elevation-3">
                <div class="card-body mb-5">
                    <h5>Kelas
                        <?= (@$tingkat_kbm || @$kelasjur) ? (@$tingkat_kbm ? $tingkat_kbm : $kelasjur) : " Semua Kelas "; ?>&nbsp;-&nbsp;<?= @$kelompok_kbm ? "Kelompok : " . $kelompok_kbm : (@$kelas_kbm ? $kelas_kbm : " Semua Kelompok "); ?>&nbsp;<?= @$keterangan_ruang; ?> <span class="badge bg-gradient-danger"><?= @$info_ruangan . " [" . @$ruangan . "]"; ?></span></h5>

                    <?php
                    $query_instruktur = mysqli_query($konek, "SELECT * FROM jadwalkbm WHERE ruangan = '$ruangan'");
                    $cek_nama_instruktur = mysqli_num_rows($query_instruktur);

                    if ($cek_nama_instruktur > 0) {
                        echo "<h6>Instruktur :</h6>";
                        $no = "";
                        while ($data_instruktur = mysqli_fetch_array($query_instruktur)) {
                            $no++;
                            $nick = @$data_instruktur['nick'];

                            $sql_instruktur = "SELECT * FROM dataguru WHERE nick = '$nick'";
                            $que_instruktur = mysqli_query($konek, $sql_instruktur);
                            $hasil_inst = mysqli_fetch_array($que_instruktur);

                            $nama_instruktur = @$hasil_inst['nama'];
                            $nip_instruktur = @$hasil_inst['nip'];
                    ?>
                            <label for=""><?= $no; ?>. <?= $nama_instruktur; ?>

                                <?php if ($nick == $nick_login) { ?>&nbsp;<i class="fas fa-check-circle" style="color: green;"></i>
                            <?php } ?>
                            </label><br>
                        <?php }
                    } else { ?>
                        <h6>Guru :</h6>
                        <label for=""><?= $nama_login; ?>&nbsp;<i class="fas fa-check-circle" style="color: green;"></i></label>
                    <?php } ?>

                    <?php if (@$hit_datasiswa) { ?>
                        <div class="alert alert-info">
                            <h5 for="">Jadwal Hari <?= $hari_indo; ?>, <?= $tanggal_indo_pilih; ?></h5>

                            <?php
                            if ($hit_jadwal_guru > 1) {
                                echo '<div class="mb-1"><i class="fas fa-info-circle text-info"></i>&nbsp;';
                                echo "Pilih List untuk ditampilkan</div>";
                            }
                            for ($c_jadwal = 0; $c_jadwal < $hit_jadwal_guru; $c_jadwal++) {
                                if ($ruangan == @$hasil_cek_ruanganguru_jadwalkbm[$c_jadwal]['ruangan']) {
                                    $_radio_jadwal = 'checked';
                                } else {
                                    $_radio_jadwal = '';
                                }
                            ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault<?= $c_jadwal; ?>" <?= $_radio_jadwal; ?> onclick="self.location = 'print_kelas.php?jur=<?= $jur_pilih; ?>&tanggal=<?= $tanggal_pilih; ?>&idjdwl=<?= $c_jadwal; ?>'">
                                    <label class="form-check-label" for="flexRadioDefault<?= $c_jadwal; ?>">
                                        <p><?= $text_jadwal_guru; ?><?= $hasil_cek_ruanganguru_jadwalkbm[$c_jadwal][$list_jadwal_guru]; ?>, Jam ke : <?= $hasil_cek_ruanganguru_jadwalkbm[$c_jadwal]['mulai_jamke']; ?>, Ruangan : <?= @$hasil_cek_ruanganguru_jadwalkbm[$c_jadwal]['info'] . " [" . @$hasil_cek_ruanganguru_jadwalkbm[$c_jadwal]['ruangan'] . "]"; ?></p>
                                    </label>
                                </div>
                            <?php } ?>

                            <p>
                                <i class="fas fa-info-circle text-warning"></i>&nbsp;<?= @$data_jurnal['jurnal']; ?>
                            </p>
                            <!-- <i class="fas fa-file text-fuchsia"></i> -->
                            <!-- <i class="fas fa-circle"></i> -->
                            <!-- <i class="far fa-circle"></i> -->
                            <!-- <i class="fas fa-heart"></i> -->
                        </div>
                    <?php } ?>

                    <div class="row">
                        <table id="example1" class="table table-hover table-bordered table-striped">
                            <thead>
                                <tr style="text-align: center; position: sticky;">
                                    <th>No.</th>
                                    <th>Tanggal</th>
                                    <th>Nama</th>
                                    <!-- <th>Detail</th> -->
                                    <th>Kelas</th>
                                    <th>Status</th>
                                    <th style="width: 25%;">Catatan</th>
                                    <th>Aff</th>
                                    <th>Psi</th>
                                    <th>Kog</th>
                                    <th><i class="fas fa-plus-circle text-indigo"></i></th>
                                    <th>Kel.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 0;

                                if ($hit_datasiswa) {
                                    while ($no < $hit_datasiswa) {

                                        // $tanggal_pilih = '2022-02-10';
                                        // $tanggal_pilih_dmY = '10-02-2022';

                                        $nis_siswa = $hasil_datasiswa[$no]['nis'];
                                        $nick_siswa = $hasil_datasiswa[$no]['nick'];

                                        $sql_cek_presensikelas = "SELECT * FROM presensikelas WHERE nis = '$nis_siswa' AND tanggal LIKE '%$tanggal_pilih%'";
                                        $que_cek_presensikelas = mysqli_query($konek, $sql_cek_presensikelas);
                                        $array_hasil = mysqli_fetch_array(@$que_cek_presensikelas);

                                        $hasil_cek_presensikelas = mysqli_num_rows(@$que_cek_presensikelas);

                                        $id_presensi_kelas = @$array_hasil['id'] ? $array_hasil['id'] : '0';
                                ?>
                                        <tr <?= @$hct_bg_keterangan; ?> style="text-align: center;">
                                            <td style="width: 5%;"><?= $no + 1; ?></td>
                                            <td><?= $nama_hari_singkat_pilih; ?>, <?= $tanggal_pilih_dmY; ?></td>

                                            <td style="text-align: left;">
                                                <?= $hasil_datasiswa[$no]['nama']; ?>
                                            </td>
                                            <!-- <td>
                                            <span class="badge badge-sm bg-info"><i class="fas fa-info"></i></span>
                                        </td> -->
                                            <td>
                                                <?= @$hasil_datasiswa[$no]['kelas']; ?></td>
                                            <td>
                                                <?= @$array_hasil['status']; ?>
                                            </td>
                                            <td>
                                                <?= @$array_hasil['catatan']; ?>
                                            </td>

                                            <td>
                                                <?= @$array_hasil['aff']; ?>
                                            </td>
                                            <td>
                                                <?= @$array_hasil['psi']; ?>
                                            </td>
                                            <td>
                                                <?= @$array_hasil['kog']; ?>
                                            </td>
                                            <td>
                                                <?= @$array_hasil['plus']; ?>
                                            </td>

                                            <!-- <input type="checkbox" class="btn-check btn-sm border-0" id="btn-check-2" checked autocomplete="off"> -->
                                            <!-- <label class="btn btn-primary" for="btn-check-2"><i class="fas fa-check-circle text-light"></i></label> -->
                                            <td><?= @$hasil_datasiswa[$no]['kelompok']; ?></td>

                                        </tr>
                                        <?php $no++; ?>
                                    <?php }
                                } else {
                                    ?>
                                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                                        <h4 style="text-align: center;">- Tidak ada kelas untuk hari yang dipilih -</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php
                                } ?>
                            </tbody>
                        </table>
                    </div>

                    <a href="kelas.php?jur=<?= $jur_pilih; ?>&tanggal=<?= $tanggal_pilih; ?>&idjdwl=<?= $c_jadwal - 1; ?>" class="btn btn-dark">
                        <i class="fas fa-arrow-left text-info"></i>&nbsp;Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php mysqli_close($konek); ?>

<script>
    $(function() {
        $("#example1").DataTable({
            dom: 'Bfltip',
            "autoWidth": false,
            "responsive": true,
            "lengthChange": true,
            "lengthMenu": [
                [50, 100, -1],
                [50, 100, "Semua"]
            ],
            "pagingType": "full",
            "language": {
                "emptyTable": "Tida ada data untuk tanggal yang dipilih.",
                "info": "Ditampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Ditampilkan 0 sampai 0 dari 0 data",
                "infoFiltered": "(Disaring dari _MAX_ total data)",
                "lengthMenu": "Tampilkan _MENU_ baris data",
                "loadingRecords": "Memuat...",
                "processing": "Memproses...",
                "search": "Cari:",
                "zeroRecords": "Tidak ditemukan data yang sesuai.",
                "paginate": {
                    "first": "<<",
                    "last": ">>",
                    "next": "lanjut >",
                    "previous": "< sebelum"
                },
            },
            "buttons": ["print", "excel", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>

<?php
function cari_data_presensi($nis_siswa, $hasil_datapresensi)
{
    $hasil_cari_presensi = array();
    foreach ($hasil_datapresensi as $dtp) {
        if ($dtp['nis'] == $nis_siswa) {
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

<style>
    @media only screen and (max-width: 768px) {

        #example1,
        #example1 #masuk {
            font-size: 12px;
        }
    }
</style>

<?php
include "views/footer.php";
?>
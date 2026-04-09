<?php
include('app/get_user.php');
$tanggal = date('Y-m-d');
$tanggal_dmY = date('d-m-Y');
$tahun = date('Y');

include("../config/konesi.php");

if (@$_GET['tanggal']) {
    $tanggal_get = date('Y-m-d', strtotime(mysqli_real_escape_string($konek, $_GET['tanggal'])));

    // if ($tanggal_get > $tanggal) {
    //     $tanggal_get = date('Y-m-d', strtotime($tanggal));
    // }

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

$cek_guru_nama = mysqli_query($konek, "SELECT * FROM dataguru WHERE nick = '$nick_login'");
$hasil_cek_guru_nama = mysqli_fetch_array($cek_guru_nama);
$nama_guru = @$hasil_cek_guru_nama['nama'];
$nip_guru = @$hasil_cek_guru_nama['nip'];

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
        $text_jadwal_guru = $tingkat_kbm . " " . $jur_kbm . ", " . "Kelompok : ";

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
    // $sortir_siswa = mysqli_real_escape_string($konek, $sortir_siswa);
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
        $sortir_siswa = mysqli_real_escape_string($konek, $sortir_siswa);
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
// include "views/header.php";
// include "views/navbar.php";
?>

<section class="content book">
    <div class="container-fluid page">
        <button onclick="tableHtmlToExcel('tblData', 'Jurnal nilai <?= $hari_indo; ?> <?= $tanggal_indo_pilih; ?>')" style="border-radius: 3px; background-color: forestgreen; color: #FFF; border: none; padding: 10px;">Export ke Excel</button>
        <button onclick="window.print();" style="border-radius: 3px; background-color: steelblue; color: #FFF; border: none; padding: 10px;">Print</button>
        <button class="btn btn-dark btn-sm" onclick="window.close();" style="border-radius: 3px; float: right; background-color: dimgrey; color: #FFF; border: none; padding: 10px;">Kembali</button>
        <div id="tblData">
            <div class="header">
                <h3 class="mt-2"><b>Daftar Presensi, Jurnal, dan Nilai</b></h3>
            </div>
            <table>
                <tr>
                    <td>Hari, Tanggal</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<?= $hari_indo; ?>, <?= $tanggal_indo_pilih; ?></td>
                </tr>
                <tr>
                    <td>
                        <?php
                        $query_instruktur = mysqli_query($konek, "SELECT * FROM jadwalkbm WHERE ruangan = '$ruangan'");
                        $cek_nama_instruktur = mysqli_num_rows($query_instruktur);

                        if ($cek_nama_instruktur > 0) {
                            echo "<p style='height: 60px; margin-top: 0px; margin-bottom: 0px;'>Instruktur</p></td><td>";
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
                                <p style="margin-top: 10px; margin-bottom: 10px;">&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<?= $no; ?>. <?= $nama_instruktur; ?></p>
                            <?php }
                        } else { ?>
                            <p>Guru</p>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<?= $nama_login; ?>
                    <?php } ?>
                    </td>
                </tr>
                <?php if (@$hit_datasiswa) { ?>
                    <?php
                    $c_jadwal = @$_GET['idjdwl'];
                    if ($ruangan == @$hasil_cek_ruanganguru_jadwalkbm[$c_jadwal]['ruangan']) {
                        $_radio_jadwal = 'checked';
                    } else {
                        $_radio_jadwal = '';
                    }
                    ?>
                    <tr>
                        <td>
                            Kelas
                        </td>
                        <td>
                            &nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<?= $text_jadwal_guru; ?><?= $hasil_cek_ruanganguru_jadwalkbm[$c_jadwal][$list_jadwal_guru]; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Jam ke
                        </td>
                        <td>
                            &nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<?= $hasil_cek_ruanganguru_jadwalkbm[$c_jadwal]['mulai_jamke']; ?>&nbsp;-&nbsp;<?= $hasil_cek_ruanganguru_jadwalkbm[$c_jadwal]['sampai_jamke'] - 1; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Ruangan
                        </td>
                        <td>
                            &nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<?= @$hasil_cek_ruanganguru_jadwalkbm[$c_jadwal]['info'] . " [" . @$hasil_cek_ruanganguru_jadwalkbm[$c_jadwal]['ruangan'] . "]"; ?>

                        </td>
                    </tr>
                    <tr>
                        <?php
                        $qu_data_jurnal = mysqli_query($konek, "SELECT jurnal FROM jurnalguru WHERE tanggal = '$tanggal_pilih' AND jamke = '" . @$mulai_kbm . "' AND kelas = '$kelas_kbm' AND ruangan = '$ruangan' AND nick = '$nick_login'");
                        $data_jurnal = mysqli_fetch_array($qu_data_jurnal);
                        ?>
                        <td>
                            Materi
                        </td>
                        <td>
                            &nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;<?= @$data_jurnal['jurnal']; ?>
                        </td>
                    </tr>
                <?php } ?>
            </table><br>

            <div class="row">
                <table id="table">
                    <thead>
                        <tr style="text-align: center; position: sticky;">
                            <th>No.</th>
                            <th>Nama</th>
                            <th style="width: 15%;">Kelas</th>
                            <th>Status</th>
                            <th style="width: 50%;">Catatan</th>
                            <th>Aff</th>
                            <th>Psi</th>
                            <th>Kog</th>
                            <th>+</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 0;

                        if ($hit_datasiswa) {
                            while ($no < $hit_datasiswa) {
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

                                    <td style="text-align: left;">
                                        <?= $hasil_datasiswa[$no]['nama']; ?>
                                    </td>
                                    <td>
                                        <?= @$hasil_datasiswa[$no]['kelas']; ?></td>
                                    <td>
                                        <?= @$array_hasil['status']; ?>
                                    </td>
                                    <td style="font-size: smaller;">
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
                        <tr class="noborder">
                            <td colspan="10">&nbsp;</td>
                        </tr>
                        <tr class="noborder">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="6">Bansari, <?= $tanggal_indo_pilih; ?></td>
                        </tr>
                        <tr class="noborder">
                            <td></td>
                            <td style="width: 60%;">Keterangan :</td>
                            <td></td>
                            <td></td>
                            <td colspan="6" style="width: 50%;">Guru Mapel</td>
                        </tr>

                        <tr class="noborder">
                            <td colspan="10">&nbsp;</td>
                        </tr>
                        <tr class="noborder">
                            <td colspan="10">&nbsp;</td>
                        </tr>

                        <tr class="noborder">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="6">
                                <u><?= $nama_login; ?></u>
                            </td>
                        </tr>
                        <tr class="noborder">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="6">
                                NIP. <?= $nip_guru; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php mysqli_close($konek); ?>

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
<style>
    .header {
        text-align: center;
    }

    #table .noborder td,
    #table .noborder tr {
        border: none;
    }

    #table,
    #table th,
    #table td {
        border: 1px solid black;
        border-collapse: collapse;
    }

    table {
        font: 11pt "Tahoma";
        font-size: 14px;
    }
</style>
<style type="text/css">
    /* Kode CSS Untuk PAGE ini dibuat oleh http://jsfiddle.net/2wk6Q/1/ */
    body {
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        background-color: #FAFAFA;
        font: 11pt "Tahoma";
    }

    * {
        box-sizing: border-box;
        -moz-box-sizing: border-box;
    }

    .page {
        width: 210mm;
        min-height: 297mm;
        padding: 20mm 20mm 20mm 30mm;
        margin: 10mm auto;
        border: 1px #D3D3D3 solid;
        /* border-radius: 5px; */
        background: white;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }

    .subpage {
        padding: 1cm;
        border: 5px red solid;
        height: 257mm;
        outline: 2cm #FFEAEA solid;
    }

    @page {
        size: A4;
        margin: 0;
    }

    @media print {

        html,
        body {
            width: 210mm;
            height: 297mm;
        }

        .page {
            margin: 0;
            border: initial;
            border-radius: initial;
            width: initial;
            min-height: initial;
            box-shadow: initial;
            background: initial;
            page-break-after: always;
        }
    }
</style>
<?php
// include "views/footer.php";
?>

<script>
    function tableHtmlToExcel(tableID, filename = '') {
        var downloadLink;
        var dataType = 'application/vnd.ms-excel';
        var tableSelect = document.getElementById(tableID);
        var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

        filename = filename ? filename + '.xls' : 'excel_data.xls';

        downloadLink = document.createElement("a");

        document.body.appendChild(downloadLink);

        if (navigator.msSaveOrOpenBlob) {
            var blob = new Blob(['\ufeff', tableHTML], {
                type: dataType
            });
            navigator.msSaveOrOpenBlob(blob, filename);
        } else {
            downloadLink.href = 'data:' + dataType + ', ' + tableHTML;

            downloadLink.download = filename;

            downloadLink.click();
        }
    }
</script>
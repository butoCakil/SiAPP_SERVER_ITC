<?php
include('app/get_user.php');
include("../config/konesi.php");
$tanggal = date('Y-m-d');
$tanggal_dmY = date('d-m-Y');
$tahun = date('Y');

if (@$_GET) {
    $kelas_pilih = @$_GET['kelas'];
} else {
    $kelas_pilih = '';
}

$sql_kelas_saya = mysqli_query($konek, "SELECT DISTINCT kelas FROM jadwalkbm WHERE nick = '$nick_login'");

$data_kelas_saya = array();
$data_kelas_ditemukan = array();
$ii = 0;
while ($hasil_kelas_saya = mysqli_fetch_array($sql_kelas_saya)) {
    $data_kelas_saya[$ii] = $hasil_kelas_saya['kelas'];

    if (preg_match("/_/i", $hasil_kelas_saya['kelas'])) {

        $pecah_kode_kelas = explode("_", $hasil_kelas_saya['kelas']);

        $re_gabung_kode_kelas = $pecah_kode_kelas[1] . $pecah_kode_kelas[0];

        if ($ii - 1 >= 0) {
            if ($data_kelas_saya[$ii] == $data_kelas_saya[$ii - 1]) {
                // $data_kelas_ditemukan[] = $re_gabung_kode_kelas;
                $data_temp = $re_gabung_kode_kelas;
            }
        } else {
            // $data_kelas_ditemukan[] = $re_gabung_kode_kelas;
            $data_temp = $re_gabung_kode_kelas;
        }
    } else {
        // $data_kelas_ditemukan[] = $hasil_kelas_saya['kelas'];
        $data_temp = $hasil_kelas_saya['kelas'];
    }

    $data_kelas_ditemukan_mod = preg_replace("/[^0-9]/", "", $data_temp);
    // echo "<pre>";
    // print_r($data_kelas_ditemukan_mod);
    // echo "</pre>";
    // die;


    if (!$data_kelas_ditemukan_mod) {
        for ($i = 1; $i <= 3; $i++) {
            $data_kelas_ditemukan[] = $data_temp . $i;
        }
    } else {
        $data_kelas_ditemukan[] = $data_temp;
    }

    $ii++;
}

// echo "<pre>";print_r($data_kelas_ditemukan);echo "</pre>";
// die;

// $data_kelas_ditemukan_uniq = array();
$data_kelas_ditemukan_uniq = array_unique($data_kelas_ditemukan);
$jml_data_kelas_uniq = count($data_kelas_ditemukan_uniq);

$data_kelas_fix = array();
foreach ($data_kelas_ditemukan_uniq as $value) {
    $data_kelas_fix[] = $value;
}

// cari info kelas di kodeinfo
// cari nick guru jurusan di jadwalgurujur
// mencari DB presensikelas berdasarkan nick guru 1 dan 2, serta Kelas

if ($kelas_pilih) {
    $kelas_pilih = mysqli_real_escape_string($konek, $kelas_pilih);
    $sql_infokelas = mysqli_query($konek, "SELECT * FROM kodeinfo WHERE kode = '$kelas_pilih'");
    $infokelas = mysqli_fetch_assoc($sql_infokelas)['info'];

    $sql_cari_guru_produktif = mysqli_query($konek, "SELECT * FROM jadwalkbm WHERE nick = '$nick_login'");
    $hasil_cari_guru_produktif = mysqli_num_rows($sql_cari_guru_produktif);

    if ($hasil_cari_guru_produktif > 0) {
        $ruangan_guru_jur = mysqli_fetch_all($sql_cari_guru_produktif)[0][1];
        $sql_ambil_guru_jur = mysqli_query($konek, "SELECT * FROM jadwalkbm WHERE ruangan = '$ruangan_guru_jur'");

        $data_ambil_guru_jur = array();
        while ($hasil_ambil_guuru_jur = mysqli_fetch_array($sql_ambil_guru_jur)) {
            $data_ambil_guru_jur[] = $hasil_ambil_guuru_jur;
        }

        $mapel = $data_ambil_guru_jur[0]['keterangan_ruang'];
        $guru_jur_1 = $data_ambil_guru_jur[0]['nick'];
        $guru_jur_2 = $data_ambil_guru_jur[1]['nick'];

        $sql_cari_dipresensikelas = mysqli_query($konek, "SELECT * FROM presensikelas WHERE ( nick_guru = '$guru_jur_1' OR nick_guru = '$guru_jur_2' ) AND kelas = '$infokelas'");
    } else {
        $sql_cari_dipresensikelas = mysqli_query($konek, "SELECT * FROM presensikelas WHERE ( nick_guru = '$nick_login') AND kelas = '$infokelas'");
    }

    $data_cari_dipresensikelas = array();
    while ($hasil_cari_dipresensikelas = mysqli_fetch_array($sql_cari_dipresensikelas)) {
        $data_cari_dipresensikelas[] = $hasil_cari_dipresensikelas;
    }

    $cari_tanggal = cari_kolom_array($data_cari_dipresensikelas, 'tanggal');

    $data_cari_tanggal_uniq = array_unique($cari_tanggal);
    $jml_data_cari_tanggal_uniq = count($data_cari_tanggal_uniq);

    $data_cari_tanggal_fix = array();
    foreach ($data_cari_tanggal_uniq as $value) {
        $data_cari_tanggal_fix[] = $value;
    }

    // echo "data_cari_tanggal_fix : ";
    // echo "<pre>";
    // print_r($data_cari_tanggal_fix);
    // echo "</pre>";
    // echo "cari_tanggal : ";
    // echo "<pre>";
    // print_r($cari_tanggal);
    // echo "</pre>";
    // echo "infokelas : ";
    // echo "<pre>";
    // print_r($infokelas);
    // echo "</pre>";
    // echo "guru_jur_1 : ";
    // echo "<pre>";
    // print_r($guru_jur_1);
    // echo "</pre>";
    // echo "guru_jur_2 : ";
    // echo "<pre>";
    // print_r($guru_jur_2);
    // echo "</pre>";
    // echo "data_cari_dipresensikelas : ";
    // echo "<pre>";
    // print_r($data_cari_dipresensikelas);
    // echo "</pre>";

    // die;

    $sql_datasiswa = mysqli_query($konek, "SELECT * FROM datasiswa WHERE kode = '$kelas_pilih'");
    $data_siswa = array();
    $hit_datasiswa = 0;
    while ($hasil_data_siswa = mysqli_fetch_array($sql_datasiswa)) {
        $data_siswa[] = $hasil_data_siswa;
        $hit_datasiswa++;
    }
}

$title = 'Nilai Kelas Saya';
$navlink = 'Kelas';

include 'views/header.php';
include 'views/navbar.php';
mysqli_close($konek);
?>

<section class="content">
    <div class="container-fluid">
        <div class="d-flex">
            <div class="col-6">
                <div class="alert alert-info">
                    <label>Pilih Kelas yang akan ditampilkan</label>
                    <form method="get">
                        <?php for ($i = 0; $i < $jml_data_kelas_uniq; $i++) { ?>
                            <div class="form-check">
                                <?php
                                if ($kelas_pilih == $data_kelas_fix[$i]) {
                                    $cek = 'checked';
                                } else {
                                    $cek = '';
                                }
                                ?>
                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2" onclick="self.location = 'nilaikelassaya.php?kelas=<?= $data_kelas_fix[$i]; ?>'" <?= $cek; ?>>
                                <label class="form-check-label" for="flexRadioDefault2">
                                    <?= $data_kelas_fix[$i]; ?>
                                </label>
                            </div>
                        <?php } ?>
                    </form>
                </div>
            </div>

            <div class="col-6">
                <button class="btn btn-dark btn-sm no-border m-1" onclick="window.close();"><i class="fas fa-arrow-alt-circle-left"></i> Kembali</button>

                <?php if ($kelas_pilih) { ?>
                    <a target="_blank" href="print_nilaikelassaya.php?kelas=<?= $kelas_pilih; ?>&a=print" class="btn btn-secondary no-border btn-sm m-1"><i class="fas fa-print text-light"></i>&nbsp;Print</a>
                    <a target="_blank" href="print_nilaikelassaya.php?kelas=<?= $kelas_pilih; ?>&a=excel" class=" btn btn-success no-border btn-sm m-1"><i class="fas fa-file-excel text-light"></i>&nbsp;Download&nbsp;Excel</a>
                <?php } ?>
            </div>
        </div>

        <div class="card elevation-3 bg-primary bg-gradient-primary border-0" style="z-index: 1;">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i>&nbsp;
                    Rekap Nilai Kelas Saya
                </h3>
                <div class="card-tools">
                    <i class="fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Menampilkan Nilai kelas saya per kelas"></i>
                    &nbsp;
                </div>
            </div>
        </div>

        <div class="col-12" style="margin-top: -20px;">
            <div class="card elevation-3">
                <div class="card-body mb-5">
                    <?php if ($kelas_pilih) { ?>
                        <div>
                            <table>
                                <tr>
                                    <td>Kelas</td>
                                    <td>&nbsp;:&nbsp;&nbsp;</td>
                                    <td><?= $kelas_pilih; ?></td>
                                </tr>
                                <tr>
                                    <td>Guru</td>
                                    <td>&nbsp;:&nbsp;&nbsp;</td>
                                    <td><?= $nama_login; ?></td>
                                </tr>
                                <tr>
                                    <td>Mapel</td>
                                    <td>&nbsp;:&nbsp;&nbsp;</td>
                                    <td><?= @$mapel; ?></td>
                                </tr>
                                <tr>
                                    <td>Semester</td>
                                    <td>&nbsp;:&nbsp;&nbsp;</td>
                                    <td>
                                        <?php
                                        if (date('m') > 6) {
                                            echo "Gasal";
                                            $thn_per = date('Y') + 1;
                                        } else {
                                            echo "Genap";
                                            $thn_per = date('Y') - 1;
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tahun Ajaran</td>
                                    <td>&nbsp;:&nbsp;&nbsp;</td>
                                    <td><?= date('Y') . "/" . $thn_per; ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>

                                        <th rowspan="2">No</th>
                                        <th rowspan="2">NIS</th>
                                        <th rowspan="2">Nama</th>
                                        <th rowspan="2">Kelas</th>

                                        <?php
                                        for ($i = 0; $i < $jml_data_cari_tanggal_uniq; $i++) {
                                            echo "<th colspan='4'>" . date('d/m', strtotime($data_cari_tanggal_fix[$i])) . "</th>";
                                        }
                                        ?>
                                    </tr>
                                    <tr>
                                        <?php for ($i = 0; $i < $jml_data_cari_tanggal_uniq; $i++) { ?>
                                            <th>A</th>
                                            <th>P</th>
                                            <th>K</th>
                                            <th>+</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    for ($i = 0; $i < $hit_datasiswa; $i++) {
                                    ?>
                                        <tr>
                                            <td><?= $i + 1; ?></td>
                                            <td><?= $data_siswa[$i]['nis']; ?></td>
                                            <td>
                                                <a target="_blank" href="catatan_siswa.php?nis=<?= $data_siswa[$i]['nis'];; ?>&info=notes&db=datasiswa" class="text-decoration-none text-dark">
                                                    <?= $data_siswa[$i]['nama']; ?>&nbsp;<i class="fas fa-info-circle text-info"></i>
                                                </a>
                                            </td>
                                            <td><?= $data_siswa[$i]['kelas']; ?></td>

                                            <?php
                                            for ($j = 0; $j < $jml_data_cari_tanggal_uniq; $j++) {
                                                $x = 0;

                                                // jika ada data siswa di tanggal ini
                                                $_datanya = array();
                                                foreach ($data_cari_dipresensikelas as $nilai) {
                                                    if ($nilai['tanggal'] == $data_cari_tanggal_fix[$j]) {
                                                        if ($nilai['nis'] == $data_siswa[$i]['nis']) {
                                                            $_datanya[] = $nilai;
                                                        }
                                                    }
                                                }

                                                if ($_datanya) {
                                                    // cari dan tampilkan data siswa
                                                    foreach ($data_cari_dipresensikelas as $nilai) {
                                                        if ($nilai['tanggal'] == $data_cari_tanggal_fix[$j]) {
                                                            if ($nilai['nis'] == $data_siswa[$i]['nis']) {
                                                                echo $nilai['aff'] ? '<td><span class="badge badge-info">' . $nilai['aff']  . '</span></td>' : '<td><span class="badge badge-danger">-</span></td>';
                                                                echo $nilai['psi'] ? '<td><span class="badge badge-info">' . $nilai['psi']  . '</span></td>' : '<td><span class="badge badge-danger">-</span></td>';
                                                                echo $nilai['kog'] ? '<td><span class="badge badge-info">' . $nilai['kog']  . '</span></td>' : '<td><span class="badge badge-danger">-</span></td>';
                                                                echo $nilai['plus'] ? '<td><span class="badge badge-info">' . $nilai['plus'] . '</span></td>' : '<td><span class="badge badge-danger">-</span></td>';
                                                            }
                                                        }
                                                    }
                                            ?>
                                                <?php } else {
                                                    echo "<td>-</td>";
                                                    echo "<td>-</td>";
                                                    echo "<td>-</td>";
                                                    echo "<td>-</td>";
                                                } ?>
                                            <?php } ?>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else { ?>
                        <h4 class="text-center">Belum ada Kelas untuk ditampilkan</h4>
                    <?php } ?>
                </div>
            </div>
        </div>
        <span class="badge badge-info"></span>
        <span class="badge badge-danger"></span>
</section>
<?php
include 'views/footer.php';

function cari_array($_data_dicari, $_data_hasil_array, $_index_array)
{
    $_hasil_cari_array = array();
    foreach ($_data_hasil_array as $_value) {
        if ($_value[$_index_array] == $_data_dicari) {
            $_hasil_cari_array[] = $_value;
        }
    }
    return $_hasil_cari_array;
}

function cari_kolom_array($_data_hasil_array, $_index_array)
{
    $_hasil_cari_array = array();
    foreach ($_data_hasil_array as $_value) {
        $_hasil_cari_array[] = $_value[$_index_array];
    }
    return $_hasil_cari_array;
}
?>
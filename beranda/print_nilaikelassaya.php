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
        $data_temp = $re_gabung_kode_kelas;
    }

    $data_kelas_ditemukan_mod = preg_replace("/[^0-9]/", "", $data_temp);

    if (!$data_kelas_ditemukan_mod) {
        for ($i = 1; $i <= 3; $i++) {
            $data_kelas_ditemukan[] = $data_temp . $i;
        }
    }

    $ii++;
}

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

    $sql_cari_guru_produktif = mysqli_query($konek, "SELECT * FROM jadwalgurujur WHERE nick = '$nick_login'");
    $hasil_cari_guru_produktif = mysqli_num_rows($sql_cari_guru_produktif);

    if ($hasil_cari_guru_produktif > 0) {
        $ruangan_guru_jur = mysqli_fetch_all($sql_cari_guru_produktif)[0][1];
        $sql_ambil_guru_jur = mysqli_query($konek, "SELECT * FROM jadwalgurujur WHERE ruangan = '$ruangan_guru_jur'");

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

    $sql_datasiswa = mysqli_query($konek, "SELECT * FROM datasiswa WHERE kode = '$kelas_pilih'");
    $data_siswa = array();
    $hit_datasiswa = 0;
    while ($hasil_data_siswa = mysqli_fetch_array($sql_datasiswa)) {
        $data_siswa[] = $hasil_data_siswa;
        $hit_datasiswa++;
    }
}

mysqli_close($konek);

$title = 'Print Nilai Kelas Saya';
$navlink = 'Kelas';
$judulfile = 'Nilai Kelas ' . $infokelas . ' ' . $mapel;
?>

<body onload="tableHtmlToExcel('tblData', '<?= $judulfile; ?>')">
    <section class="content">
        <div id="tblData" class="page" class="container-fluid">
            <div class="header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1 mt-0"></i>&nbsp;
                    Rekap Nilai Kelas Saya
                </h3>
                <div class="card-tools">
                    <i class="fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Menampilkan Nilai kelas saya per kelas"></i>
                    &nbsp;
                </div>
            </div>

            <div class="col-12" style="margin-top: -20px;">
                <div class="card-body mb-5">
                    <?php if ($kelas_pilih) { ?>
                        <div>
                            <table>
                                <tr>
                                    <td>Kelas</td>
                                    <td>&nbsp;:&nbsp;&nbsp;</td>
                                    <td><?= $infokelas; ?></td>
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
                            <table id="table" class="table table-bordered table-striped">
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
                                                <?= $data_siswa[$i]['nama']; ?>
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
            <span class="badge badge-info"></span>
            <span class="badge badge-danger"></span>
    </section>
</body>
<?php

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
        margin-top: 0px;
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
        font: 10pt "Tahoma";
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
        font: 10pt "Tahoma";
    }

    * {
        box-sizing: border-box;
        -moz-box-sizing: border-box;
    }

    .page {
        width: 210mm;
        min-height: 297mm;
        padding: 10mm 10mm 10mm 20mm;
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

    th,
    td {
        min-width: 15px;
        padding: 1px 1px;
    }
</style>
<?php
// include "views/footer.php";
?>
<?php if (@$_GET['a'] == 'print') { ?>
    <script>
        window.print();
    </script>
<?php } elseif (@$_GET['a'] == 'excel') { ?>

    <script type="text/javascript">
        function tableHtmlToExcel(tableID, filename = '') {
            var downloadLink;
            var dataType = 'application/vnd.ms-excel';
            var tableSelect = document.getElementById('tblData');
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

            window.close();
        }
    </script>
<?php } ?>
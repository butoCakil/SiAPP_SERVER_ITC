<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

include('../config/konesi.php');

$q = mysqli_query($konek, "SELECT info FROM statusnya");
$status = mysqli_fetch_assoc($q);
$harikerja = $status['info'];

$title = 'Rekap Presensi ';
$navlink = 'Rekap';
$navlink_sub = 'bulanan';

$nokartu_login = @$_SESSION['nokartu_login'] ? $_SESSION['nokartu_login'] : '';

// test

$kelasjur = @$_GET['kelasjur'];

$kelasjur = $kelasjur ? $kelasjur : str_replace(" ", "", $ket_akses_login);

if ($kelasjur) {
    $pilih_q = $kelasjur;
} else {
    // ============ sinkronkan (belum)
    $pilih_q = "";
}

if ($pilih_q) {
    // Prepare the SELECT statement to check nokartu in the 'datasiswa' table
    $query_select_datasiswa = "SELECT nis, nama, nick, kelas, foto, kelompok, t_waktu_telat, tingkat, jur, email FROM datasiswa WHERE kode = ?";
    $stmt_select_datasiswa = mysqli_stmt_init($konek);
    mysqli_stmt_prepare($stmt_select_datasiswa, $query_select_datasiswa);
    mysqli_stmt_bind_param(
        $stmt_select_datasiswa,
        "s",
        $pilih_q
    );
    mysqli_stmt_execute($stmt_select_datasiswa);
    $result_select_datasiswa = mysqli_stmt_get_result($stmt_select_datasiswa);

    $datasiswa = array();
    foreach ($result_select_datasiswa as $dts) {
        $datasiswa[] = $dts;
    }

    mysqli_stmt_close($stmt_select_datasiswa);

    // Prepare the SELECT statement to check nokartu in the 'datapresensi' table
    $query_select_datapresensi = "SELECT * FROM datapresensi WHERE kode = ?";
    $stmt_select_datapresensi = mysqli_stmt_init($konek);
    mysqli_stmt_prepare($stmt_select_datapresensi, $query_select_datapresensi);
    mysqli_stmt_bind_param(
        $stmt_select_datapresensi,
        "s",
        $pilih_q
    );
    mysqli_stmt_execute($stmt_select_datapresensi);
    $result_select_datapresensi = mysqli_stmt_get_result($stmt_select_datapresensi);

    $datapresensi = array();
    foreach ($result_select_datapresensi as $dtp) {
        $datapresensi[] = $dtp;
    }

    mysqli_stmt_close($stmt_select_datapresensi);

    $kelas_datasiswa = @$datasiswa[0]['kelas'];

    $title = $title . $kelas_datasiswa . ' Bulan ' . $nama_bulan_indo_pilih . ' ' . $tahun_pilih;
} else {
    $title = "Rekap Kelas Perbulan";
}

if (!empty($datasiswa)) {
    ?>


    <body onload="tableHtmlToExcel('tblData', '<?= $title; ?>')">
        <section class="content">
            <div id="tblData" class="page" class="container-fluid">
                <div class="header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1 mt-0"></i>&nbsp;
                        <?= $title; ?>
                    </h3>
                    <div class="card-tools">
                        <i class="fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Menampilkan Nilai kelas saya per kelas"></i>
                        &nbsp;
                    </div>
                </div>

                <div class="col-12" style="margin-top: -20px;">
                    <div class="card-body mb-5">
                        <table>
                            <tr>
                                <td>Kelas</td>
                                <td>&nbsp;:&nbsp;<?= $datasiswa[0]['kelas']; ?></td>
                            </tr>
                            <tr>
                                <td>Jurusan</td>
                                <td>&nbsp;:&nbsp;<?= $datasiswa[0]['jur']; ?></td>
                            </tr>
                            <?php
                            // mencari wali kelas
                            // Prepare the SELECT statement to check nokartu in the 'dataguru' table
                            $akses_ = "Wali Kelas";
                            $query_select_dataguru = "SELECT nip, nama FROM dataguru WHERE akses = ? AND ket_akses = ?";
                            $stmt_select_dataguru = mysqli_stmt_init($konek);
                            mysqli_stmt_prepare($stmt_select_dataguru, $query_select_dataguru);
                            mysqli_stmt_bind_param(
                                $stmt_select_dataguru,
                                "ss",
                                $akses_,
                                $kelas_datasiswa
                            );
                            mysqli_stmt_execute($stmt_select_dataguru);
                            $result_select_dataguru = mysqli_stmt_get_result($stmt_select_dataguru);

                            if ($row = mysqli_fetch_assoc($result_select_dataguru)) {
                                $nama_guru = $row['nama'];
                                $nip_guru = @$row['nip'];
                            } else {
                                $nama_guru = "";
                                $nip_guru = "";
                            }

                            // Tutup prepared statement
                            mysqli_stmt_close($stmt_select_dataguru);
                            ?>
                            <tr>
                                <td>Wali Kelas</td>
                                <td>&nbsp;:&nbsp;<?= $nama_guru; ?></td>
                            </tr>
                            <?php if (@$nip_guru) { ?>
                                <tr>
                                    <td>NIP</td>
                                    <td>&nbsp;:&nbsp;<?= $nip_guru; ?></td>
                                </tr>
                            <?php } ?>

                            <tr>
                                <td>Semester</td>
                                <td>&nbsp;:&nbsp;<?php
                                if ($bulan_pilih > 6) {
                                    echo "Gasal";
                                    $thn_per = $tahun_pilih + 1;
                                    $thn_ajr = $tahun_pilih . "/$thn_per";
                                } else {
                                    echo "Genap";
                                    $thn_per = $tahun_pilih - 1;
                                    $thn_ajr = "$thn_per/" . $tahun_pilih;
                                }
                                ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Tahun Ajaran</td>
                                <td>&nbsp;:&nbsp;<?= $thn_ajr; ?></td>
                            </tr>
                        </table>
                        <br>

                        <?php
                        $tanggal = "$tahun_pilih-$bulan_pilih-1";
                        $jumlah_hari_kerja = hitungan_hari_kerja($tanggal);
                        $jumlah_hari = hitungan_hari($tanggal);
                        ?>

                        <table id="table" class="table table-bordered table-striped">
                            <thead>
                                <tr class="header1">
                                    <th rowspan="2" class="freez1">No</th>
                                    <th rowspan="2" class="freez1">NIS</th>
                                    <th rowspan="2" class="freez1">Nama</th>
                                    <th colspan="<?= $jumlah_hari_kerja; ?>">Tanggal</th>
                                    <th colspan="5">Keterangan</th>
                                </tr>

                                <tr class="header1">
                                    <?php
                                    for ($i = 1; $i <= $jumlah_hari; $i++) {
                                        $tanggal_loop = "$tahun_pilih-" . duadigit($bulan_pilih) . "-" . duadigit($i);
                                        $bulan_ini = "$tahun_pilih-" . duadigit($bulan_pilih);

                                        $hari_singkat = hari_indo_singkat($tanggal_loop);

                                        if ($harikerja == '5') {
                                            $harilibur = limaharikerja($tanggal_loop);
                                        } elseif ($harikerja == '6') {
                                            $harilibur = enamharikerja($tanggal_loop);
                                        }

                                        $minggu_t = date('W', strtotime($tanggal_loop));
                                        if ($minggu_t % 2) {
                                            $bg_col = "secondary";
                                        } else {
                                            $bg_col = "light";
                                        }

                                        if (!$harilibur) {
                                            echo "<th class='kolomAbsen bg-$bg_col'>$i<br>$hari_singkat</th>";
                                            // echo "<th>$i<br>$hari_singkat</th>";
                                        }
                                    }
                                    ?>

                                    <th>MSK</th>
                                    <th>TLT</th>
                                    <th>TPM</th>
                                    <th>TPP</th>
                                    <th>ALP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total_masuk = 0;
                                $total_alpa = 0;
                                for ($j = 0; $j < count($datasiswa); $j++) {
                                    $masuk_MSK = 0;
                                    $masuk_TLT = 0;
                                    $pulang_PLG = 0;
                                    $pulang_PA = 0;
                                    $alpa_A = 0;
                                    $TPM = 0;
                                    $TPP = 0;
                                    $nis_datasiswa = $datasiswa[$j]['nis'];
                                    $nama_datasiswa = $datasiswa[$j]['nama'];
                                    ?>

                                    <tr class="perharipertanggal">
                                        <td class="freez1"><?= ($j + 1); ?></td>
                                        <td class="freez1"><?= $nis_datasiswa; ?></td>
                                        <td class="freez1" style="text-align: left;">
                                            <?= $nama_datasiswa; ?>
                                        </td>

                                        <?php
                                        for ($k = 1; $k <= $jumlah_hari; $k++) {
                                            $temp_tpm = 0;
                                            $temp_tpp = 0;

                                            $tanggal_loop = "$tahun_pilih-" . duadigit($bulan_pilih) . "-" . duadigit($k);

                                            $tgl_Ymd = date('Ymd', strtotime($tanggal_loop));
                                            $deskripsi = tanggalMerah($tgl_Ymd);

                                            if ($harikerja == '5') {
                                                $harilibur = limaharikerja($tanggal_loop);
                                            } elseif ($harikerja == '6') {
                                                $harilibur = enamharikerja($tanggal_loop);
                                            }

                                            if ($harilibur == false && !$deskripsi) {
                                                $hasil_pre = cari_data_ganda($datapresensi, $tanggal_loop, "tanggal", $datasiswa[$j]['nis'], "nomorinduk");
                                                ?>
                                                <?php
                                                $keterangan_masuk = (@$hasil_pre[0]['ketmasuk']);
                                                $keterangan_pulang = (@$hasil_pre[0]['ketpulang']);

                                                if ($keterangan_masuk == "MSK") {
                                                    $bg_masuk = "bg-success";
                                                    $masuk_MSK++;
                                                } elseif ($keterangan_masuk == "TLT") {
                                                    $bg_masuk = "bg-warning";
                                                    $masuk_MSK++;
                                                    $masuk_TLT++;
                                                } elseif ($keterangan_masuk == "-") {
                                                    $bg_masuk = "bg-primary";
                                                    $keterangan_masuk = "TPM";
                                                    $TPM++;
                                                    $masuk_MSK++;
                                                    $temp_tpm++;
                                                } else {
                                                    $bg_masuk = "bg-danger";
                                                }

                                                if ($keterangan_pulang == "PLG") {
                                                    $bg_pulang = "bg-success";
                                                    $pulang_PLG++;
                                                } elseif ($keterangan_pulang == "PA") {
                                                    $bg_pulang = "bg-warning";
                                                    $pulang_PLG++;
                                                    $pulang_PA++;
                                                } elseif ($keterangan_pulang == "-") {
                                                    $bg_pulang = "bg-primary";
                                                    $keterangan_pulang = "TPP";
                                                    $TPP++;
                                                    $temp_tpp++;
                                                } else {
                                                    $bg_pulang = "bg-danger";
                                                }

                                                if ($temp_tpm + $temp_tpp == 2) {
                                                    $alpa_A++;
                                                    $masuk_MSK--;
                                                    $TPP--;
                                                    $TPM--;
                                                    $keterangan_masuk = "";
                                                    $keterangan_pulang = "";
                                                }

                                                if (!$keterangan_masuk && !$keterangan_pulang) {
                                                    $keterangan = "A";
                                                    $alpa_A++;
                                                } else {
                                                    $keterangan = ($keterangan_masuk ? $keterangan_masuk : "-") . "<br>" . ($keterangan_pulang ? $keterangan_pulang : "-");
                                                }
                                                ?>

                                                <td id="setketerangan<?= $nis_datasiswa; ?><?= $tanggal_loop; ?>">
                                                    <?= $keterangan; ?>
                                                </td>
                                                <?php
                                            }
                                        }
                                        ?>
                                        <td><?= $masuk_MSK; ?></td>
                                        <td><?= $masuk_TLT; ?></td>
                                        <td><?= $TPM; ?></td>
                                        <td><?= $TPP; ?></td>
                                        <td><?= $alpa_A; ?></td>
                                    </tr>
                                    <?php

                                    $total_masuk = $total_masuk + $masuk_MSK;
                                    $total_alpa = $total_alpa + $alpa_A;
                                }
                                ?>

                                <tr></tr>
                                <tr>
                                    <td></td>
                                    <td>Total Masuk</td>
                                    <td>:</td>
                                    <td><?= $total_masuk; ?></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Total Alpha</td>
                                    <td>:</td>
                                    <td><?= $total_alpa; ?></td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
                <span class="badge badge-info"></span>
                <span class="badge badge-danger"></span>
        </section>
    </body>

    <?php
} else {
    echo "Tidak ada data";
}
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
        /* width: 210mm; */
        /* min-height: 297mm; */
        width: 297mm;
        min-height: 210mm;
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
        size: A4 landscape;
        margin: 0;
    }

    @media print {

        html,
        body {
            /* width: 210mm; */
            /* height: 297mm; */
            width: 297mm;
            height: 210mm;
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
function hitungan_hari($_tanggal)
{
    // Ambil tahun dan bulan dari tanggal yang diberikan
    $year = date('Y', strtotime($_tanggal));
    $month = date('n', strtotime($_tanggal));

    // Hitung jumlah hari dalam bulan yang ditentukan
    $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    return $jumlah_hari;
}

function hitungan_hari_kerja($_tanggal)
{
    // Ambil tahun dan bulan dari tanggal yang diberikan
    $year = date('Y', strtotime($_tanggal));
    $month = date('n', strtotime($_tanggal));

    // Hitung jumlah hari dalam bulan yang ditentukan
    $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    // Inisialisasi jumlah hari kerja
    $jumlah_hari_kerja = 0;

    // Loop untuk setiap tanggal dalam bulan yang ditentukan
    for ($i = 1; $i <= $jumlah_hari; $i++) {
        // Tentukan hari dalam seminggu (0 = Minggu, 6 = Sabtu)
        $hari = date('w', mktime(0, 0, 0, $month, $i, $year));

        // Jika hari bukan Sabtu (6) atau Minggu (0), tambahkan ke jumlah hari kerja
        if ($hari != 0 && $hari != 6) {
            $jumlah_hari_kerja++;
        }
    }

    return $jumlah_hari_kerja;
    // return $jumlah_hari;
}
?>

<style>
    .kolomAbsen {
        width: 5vh;
    }

    .perharipertanggal td {
        display: table-cell;
        vertical-align: middle;
        text-align: center;
    }
</style>
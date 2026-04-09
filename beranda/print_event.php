<?php
// ini_set('display_errors', 1); //Atauerror_reporting(E_ALL && ~E_NOTICE);

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
        header("Location: event.php?bulan=$bulan_tahun_pilih");
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

$title = 'Presensi Kegiatan/Pembiasaan<br>';
$navlink = 'Wali';
$navlink_sub = 'kegiatan';

if ($bulan_tahun_pilihplus == '#') {
    $bulan_tahun_pilihplus = '#';
    $disable = ' disabled btn btn-secondary';
    $ket = 'masuk disable';
} else {
    $bulan_tahun_pilihplus = 'event.php?bulan=' . $bulan_tahun_pilihplus;
    $disable = '';
    $ket = 'tidak masuk disable';
}

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
    $query_select_datapresensi = "SELECT * FROM presensiEvent";
    $stmt_select_datapresensi = mysqli_stmt_init($konek);
    mysqli_stmt_prepare($stmt_select_datapresensi, $query_select_datapresensi);
    mysqli_stmt_execute($stmt_select_datapresensi);
    $result_select_datapresensi = mysqli_stmt_get_result($stmt_select_datapresensi);

    $datapresensi = array();
    while ($dtp = mysqli_fetch_assoc($result_select_datapresensi)) {
        $datapresensi[] = $dtp;
    }

    mysqli_stmt_close($stmt_select_datapresensi);


    // ==============
    // Prepare the SELECT statement to check nokartu in the 'daftarijin' table
    $query_select_daftarijin = "SELECT * FROM `daftarijin` WHERE `info` = 'Perijinan Masjid'";
    $stmt_select_daftarijin = mysqli_stmt_init($konek);
    mysqli_stmt_prepare($stmt_select_daftarijin, $query_select_daftarijin);
    mysqli_stmt_execute($stmt_select_daftarijin);
    $result_select_daftarijin = mysqli_stmt_get_result($stmt_select_daftarijin);

    $daftarijin = array();
    while ($dtp = mysqli_fetch_assoc($result_select_daftarijin)) {
        $daftarijin[] = $dtp;
    }

    mysqli_stmt_close($stmt_select_daftarijin);

    // ==============


    // Inisialisasi array untuk menyimpan hasil pencarian
    $hasil_pencarian = array();

    // Loop melalui data siswa
    foreach ($datasiswa as $siswa) {
        $nis = $siswa['nis'];
        // Loop melalui data presensi
        foreach ($datapresensi as $presensi) {
            // Jika nis pada data presensi sama dengan nis pada data siswa
            if ($presensi['nis'] == $nis) {
                // Tambahkan data presensi ke hasil pencarian
                $hasil_pencarian[] = array(
                    'nis' => $nis,
                    'nama' => $siswa['nama'],
                    'kelas' => $siswa['kelas'],
                    'ruang' => $presensi['ruang'],
                    'mulai' => $presensi['mulai'],
                    'selesai' => $presensi['selesai'],
                    'jam' => $presensi['jam'],
                    'tanggal' => $presensi['tanggal'],
                    'keterangan' => $presensi['keterangan']
                );
            }
        }
    }

    // Tampilkan hasil pencarian
    // echo "<pre>";
    // print_r($hasil_pencarian);
    // echo "</pre>";
    // die;

    $kelas_datasiswa = @$datasiswa[0]['kelas'];

    $title = $title . ' <b>' . $kelas_datasiswa . '</b> Bulan ' . $nama_bulan_indo_pilih . ' ' . $tahun_pilih;
} else {


    $title = "Rekap Kelas Perbulan";
}
?>


<body onload="tableHtmlToExcel('tblData', '<?= html_entity_decode(strip_tags($title)); ?>')">
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
                            <td>&nbsp;:&nbsp;<?= @$datasiswa[0]['kelas']; ?></td>
                        </tr>
                        <tr>
                            <td>Jurusan</td>
                            <td>&nbsp;:&nbsp;<?= @$datasiswa[0]['jur']; ?></td>
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
                    </table>
                    <br>

                    <?php
                    $tanggal = "$tahun_pilih-$bulan_pilih-1";
                    $jumlah_hari_kerja = hitung_hari_kerja($tanggal);
                    $jumlah_hari = hitung_hari($tanggal);
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

                                <th>HDR</th>
                                <th>IM</th>
                                <th>ALP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            for ($j = 0; $j < count($datasiswa); $j++) {
                                $masuk_mulai = 0;
                                $masuk_selesai = 0;

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
                                        $tidakAda = 0;
                                        $temp_tpm = 0;
                                        $temp_tpp = 0;
                                        $mens = 0;

                                        for ($k = 1; $k <= $jumlah_hari; $k++) {

                                            $tanggal_loop = "$tahun_pilih-" . duadigit($bulan_pilih) . "-" . duadigit($k);

                                            $tgl_Ymd = date('Ymd', strtotime($tanggal_loop));
                                            $deskripsi = tanggalMerah($tgl_Ymd);

                                            if ($harikerja == '5') {
                                                $harilibur = limaharikerja($tanggal_loop);
                                            } elseif ($harikerja == '6') {
                                                $harilibur = enamharikerja($tanggal_loop);
                                            }

                                            if ($harilibur == false && !$deskripsi) {
                                                $hasil_pre = cari_data_ganda($datapresensi, $tanggal_loop, "tanggal", $datasiswa[$j]['nis'], "nis");
                                                ?>
                                                <?php
                                                $ket_mulai = (@$hasil_pre[0]['mulai']);
                                                $ket_mulai = substr($ket_mulai, 0, 5); // Hasil: "14:23"
                                                $ket_selesai = (@$hasil_pre[0]['selesai']);

                                                date_default_timezone_set('Asia/Jakarta');
                                                $tanggal___ = date('Y-m-d');    

                                                if (strtotime($tanggal_loop) > strtotime($tanggal___)) {
                                                    echo "<td><span class='badge bg-secondary'>-</span></td>";
                                                } else {

                                                    if ($ket_mulai) {
                                                        $bg_mulai = "bg-success";
                                                        $masuk_mulai++;
                                                    } else {
                                                        $bg_mulai = "bg-danger";
                                                    }

                                                    if ($ket_selesai) {
                                                        $bg_selesai = "bg-success";
                                                        $masuk_selesai++;
                                                    } else {
                                                        $bg_selesai = "bg-danger";
                                                    }

                                                    if (!$ket_mulai) {
                                                        $keterangan = "<span class='badge bg-danger'>A</span>";
                                                    } else {
                                                        // $keterangan = "<span class='badge $bg_mulai'>" . ($ket_mulai ? $ket_mulai : "-") . "</span><span class='badge $bg_selesai'>" . ($ket_selesai ? $ket_selesai : "-") . "</span>";
                                                        $keterangan = "<span class='badge $bg_mulai'>" . ($ket_mulai ? $ket_mulai : "-") . "</span>";
                                                    }

                                                    if (!$ket_mulai && !$ket_selesai) {
                                                        $hasil_ijin = cari_data_ganda($daftarijin, $tanggal_loop, "tanggalijin", $datasiswa[$j]['nis'], "nis");

                                                        $ket_ijin = (@$hasil_ijin[0]['kode']);

                                                        if ($ket_ijin) {
                                                            $bg_mulai = "bg-primary";
                                                            $bg_selesai = "bg-primary";
                                                            $keterangan = "<span class='badge bg-primary'>M</span>";
                                                            $mens++;
                                                        }
                                                    }

                                                    if(empty($ket_mulai) && empty($ket_ijin)){
                                                        $tidakAda++;
                                                    }
                                                    ?>

                                                <td id="setketerangan<?= $nis_datasiswa; ?><?= $tanggal_loop; ?>">
                                                        <?= $keterangan; ?>
                                                </td>
                                                <?php
                                                }
                                            }
                                        }
                                    ?>
                                    <td><?= $masuk_mulai; ?></td>
                                    <td><?= $mens; ?></td>
                                    <td><?= $tidakAda; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <span class="badge badge-info"></span>
            <span class="badge badge-danger"></span>
    </section>
</body>

<?php if (@$_GET['a'] == 'print') { ?>
    <script>
        window.print();
    </script>
<?php } elseif (@$_GET['a'] == 'excel') { ?>

    <script type="text/javascript">
        function tableHtmlToExcel(tableID, filename = '') {
            var downloadLink;
            // var dataType = 'application/vnd.ms-excel';
            var dataType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
            var tableSelect = document.getElementById('tblData');
            var tableHTML = tableSelect.outerHTML;

            filename = filename ? filename + '.xls' : 'excel_data.xls';

            downloadLink = document.createElement("a");

            document.body.appendChild(downloadLink);

            // if (navigator.msSaveOrOpenBlob) {
            //     var blob = new Blob(['\ufeff', tableHTML], {
            //         type: dataType
            //     });
            //     navigator.msSaveOrOpenBlob(blob, filename);
            // } else {
            //     downloadLink.href = 'data:' + dataType + ', ' + tableHTML;

            //     downloadLink.download = filename;

            //     downloadLink.click();
            // }

            // Menggunakan Blob untuk mendownload file
            var blob = new Blob(['\ufeff', tableHTML], { type: dataType });
            downloadLink.href = URL.createObjectURL(blob);
            downloadLink.download = filename;

            downloadLink.click();

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
mysqli_close($konek);
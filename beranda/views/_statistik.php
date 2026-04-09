<?php
if (@$_GET['page'] == 'chart') {
    $tmb1_aktif = 'disabled';
    $tmb2_aktif = '';
} elseif (@$_GET['page'] == 'table') {
    $tmb1_aktif = '';
    $tmb2_aktif = 'disabled';
} else {
    $tmb1_aktif = '';
    $tmb2_aktif = '';
}
?>

<!-- Small boxes (Stat box) -->

<style>
    #grafik_001 .progress {
        margin-bottom: 20px;
    }

    #tabel_chart #myChart {
        max-height: 300px;
        overflow: auto;
        width: 100%;
    }

    #tabel_ijin .tabel_curva_1 {
        max-height: 450px;
        overflow: auto;
        width: 100%;
    }

    .tombol_header {
        display: flex;
        justify-content: center;
        margin-left: auto;
        margin-right: auto;
    }

    /* .tombol_header .btn-group .active {} */
</style>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <!-- AREA CHART -->
                <div class="card card-navy elevation-2">
                    <div class="card-header bg-gradient-navy rounded">
                        <div class="tombol_header">
                            <div class="btn-group">
                                <a href="?page=chart" class="btn btn-primary bg-gradient-primary elevation-2 <?= $tmb1_aktif; ?>">
                                    <!-- <i class="fas fa-chart"></i> -->
                                    <?php if ($tmb1_aktif) { ?>
                                        <i class="fas fa-spinner fa-spin"></i>
                                    <?php } else { ?>
                                        <i class="fas fa-chart-area"></i>
                                    <?php } ?>
                                    &nbsp;
                                    Grafik
                                </a>
                                <!-- <a href="?page=table" class="btn btn-success bg-gradient-success elevation-2 <?= $tmb2_aktif; ?>"> -->
                                <a href="../app/mqtt/log/" class="btn btn-success bg-gradient-success elevation-2 <?= $tmb2_aktif; ?>">
                                    <?php if ($tmb2_aktif) { ?>
                                        <i class="fas fa-spinner fa-spin"></i>
                                    <?php } else { ?>
                                        <i class="fas fa-table"></i>
                                    <?php } ?>
                                    &nbsp;
                                    Log
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
date_default_timezone_set('Asia/Jakarta');
$tanggal___ = date('Y-m-d');

$get_tgl = @$_GET['tgl'];
if ($get_tgl) {
    $tanggal_pilih = date('Y-m-d', strtotime($get_tgl));
} else {
    $tanggal_pilih = date('Y-m-d');
}

$bulan_pilih = date('m', strtotime($tanggal_pilih));
$tahun_pilih = date('Y', strtotime($tanggal_pilih));
$nama_bulan_pilih = date('F', strtotime($tahun_pilih . "-" . $bulan_pilih . "-01"));
$bulanBahasaIndonesia = bulanIndo($nama_bulan_pilih);

$tanggal_awal_bulan_pilih = $tahun_pilih . "-" . duadigit($bulan_pilih) . "-01";
$tanggal_akhir_bulan_pilih = $tahun_pilih . "-" . duadigit($bulan_pilih) . "-31";

include '../config/konesi.php';

// Prepared statement untuk SELECT info FROM statusnya
$sql_status = $konek->prepare("SELECT info FROM statusnya");
$sql_status->execute();
$result_status = $sql_status->get_result();
$status = $result_status->fetch_assoc();
$harikerja = $status['info'];
$sql_status->close();

// Prepared statement untuk SELECT * FROM kodeinfo
$sql_kodeinfo = $konek->prepare("SELECT * FROM kodeinfo ORDER BY info ASC");
$sql_kodeinfo->execute();
$result_kodeinfo = $sql_kodeinfo->get_result();
$data_kodeinfo = $result_kodeinfo->fetch_all(MYSQLI_ASSOC);
$sql_kodeinfo->close();

// Prepared statement untuk SELECT * FROM datapresensi WHERE tanggal = ?
$sql_kehadiran_hari_ini = $konek->prepare("SELECT * FROM datapresensi WHERE tanggal = ? AND kode <> 'GR'");
$sql_kehadiran_hari_ini->bind_param("s", $tanggal___);
$sql_kehadiran_hari_ini->execute();
$result_kehadiran_hari_ini = $sql_kehadiran_hari_ini->get_result();
$data_kehadiran_hari_ini = $result_kehadiran_hari_ini->fetch_all(MYSQLI_ASSOC);
$sql_kehadiran_hari_ini->close();

// Prepared statement untuk SELECT * FROM datapresensi WHERE tanggal = ?
$sql_kehadiran_bulan_ini = $konek->prepare("SELECT * FROM datapresensi WHERE tanggal  BETWEEN ? AND ? AND kode <> 'GR'");
$sql_kehadiran_bulan_ini->bind_param("ss", $tanggal_awal_bulan_pilih, $tanggal_akhir_bulan_pilih);
$sql_kehadiran_bulan_ini->execute();
$result_kehadiran_bulan_ini = $sql_kehadiran_bulan_ini->get_result();
$data_kehadiran_bulan_ini = $result_kehadiran_bulan_ini->fetch_all(MYSQLI_ASSOC);
$sql_kehadiran_bulan_ini->close();

// Prepared statement untuk SELECT * FROM datapresensi WHERE kode <> 'GR'
$sql_kehadiran_semua = $konek->prepare("SELECT * FROM datapresensi WHERE kode <> 'GR'");
$sql_kehadiran_semua->execute();
$result_kehadiran_semua = $sql_kehadiran_semua->get_result();
$data_kehadiran_semua = $result_kehadiran_semua->fetch_all(MYSQLI_ASSOC);
$sql_kehadiran_semua->close();

// Prepared statement untuk SELECT * FROM presensikelas WHERE tanggal = ?
$sql_kehadiran_kelas_hari_ini = $konek->prepare("SELECT * FROM presensikelas WHERE tanggal = ?");
$sql_kehadiran_kelas_hari_ini->bind_param("s", $tanggal_pilih);
$sql_kehadiran_kelas_hari_ini->execute();
$result_kehadiran_kelas_hari_ini = $sql_kehadiran_kelas_hari_ini->get_result();
$data_kehadiran_kelas_hari_ini = $result_kehadiran_kelas_hari_ini->fetch_all(MYSQLI_ASSOC);
$sql_kehadiran_kelas_hari_ini->close();

// Prepared statement untuk SELECT * FROM presensikelas
$sql_kehadiran_kelas = $konek->prepare("SELECT * FROM presensikelas");
$sql_kehadiran_kelas->execute();
$result_kehadiran_kelas = $sql_kehadiran_kelas->get_result();
$data_kehadiran_kelas = $result_kehadiran_kelas->fetch_all(MYSQLI_ASSOC);
$sql_kehadiran_kelas->close();

// Prepared statement untuk SELECT * FROM datasiswa
// $sql_datasiswa = $konek->prepare("SELECT nis, kelas FROM datasiswa");
$sql_datasiswa = $konek->prepare(
    "SELECT nis, kelas FROM datasiswa WHERE kelas NOT LIKE 'XII%'"
);

$sql_datasiswa->execute();
$result_datasiswa = $sql_datasiswa->get_result();
$data_datasiswa = $result_datasiswa->fetch_all(MYSQLI_ASSOC);
$sql_datasiswa->close();

// Prepared statement untuk SELECT * FROM dataguru WHERE status = 'Guru'
$sql_dataguru = $konek->prepare("SELECT * FROM dataguru WHERE status = 'Guru'");
$sql_dataguru->execute();
$result_dataguru = $sql_dataguru->get_result();
$data_dataguru = $result_dataguru->fetch_all(MYSQLI_ASSOC);
$sql_dataguru->close();

// Prepared statement untuk SELECT * FROM jadwalkbm WHERE tanggal = ?
$sql_jadwalkbm = $konek->prepare("SELECT * FROM jadwalkbm WHERE tanggal = ?");
$sql_jadwalkbm->bind_param("s", $tanggal_pilih);
$sql_jadwalkbm->execute();
$result_jadwalkbm = $sql_jadwalkbm->get_result();
$data_jadwalkbm = $result_jadwalkbm->fetch_all(MYSQLI_ASSOC);
$sql_jadwalkbm->close();

// Prepared statement untuk SELECT * FROM jurnalguru WHERE tanggal = ?
$sql_jurnalguru = $konek->prepare("SELECT * FROM jurnalguru WHERE tanggal = ?");
$sql_jurnalguru->bind_param("s", $tanggal_pilih);
$sql_jurnalguru->execute();
$result_jurnalguru = $sql_jurnalguru->get_result();
$data_jurnalguru = $result_jurnalguru->fetch_all(MYSQLI_ASSOC);
$sql_jurnalguru->close();

$sql_presensiEvent = $konek->prepare("SELECT * FROM presensiEvent WHERE tanggal = ?");
$sql_presensiEvent->bind_param("s", $tanggal_pilih);
$sql_presensiEvent->execute();
$result_presensiEvent = $sql_presensiEvent->get_result();
$data_presensiEvent = $result_presensiEvent->fetch_all(MYSQLI_ASSOC);
$sql_presensiEvent->close();

$output = [];

foreach ($data_presensiEvent as $row) {
    $nis = $row['nis'];

    // Inisialisasi 1x per NIS
    if (!isset($output[$nis])) {
        $output[$nis] = [
            'nis'   => $nis,
            'sesi1' => 0,
            'sesi2' => 0,
            'mens'  => 0
        ];
    }

    // Jika sudah mens, skip proses lain
    if ($output[$nis]['mens'] === 1) {
        continue;
    }

    // Validasi mens (prioritas tertinggi)
    if ($row['ruang'] === 'Izin Mens') {
        $output[$nis]['mens']  = 1;
        $output[$nis]['sesi1'] = 0;
        $output[$nis]['sesi2'] = 0;
        continue;
    }

    // Mapping sesi (hanya jika bukan mens)
    if (strtoupper($row['keterangan']) === 'DZUHUR') {
        $output[$nis]['sesi1'] = 1;
    }

    if (strtoupper($row['keterangan']) === 'ASHAR') {
        $output[$nis]['sesi2'] = 1;
    }
}

// echo "<pre>";
// print_r($output);
// echo "</pre>";

$jml_presensi_semua = count(@$data_kehadiran_semua);

$_jml_semua_siswa = count(@$data_datasiswa);

$_jml_tercatat_hadir_gate = count($data_kehadiran_hari_ini);
$_persen_tercatat_gate = round((@$_jml_tercatat_hadir_gate / $_jml_semua_siswa) * 100, 2);

$_jml_tercatat_hadir = count(@$data_kehadiran_kelas_hari_ini);
$_persen_tercatat = round((@$_jml_tercatat_hadir / @$_jml_semua_siswa) * 100, 2);

$_semua_data_presensi_kelas = count(@$data_kehadiran_kelas);
if ($_semua_data_presensi_kelas >= 100) {
    $_semua_data_presensi_kelas = $_semua_data_presensi_kelas / 1000;
}

mysqli_close($konek);
?>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-9 chart_hari_ini">
                <div class="card card-navy">
                    <div class="card-header bg-gradient-navy">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i>
                            &nbsp;
                            Kehadiran Siswa Hari ini (%)
                        </h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div id="tabel_ijin" class="card-body">
                        <div class="">
                            <?php
                            // echo "<pre>";
                            // print_r($data_kehadiran_hari_ini);
                            // echo "</pre>";
                            ?>
                            <div class="row w-100 mb-n2 text-center">
                                <div class="col-2 mb-1">
                                    <b>
                                        KELAS
                                    </b>
                                </div>
                                <div class="col-6 my-1">
                                    <b>
                                        PROGRES KEHADIRAN (%)
                                    </b>
                                </div>
                                <div class="col-4 my-1">
                                    <b>
                                        KETERANGAN
                                    </b>
                                    <div style="font-size: 12px;">
                                        <span class="badge badge-sm badge-success">M</span>
                                        <span class="badge badge-sm badge-warning">T</span>
                                        <span class="badge badge-sm badge-info">I</span>
                                        <span class="badge badge-sm badge-danger">A</span>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <?php
                            $i = 0;
                            foreach ($data_kodeinfo as $value_kodeinfo) {
                                if ($i % 2 != 0) {
                                    $bg_baris = " bg-secondary";
                                } else {
                                    $bg_baris = " bg-dark";
                                }

                                if ($i > 2) {
                                    $jur = $value_kodeinfo['jur'];

                                    if ($jur == 'AT') {
                                        $bg_bar1 = 'success';
                                    } elseif ($jur == 'DKV') {
                                        $bg_bar1 = 'primary';
                                    } elseif ($jur == 'TE') {
                                        $bg_bar1 = 'warning';
                                    } else {
                                        $bg_bar1 = 'dark';
                                    }
                            ?>
                                    <a href="#myChart<?= $i - 2; ?>" class="row w-100 border aktif_sorot text-dark" style="cursor: pointer; text-decoration: none;">
                                        <div class="col-2 mt-2">
                                            <h6 style="font-size: 12px;"><?= $value_kodeinfo['info']; ?></h6>
                                        </div>

                                        <?php
                                        $_data_satukelas = cari_array_($value_kodeinfo['info'], $data_datasiswa, 'kelas');
                                        $_jml_1_kelas = count($_data_satukelas);

                                        $_data_presensi_gate = cari_array_($value_kodeinfo['info'], $data_kehadiran_hari_ini, 'info');
                                        $_jml_data = count($_data_presensi_gate);
                                        $_jml_data = (int)$_jml_data * 1;

                                        $_jml_ = $_jml_data + $_jml_1_kelas;

                                        $ww = 0;
                                        $_H = 0;
                                        $_T = 0;
                                        $_I = 0;
                                        $_S = 0;
                                        $_A = 0;
                                        $_TPM = 0;

                                        foreach ($_data_presensi_gate as $value_data_kehadiran) {
                                            $__hdir = $_data_presensi_gate[$ww]['ketmasuk'];
                                            $__plg = $_data_presensi_gate[$ww]['ketpulang'];
                                            $_data_hadir[] = $__hdir;
                                            $_data_pulang[] = $__plg;

                                            // echo "$__hdir<br>";

                                            if ($__hdir == 'MSK') {
                                                $_H++;
                                            } elseif (($__plg == "P" || $__plg == "PA") && $__hdir == '-') {
                                                $_TPM++;
                                            } elseif ($__hdir == 'TLT') {
                                                $_T++;
                                            } elseif ($__hdir == 'Ijin') {
                                                $_I++;
                                            } elseif ($__hdir == 'Sakit') {
                                                $_S++;
                                            } else {
                                                $_A++;
                                            }

                                            $__hdir = "";
                                            $ww++;
                                        }

                                        if ($_jml_1_kelas > 0) {
                                            // $_persen_H = round($_H / $_jml_data * 100, 2);
                                            $_persen_H = (($_H * 100) / $_jml_1_kelas);
                                            $_persen_T = (($_T * 100) / $_jml_1_kelas);
                                            $_persen_I = (($_I * 100) / $_jml_1_kelas);
                                            $_persen_S = (($_S * 100) / $_jml_1_kelas);
                                            $_persen_TPM = (($_TPM * 100) / $_jml_1_kelas);
                                            // $_persen_A = round(($_A * 100) / $_jml_1_kelas);

                                            // $jml_A = $_jml_1_kelas - $_jml_data;
                                            $jml_A = $_jml_1_kelas - (@$_H + @$_TPM + @$_T);
                                            $_persen_A = (($jml_A * 100) / $_jml_1_kelas);

                                            $_persen_IS = $_persen_I + $_persen_S;
                                        } else {
                                            $_persen_H = 0;
                                            $_persen_T = 0;
                                            $_persen_I = 0;
                                            $_persen_S = 0;
                                            $_persen_A = 100;
                                            $_persen_IS = 0;
                                            $_persen_TPM = 0;
                                        }

                                        // $_jml_persen_ = $_persen_H + $_persen_T + $_persen_I + $_persen_S + $_persen_A;
                                        // $_jml_persen_ = (($_jml_data * 100) / $_jml_1_kelas);
                                        if ($_jml_1_kelas != 0) {
                                            $_jml_persen_ = number_format(($_jml_data * 100) / $_jml_1_kelas, 2);
                                        } else {
                                            $_jml_persen_ = 0; // atau nilai default lain sesuai kebutuhan
                                        }
                                        ?>

                                        <div class="col-6 mt-2 mb-1">
                                            <div class="progress border">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= @$_persen_H; ?>%" aria-valuenow="<?= @$_persen_H; ?>" aria-valuemin="0" aria-valuemax="100">
                                                    <?= @$_H; ?>
                                                </div>
                                                <div class="progress-bar bg-secondary" role="progressbar" style="width: <?= @$_persen_TPM; ?>%" aria-valuenow="<?= @$_persen_TPM; ?>" aria-valuemin="0" aria-valuemax="100">
                                                    <?= @$_TPM; ?>
                                                </div>
                                                <div class="progress-bar bg-warning" role="progressbar" style="width: <?= @$_persen_T; ?>%" aria-valuenow="<?= @$_persen_T; ?>" aria-valuemin="0" aria-valuemax="0">
                                                    <?= @$_T; ?>
                                                </div>
                                                <div class="progress-bar bg-info" role="progressbar" style="width: <?= @$_persen_IS; ?>%" aria-valuenow="<?= @$_persen_IS; ?>" aria-valuemin="0" aria-valuemax="100">
                                                    <?= @$_I + @$_S; ?>
                                                </div>
                                                <div class="progress-bar bg-danger" role="progressbar" style="width: <?= @$_persen_A; ?>%" aria-valuenow="<?= @$_persen_A; ?>" aria-valuemin="0" aria-valuemax="0">
                                                    <?= (@$_persen_A > 99) ? $_jml_1_kelas : @$jml_A; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 my-1" style="font-size: 11px;">
                                            <span class="badge bg-black text-bg-light">
                                                <?= $_jml_persen_; ?>&nbsp;%
                                            </span>
                                            (<?= $_jml_data; ?>/<?= $_jml_1_kelas; ?>)

                                            <span class="badge badge-success"><?= @$_H + @$_TPM + @$_T; ?></span>
                                            <span class="badge badge-warning"><?= @$_T; ?></span>
                                            <span class="badge badge-info"><?= (@$_S + @$_I); ?></span>
                                            <span class="badge badge-danger"><?= (@$_persen_A > 99) ? $_jml_1_kelas : @$jml_A; ?></span>
                                        </div>
                                    </a>
                            <?php }
                                $i++;
                            } ?>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <div style="font-size: 11px; font-style: italic;">
                                <?php
                                $_persen_tercatat_gate_q = ($_jml_tercatat_hadir_gate / $_jml_semua_siswa) * 100;
                                ?>
                                Data masuk:&nbsp;<?= $_jml_tercatat_hadir_gate; ?>&nbsp;/&nbsp;<?= $_jml_semua_siswa; ?>&nbsp;(<?= number_format($_persen_tercatat_gate_q, 2); ?>&nbsp;%)

                            </div>

                            <div>
                                <button onclick="printPortrait()" class="btn-print">Print</button>
                                <style>
                                    .btn-print {
                                        background-color: #007bff;
                                        color: #fff;
                                        padding: 4px 8px;
                                        border: none;
                                        border-radius: 4px;
                                        cursor: pointer;
                                        transition: background-color 0.3s;
                                    }

                                    .btn-print:hover {
                                        background-color: #0056b3;
                                    }
                                </style>

                                <script>
                                    function printPortrait() {
                                        // Buat elemen style dan tambahkan aturan CSS untuk orientasi potret
                                        var style = document.createElement('style');
                                        style.textContent = `
        @media print {
            body * {
                visibility: hidden;
            }
            .chart_hari_ini, .chart_hari_ini * {
                visibility: visible;
            }
            .chart_hari_ini {
                position: absolute;
                left: 0;
                top: 0;
            }
            @page {
                size: A4 portrait;
            }
        }
    `;
                                        document.head.appendChild(style);

                                        // Cetak halaman
                                        window.print();

                                        // Hapus elemen style setelah mencetak
                                        style.remove();
                                    }
                                </script>

                            </div>
                            <div>
                                <a href="#tabelrekappersenbulan" class="btn btn-outline-secondary btn-sm float-md-right">
                                    <i class="fas fa-info"></i>
                                    &nbsp;
                                    Selengkapnya
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <!-- AREA CHART -->
                <div class="card card-navy">
                    <div class="card-header bg-gradient-navy">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie"></i>
                            &nbsp;
                            Info
                        </h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <?php
                    // data 1 hari presensi
                    $_data_presensi_kelas = cari_array_orderby($data_kehadiran_hari_ini, 'status');
                    $_jml_data = count($_data_presensi_kelas);
                    $_jml_data = (int)$_jml_data * 1;

                    $_jml_ = $_jml_data + $_jml_1_kelas;

                    $_data_hadir = array();
                    $jj = 0;
                    $_H = 0;
                    $_T = 0;
                    $_I = 0;
                    $_S = 0;
                    $_A = 0;
                    $_TPP = 0;
                    $_TPM = 0;

                    foreach ($_data_presensi_kelas as $value_data_kehadiran) {
                        $__hdir = $_data_presensi_kelas[$jj]['ketmasuk'];
                        $__hpulang = $_data_presensi_kelas[$jj]['ketpulang'];
                        $_data_hadir[] = $__hdir;

                        if (($__hdir == 'MSK' || $__hdir == 'TLT') && ($__hpulang == "P" || $__hpulang == "PA")) {
                            $_H++;
                        } elseif ($__hdir == "-" && ($__hpulang == "P" || $__hpulang == "PA")) {
                            $_TPM++;
                        } elseif ($__hpulang == "-" && ($__hdir == "MSK" || $__hdir == "TLT")) {
                            $_TPP++;
                        } elseif ($__hdir == 'Ijin') {
                            $_I++;
                        } elseif ($__hdir == 'Sakit') {
                            $_S++;
                        } else {
                            $_A++;
                        }

                        if ($__hdir == 'TLT') {
                            $_T++;
                        }

                        $jj++;
                    }

                    if ($_jml_data > 0) {
                        $_total_H = $_TPM + $_TPP + $_H;
                        // $_persen_H = round($_H / $_jml_data * 100, 2);
                        $_persen_H = round(((($_total_H) * 100) / $_jml_semua_siswa), 1);
                        $_persen_T = round((($_T * 100) / $_jml_semua_siswa), 1);
                        $_persen_I = round((($_I * 100) / $_jml_semua_siswa), 1);
                        $_persen_S = round((($_S * 100) / $_jml_semua_siswa), 1);
                        $_persen_A = round((($_A * 100) / $_jml_semua_siswa), 1);
                        $_persen_TPM = round((($_TPM * 100) / $_jml_semua_siswa), 1);
                        $_persen_TPP = round((($_TPP * 100) / $_jml_semua_siswa), 1);
                        $_persen_jml_data = round((($_jml_data * 100) / $_jml_semua_siswa), 1);

                        $_persen_IS = $_persen_I + $_persen_S;
                        // echo "_persen_H: " . $_persen_H . "<br>";
                        // echo "H: " . $_H . "<br>";
                        // echo "T: " . $_T . "<br>";
                        // echo "I: " . $_I . "<br>";
                        // echo "S: " . $_S . "<br>";
                        // echo "A: " . $_A . "<br>";
                        // echo $_jml_data . "<pre>";
                        // print_r($_data_hadir);
                        // echo "</pre>";
                    } else {
                        $_persen_H = 0;
                        $_persen_T = 0;
                        $_persen_I = 0;
                        $_persen_S = 0;
                        $_persen_A = 0;
                        $_persen_IS = 0;
                        $_persen_TPM = 0;
                        $_persen_TPP = 0;
                    }

                    $_ALP = $_jml_semua_siswa - ($_jml_data);
                    $persenHadirHariini = $_persen_jml_data;
                    $tidakHadirHariini = 100 - $persenHadirHariini;

                    ?>

                    <div id="tabel_ijin" class="card-body">
                        <h6 class="text-center">Kehadiran hari ini</h6>
                        <div class="tabel_curva_1">
                            <div class="d-flex justify-content-around">
                                <div class="progress progress-bar-vertical">
                                    <div class="progress-bar bg-success" role="progressbar" aria-valuenow="<?= $_persen_jml_data; ?>" aria-valuemin="0" aria-valuemax="100" style="height: <?= $_persen_jml_data; ?>%;">
                                        <span class="sr-only"><?= $_persen_jml_data; ?>%</span>
                                    </div>
                                </div>
                                <div class="progress progress-bar-vertical">
                                    <div class="progress-bar bg-secondary" role="progressbar" aria-valuenow="<?= $_persen_TPM; ?>" aria-valuemin="0" aria-valuemax="100" style="height: <?= $_persen_TPM; ?>%;">
                                        <span class="sr-only"><?= $_persen_TPM; ?>%</span>
                                    </div>
                                </div>
                                <div class="progress progress-bar-vertical">
                                    <div class="progress-bar bg-dark" role="progressbar" aria-valuenow="<?= $_persen_TPP; ?>" aria-valuemin="0" aria-valuemax="100" style="height: <?= $_persen_TPP; ?>%;">
                                        <span class="sr-only"><?= $_persen_TPP; ?>%</span>
                                    </div>
                                </div>
                                <div class="progress progress-bar-vertical">
                                    <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="<?= $_persen_T; ?>" aria-valuemin="0" aria-valuemax="100" style="height: <?= $_persen_T; ?>%;">
                                        <span class="sr-only"><?= $_persen_T; ?>%</span>
                                    </div>
                                </div>
                                <div class="progress progress-bar-vertical">
                                    <div class="progress-bar bg-danger" role="progressbar" aria-valuenow="<?= $tidakHadirHariini; ?>" aria-valuemin="0" aria-valuemax="100" style="height: <?= $tidakHadirHariini; ?>%;">
                                        <span class="sr-only"><?= $tidakHadirHariini; ?>%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-around">
                                <div>
                                    <div><?= $_persen_jml_data; ?>%</div>
                                    <span class="badge badge-sm badge-success">
                                        <?= $_jml_data; ?>
                                    </span>
                                    <div>MSK</div>
                                </div>
                                <div>
                                    <div><?= $_persen_TPM; ?>%</div>
                                    <span class="badge badge-sm badge-secondary">
                                        <?= $_TPM; ?>
                                    </span>
                                    <div>TPM</div>
                                </div>
                                <div>
                                    <div><?= $_persen_TPP; ?>%</div>
                                    <span class="badge badge-sm badge-dark">
                                        <?= $_TPP; ?>
                                    </span>
                                    <div>TPP</div>
                                </div>
                                <div>
                                    <div><?= $_persen_T; ?>%</div>
                                    <span class="badge badge-sm badge-warning">
                                        <?= $_T; ?>
                                    </span>
                                    <div>TLT</div>
                                </div>
                                <div>
                                    <div><?= $tidakHadirHariini; ?>%</div>
                                    <span class="badge badge-sm badge-danger">
                                        <?= $_ALP; ?>
                                    </span>
                                    <div>ALP</div>
                                </div>
                            </div>

                            <div>
                                Jumlah Siswa: <?= $_jml_semua_siswa; ?>
                            </div>
                            <hr>
                            <div class="border border-2 rounded px-1 py-0 mb-1">
                                <p>Keterangan :<br>
                                    <span class="badge badge-sm badge-success">M</span>
                                    <span class="badge badge-sm badge-success">MSK</span>
                                    : MASUK<br>
                                    <span class="badge badge-sm badge-warning">T</span>
                                    <span class="badge badge-sm badge-warning">TLT</span>
                                    : TERLAMBAT<br>
                                    <span class="badge badge-sm badge-secondary">TPM</span>
                                    : Tidak Presensi Masuk<br>
                                    <span class="badge badge-sm badge-dark">TPP</span>
                                    : Tidak Presensi Pulang<br>
                                    <span class="badge badge-sm badge-info">I</span>
                                    <span class="badge badge-sm badge-info">IZN</span>
                                    : IJIN<br>
                                    <span class="badge badge-sm badge-danger">A</span>
                                    <span class="badge badge-sm badge-danger">ALP</span>
                                    : ALPA / Tidak&nbsp;Masuk / Tidak&nbsp;Presensi
                                </p>
                            </div>

                            <style>
                                .progress-bar-vertical {
                                    width: 20px;
                                    min-height: 100px;
                                    margin-right: 20px;
                                    float: left;
                                    display: -webkit-box;
                                    /* OLD - iOS 6-, Safari 3.1-6, BB7 */
                                    display: -ms-flexbox;
                                    /* TWEENER - IE 10 */
                                    display: -webkit-flex;
                                    /* NEW - Safari 6.1+. iOS 7.1+, BB10 */
                                    display: flex;
                                    /* NEW, Spec - Firefox, Chrome, Opera */
                                    align-items: flex-end;
                                    -webkit-align-items: flex-end;
                                    /* Safari 7.0+ */
                                }

                                .progress-bar-vertical .progress-bar {
                                    width: 100%;
                                    height: 0;
                                    -webkit-transition: height 0.6s ease;
                                    -o-transition: height 0.6s ease;
                                    transition: height 0.6s ease;
                                }
                            </style>

                        </div>

                        <?php
                        // data 1 hari presensi
                        $_data_presensi_kelas = cari_array_orderby($data_kehadiran_semua, 'status');
                        $_jml_data = count($_data_presensi_kelas);
                        $_jml_data = (int)$_jml_data * 1;

                        $_jml_ = $_jml_data + $_jml_1_kelas;

                        $_data_hadir = array();
                        $jj = 0;
                        $_H = 0;
                        $_T = 0;
                        $_I = 0;
                        $_S = 0;
                        $_A = 0;
                        $_TPP = 0;
                        $_TPM = 0;

                        foreach ($_data_presensi_kelas as $value_data_kehadiran) {
                            $__hdir = $_data_presensi_kelas[$jj]['ketmasuk'];
                            $__hpulang = $_data_presensi_kelas[$jj]['ketpulang'];
                            $_data_hadir[] = $__hdir;

                            if ($__hdir == 'MSK') {
                                $_H++;
                            } elseif ($__hdir == "-" && ($__hpulang == "P" || $__hpulang == "PA")) {
                                $_TPM++;
                            } elseif ($__hpulang == "-" && ($__hdir == "MSK" || $__hdir == "TLT")) {
                                $_TPP++;
                            } elseif ($__hdir == 'TLT') {
                                $_T++;
                            } elseif ($__hdir == 'Ijin') {
                                $_I++;
                            } elseif ($__hdir == 'Sakit') {
                                $_S++;
                            } else {
                                $_A++;
                            }

                            $jj++;
                        }

                        if ($_jml_data > 0) {
                            // $_persen_H = round($_H / $_jml_data * 100, 2);
                            $_persen_H = round((($_H * 100) / $jml_presensi_semua), 1);
                            $_persen_T = round((($_T * 100) / $jml_presensi_semua), 1);
                            $_persen_I = round((($_I * 100) / $jml_presensi_semua), 1);
                            $_persen_S = round((($_S * 100) / $jml_presensi_semua), 1);
                            $_persen_A = round((($_A * 100) / $jml_presensi_semua), 1);
                            $_persen_TPM = round((($_TPM * 100) / $jml_presensi_semua), 1);
                            $_persen_TPP = round((($_TPP * 100) / $jml_presensi_semua), 1);

                            $_persen_IS = $_persen_I + $_persen_S;
                            // echo "_persen_H: " . $_persen_H . "<br>";
                            // echo "H: " . $_H . "<br>";
                            // echo "T: " . $_T . "<br>";
                            // echo "I: " . $_I . "<br>";
                            // echo "S: " . $_S . "<br>";
                            // echo "A: " . $_A . "<br>";
                            // echo $_jml_data . "<pre>";
                            // print_r($_data_hadir);
                            // echo "</pre>";
                        } else {
                            $_persen_H = 0;
                            $_persen_T = 0;
                            $_persen_I = 0;
                            $_persen_S = 0;
                            $_persen_A = 0;
                            $_persen_IS = 0;
                            $_persen_TPM = 0;
                            $_persen_TPM = 0;
                        }

                        ?>
                        <h6 class="text-center">Rata-rata Seluruh Kehadiran</h6>
                        <div class="tabel_curva_1">
                            <div class="d-flex justify-content-around">
                                <div class="progress progress-bar-vertical">
                                    <div class="progress-bar bg-success" role="progressbar" aria-valuenow="<?= $_persen_H + $_persen_TPP; ?>" aria-valuemin="0" aria-valuemax="100" style="height: <?= $_persen_H + $_persen_TPP; ?>%;">
                                        <span class="sr-only"><?= $_persen_H + $_persen_TPP; ?>%</span>
                                    </div>
                                </div>
                                <div class="progress progress-bar-vertical">
                                    <div class="progress-bar bg-secondary" role="progressbar" aria-valuenow="<?= $_persen_TPM; ?>" aria-valuemin="0" aria-valuemax="100" style="height: <?= $_persen_TPM; ?>%;">
                                        <span class="sr-only"><?= $_persen_TPM; ?>%</span>
                                    </div>
                                </div>
                                <div class="progress progress-bar-vertical">
                                    <div class="progress-bar bg-dark" role="progressbar" aria-valuenow="<?= $_persen_TPP; ?>" aria-valuemin="0" aria-valuemax="100" style="height: <?= $_persen_TPP; ?>%;">
                                        <span class="sr-only"><?= $_persen_TPP; ?>%</span>
                                    </div>
                                </div>
                                <div class="progress progress-bar-vertical">
                                    <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="<?= $_persen_T; ?>" aria-valuemin="0" aria-valuemax="100" style="height: <?= $_persen_T; ?>%;">
                                        <span class="sr-only"><?= $_persen_T; ?>%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-around">
                                <div>
                                    <div><?= $_persen_H + $_persen_TPP; ?>%</div>
                                    <div>MSK</div>
                                </div>
                                <div>
                                    <div><?= $_persen_TPM; ?>%</div>
                                    <div>TPM</div>
                                </div>
                                <div>
                                    <div><?= $_persen_TPP; ?>%</div>
                                    <div>TPP</div>
                                </div>
                                <div>
                                    <div><?= $_persen_T; ?>%</div>
                                    <div>TLT</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <a href="#tabelrekappersenbulan" class="btn btn-outline-secondary btn-sm float-md-right">
                            <i class="fas fa-info"></i>
                            &nbsp;
                            Selengkapnya
                        </a>
                    </div>
                    <!-- /. card-footer -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-navy" id="tabelrekappersenbulan">
                    <div class="card-header bg-gradient-navy">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i>
                            &nbsp;
                            Pembiasaan Siswa
                        </h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div id="" class="card-body">
                        <!-- <div class="row align-items-center mb-2"> -->
                        <table class="">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="fw-bold">
                                            Kelas
                                        </div>
                                    </th>

                                    <th>
                                        <!-- <div class="col-6"> -->
                                        <span class="badge bg-success">DHR + ASR</span>
                                        <span class="badge bg-info">DZUHUR</span>
                                        <span class="badge bg-warning">ASHAR</span>
                                        <span class="badge bg-primary">Izin Mens</span>
                                        <span class="badge bg-danger">Alpha</span>
                                        <!-- </div> -->
                                    </th>

                                    <!-- <div class="col-4 small"> -->
                                    <th> <span class="badge bg-success">D+A</span></th>
                                    <th><span class="badge bg-info">D</span></th>
                                    <th><span class="badge bg-warning">A</span></th>
                                    <th><span class="badge bg-primary">I</span></th>
                                    <th><span class="badge bg-danger">Alp</span></th>
                                    <th><span class="badge bg-dark">Tot</span></th>
                                    <!-- </div> -->
                                </tr>
                            </thead>
                            <!-- </div> -->

                            <tbody>
                                <?php
                                /* =========================
                            INDEX PRESENSI BY NIS
                            ========================= */
                                $presensiByNis = [];
                                foreach ($output as $row) {
                                    $presensiByNis[$row['nis']] = $row;
                                }

                                /* =========================
                            AGREGASI PER KELAS
                            ========================= */
                                $kelasData = [];

                                foreach ($data_datasiswa as $row) {
                                    $kelas = $row['kelas'];
                                    $nis   = $row['nis'];

                                    if (!isset($kelasData[$kelas])) {
                                        $kelasData[$kelas] = [
                                            'total'        => 0,

                                            // statistik asli (event-based)
                                            'sesi1'        => 0,
                                            'sesi2'        => 0,

                                            // visual-only (person-based, eksklusif)
                                            'onlisesi1'    => 0,
                                            'onlisesi2'    => 0,
                                            'duasesi'      => 0,
                                            'mens'         => 0
                                        ];
                                    }

                                    $kelasData[$kelas]['total']++;

                                    if (isset($presensiByNis[$nis])) {

                                        $p = $presensiByNis[$nis];

                                        // ===== statistik asli (boleh overlap)
                                        $kelasData[$kelas]['sesi1'] += $p['sesi1'];
                                        $kelasData[$kelas]['sesi2'] += $p['sesi2'];

                                        // ===== klasifikasi visual (EKSKLUSIF)
                                        if ($p['mens'] == 1) {
                                            $kelasData[$kelas]['mens']++;
                                        } elseif ($p['sesi1'] == 1 && $p['sesi2'] == 1) {
                                            $kelasData[$kelas]['duasesi']++;
                                        } elseif ($p['sesi1'] == 1) {
                                            $kelasData[$kelas]['onlisesi1']++;
                                        } elseif ($p['sesi2'] == 1) {
                                            $kelasData[$kelas]['onlisesi2']++;
                                        }
                                    }
                                }

                                /* =========================
                            OUTPUT BAR PER KELAS
                            ========================= */
                                $tot_sesi1 = 0;
                                $tot_sesi2 = 0;
                                $tot_mens = 0;
                                $tot_alpa = 0;
                                foreach ($kelasData as $kelas => $d) {

                                    $total = $d['total'];
                                    if ($total == 0) continue;

                                    $alpha = $total - ($d['onlisesi1'] + $d['onlisesi2'] + $d['duasesi'] + $d['mens']);
                                    if ($alpha < 0) $alpha = 0;

                                    // UNTUK BAR PER KELAS
                                    $p_sesi1 = round(($d['sesi1'] / $total) * 100, 2);
                                    $p_sesi2 = round(($d['sesi2'] / $total) * 100, 2);
                                    $p_duasesi   = round(($d['duasesi']   / $total) * 100, 2);
                                    $p_only_s1   = round(($d['onlisesi1'] / $total) * 100, 2);
                                    $p_only_s2   = round(($d['onlisesi2'] / $total) * 100, 2);
                                    $p_mens      = round(($d['mens']      / $total) * 100, 2);
                                    $p_alpha     = round(($alpha     / $total) * 100, 2);

                                    // UNTUK BAR TOTAL
                                    $tot_sesi1 = $tot_sesi1 + $d['sesi1'];
                                    $tot_sesi2 = $tot_sesi2 + $d['sesi2'];
                                    $tot_duasesi = $tot_duasesi + $d['duasesi'];
                                    $tot_onlisesi1 = $tot_onlisesi1 + $d['onlisesi1'];
                                    $tot_onlisesi2 = $tot_onlisesi2 + $d['onlisesi2'];
                                    $tot_mens = $tot_mens + $d['mens'];
                                    $tot_alpa = $tot_alpa + $alpha;
                                ?>
                                    <tr>
                                        <!-- ====== KELAS ====== -->
                                        <!-- <div class="row align-items-center mb-2"> -->
                                        <!-- <div class="col-2"> -->
                                        <td>
                                            <?= htmlspecialchars($kelas); ?>
                                        </td>
                                        <!-- </div> -->

                                        <td style="width:50vw">
                                            <!-- <div class="col-6"> -->
                                            <div class="">
                                                <div class="progress border">

                                                    <!-- HIJAU : DUA SESI -->
                                                    <div class="progress-bar bg-success"
                                                        style="width: <?= $p_duasesi ?>%">
                                                        <?= $d['duasesi'] ?>
                                                    </div>

                                                    <!-- HIJAU : SESI 1 -->
                                                    <div class="progress-bar bg-info"
                                                        style="width: <?= $p_only_s1 ?>%">
                                                        <?= $d['onlisesi1'] ?>
                                                    </div>

                                                    <!-- KUNING : SESI 2 -->
                                                    <div class="progress-bar bg-warning"
                                                        style="width: <?= $p_only_s2 ?>%">
                                                        <?= $d['onlisesi2'] ?>
                                                    </div>

                                                    <!-- BIRU : MENS -->
                                                    <div class="progress-bar bg-primary"
                                                        style="width: <?= $p_mens ?>%">
                                                        <?= $d['mens'] ?>
                                                    </div>

                                                    <!-- MERAH : ALPHA -->
                                                    <div class="progress-bar bg-danger"
                                                        style="width: <?= $p_alpha ?>%">
                                                        <?= $alpha ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- <div class="col-4 small"> -->
                                        <td><span class="badge bg-success"><?= $d['duasesi'] ?></span></td>
                                        <td><span class="badge bg-info"><?= $d['sesi1'] ?></span></td>
                                        <td><span class="badge bg-warning"><?= $d['sesi2'] ?></span></td>
                                        <td><span class="badge bg-primary"><?= $d['mens'] ?></span></td>
                                        <td><span class="badge bg-danger"><?= $alpha ?></span></td>
                                        <td><span class="badge bg-dark"><?= $total ?></span></td>
                                        <!-- </div> -->
                                        <!-- </div> -->
                                    </tr>
                                <?php }


                                $persen_sesi1 = round(($tot_sesi1 / $_jml_semua_siswa) * 100, 2);
                                $persen_sesi2 = round(($tot_sesi2 / $_jml_semua_siswa) * 100, 2);
                                $persen_mens = round(($tot_mens / $_jml_semua_siswa) * 100, 2);
                                $persen_alpa = round(($tot_alpa / $_jml_semua_siswa) * 100, 2);
                                ?>

                            </tbody>
                        </table>
                        <hr>

                        <div class="col-6">
                            <div class="tabel_curva_1">
                                <div class="d-flex justify-content-around">
                                    <div class="progress progress-bar-vertical">
                                        <div class="progress-bar bg-success" role="progressbar" aria-valuenow="<?= $persen_sesi1; ?>" aria-valuemin="0" aria-valuemax="100" style="height: <?= $persen_sesi1; ?>%;">
                                            <!-- Jumlah Semua Sesi 1 -->
                                            <span class="sr-only"><?= $persen_sesi1; ?>%</span>
                                        </div>
                                    </div>
                                    <div class="progress progress-bar-vertical">
                                        <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="<?= $persen_sesi2; ?>" aria-valuemin="0" aria-valuemax="100" style="height: <?= $persen_sesi2; ?>%;">
                                            <!-- Jumlah semua sesi 2 -->
                                            <span class="sr-only"><?= $persen_sesi2; ?>%</span>
                                        </div>
                                    </div>
                                    <div class="progress progress-bar-vertical">
                                        <div class="progress-bar bg-primay" role="progressbar" aria-valuenow="<?= $persen_mens; ?>" aria-valuemin="0" aria-valuemax="100" style="height: <?= $persen_mens; ?>%;">
                                            <!-- Jumlah Semua mens -->
                                            <span class="sr-only"><?= $persen_mens; ?>%</span>
                                        </div>
                                    </div>
                                    <div class="progress progress-bar-vertical">
                                        <div class="progress-bar bg-danger" role="progressbar" aria-valuenow="<?= $persen_alpa; ?>" aria-valuemin="0" aria-valuemax="100" style="height: <?= $persen_alpa; ?>%;">
                                            <!-- Jumlah Alpa -->
                                            <span class="sr-only"><?= $persen_alpa; ?>%</span>
                                        </div>
                                    </div>
                                    <div class="progress progress-bar-vertical">
                                        <div class="progress-bar bg-dark" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="height: 100%;">
                                            <!-- Jumlah Total siswa -->
                                            <span class="sr-only"><?= $_jml_semua_siswa; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-around">
                                <div>
                                    <div><?= $persen_sesi1; ?>%</div>
                                    <span class="badge badge-sm badge-success">
                                        <?= $tot_sesi1; ?>
                                    </span>
                                    <div>DHR</div>
                                </div>
                                <div>
                                    <div><?= $persen_sesi2; ?>%</div>
                                    <span class="badge badge-sm badge-warning">
                                        <?= $tot_sesi2; ?>
                                    </span>
                                    <div>ASR</div>
                                </div>
                                <div>
                                    <div><?= $persen_mens; ?>%</div>
                                    <span class="badge badge-sm badge-primary">
                                        <?= $tot_mens; ?>
                                    </span>
                                    <div>IM</div>
                                </div>
                                <div>
                                    <div><?= $persen_alpa; ?>%</div>
                                    <span class="badge badge-sm badge-danger">
                                        <?= $tot_alpa; ?>
                                    </span>
                                    <div>ALP</div>
                                </div>
                                <div>
                                    <span class="badge badge-sm badge-dark">
                                        <?= $_ALP; ?>
                                    </span>
                                    <div>TOT</div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="event.php" class="btn btn-outline-secondary btn-sm float-md-right">
                                        <i class="fas fa-info"></i>
                                        &nbsp;
                                        Selengkapnya
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-navy" id="tabelrekappersenbulan">
                    <div class="card-header bg-gradient-navy">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i>
                            &nbsp;
                            Rekap Presentase Kehadiran Siswa (%)
                        </h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div id="" class="card-body">
                        <?php
                        include "_statistikperbulan.php";
                        ?>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="rekapbulan.php" class="btn btn-outline-secondary btn-sm float-md-right">
                                    <i class="fas fa-info"></i>
                                    &nbsp;
                                    Selengkapnya
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <!-- AREA CHART -->
                <div class="card card-navy">
                    <div class="card-header bg-gradient-navy">
                        <h3 class="card-title">Status kelas</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="" class="form-text"><span class="text-danger m-0">*</span>&nbsp;dalam pengembangan</div>
                        <div class="d-flex">
                            <div class="chart col-5 shadow rounded">
                                <h3 class="text-center">NA</h3>
                                <div class="d-flex flex-wrap d-grid gap-1">
                                    <?php for ($i = 0; $i < 16; $i++) { ?>
                                        <div class="d-flex bg-light shadow rounded p-2 m-auto gap-1">
                                            <div class="bg-info p-2 m-auto rounded">R <?= $i + 3; ?></div>
                                            <div class="bg-danger p-2 m-auto rounded">Status</div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="d-flex flex-column col-7 shadow rounded">
                                <div class="chart pb-3 shadow rounded">
                                    <h3 class="text-center">AT</h3>
                                    <div class="d-flex flex-wrap d-grid gap-1">
                                        <?php for ($i = 0; $i < 5; $i++) { ?>
                                            <div class="d-flex bg-light shadow rounded p-2 m-auto gap-1">
                                                <div class="bg-info p-2 m-auto rounded">Lab <?= $i + 1; ?></div>
                                                <div class="bg-danger p-2 m-auto rounded">Status</div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="chart pb-3 shadow rounded">
                                    <h3 class="text-center">DKV</h3>
                                    <div class="d-flex flex-wrap d-grid gap-1">
                                        <?php for ($i = 0; $i < 5; $i++) { ?>
                                            <div class="d-flex bg-light shadow rounded p-2 m-auto gap-1">
                                                <div class="bg-info p-2 m-auto rounded">Lab <?= $i + 1; ?></div>
                                                <div class="bg-danger p-2 m-auto rounded">Status</div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="chart pb-3 shadow rounded">
                                    <h3 class="text-center">TE</h3>
                                    <div class="d-flex flex-wrap d-grid gap-1">
                                        <?php for ($i = 0; $i < 5; $i++) { ?>
                                            <div class="d-flex bg-light shadow rounded p-2 m-auto gap-1">
                                                <div class="bg-info p-2 m-auto rounded">Lab <?= $i + 1; ?></div>
                                                <div class="bg-danger p-2 m-auto rounded">Status</div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <!-- AREA CHART -->
                <div class="card card-navy">
                    <div class="card-header bg-gradient-navy">
                        <h3 class="card-title">Jadwal Kelas</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div id="" class="form-text"><span class="text-danger m-0">*</span>&nbsp;dalam pengembangan</div>
                        <div class="table-responsive">
                            <table class="table">
                                <?php
                                foreach ($data_dataguru as $value_guru) {
                                    $nick_guru = $value_guru['nick'];
                                    $nama_guru = $value_guru['nama'];
                                ?>
                                    <tr>
                                        <td><?= $nama_guru; ?></td>
                                        <td>
                                            <?php
                                            $data_guru_di_kbm = cari_array_($nick_guru, $data_jadwalkbm, 'nick');
                                            if ($data_guru_di_kbm) {
                                                $jml_dat = count($data_guru_di_kbm);

                                                for ($i = 0; $i < $jml_dat; $i++) {
                                                    echo '<div class="d-flex d-grid gap-1">';

                                                    echo "<div class='badge badge-secondary'>" .  $i + 1 . ". </div>";

                                                    $mulai_jamke = $data_guru_di_kbm[$i]['mulai_jamke'];
                                                    $sampai_jamke = $data_guru_di_kbm[$i]['sampai_jamke'];
                                                    $ruangan = $data_guru_di_kbm[$i]['ruangan'];
                                                    $kelas = $data_guru_di_kbm[$i]['kelas'];
                                                    $info = $data_guru_di_kbm[$i]['info'];

                                                    echo "<div class='badge badge-dark'>" . $mulai_jamke . ' - ' . $sampai_jamke . "</div>";
                                                    echo "<div class='badge badge-danger'>" . $ruangan . "</div>";
                                                    echo "<div class='badge badge-warning'>" . $kelas . "</div>";
                                                    echo "<div class='badge badge-success'>" . $info . "</div>";

                                                    echo "</div>";
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $_data_jurnal = cari_array_($nick_guru, $data_jurnalguru, 'nick');
                                            if ($_data_jurnal) {
                                                // $jml_dat = count($_data_jurnal);

                                                for ($i = 0; $i < count($data_jurnalguru); $i++) {
                                                    $jurnal = $data_jurnalguru[$i]['jurnal'];
                                                    echo $i + 1 . '. ' . $jurnal . '<br>';
                                                }
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
</section>
<!-- /.content -->

<style>
    .grafik_progress_vertikal {
        height: 380px;
    }

    .grafik_progress_vertikal .progress {
        height: 80%;
    }

    .grafik_progress_vertikal .label_progres {
        /* vertikal degree */
        -webkit-transform: rotate(60deg);
        -moz-transform: rotate(60deg);
        -ms-transform: rotate(60deg);
        -o-transform: rotate(60deg);
        transform: rotate(60deg);
    }

    .grafik_progress_vertikal .div_label {
        /* background-color: aqua; */
        margin-top: 20px;
        color: darkgray;
    }
</style>

<?php

function cari_array_($_data_dicari, $_data_hasil_array, $_index_array)
{
    $_hasil_cari_array_ = array();
    foreach ($_data_hasil_array as $_value) {
        if ($_value[$_index_array] == $_data_dicari) {
            $_hasil_cari_array_[] = $_value;
        }
    }
    return $_hasil_cari_array_;
}

function cari_array_orderby($_data_hasil_array, $_index_array)
{
    $_hasil_cari_array_ = array();
    foreach ($_data_hasil_array as $_value) {
        $_hasil_cari_array_[] = $_value;
    }
    return $_hasil_cari_array_;
}

function cari_tanggal($tanggal_pilih, $_array)
{
    $hasil_cari_tanggal = array();
    foreach ($_array as $dtp) {
        if ($dtp['tanggal'] == $tanggal_pilih) {
            $hasil_cari_tanggal[] = $dtp;
        }
    }
    return $hasil_cari_tanggal;
}
?>

<script>
    $(function() {
        /* jQueryKnob */

        $('.knob').knob({
            /*change : function (value) {
             //console.log("change : " + value);
             },
             release : function (value) {
             console.log("release : " + value);
             },
             cancel : function () {
             console.log("cancel : " + this.value);
             },*/
            draw: function() {

                // "tron" case
                if (this.$.data('skin') == 'tron') {

                    var a = this.angle(this.cv) // Angle
                        ,
                        sa = this.startAngle // Previous start angle
                        ,
                        sat = this.startAngle // Start angle
                        ,
                        ea // Previous end angle
                        ,
                        eat = sat + a // End angle
                        ,
                        r = true

                    this.g.lineWidth = this.lineWidth

                    this.o.cursor &&
                        (sat = eat - 0.3) &&
                        (eat = eat + 0.3)

                    if (this.o.displayPrevious) {
                        ea = this.startAngle + this.angle(this.value)
                        this.o.cursor &&
                            (sa = ea - 0.3) &&
                            (ea = ea + 0.3)
                        this.g.beginPath()
                        this.g.strokeStyle = this.previousColor
                        this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sa, ea, false)
                        this.g.stroke()
                    }

                    this.g.beginPath()
                    this.g.strokeStyle = r ? this.o.fgColor : this.fgColor
                    this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sat, eat, false)
                    this.g.stroke()

                    this.g.lineWidth = 2
                    this.g.beginPath()
                    this.g.strokeStyle = this.o.fgColor
                    this.g.arc(this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * Math.PI, false)
                    this.g.stroke()

                    return false
                }
            }
        })
        /* END JQUERY KNOB */

        //INITIALIZE SPARKLINE CHARTS
        var sparkline1 = new Sparkline($('#sparkline-1')[0], {
            width: 240,
            height: 70,
            lineColor: '#92c1dc',
            endColor: '#92c1dc'
        })
        var sparkline2 = new Sparkline($('#sparkline-2')[0], {
            width: 240,
            height: 70,
            lineColor: '#f56954',
            endColor: '#f56954'
        })
        var sparkline3 = new Sparkline($('#sparkline-3')[0], {
            width: 240,
            height: 70,
            lineColor: '#3af221',
            endColor: '#3af221'
        })

        sparkline1.draw([1000, 1200, 920, 927, 931, 1027, 819, 930, 1021])
        sparkline2.draw([515, 519, 520, 522, 652, 810, 370, 627, 319, 630, 921])
        sparkline3.draw([15, 19, 20, 22, 33, 27, 31, 27, 19, 30, 21])

    })
</script>

<style>
    .aktif_sorot:hover {
        background-color: var(--bs-secondary);
        color: aliceblue;
    }
</style>

<!-- 
    Warning: Undefined variable $jml_A in C:\xampp\htdocs\siapps\beranda\views\_statistik.php on line 318

 -->
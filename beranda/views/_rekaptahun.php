<?php
include('views/header.php');

?>
<link rel="stylesheet" href="css/tabeltahun.css">

<section class="content">
    <div class="container-fluid">
        <div class="card bg-primary bg-gradient-primary elevation-2">
            <div id="header_rekap" class="card-body">
                <div style="display: flex; justify-content: baseline; justify-content: space-between;">
                    <a class="nav-link bg-light bg-gradient-light elevation-2" style="border-radius: 5px;" href="rekaptahun.php?tahun=<?= $tahun_pilih_min; ?>"><i class="fas fa-angle-double-left"></i>
                        <span>Sebelumnya</span>
                    </a>
                    <h4 class="mt-2"><b><?= $tahun_pilih; ?></h4>
                    <a class="nav-link elevation-2 bg-bg-gradient-light bg-light<?= $disable; ?>" style="border-radius: 5px;" href="<?= $tahun_pilih_plus; ?>">
                        <span>Berikutnya</span>
                        <i class="fas fa-angle-double-right"></i></a>
                </div>
            </div>
        </div>

        <?php
        $sql = "SELECT * FROM datapresensi WHERE nokartu = '$nokartu_login'";
        $query = mysqli_query($konek, $sql);
        $data[] = array();
        while ($dataq = mysqli_fetch_array($query)) {
            $data[] = $dataq;
        }

        // printf('<pre>%s</pre>', var_export($data, true));

        /*
array (
  0 => 
  array (
  ),
  1 => 
  array (
    0 => '723',
    'id' => '723',
    1 => '1347124961249278935253',
    'nokartu' => '1347124961249278935253',
    2 => 'Kiandra Purnawati',
    'nama' => 'Kiandra Purnawati',
    3 => 'X TAV 1',
    'info' => 'X TAV 1',
    4 => 'sarah.pratiwi__cleopatra564.jpg',
    'foto' => 'sarah.pratiwi__cleopatra564.jpg',
    5 => '11:25:00',
    'waktumasuk' => '11:25:00',
    6 => 'TLT',
    'ketmasuk' => 'TLT',
    7 => '04:25:00',
    'a_time' => '04:25:00',
    8 => NULL,
    'waktupulang' => NULL,
    9 => NULL,
    'ketpulang' => NULL,
    10 => NULL,
    'b_time' => NULL,
    11 => '2022-03-03',
    'tanggal' => '2022-03-03',
    12 => 'PJJ',
    'keterangan' => 'PJJ',
    13 => '2022-03-03 17:19:30',
    'updated_at' => '2022-03-03 17:19:30',
    14 => 'XTAV1',
    'kode' => 'XTAV1',
  ),
  2 => 
  array (
    0 => '756',
    'id' => '756',
    1 => '1347124961249278935253',
    'nokartu' => '1347124961249278935253',
    2 => 'Kiandra Purnawati',
    'nama' => 'Kiandra Purnawati',
    3 => 'X TAV 1',
    'info' => 'X TAV 1',
    4 => 'sarah.pratiwi__cleopatra564.jpg',
    'foto' => 'sarah.pratiwi__cleopatra564.jpg',
    5 => NULL,
    'waktumasuk' => NULL,
    6 => NULL,
    'ketmasuk' => NULL,
    7 => NULL,
    'a_time' => NULL,
    8 => NULL,
    'waktupulang' => NULL,
    9 => NULL,
    'ketpulang' => NULL,
    10 => NULL,
    'b_time' => NULL,
    11 => '2022-03-14',
    'tanggal' => '2022-03-14',
    12 => 'Ijin',
    'keterangan' => 'Ijin',
    13 => '2022-03-13 10:14:57',
    'updated_at' => '2022-03-13 10:14:57',
    14 => 'XTAV1',
    'kode' => 'XTAV1',
  ),
  3 => 
  array (
    0 => '764',
    'id' => '764',
    1 => '1347124961249278935253',
    'nokartu' => '1347124961249278935253',
    2 => 'Kiandra Purnawati',
    'nama' => 'Kiandra Purnawati',
    3 => 'X TAV 1',
    'info' => 'X TAV 1',
    4 => 'sarah.pratiwi__cleopatra564.jpg',
    'foto' => 'sarah.pratiwi__cleopatra564.jpg',
    5 => '07:34:34',
    'waktumasuk' => '07:34:34',
    6 => 'TLT',
    'ketmasuk' => 'TLT',
    7 => '00:34:34',
    'a_time' => '00:34:34',
    8 => NULL,
    'waktupulang' => NULL,
    9 => NULL,
    'ketpulang' => NULL,
    10 => NULL,
    'b_time' => NULL,
    11 => '2022-03-15',
    'tanggal' => '2022-03-15',
    12 => '',
    'keterangan' => '',
    13 => '2022-03-15 07:34:34',
    'updated_at' => '2022-03-15 07:34:34',
    14 => 'XTAV1',
    'kode' => 'XTAV1',
  ),
)
*/


        // print_r('<br>');
        // print_r('<br>');
        // print_r('// hasil fungsi cari data array : ');
        // $hasilnya = caridiarrray($query, $data, '2022-03-03');
        // print_r($hasilnya);
        // printf('<pre>%s</pre>', var_export($hasilnya, true));
        // print_r('<br>');
        // print_r('// ambil dong');

        // $keteranga_masuk = $hasilnya['ketmasuk'];
        // print_r('<br>');
        // print_r('ketersangka masuk : ' . $keteranga_masuk);
        // // $a_time = $hasilnya['a_time'];
        // print_r('<br>');
        // // print_r('a_time masuk : ' . $a_time);
        // $keteranga_masuk = $hasilnya['ketmasuk'];
        // print_r('<br>');
        // print_r('ketersangka masuk : ' . $keteranga_masuk);
        // // $a_time = $hasilnya['a_time'];
        // print_r('<br>');
        // // print_r('a_time masuk : ' . $a_time);

        // die;

        $q = mysqli_query($konek, "SELECT info FROM statusnya");
        $status = mysqli_fetch_assoc($q);
        $harikerja = $status['info'];

        ?>

        <div class="card elevation-2 mb-5">
            <div class="card-header bg-gradient-primary">
                <h3 class="card-title">Data Pertahun <?= $tahun_pilih; ?></h3>
                <div class="card-tools">
                    <i class="fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Menampilkan Catatan Presensi selama satu tahun"></i>
                    <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                    <!-- <button type="button" class="btn btn-tool" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button> -->
                </div>

            </div>

            <style>
                #demoCW {
                    width: 100%;
                    height: 500px;
                    overflow: auto;
                }

                #demoCT #thead_bulan #thead_namabulan {
                    position: sticky;
                    top: 0;
                    z-index: 2;
                    background: #abcdef;
                }

                #demoCT tbody th {
                    position: sticky;
                    left: 0;
                    z-index: 1;
                    background: #eeefff;
                }

                #demoCT #thead_bulan #thead_tgl {
                    position: sticky;
                    left: 0;
                    top: 0;
                    z-index: 3;
                    background: #0abcde;
                }
            </style>
            <!-- /.card-header -->
            <div class="card-body">
                <div class="">
                    <div id="demoCW" class="table table-responsive">
                        <table id="demoCT" class="table table-bordered elevation-1">
                            <!-- <thead>
                                <th rowspan="2" style="text-align: center;">#</th>
                                <th colspan="12" style="text-align: center;">Bulan</th>
                            </thead> -->
                            <thead>
                                <tr id="thead_bulan" style="height: 100px;">
                                    <th id="thead_tgl" style="width: 20px;">Tgl</th>
                                    <?php
                                    $nu = 0;
                                    while ($nu < 12) {
                                        $nu++;
                                        $bulan_indo_a = namasingkat_bulan_indo_dariangka($nu);
                                    ?>
                                        <th id="thead_namabulan" style="vertical-align: middle; text-align: center;"><?= $bulan_indo_a; ?></th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $ni = 0;
                                while ($ni <= 30) {
                                    $ni++;
                                ?>
                                    <tr style="text-align: center;">
                                        <th><?= $ni; ?></th>
                                        <?php
                                        $na = 0;
                                        while ($na < 12) {
                                            $na++;
                                            include('../config/konesi.php');
                                            $tanggal_pilih = $tahun_pilih . '-' . sprintf('%02d', $na) . '-' . sprintf('%02d', $ni);

                                            $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $na, $tahun_pilih);

                                            if ($jumlahHari >= $ni) {

                                                $hasilnya = caridiarrray($query, $data, $tanggal_pilih);

                                                $tgl_Ymd = date('Ymd', strtotime($tanggal_pilih));

                                                $deskripsi = tanggalMerah($tgl_Ymd);
                                                // parsing ke array deskripsi
                                                $deskripsi_ar = explode(': ', $deskripsi);
                                                $deskripsi_array = explode(' ', @$deskripsi_ar[1]);
                                                // ambil data array deskripsi
                                                $deskripsi_array_1 = @$deskripsi_array[1] . ' ' . @$deskripsi_array[2];


                                                if (
                                                    $harikerja == '5'
                                                ) {
                                                    $harilibur = limaharikerja($tanggal_pilih);
                                                } elseif ($harikerja == '6') {
                                                    $harilibur = enamharikerja($tanggal_pilih);
                                                }

                                                if ($harilibur == false && !$deskripsi) {

                                                    $keterangan_masuk = @$hasilnya['ketmasuk'] ? $hasilnya['ketmasuk'] : '-';
                                                    $keterangan_pulang = @$hasilnya['ketpulang'] ? $hasilnya['ketpulang'] : '-';
                                                    $_a_time = @$hasilnya['a_time'] ? $hasilnya['a_time'] : '-';
                                                    $_b_time = @$hasilnya['b_time'] ? $hasilnya['b_time'] : '-';

                                                    if ($keterangan_masuk == 'MSK') {
                                                        $keterangan_masuk = $_a_time;
                                                        $bg_alert_masuk = 'success';
                                                    } elseif ($keterangan_masuk == 'TLT') {
                                                        $keterangan_masuk = $_a_time;
                                                        $bg_alert_masuk = 'warning';
                                                    } elseif ($keterangan_masuk != '-') {
                                                        $bg_alert_masuk = 'info';
                                                    } else {
                                                        $bg_alert_masuk = 'style="display: none;';
                                                    }

                                                    if ($keterangan_pulang == 'PLG') {
                                                        $keterangan_pulang = $_b_time;
                                                        $bg_alert_pulang = 'success';
                                                    } elseif ($keterangan_pulang == "PA") {
                                                        $keterangan_pulang = $_b_time;
                                                        $bg_alert_pulang = 'warning';
                                                    } elseif ($keterangan_pulang == '' || $keterangan_pulang == '-') {
                                                        $bg_alert_pulang = 'style="display: none;';
                                                    } else {
                                                        $bg_alert_pulang = 'info';
                                                    }
                                                } else {
                                                    if ($harilibur == true) {
                                                        $deskripsi_array_1 = 'weekend';
                                                        $bg_alert_pulang = 'secondary';
                                                    } else if ($deskripsi_array_1) {
                                                        $bg_alert_pulang = 'dark';
                                                    } else {
                                                        $bg_alert_pulang = 'light';
                                                    }

                                                    $keterangan_masuk = 'Libur';
                                                    $keterangan_pulang = $deskripsi_array_1;
                                                    $bg_alert_masuk = 'danger';
                                                }

                                                $bg_kolom = '';
                                            } else {
                                                $bg_kolom = 'class="bg-secondary progress-bar-striped"';
                                                $keterangan_masuk = '';
                                                $keterangan_pulang = '';
                                                $bg_alert_masuk = 'light" style="display: none;';
                                                $bg_alert_pulang = 'light" style="display: none;';
                                            }
                                        ?>
                                            <td <?= $bg_kolom; ?>>
                                                <div class="badge badge-<?= $bg_alert_masuk; ?>" style="display: flex; flex-direction: column; margin-bottom: 2px;">
                                                    <?= $keterangan_masuk; ?>
                                                </div>
                                                <div class="badge badge-<?= $bg_alert_pulang; ?>" style="display: flex; flex-direction: column; margin-bottom: 2px;">
                                                    <?= $keterangan_pulang; ?>
                                                </div>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div id="footer_print" class="btn-group mb-3">
                    <button class="btn btn-dark bg-gradient-dark elevation-2" style="border: none;">
                        <i class="fas fa-print">&nbsp;</i>
                        <span>
                            Print
                        </span>
                    </button>
                    <button class="btn btn-danger bg-gradient-danger elevation-2" style="border: none;">
                        <i class="fas fa-file-pdf">&nbsp;</i>
                        <span>
                            Export PDF
                        </span>
                    </button>
                    <button class="btn btn-success bg-gradient-success elevation-2" style="border: none;">
                        <i class="fas fa-file-excel">&nbsp;</i>
                        <span>
                            Export Excel
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('views/footer.php'); ?>

<?php
// mencari data di array
function caridiarrray($q, $array, $yangdicari)
{
    $result = array();
    $hitung = mysqli_num_rows($q);
    $hit = 0;
    while ($hit <= $hitung) {
        $cari_data = array_search($yangdicari, $array[$hit]);
        if ($cari_data == '11') {
            $ketmasuk = $array[$hit][6];
            $a_time = $array[$hit][7];
            $ketpulang = $array[$hit][9];
            $b_time = $array[$hit][10];
            $ket = @$array[$hit][12];

            if ($ketpulang == '') {
                $ketpulang = @$array[$hit][12];
                $ket = $ketpulang;
            }

            if ($ketmasuk == '') {
                $ketmasuk = @$array[$hit][12];
                $ket = $ketmasuk;
            }
            // echo "Array ke: " . $hit . " ada datanya<br>";
            $result = array(
                'ketmasuk' => $ketmasuk,
                'a_time' => $a_time,
                'ketpulang' => $ketpulang,
                'b_time' => $b_time,
                'ket' => $ket,
            );
        } else {
            // if (@$array[$hit][12]) {
            //     $ijin = $array[$hit][12];

            //     $result = array(
            //         'ketmasuk' => $ijin,
            //         'ketpulang' => $ijin,
            //     );
            // }
        }
        // else {
        //     echo "Array ke: " . $hit . " tidak ada datanya<br>";
        // }
        $hit++;
    }

    return $result;
}
?>
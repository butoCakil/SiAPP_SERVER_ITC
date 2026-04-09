<?php

// hitung jumlah data dari nama Prabu Prabu Prasasta
function hitung_presensi_per_data($nokartu___, $hasil_datapresensi___, $tanggal___)
{
    $hitung_presensi_per_data = 0;
    foreach ($hasil_datapresensi___ as $datapresensi_) {
        if (($datapresensi_['nokartu'] == $nokartu___) && (date('mY', strtotime($datapresensi_['tanggal'])) == date('mY', strtotime($tanggal___)))) {
            $hitung_presensi_per_data++;
        }
    }
    return $hitung_presensi_per_data;
}

// $nokartu_data = $hasil_data[33]['nokartu'];
// $nama_ = $hasil_data[33]['nama'];
// $tanggal_ = date('Y-m-d', strtotime('2022-02-01'));

// $hit_per_data_2 = hitung_presensi_per_data($nokartu_data, $hasil_datapresensi, $tanggal_);

// print_r('no kartu : ' . $nokartu_data . '<br>');
// print_r('nama : ' . $nama_ . '<br>');
// print_r('<br>');
// print_r('tanggal_ : ' . $tanggal_ . '<br>');
// print_r('<br>');
// print_r('hit data nama : ' . $hit_per_data_2 . '<br>');
// $bul = 0;
// $hari_masuk = array();
// $hari_libur = array();
// while ($bul < 12) {
//     $bul++;
//     $hari_masuk[$bul] = 0;
//     $hari_libur[$bul] = 0;
//     $namaBulan_dariAngka = namasingkat_bulan_indo_dariangka($bul);
//     $jumlah_hari = jmlhari_($bul, $tahun_pilih);
//     $nn = '';
//     while ($nn < $jumlah_hari) {
//         $nn++;
//         $tanggal_pilih_1 = $tahun_pilih . '-' . $bul . '-' . $nn;
//         $tgl_Ymd = date('Ymd', strtotime($tanggal_pilih_1));
//         $liburnas = tanggalMerah($tgl_Ymd);
//         if ($liburnas == '') {
//             $hari_libur_an = limaharikerja($tanggal_pilih_1);
//             if ($hari_libur_an == false) {
//                 $hari_masuk[$bul]++;
//             } else {
//                 $hari_libur[$bul]++;
//             }
//         } else {
//             $hari_libur_an = true;
//             $hari_libur[$bul]++;
//         }
//     }
// }

// print_r('namaBulan_dariAngka: ');
// print_r($namaBulan_dariAngka);
// print_r('<br>');
// print_r('jumlah_hari: ');
// print_r($jumlah_hari);
// print_r('<br>');
// print_r('<br>');
// print_r('tanggal_pilih_1: ');
// print_r($tanggal_pilih_1);
// print_r('<br>');
// print_r('<br>');
// print_r('hari_masuk: ');
// printf('<pre>%s</pre>', print_r($hari_masuk, true));
// print_r('<br>');
// print_r('<br>');
// print_r('$hari_libur: ');
// printf('<pre>%s</pre>', print_r($hari_libur, true));
// print_r('<br>');
// print_r('$hasil_data: ');
// printf('<pre>%s</pre>', print_r($hasil_data, true));
// print_r('<br>');
// print_r('$hasil_datapresensi: ');
// printf('<pre>%s</pre>', print_r($hasil_datapresensi, true));

// die;

?>

<?php include('views/header.php'); ?>
<?php
// echo 'ket akses login: ' . $ket_akses_login;
?>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div id="header_rekap" class="card bg-primary bg-gradient-primary elevation-3" style="border: none;">
                    <!-- /.card-header -->
                    <div class="card-body ">
                        <div style="display: flex; justify-content: baseline; justify-content: space-between;">
                            <div>
                                <a class="nav-link bg-light elevation-3" style="border-radius: 5px;" href="<?= $link_datab; ?><?= $tanggal_pilih_min; ?>">
                                    <div style="display: flex; gap: 10px;">
                                        <i class="fas fa-angle-double-left"></i>
                                        <span>Sebelumnya</span>
                                    </div>
                                </a>
                            </div>
                            <div style="display: flex; flex-direction: column; text-align: center;">
                                <div>
                                    <h4 class="mt-2"><b><?= $tahun_pilih; ?></h4>
                                </div>
                            </div>
                            <div>
                                <a class="nav-link bg-light elevation-3 btn btn-secondary <?= $disabled_plus; ?>" style="border-radius: 5px;" href="<?= $tanggal_pilih_plus; ?>">
                                    <div style="display: flex; gap: 10px;">
                                        <span>Berikutnya</span>
                                        <i class="fas fa-angle-double-right"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12">
                <div id="daftarhadirgtk_001" class="card elevation-3">
                    <div class="card-header bg-primary bg-gradient-primary">
                        <div class="card-tools">
                            <i class="fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Menampilkan Catatan Presensi selama satu Bulan"></i>

                            <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            <?= $title; ?>
                        </h3>

                    </div>

                    <style>
                        /* (A) TABLE WRAPPER */
                        #demoCW {
                            width: 100%;
                            height: 500px;
                            overflow: auto;
                        }

                        /* (B) STICKY HEADERS */
                        #demoCT #thead th,
                        #demoCT tbody th {
                            position: sticky;
                            left: 0;
                            z-index: 1;
                        }

                        #demoCT #thead {
                            position: sticky;
                            top: 0;
                            z-index: 2;
                        }

                        #demoCT #thead #thnama {
                            position: sticky;
                            top: 0;
                            z-index: 3;
                        }

                        #demoCT #thead th {
                            background: #abcdef
                        }

                        #demoCT tr th {
                            background: #eeefff;
                        }

                        #demoCT tbody #pp {
                            height: 40px;
                            width: 40px;
                            border-radius: 50%;
                            object-fit: cover;
                            object-position: top;
                            box-shadow: 1px 1px 3px #000000;
                            display: block;
                            margin: auto;
                        }
                    </style>

                    <div class="card-body">
                        <div id="demoCW" class="table table-responsive">
                            <table id="demoCT" class="table table-bordered">
                                <!-- <thead>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td colspan="12" style="text-align: center;">Bulan</td>
                                </thead> -->
                                <tr id="thead" style="text-align: center;">
                                    <th>No.</th>
                                    <th>Foto</td>
                                    <th id="tdnama">Nama</th>
                                    <?php
                                    $bul = 0;
                                    $hari_masuk = array();
                                    $hari_libur = array();
                                    while ($bul < 12) {
                                        $bul++;
                                        $hari_masuk[$bul] = 0;
                                        $hari_libur[$bul] = 0;

                                        $namaBulan_dariAngka = namasingkat_bulan_indo_dariangka($bul);

                                        $jumlah_hari = jmlhari_($bul, $tahun_pilih);

                                        // menghitung jumlah hari masuk
                                        $nn = 0;
                                        while ($nn < $jumlah_hari) {
                                            $nn++;
                                            $tanggal_pilih_1 = $tahun_pilih . '-' . $bul . '-' . $nn;
                                            $tgl_Ymd = date('Ymd', strtotime($tanggal_pilih_1));

                                            $liburnas = tanggalMerah($tgl_Ymd);

                                            if ($liburnas == '') {
                                                $hari_libur_an = limaharikerja($tanggal_pilih_1);

                                                if ($hari_libur_an == false) {
                                                    $hari_masuk[$bul]++;
                                                } else {
                                                    $hari_libur[$bul]++;
                                                }
                                            } else {
                                                $hari_libur_an = true;
                                                $hari_libur[$bul]++;
                                            }
                                        }
                                    ?>
                                        <th><?= $namaBulan_dariAngka; ?></th>
                                    <?php } ?>
                                </tr>
                                <?php
                                $no = 1;
                                while ($no < $hit_data) {
                                    $foto_data = (@$hasil_data[$no]['foto']) ? $hasil_data[$no]['foto'] : 'default.jpg';
                                    $nokartu_data = $hasil_data[$no]['nokartu'];
                                    // $hit_per_data = hitung_presensi_per_data($nokartu_data, $hasil_datapresensi);
                                ?>
                                    <tbody>
                                        <th style="text-align: center; vertical-align: middle;"><?= $no; ?></th>
                                        <th style="width: fit-content;"><img id="pp" src="../img/user/<?= $foto_data; ?>"></th>
                                        <th style="max-width: 110px; vertical-align: middle;"><?= $hasil_data[$no]['nama']; ?></th>
                                        <?php $no++; ?>

                                        <?php
                                        $status_perbulan = 0;
                                        while ($status_perbulan < 12) {
                                            $status_perbulan++;

                                            $tanggal_ = $tahun_pilih . '-' . sprintf('%02d', $status_perbulan) . '-01';
                                            $hit_per_data = hitung_presensi_per_data($nokartu_data, $hasil_datapresensi, $tanggal_);

                                            $jumlah_hari_masuk = $hari_masuk[$status_perbulan];

                                            $persentase = (sprintf('%02d', (100 * ($hit_per_data) / $jumlah_hari_masuk)));
                                            if ($persentase < 80 && $persentase >= 70) {
                                                $class_bg = 'badge-warning';
                                            } elseif ($persentase == 0) {
                                                $class_bg = 'badge-secondary';
                                            } elseif ($persentase > 0) {
                                                $class_bg = 'badge-danger';
                                            } else {
                                                $class_bg = 'badge-success';
                                            }
                                        ?>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <span class="badge <?= $class_bg; ?>"><?= $persentase; ?> %</span>
                                            </td>
                                        <?php } ?>
                                    <?php } ?>
                                    </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="footer_print" class="card-footer">
                        <div class="btn-group">
                            <button class="btn btn-dark bg-gradient-dark elevation-2" style="border: none;">
                                <i class="fas fa-print"></i>&nbsp;
                                <span>
                                    Print
                                </span>
                            </button>
                            <button class="btn btn-danger bg-gradient-danger elevation-2" style="border: none;">
                                <i class="fas fa-file-pdf"></i>&nbsp;
                                <span>
                                    PDF
                                </span>
                            </button>
                            <button class="btn btn-success bg-gradient-success elevation-2" style="border: none;">
                                <i class="fas fa-file-excel"></i>&nbsp;
                                <span>
                                    Excel
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
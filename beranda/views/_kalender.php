<?php
date_default_timezone_set('Asia/Jakarta');

if (@$_GET['kalender_']) {
    $tanggal_pilih = $_GET['kalender_'];
} else {
    if (@$_GET['kalender']) {
        // set waktu sesuai lokasi
        // jika bukan format tanggal (tahun[4]-bulan[2])
        if (!preg_match('/^[0-9]{4}-[0-9]{2}$/', $_GET['kalender'])) {
            $tanggal_pilih = date('Y-m-d');
        } else {
            $tanggal_pilih = $_GET['kalender'];
        }
    } else {
        $tanggal_pilih = date('Y-m-d');
        // $tanggal_pilih = date('Y-m-d', strtotime('2022-04-01'));
    }
}

$bulan_pilih = date('m', strtotime($tanggal_pilih));
$tahun_pilih = date('Y', strtotime($tanggal_pilih));
$bulan_indo_pilih = bulanIndo(date('F', strtotime($tanggal_pilih)));
$tahun_bulan_pilih = date('Y-m', strtotime($tanggal_pilih));

$date = hdate($tanggal_pilih);

// kalkulasi info kalender
$bulan = date('m');
$tahun = date('Y');

$sql = 'SELECT * FROM datapresensi WHERE MONTH(tanggal) = ' . $bulan . ' AND YEAR(tanggal) = ' . $tahun . ' AND nokartu = "' . $nokartu_login . '" ORDER BY tanggal ASC';
$query = mysqli_query($konek, $sql);
$hasil_data_status = mysqli_fetch_all($query, MYSQLI_ASSOC);

// cari keterangan pada tanggal datapresensi
function cari_keterangan($datacari___, $tgl___, $hasil___)
{
    $keterangan = '';
    foreach ($hasil___ as $data_status) {
        if ($tgl___ == $data_status['tanggal']) {
            $keterangan = $data_status[$datacari___];
        }
    }
    return $keterangan;
}

// cek apakah data presensi pada tanggal ini ada atau tidak
function cek_data_presensi($tgl___, $hasil___)
{
    $ada = false;
    foreach ($hasil___ as $data_status) {
        if ($tgl___ == $data_status['tanggal']) {
            $ada = true;
        }
    }
    return $ada;
}

// $keterangan_masuk = cari_keterangan('ketmasuk', '2022-04-04', $hasil_data_status);
// $keterangan_pulang = cari_keterangan('ketpulang', '2022-04-04', $hasil_data_status);
// $keterangan = cari_keterangan('keterangan', '2022-04-04', $hasil_data_status);
// $waktumasuk = cari_keterangan('waktumasuk', '2022-04-04', $hasil_data_status);
// $waktupulang = cari_keterangan('waktupulang', '2022-04-04', $hasil_data_status);
// $a_time = cari_keterangan('a_time', '2022-04-04', $hasil_data_status);
// $b_time = cari_keterangan('b_time', '2022-04-04', $hasil_data_status);

// print_r('2022-04-04 <br>');
// print_r('ketmasuk: ' . $keterangan_masuk);
// print_r('<br>');
// print_r('ketpulang: ' . $keterangan_pulang);
// print_r('<br>');
// print_r('keterangan: ' . $keterangan);
// print_r('<br>');
// print_r('waktumasuk: ' . $waktumasuk);
// print_r('<br>');
// print_r('waktupulang: ' . $waktupulang);
// print_r('<br>');
// print_r('a_time: ' . $a_time);
// print_r('<br>');
// print_r('b_time: ' . $b_time);
// print_r('<br>');
// print_r('<br>----------------------------------------------------------');
// print_r('<br>');

// $tanggal = $hasil_data_status[0]['tanggal'];
// $status_datapresensi_masuk = $hasil_data_status[0]['keterangan'];

// print_r('tanggal : ' . $tanggal);
// print_r('<br>');
// print_r('status_datapresensi : ' . $status_datapresensi_masuk);
// print_r('<br>');

// // print_r($hasil_data_status);
// print_r('<br>');
// printf('Status presensi: ' . $data_user["nama"] . '<br><pre>%s</pre>', print_r($hasil_data_status, true));

// die;

?>

<style>
    #kalender0000 {
        display: flex;
        flex-wrap: wrap;
        justify-content: stretch;
        align-items: stretch;
        margin-left: 20px;
        margin-bottom: 20px;
    }

    .tgl_kalender0000 {
        width: 12%;
        height: 75px;
        text-align: end;
        /* border: 1px solid #ccc; */
        border-radius: 5px;
        margin: 3px;
    }

    .tgl_kalender0000 h3 {
        margin: 5px;
        padding: 5px;
        font-size: 1.2em;
    }

    #hari_kal {
        display: flex;
        justify-content: space-around;
        margin-top: 10px;
        font-size: 18px;
        font-weight: 600;
        margin-left: 20px;
    }

    #namahari_tanggalan {
        text-align: center;
        color: dimgray;
    }

    #keterangantgl {
        font-size: 10px;
    }

    #box_kalender {
        padding: 5px;
        margin: 0;
    }

    #namahari_tanggalan th {
        font-size: 0.8em;
    }

    #namahari_tanggalan th,
    #namahari_tanggalan td {
        max-width: 40px;
    }

    #tanggalanya {
        font-size: 1em;
    }

    #tglarabic {
        font-size: 0.5em;
        font-weight: 300;
        position: absolute;
        margin-top: -10px;
    }

    #tutup_detail_tanggal {
        position: absolute;
        top: 10;
        right: 0;
        margin-right: 10px;
        padding: 7px 10px 0px 10px;
        cursor: pointer;
        text-decoration: none;
        border: none;
    }

    @media screen and (max-width: 1557px) {
        #namahari_tanggalan th {
            font-size: 0.6em;
            text-align: center;
            font-weight: 600;
        }

        #tanggalnya {
            font-size: 0.7em;
            text-align: center;
            font-weight: 600;
            padding: 1px;
            /* border-radius: 10px; */
        }
    }

    @media screen and (max-width: 1384px) {
        #tanggalnya {
            font-size: 0.5em;
            text-align: center;
            font-weight: 400;
            padding: 1px;
            /* border-radius: 10px; */
        }
    }

    @media only screen and (max-width: 991px) {

        #namahari_tanggalan th {
            font-size: 0.6em;
            text-align: center;
            font-weight: 600;
        }

        #tanggalan01 {
            height: fit-content;
            width: 10px;
        }

        #tanggalnya {
            font-size: 0.7em;
            text-align: center;
            font-weight: 600;
            padding: 1px;
            /* border-radius: 10px; */
        }

        #keterangantgl {
            font-size: 0.4em;
        }
    }

    @media only screen and (max-width:488px) {
        .collapse {
            width: 100%;
        }
    }

    @media only screen and (max-width: 399px) {
        #tanggalnya {
            font-size: 0.5em;
            text-align: center;
            font-weight: 600;
            padding: 1px;
            /* border-radius: 10px; */
        }

        .collapse {
            font-size: 0.8em;
        }

        .collapse h5 {
            font-size: 1.4em;
        }

        .collapse h6 {
            font-size: 1.2em;
        }

        .collapse a {
            font-size: 0.8em;
            margin-top: -12px;
        }
    }
</style>

<?php



?>

<!-- 
<div class="card card-primary card-outline">
    <div class="card-body box-profile">
        <div style="display: flex; justify-content: space-between;">
            <div>
                <div class="btn-group">
                    <a type="button" class="btn btn-primary" onclick="kalender_bulan_minus()">
                        <i class="fas fa-chevron-circle-left"></i>
                    </a>
                </div>
            </div>
            <div style="text-align: center;">
                <h1><?= $bulan_indo_pilih; ?> <?= $tahun_pilih; ?></h1>
            </div>
            <div>
                <div class="btn-group">
                    <a type="button" class="btn btn-primary" onclick="kalender_tahun_plus()">
                        <i class="fas fa-chevron-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <table>
        <tr id="hari_kal">
            <td>Senin</td>
            <td>Selasa</td>
            <td>Rabu</td>
            <td>Kamis</td>
            <td>Jum'at</td>
            <td>Sabtu</td>
            <td>Minggu</td>
        </tr>
        <?php
        $tahun_bulan_pilih = date('Y-m', strtotime($tanggal_pilih));
        $bulan_pilih = date('m', strtotime($tanggal_pilih));
        $tahun_pilih = date('Y', strtotime($tanggal_pilih));
        $tanggal_pilih_plus = date('Y-m-d', strtotime('+1 month', strtotime($tanggal_pilih)));
        $tanggal_pilih_minus = date('Y-m-d', strtotime('-1 month', strtotime($tanggal_pilih)));

        $bulan_min = date('m', strtotime($tanggal_pilih_minus));
        $tahun_min = date('Y', strtotime($tanggal_pilih_minus));

        $tanggal_hari_minggu_terakhir = tanggal_hariminggu_terakhir($tahun_min, $bulan_min);

        if (@$tanggal_hari_minggu_terakhir[5]) {
            $tanggal_hariminggu_terakhir = $tanggal_hari_minggu_terakhir[5];
        } else {
            $tanggal_hariminggu_terakhir = $tanggal_hari_minggu_terakhir[4];
        }

        $jumlah_hari_bulan_ini = cal_days_in_month(CAL_GREGORIAN, $bulan_pilih, $tahun_pilih);

        $bulan_min_1 = $bulan_pilih - 1;
        if ($bulan_min_1 == 0) {
            $bulan_min_1 = 12;
            $tahun_pilih_1 = $tahun_pilih - 1;
        } else if ($bulan_min_1 < 10) {
            $bulan_min_1 = '0' . $bulan_min_1;
            $tahun_pilih_1 = $tahun_pilih;
        } else {
            $tahun_pilih_1 = $tahun_pilih;
        }

        $jumlah_hari_bulan_sebelumnya = cal_days_in_month(CAL_GREGORIAN, $bulan_min_1, $tahun_pilih_1);
        $tgl_kal = $tanggal_hariminggu_terakhir;
        $tgl_aktif = false;
        $bulan_kal = $bulan_min;
        ?>
        <tr id="kalender0000">
            <?php
            for ($i = 0; $i < 35; $i++) {
                $tgl_kal++;
                if ($tgl_kal > $jumlah_hari_bulan_sebelumnya) {
                    $tgl_kal = 1;
                    $jumlah_hari_bulan_sebelumnya = $jumlah_hari_bulan_ini;
                    $bulan_kal++;
                    if ($tgl_aktif == false) {
                        $tgl_aktif = true;
                    } else {
                        $tgl_aktif = false;
                    }
                }

                $tgl_Ymd = $tahun_pilih . '-' . $bulan_kal . '-' . sprintf('%02d', $tgl_kal);
                $tgl_YMD = date('Ymd', strtotime($tgl_Ymd));
                $liburNas = tanggalMerah($tgl_YMD);
                if ($liburNas != '') {
                    $bg_kal = ' bg-danger';
                    $pecah = explode(':', @$liburNas);
                    $pecah2 = explode(' ', @$pecah[1]);
                    $label_tgl = @$pecah2[1] . ' ' . @$pecah2[2] . ' ' . @$pecah2[3];
                } else {
                    $label_tgl = '';
                }

                if ((($i + 1) % 7 == 0) || (($i + 2) % 7 == 0)) {

                    if ($tgl_aktif == true) {
                        $bg_kal = ' bg-danger';
                        $txt_kal = 'style="font-weight: bold;"';
                    } else {
                        $bg_kal = '  bg-secondary';
                        $txt_kal = 'style="font-style: italic; font-size: 14px; text-align: left;"';
                    }
                } else {
                    if ($tgl_aktif == true && !$liburNas) {
                        $bg_kal = ' bg-light';
                        $txt_kal = 'style="font-weight: bold;"';
                    } else {
                        if ($liburNas) {
                            if ($tgl_aktif == true) {
                                $bg_kal = ' bg-danger';
                                $txt_kal = 'style="font-weight: bold;"';
                            } else {
                                $bg_kal = ' bg-secondary';
                                $txt_kal = 'style="font-style: italic; font-size: 14px; text-align: left;"';
                            }
                        } else {
                            $bg_kal = '  bg-secondary';
                            $txt_kal = 'style="font-style: italic; font-size: 14px; text-align: left;"';
                        }
                    }
                }

                if (($tahun_bulan_pilih . '-' . sprintf('%02d', $tgl_kal)) == date('Y-m-d')) {
                    $bg_kal = ' bg-info';
                    $label_tgl = 'Hari ini';
                    $txt_kal = 'style="font-weight: bolder; font-size: 24px; text-align: center;"';
                }
            ?>
                <td class="tgl_kalender0000<?= $bg_kal; ?>">
                    <h3 <?= $txt_kal; ?>><?= $tgl_kal; ?></h3>
                    <h6 style="margin: 0.2em; font-size: 0.7em;"><?= @$label_tgl; ?></h6>
                </td>
            <?php } ?>
        </tr>
    </table>
</div> -->


<div id="box_kalender" class="card-body">
    <div style="display: flex; justify-content: space-between;">
        <div>
            <div class="btn-group">
                <button type="button" class="btn btn-dark" onclick="kalender_goto('<?= $tanggal_pilih_minus; ?>')">
                    <i class="fas fa-chevron-circle-left"></i>
                </button>
            </div>
        </div>
        <div style="text-align: center; color: dimgray;">
            <h1><?= $bulan_indo_pilih; ?> <?= $tahun_pilih; ?></h1>
            <h6><?= $date['hijriday'] . ' ' . $date['hijrimonth'] . ' ' . $date['hijriyear']; ?></h6>
        </div>
        <div>
            <div class="btn-group">
                <button type="button" class="btn btn-dark" onclick="kalender_goto('<?= $tanggal_pilih_plus; ?>')">
                    <i class="fas fa-chevron-circle-right"></i>
                </button>
            </div>
        </div>
    </div>
    <div id="accordion" class="table-responsive">
        <table id="namahari_tanggalan" class="table table-bordered">
            <tr class="bg-secondary">
                <th>Sen</th>
                <th>Sel</th>
                <th>Rab</th>
                <th>Kam</th>
                <th class="bg-success">Jum</th>
                <th>Sab</th>
                <th class="bg-danger">Min</th>
            </tr>
            <?php
            $tahun_bulan_pilih = date('Y-m', strtotime($tanggal_pilih));
            $bulan_pilih = date('m', strtotime($tanggal_pilih));
            $tahun_pilih = date('Y', strtotime($tanggal_pilih));
            $tanggal_pilih_plus = date('Y-m-d', strtotime('+1 month', strtotime($tanggal_pilih)));
            $tanggal_pilih_minus = date('Y-m-d', strtotime('-1 month', strtotime($tanggal_pilih)));

            $bulan_min = date('m', strtotime($tanggal_pilih_minus));
            $tahun_min = date('Y', strtotime($tanggal_pilih_minus));

            $tanggal_hari_minggu_terakhir = tanggal_hariminggu_terakhir($tahun_min, $bulan_min);

            if (@$tanggal_hari_minggu_terakhir[5]) {
                $tanggal_hariminggu_terakhir = $tanggal_hari_minggu_terakhir[5];
            } else {
                $tanggal_hariminggu_terakhir = $tanggal_hari_minggu_terakhir[4];
            }

            $jumlah_hari_bulan_ini = cal_days_in_month(CAL_GREGORIAN, $bulan_pilih, $tahun_pilih);

            $bulan_min_1 = $bulan_pilih - 1;
            if ($bulan_min_1 == 0) {
                $bulan_min_1 = 12;
                $tahun_pilih_1 = $tahun_pilih - 1;
            } else if ($bulan_min_1 < 10) {
                $bulan_min_1 = '0' . $bulan_min_1;
                $tahun_pilih_1 = $tahun_pilih;
            } else {
                $tahun_pilih_1 = $tahun_pilih;
            }

            $bulan_plus_1 = $bulan_pilih + 1;
            if ($bulan_plus_1 == 13) {
                $bulan_plus_1 = 1;
                $tahun_pilih_2 = $tahun_pilih + 1;
            } else if ($bulan_plus_1 < 10) {
                $bulan_plus_1 = '0' . $bulan_plus_1;
                $tahun_pilih_2 = $tahun_pilih;
            } else {
                $tahun_pilih_2 = $tahun_pilih;
            }

            $jumlah_hari_bulan_sebelumnya = cal_days_in_month(CAL_GREGORIAN, $bulan_min_1, $tahun_pilih_1);
            $jumlah_hari_bulan_berikutnya = cal_days_in_month(CAL_GREGORIAN, $bulan_plus_1, $tahun_pilih_2);

            $tgl_kal_ = $tanggal_hariminggu_terakhir;

            if (($tanggal_hariminggu_terakhir) <= ($jumlah_hari_bulan_sebelumnya - 6)) {
                $jumlah_minggu_bulan_ini = 1;
            } else {
                $jumlah_minggu_bulan_ini = 0;
            }

            $tgl_aktif = false;
            $bulan_kal = $bulan_min;

            $mmgg = 0;
            while ($mmgg < (5 + $jumlah_minggu_bulan_ini)) {
                $mmgg++;

            ?>
                <tr>
                    <?php
                    $hhrr = 0;
                    $sss = '';
                    while ($hhrr < 7) {
                        $hhrr++;
                        $hit_kol = $hhrr + (7 * ($mmgg - 1));
                        $tgl_kal_++;

                        if ($tgl_kal_ > $jumlah_hari_bulan_sebelumnya) {
                            $sss = $sss . ', ' . '001';
                            if ($bulan_kal == $bulan_pilih) {
                                $sss = $sss . ', ' . '002';
                                if ($tgl_kal_ > $jumlah_hari_bulan_ini) {
                                    $sss = $sss . ', ' . '003';
                                    $tgl_kal_ = 1;
                                    $bulan_kal++;
                                    $jumlah_hari_bulan_sebelumnya = $jumlah_hari_bulan_ini;
                                } else {
                                    $sss = $sss . ', ' . '004';
                                    $tgl_kal_ = 1;
                                    $jumlah_hari_bulan_sebelumnya = $jumlah_hari_bulan_ini;
                                }
                            } else {

                                if ($tgl_kal_ <= $jumlah_hari_bulan_ini) {
                                    $sss = $sss . ', ' . '005';
                                    $tgl_kal_ = 1;
                                    $bulan_kal++;
                                    $jumlah_hari_bulan_sebelumnya = $jumlah_hari_bulan_ini;
                                } else {
                                    $sss = $sss . ', ' . '006';
                                    $tgl_kal_ = 1;
                                    $jumlah_hari_bulan_sebelumnya = $jumlah_hari_bulan_ini;
                                    $bulan_kal++;
                                }
                            }

                            if ($tgl_aktif == false) {
                                $tgl_aktif = true;
                            } else {
                                $tgl_aktif = false;
                            }
                        }

                        $label_tgl_2 = '';
                        $warna_tgl = 'text-secondary';
                        $outline_status1 = '';
                        $outline_status2 = '';
                        $outline_status3 = '';
                        $outline_status4 = '';

                        $bg_jdwalguru = 'danger';
                        $bg_jurnalguru = 'danger';

                        $bg_status1 = 'danger';
                        $bg_status2 = 'danger';
                        $detail_tanggal = '';
                        $alert_detail = ' alert-default-secondary';

                        $tgl_Ymd = $tahun_pilih . '-' . sprintf('%02d', $bulan_kal) . '-' . sprintf('%02d', $tgl_kal_);

                        $cek = cek_data_presensi($tgl_Ymd, $hasil_data_status);

                        if ($cek == true) {

                            $keterangan_masuk = cari_keterangan('ketmasuk', $tgl_Ymd, $hasil_data_status);
                            $keterangan_pulang = cari_keterangan('ketpulang', $tgl_Ymd, $hasil_data_status);
                            $keterangan = cari_keterangan('keterangan', $tgl_Ymd, $hasil_data_status);
                            $waktumasuk = cari_keterangan('waktumasuk', $tgl_Ymd, $hasil_data_status);
                            $waktupulang = cari_keterangan('waktupulang', $tgl_Ymd, $hasil_data_status);
                            $a_time = cari_keterangan('a_time', $tgl_Ymd, $hasil_data_status);
                            $b_time = cari_keterangan('b_time', $tgl_Ymd, $hasil_data_status);

                            if ($keterangan_masuk == 'MSK') {
                                $bg_status1 = 'success';
                            } else if ($keterangan_masuk == 'TLT') {
                                $bg_status1 = 'warning';
                            } else {
                                if ($keterangan) {
                                    $bg_status1 = 'primary';
                                } else {
                                    $bg_status1 = 'danger';
                                }
                            }

                            if ($keterangan_pulang == 'PLG') {
                                $bg_status2 = 'success';
                            } elseif ($keterangan_pulang == "PA") {
                                $bg_status2 = 'warning';
                            } else {
                                if ($keterangan) {
                                    $bg_status2 = 'primary';
                                } else {
                                    $bg_status2 = 'danger';
                                }
                            }

                            $detail_tanggal =  '
                            <div>
                                Waktu masuk : 
                                    <span class="badge badge-' . $bg_status1 . '">' . $waktumasuk . '</span><br>
                                Keterangan Masuk : 
                                    <span class="badge badge-' . $bg_status1 . '">' . $keterangan_masuk . '</span><br>
                                time : 
                                    <span class="badge badge-' . $bg_status1 . '">' . $a_time . '</span><br>
                                Waktu pulang : 
                                    <span class="badge badge-' . $bg_status2 . '">' . $waktupulang . '</span><br>
                                Keterangan Pulang : 
                                    <span class="badge badge-' . $bg_status2 . '">' . $keterangan_pulang . '</span><br>
                                time : 
                                    <span class="badge badge-' . $bg_status2 . '">' . $b_time . '</span><br>
                                Keterangan : 
                                    <span class="badge badge-info">' . $keterangan . '</span><br>
                            </div>';
                        }

                        $tgl_YMD = date('Ymd', strtotime($tgl_Ymd));
                        $liburNas = tanggalMerah($tgl_YMD);
                        if ($liburNas != '') {
                            $bg_kal_ = ' bg-danger bg-gradient-danger';
                            $warna_tgl = 'text-white';
                            $alert_detail = ' alert-default-danger';
                            $pecah = explode(':', @$liburNas);
                            $pecah2 = explode(' ', @$pecah[1]);
                            $label_tgl_ = @$pecah2[1] . ' ' . @$pecah2[2] . ' ' . @$pecah2[3];
                        } else {
                            $label_tgl_ = '';
                        }

                        // ambil infoo guru
                        $ambil_jdwal = cari_tanggal($tgl_Ymd, $data_jadwalguru);
                        $ambil_jurnal = cari_tanggal($tgl_Ymd, $data_jurnalguru);

                        if ($ambil_jdwal) {
                            $bg_jdwalguru = 'primary';
                            $ij = 0;
                            $_help_info = '<div>
                                    <i class="text-secondary"><i class="fas fa-info-circle"></i> Klik Jam KBM untuk menuju Kelas yang dipilih.</i></div>';

                            foreach ($ambil_jdwal as $value_jdwl) {
                                $info_jam = $value_jdwl['mulai_jamke'] . ' - ' . $value_jdwl['sampai_jamke'];
                                $info_ruangan = $value_jdwl['ruangan'];
                                $info_info = $value_jdwl['info'];
                                $info_kelas = $value_jdwl['kelas'];
                                $info_jur = $value_jdwl['jur'];

                                $detail_info = '
                                <div>
                                    <div class="ml-3 p-1">
                                        <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault' . $ij . '" onclick="self.location = \'kelas.php?jur=' . $info_jur . '&tanggal=' . $tgl_Ymd . '&idjdwl=' . $ij . '\'">
                                        <label class="form-check-label" for="flexRadioDefault' . $ij . '">Jam : <b class="badge badge-dark text-light">' . $info_jam . '</b></label><br>
                                        Ruang / Kelas : 
                                        <span class="badge badge-primary">' . $info_ruangan . '</span>&nbsp;
                                        <span class="badge badge-warning text-dark">' . $info_info . '</span>&nbsp;
                                        <span class="badge badge-success">' . $info_kelas . '</span>&nbsp;
                                        <span class="badge badge-danger">' . $info_jur . '</span><br>
                                    </div>
                                </div>
                                ';
                                $detail_tanggal = $detail_tanggal . $detail_info;
                                $ij++;


                                if ($ambil_jurnal) {
                                    $bg_jurnalguru = 'success';

                                    $info_jurnal = $ambil_jurnal[0]['jurnal'];

                                    $detail_jurnal = '
                                    <div class="ml-3 p-1">
                                        <span class="badge badge-secondary">Jurnal Mengajar : </span>
                                        <span>"' . $info_jurnal . '"</span>
                                    </div>
                                    ';
                                } else {
                                    $detail_jurnal = '
                                    <div class="ml-3 p-1">
                                        <span class="badge badge-secondary">Jurnal Mengajar : </span>
                                        <span><i>- belum diisi -</i></span>
                                    </div>
                                    ';
                                }
                                $detail_tanggal = $detail_tanggal . $detail_jurnal;
                            }

                            $detail_tanggal = $_help_info . $detail_tanggal;
                        } else {
                            $bg_jdwalguru = '';
                            $bg_jurnalguru = '';
                        }

                        if ((($hit_kol) % 7 == 0) || (($hit_kol + 1) % 7 == 0)) {
                            if ($tgl_aktif == true) {
                                $bg_kal_ = ' bg-danger bg-gradient-danger';
                                $warna_tgl = 'text-white';
                                $alert_detail = ' alert-default-danger';
                                $txt_kal_ = 'style="vertical-align: middle; font-size: 30px; font-weight: bolder;"';
                            } else {
                                $warna_tgl = 'text-white';
                                $bg_kal_ = ' bg-danger bg-gradient-danger';
                                $txt_kal_ = 'style="font-style: italic; font-size: 18px; font-weight: 100; text-align: right;"';
                            }
                        } elseif (($hit_kol + 2) % 7 == 0) {
                            if ($tgl_aktif == true) {
                                $txt_kal_ = 'style="vertical-align: middle; font-size: 30px; font-weight: bolder;"';

                                if ($liburNas != '') {
                                    $bg_kal_ = 'bg-danger bg-gradient-danger';
                                    $warna_tgl = 'text-white';
                                    $alert_detail = ' alert-default-danger';
                                } else {
                                    // $bg_kal_ = ' bg-success bg-gradient-success text-dark';
                                    $warna_tgl = 'text-dark';
                                    $bg_kal_ = ' bg-light bg-gradient-light text-dark';
                                    $alert_detail = ' alert-default-warning';
                                    $outline_status1 = 'card-' . $bg_status1 . ' card-outline';
                                    $outline_status2 = 'card-' . $bg_status2 . ' card-outline';
                                    $outline_status3 = 'card-' . $bg_jdwalguru . ' card-outline';
                                    $outline_status4 = 'card-' . $bg_jurnalguru . ' card-outline';
                                }
                            } else {

                                $bg_kal_ = '';
                                $txt_kal_ = 'style="font-style: italic; font-size: 18px; font-weight: 100; text-align: right;"';
                            }
                        } else {
                            if ($tgl_aktif == true && !$liburNas) {
                                $warna_tgl = 'text-dark';
                                $outline_status1 = 'card-' . $bg_status1 . ' card-outline';
                                $outline_status2 = 'card-' . $bg_status2 . ' card-outline';
                                $outline_status3 = 'card-' . $bg_jdwalguru . ' card-outline';
                                $outline_status4 = 'card-' . $bg_jurnalguru . ' card-outline';
                                $bg_kal_ = ' bg-light bg-gradient-light';
                                $alert_detail = ' alert-default-warning';
                                $txt_kal_ = 'style="vertical-align: middle; font-size: 30px; font-weight: bolder;"';
                            } else {
                                if ($liburNas) {
                                    if ($tgl_aktif == true) {
                                        $bg_kal_ = ' bg-danger bg-gradient-danger';
                                        $warna_tgl = 'text-white';
                                        $alert_detail = ' alert-default-danger';
                                        $txt_kal_ = 'style="vertical-align: middle; font-size: 30px; font-weight: bolder;"';
                                    } else {
                                        $bg_kal_ = 'bg-danger bg-gradient-danger';
                                        $txt_kal_ = 'style="font-style: italic; font-size: 18px; font-weight: 100; text-align: right;"';
                                    }
                                } else {
                                    $bg_kal_ = '';
                                    $txt_kal_ = 'style="font-style: italic; font-size: 18px; font-weight: 100; text-align: right;"';
                                }
                            }
                        }

                        if (date('Y-m-d', strtotime($tgl_Ymd)) == date('Y-m-d')) {
                            // $bg_kal_ = ' ';
                            $bg_kal_ = ' bg-info bg-gradient-bluelight elevation-5';
                            $warna_tgl = 'text-white';
                            $alert_detail = ' alert-default-info';
                            $label_tgl_ = $label_tgl_;
                            $label_tgl_2 = "Hari ini";
                            $txt_kal_ = 'style="font-weight: bolder; font-size: 34px; vertical-align: middle;"';
                        }

                        // $haripasaran = tglJawa($tgl_Ymd);
                        $harijawadanhijriah = hdate($tgl_Ymd);
                    ?>
                        <td id="tanggalan01" class="<?= $bg_kal_; ?>" <?= $txt_kal_; ?>>
                            <div id="tanggalnya">
                                <div id="tglarabic">
                                    <?= arabic_number(@$harijawadanhijriah['hijriday']); ?>
                                </div>
                                <!-- <div>
                                        <span class="badge badge-warning" style="font-size: 25%;">12</span>
                                        <span class="badge badge-success" style="font-size: 25%;">12</span>
                                    </div> -->
                                <a class="<?= $warna_tgl; ?>" data-toggle="collapse" href="#collapseKalender_<?= $tgl_YMD; ?>" style="text-decoration: none;">
                                    <!-- <div style="text-shadow: 0px 0px 4px rgb(255, 255, 255);"> -->
                                    <div>
                                        <?= $tgl_kal_; ?>
                                        <!-- <h6> <?= date('Y-m-d', strtotime($tgl_Ymd)) . ' ' . date('Y-m-d', strtotime($tanggal_pilih)); ?></h6> -->
                                        <!-- <h6><?= $tgl_kal_ . ' ' . $jumlah_hari_bulan_ini; ?></h6> -->
                                        <!-- <h6><?= $tanggal_hariminggu_terakhir; ?></h6> -->
                                        <!-- <h6><?= $sss; ?></h6> -->
                                        <!-- <h6><?= $keterangan_masuk . ', ' . $keterangan; ?></h6> -->
                                    </div>

                                    <?php if ($datab_login == 'datasiswa') { ?>
                                        <div style="display: flex; justify-content: center; margin-bottom: 0px;">
                                            <div class="<?= $outline_status1; ?>" style="width: 50px;"></div>
                                            <div class="<?= $outline_status2; ?>" style="width: 50px;"></div>
                                        </div>
                                    <?php } ?>

                                    <?php if ($datab_login == 'dataguru') { ?>
                                        <div style="display: flex; justify-content: center; margin-top: 1px;">
                                            <div class="<?= $outline_status3; ?>" style="width: 50px;"></div>
                                            <div class="<?= $outline_status4; ?>" style="width: 50px;"></div>
                                        </div>
                                    <?php } ?>
                                    <div>
                                        <h6 id="keterangantgl" for="tanggalnya"><?= $label_tgl_; ?></h6>
                                        <h6 id="keterangantgl" for="tanggalnya">
                                            <?= @$harijawadanhijriah['tgljawa'] . ' ' . @$harijawadanhijriah['pasaranjawa']; ?>
                                        </h6>
                                    </div>
                                </a>
                            </div>
                        </td>

                        <style>
                            .w-fit {
                                width: fit-content;
                            }
                        </style>
                        <!-- collapse -->
                        <div id="collapseKalender_<?= $tgl_YMD; ?>" class="collapse alert<?= $alert_detail; ?>" data-parent="#accordion">
                            <!-- close collapse button -->
                            <a id="tutup_detail_tanggal" class="btn btn-danger bg-gradient-danger elevation-1" data-toggle="collapse" href="#collapseKalender_<?= $tgl_YMD; ?>">
                                <label style="cursor: pointer;">Tutup</label>&nbsp;&nbsp;
                                <i class="fas fa-times"></i>
                            </a>
                            <div class="card-body" style="display: flex; flex-direction: column;">
                                <h6>Detail Info : </h6>
                                <?php if (@$label_tgl_ || @$label_tgl_2) { ?>
                                    <h5 class="bg-info w-fit p-2 rounded"><?= $label_tgl_; ?><?= ' ' . @$label_tgl_2; ?></h5>
                                <?php } ?>
                                <h6>
                                    Hari,&nbsp;Tanggal&nbsp;: &nbsp;
                                    <?= hariIndo(date('l', strtotime($tgl_Ymd))); ?>,&nbsp;
                                    <?= sprintf('%02d', $tgl_kal_) . ' ' . bulanIndo(date('F', strtotime($tgl_Ymd))) . ' ' . $tahun_pilih; ?>
                                    <br>
                                    <i>
                                        (<?= @$harijawadanhijriah['hijriday'] . ' ' . @$harijawadanhijriah['hijrimonth'] . ' ' . @$harijawadanhijriah['hijriyear']; ?>)&nbsp;
                                        (<?= @$harijawadanhijriah['tgljawa'] . ' ' . @$harijawadanhijriah['pasaranjawa']; ?>)
                                    </i>
                                </h6>
                                <p>
                                    <?= @$detail_tanggal ? $detail_tanggal : '<i>- tidak ada info lain -</i>'; ?>
                                </p>
                            </div>
                        </div>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>


<?php

// convert to Java and hijri Calendar
// function 1
function intPart($floatNum)
{
    return ($floatNum < -0.0000001 ? ceil($floatNum - 0.0000001) : floor($floatNum + 0.0000001));
}

// function 2
function hdate($tanggal)
{
    $iday = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
    $imonth = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');

    $aday = array('Al-Ahad', 'Al-Itsnayna', 'Ats-Tsalatsa\'', 'Al-Arba\'a', 'Al-Hamis', 'Al-Jum\'a', 'As-Sabt');
    $amonth = array('Muharram', 'Safar', 'Rabi\'ul Awal', 'Rabi\'ul Akhir', 'Jumadil Awal', 'Jumadil Akhir', 'Rajab', 'Sya\'ban', 'Ramadhan', 'Syawal', 'Dzul Qa\'dah', 'Dzul Hijjah');

    $jmonth = array('Suro', 'Sapar', 'Mulud', 'Ba\'da Mulud', 'Jumadil Awal', 'Jumadil Akhir', 'Rejeb', 'Ruwah', 'Poso', 'Syawal', 'Dulkaidah', 'Besar');
    $jday = array('Pon', 'Wage', 'Kliwon', 'Legi', 'Pahing');
    // $jday = array('Pon', 'Wag', 'Kli', 'Leg', 'Pah');

    $tgl = explode('-', $tanggal);
    $year = $tgl[0];
    $month = $tgl[1];
    $day = $tgl[2];

    $julian = GregorianToJD($month, $day, $year);
    if ($julian >= 1937808 && $julian <= 536838867) {
        $date = cal_from_jd($julian, CAL_GREGORIAN);
        $d = $day;
        $m = (int)$month - 1;
        $y = $year;

        $mPart = ($m - 13) / 12;
        $jd = intPart((1461 * ($y + 4800 + intPart($mPart))) / 4) + intPart((367 * ($m - 1 - 12 * (intPart($mPart)))) / 12) - intPart((3 * (intPart(($y + 4900 + intPart($mPart)) / 100))) / 4) + $d - 32075;

        $l = $jd - 1948440 + 10632;
        $n = intPart(($l - 1) / 10631);
        $l = $l - 10631 * $n + 354;
        $j = (intPart((10985 - $l) / 5316)) * (intPart((50 * $l) / 17719)) + (intPart($l / 5670)) * (intPart((43 * $l) / 15238));
        $l = $l - (intPart((30 - $j) / 15)) * (intPart((17719 * $j) / 50)) - (intPart($j / 16)) * (intPart((15238 * $j) / 43)) + 29;

        $m = intPart((24 * $l) / 709);
        $d = $l - intPart((709 * $m) / 24);
        $y = 30 * $n + $j - 30;
        $yj = $y + 512;
        $h = ($julian + 3) % 5;
        if ($julian <= 1948439) $y--;

        return array(
            'tgl' => $date['day'],
            'hari' => $iday[$date['dow']],
            'bulan' => @$imonth[$date['month']],
            'tahun' => $date['year'],
            'tglhijri' => $aday[$date['dow']],
            'hijriday' => $d,
            'hijrimonth' => $amonth[$m - 1],
            'hijriyear' => $y,
            'pasaranjawa' => $jday[$h],
            // 'harJawa' => $iday[$date['dow']], 
            'tgljawa' => $d,
            'tahunjawa' => $yj,
            'bulanjawa' => $jmonth[$m - 1],
        );
    } else return false;
}

// convert to arabic string
function arabic_number($number)
{
    $arabic_number = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');
    return str_replace(range(0, 9), $arabic_number, $number);
}

function cari_tanggal($tanggal_pilih, $hasil_array)
{
    $hasil_cari_tanggal = array();
    foreach ($hasil_array as $dtp) {
        if ($dtp['tanggal'] == $tanggal_pilih) {
            $hasil_cari_tanggal[] = $dtp;
        }
    }
    return $hasil_cari_tanggal;
}
// $imonth = Array( 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
// $amonth = Array( 'Muharram','Safar','Rabi\'ul Awal','Rabi\'ul Akhir','Jumadil Awal','Jumadil Akhir','Rajab','Sya\'ban','Ramadhan','Syawal','Dzul Qa\'dah','Dzul Hijjah');
// $jmonth = Array( 'Suro','Sapar','Mulud','Ba\'da Mulud','Jumadil Awal','Jumadil Akhir','Rejeb','Ruwah','Poso','Syawal','Dulkaidah','Besar');

// $aday = Array('Al-Ahad','Al-Itsnayna','Ats-Tsalatsa\'','Al-Arba\'a','Al-Hamis','Al-Jum\'a','As-Sabt');
// $iday = Array('Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu');
// $jday = Array('Pon','Wage','Kliwon','Legi','Pahing');

// $date = hdate($tanggal_pilih);

// echo 'hari ini adalah hari ' . $date['hari'] . ', tanggal ' . $date['tgl'] . ' ' . $date['bulan'] . ' ' . $date['tahun'] . '<br>';
// echo 'hari ini hijri adalah hari ' . $date['tglhijri'] . ', tanggal ' . $date['hijriday'] . ' ' . $date['hijrimonth'] . ' ' . $date['hijriyear'] . '<br>';
// echo 'hari jawa adalah hari ' . $date['pasaranjawa'] . ', tanggal ' . $date['tgljawa'] . ' ' . $date['bulanjawa'] . ' ' . $date['tahunjawa'] . '<br>';

// print_r('Hari ini : ' . date('Y-m-d') . '<br>');
// printf('<pre> %s </pre>', print_r(hdate(date('Y-m-d')), true));
// print_r('Hari Kemerdekaaan : ');
// printf("<pre> %s </pre>", print_r(hdate('1945-08-17'), true));
?>
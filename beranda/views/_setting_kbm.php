<section class="content">
    <div class="container-fluid">
        <div class="card bg-dark bg-gradient-dark elevation-3" style="border: none; z-index: 1;">
            <div id="header_rekap" class="card-body">
                <div style="display: flex; justify-content: baseline; justify-content: space-between;">

                    <style>
                        .tombolsettingkbm {
                            display: flex;
                            gap: 10px;
                        }
                    </style>
                    <div class="tombolsettingkbm">
                        <div>
                            <a href="?jur=AT" class="btn btn-success bg-gradient-success elevation-3 border-0<?= (@$_GET['jur'] == 'AT') ? ' disabled' : ''; ?>">
                                <?= @$_GET['jur'] == 'AT' ? '<i class="fas fa-cog fa-spin"></i>' : ''; ?>
                                &nbsp;AT
                            </a>
                        </div>
                        <div>
                            <a href="?jur=DKV" class="btn btn-primary bg-gradient-primary elevation-3 border-0<?= (@$_GET['jur'] == 'DKV') ? ' disabled' : ''; ?>">
                                <?= @$_GET['jur'] == 'DKV' ? '<i class="fas fa-cog fa-spin"></i>' : ''; ?>
                                &nbsp;DKV
                            </a>
                        </div>
                        <div>
                            <a href="?jur=TE" class="btn btn-warning bg-gradient-warning elevation-3 border-0<?= (@$_GET['jur'] == 'TE') ? ' disabled' : ''; ?>">
                                <?= @$_GET['jur'] == 'TE' ? '<i class="fas fa-cog fa-spin"></i>' : ''; ?>
                                &nbsp;TE
                            </a>
                        </div>
                        <div>
                            <a href="?set=jam" class="btn btn-warning text-light bg-gradient-danger elevation-3 border-0<?= (@$_GET['set'] == 'jam') ? ' disabled' : ''; ?>">
                                <?= @$_GET['set'] == 'jam' ? '<i class="fas fa-cog fa-spin"></i>' : ''; ?>
                                &nbsp;Set Jam
                            </a>
                        </div>
                        <div class="float-end">
                            <a href="?jadwal_semster=true" class="btn btn-dark text-light bg-gradient-secondary elevation-3 border-0<?= (@$_GET['set'] == 'jam') ? ' disabled' : ''; ?>">
                                <i class="fas fa-table"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>


<?php
if (@$na == 'NA') {
    $disabled_na = "disabled";
    $icon_na = '<i class="fas fa-spinner fa-spin text-light"></i>';

    $disabled_x = "";
    $disabled_xi = "";
    $disabled_xii = "";

    $icon_x = '';
    $icon_xi = '';
    $icon_xii = '';
} else {
    if (@$tingkat == 'X') {
        $disabled_x = "disabled";
        $disabled_xi = "";
        $disabled_xii = "";

        $icon_x = '<i class="fas fa-spinner fa-spin text-light"></i>';
        $icon_xi = '';
        $icon_xii = '';
    } else if (@$tingkat == 'XI') {
        $disabled_x = "";
        $disabled_xi = "disabled";
        $disabled_xii = "";

        $icon_x = '';
        $icon_xi = '<i class="fas fa-spinner fa-spin text-light"></i>';
        $icon_x = '';
    } else if (@$tingkat == 'XII') {
        $disabled_x = "";
        $disabled_xi = "";
        $disabled_xii = "disabled";

        $icon_x = '';
        $icon_xi = '';
        $icon_xii = '<i class="fas fa-spinner fa-spin text-light"></i>';
    } else {
        $disabled_x = "";
        $disabled_xi = "";
        $disabled_xii = "";

        $icon_x = '';
        $icon_xi = '';
        $icon_xii = '';
    }

    $disabled_na = '';
    $icon_na = '';
}
?>

<?php if (@$_GET['set'] != 'jam') { ?>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 connectedSortable">
                    <div class="card elevation-5">
                        <div class="card-header bg-gradient-navy">
                            <h3 class="card-title">
                                <i class="fas fa-cog fa-spin"></i></i>&nbsp;
                                Jadwal&nbsp;<?= mysqli_real_escape_string($konek, $_GET['jur']); ?>&nbsp;<span class="badge bg-light"><?= $kelompokkelas; ?></span>&nbsp;<span class="badge bg-light"><?= $set_bulan; ?> / <?= date('Y'); ?></span>
                            </h3>
                            <div class="card-tools">
                                <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Mengubah Setting Aplikasi, dan mengatur Admin"></i>
                                <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <!-- <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button> -->
                            </div>
                        </div>
                        <div class="card-body">
                            <style>
                                .card-body #jadwal_bulan {
                                    background: whitesmoke;
                                    padding: 5px;
                                }

                                .card-body .tanggal {
                                    background-color: white;
                                    display: flex;
                                    flex-wrap: wrap;
                                }

                                .card-body .tanggal h1 {
                                    width: 14%;
                                    text-align: center;
                                    border: 1mm outset rgba(211, 220, 50, .6);
                                    padding: 5px;
                                }

                                .card-body .tanggal h1 span {
                                    font-size: 14px;
                                }

                                @media screen and (max-width: 690px) {
                                    .tanggal #namahari {
                                        font-size: 9px;
                                    }

                                    .card-body .tanggal {
                                        display: flex;
                                        flex-direction: column;
                                    }

                                    .card-body .tanggal h1 {
                                        width: 100%;
                                    }

                                    #jadwal_bulan .col-6 {
                                        width: 100%;
                                        padding: 10px;
                                    }

                                    .tanggal #displaynone_tgl {
                                        display: none;
                                    }
                                }
                            </style>
                            <div id="jadwal_bulan" class="jadwal_bulan">
                                <div class="row">
                                    <div class="col-6">
                                        <div>
                                            <label for="set_pilih_kelompok">Pilih Kelompok / Kelas</label>
                                            <select id="set_pilih_kelompok" name="kel" class="form-select form-select" aria-label=".form-select-sm example" onchange="self.location = 'setting_kbm.php?' + '&jur=<?= $jur; ?>&bulanset=<?= $set_bulan; ?>' + '&kel=' + this.value">
                                                <option>- Pilih Kelompok -</option>
                                                <?php

                                                $kelas = '';
                                                for ($kel = 0; $kel < $jumlah_datakelompok; $kel++) {

                                                    if ($kelompokkelas == $data_kelompok[$kel]['kode']) {
                                                        $select_kel_ = 'selected';
                                                        $bold_kel = 'fw-bold';
                                                        $icon_kel = '->';
                                                        $kelas = $data_kelompok[$kel]['id'];
                                                    } else {
                                                        $select_kel_ = '';
                                                        $bold_kel = '';
                                                        $icon_kel = '';
                                                    }
                                                ?>
                                                    <option <?= $select_kel_; ?> class="<?= $bold_kel; ?>" value="<?= $data_kelompok[$kel]['kode']; ?>"><?= $icon_kel; ?>&nbsp;<?= $data_kelompok[$kel]['info']; ?></option>
                                                <?php } ?>
                                                <option value="">- Tidak diset -</option>
                                            </select>
                                        </div>
                                        <div class="mt-3">
                                            <label for="pilih_bulan_set_kbm">Pilih Bulan</label>
                                            <select id="pilih_bulan_set_kbm" class="form-select form-select" aria-label=".form-select-sm example" onchange="self.location = 'setting_kbm.php?' + '&jur=<?= $jur; ?>&kel=<?= $kelompokkelas; ?>' + '&bulanset=' + this.value">

                                                <?php
                                                for ($bu = 0; $bu <= 12; $bu++) {
                                                    $array_bulan = array();
                                                    $array_bulan = [
                                                        '- Pilih Bulan -',
                                                        'Januari',
                                                        'Februari',
                                                        'Maret',
                                                        'April',
                                                        'Mei',
                                                        'Juni',
                                                        'Juli',
                                                        'Agustus',
                                                        'September',
                                                        'Oktober',
                                                        'November',
                                                        'Desember'
                                                    ];

                                                    if ((int)$set_bulan == $bu) {
                                                        $select_bulan = 'selected';
                                                        $bold_bulan = 'fw-bold';
                                                        $icon_bulan = '->';
                                                    } else {
                                                        $select_bulan = '';
                                                        $bold_bulan = '';
                                                        $icon_bulan = '';
                                                    }
                                                ?>
                                                    <!-- <option selected>Pilih Bulan</option> -->
                                                    <option value="<?= $bu; ?>" <?= $select_bulan; ?> class="<?= $bold_bulan; ?>"><?= $icon_bulan; ?>&nbsp;<?= $array_bulan[$bu]; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="alert alert-info alert-dismissible" role="alert">
                                            <i class="fas fa-info-circle"></i>
                                            <b>Setting Jadwal</b>
                                            <ul>
                                                <li>Pilih Kelas / Kelompok Praktik</li>
                                                <li>Kemudian, Sesuaikan Bulan yang akan di setting</li>
                                                <li>Pilih Tanggal dan hari yang akan di setting.</li>
                                                <li>Tentukan, apakah jadwal yang akan di set untuk 1 (satu) hari penuh atau jadwal perjam.</li>
                                                <li>Pilih jadwal untuk satu hari penuh dengan mencentang 'Set Full Day'</li>
                                                <li>Jadwal perjam, klik tombol 'Set Perjam'</li>
                                            </ul>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="tanggal mx-2">
                                    <?php
                                    $_tgl_ = 0;
                                    // $_hari_ef = 0;
                                    for ($ii = 1; $ii < ($jumlahhari_bulan + (int) $angka_hari); $ii++) {


                                        if ($ii >= (int) $angka_hari) {
                                            echo "<h1 id='display_tgl'>";
                                            $_tgl_++;
                                            $format = strtotime(date('Y') . '-' . sprintf('%02d', $set_bulan) . '-' . sprintf('%02d', $_tgl_));
                                            $Y_m_d = date('Y-m-d', $format);
                                            $_tgl_set = date('l', $format);

                                            $_hari_kerja = lima_harikerja_ing($_tgl_set);

                                            if ($_hari_kerja == true) {
                                                $warna_hari = 'dark';
                                            } else {
                                                $warna_hari = 'danger';
                                            }

                                            // $warna_hari = 'dark';
                                            $hariindo = hariIndo($_tgl_set);

                                            echo "<span id='namahari' class='badge badge-sm badge-" . $warna_hari . "'>" . $hariindo . "</span><br>";
                                            echo $_tgl_;

                                            if ($_hari_kerja == true) {
                                                $hasil_sq_cek_jad = cari_diarray($Y_m_d, $data_jadwalkbm, 'tanggal');

                                                $cek_ruangan_jad = @$hasil_sq_cek_jad[0]['ruangan'];
                                                $cek_nick_jad_1 = @$hasil_sq_cek_jad[0]['nick'];
                                                $cek_nick_jad_2 = @$hasil_sq_cek_jad[1]['nick'];

                                                $_cek_mulai_jamke = @$hasil_sq_cek_jad[0]['mulai_jamke'];
                                                $_cek_sampai_jamke = @$hasil_sq_cek_jad[0]['sampai_jamke'];

                                                if ($_cek_sampai_jamke == '11' && $_cek_mulai_jamke == '1') {
                                                    $cek_box_checked = 'checked';
                                                    $check_box_display = 'display:block';
                                                    $button_display = 'display:none';
                                                } else {
                                                    $cek_box_checked = '';
                                                    $check_box_display = 'display:none';
                                                    $button_display = 'display:block';
                                                }
                                    ?>
                                                <div>
                                                    <span id="icon_pilih_ruang_<?= $_tgl_; ?>">
                                                        <?php if (@$cek_ruangan_jad) { ?>
                                                            <i class="fas fa-check-circle text-success"></i>
                                                        <?php } else { ?>
                                                            <i class="far fa-circle text-primary"></i>
                                                        <?php } ?>
                                                    </span>
                                                    <span id="icon_pilih_guru_<?= $_tgl_; ?>">
                                                        <?php if (@$cek_nick_jad) { ?>
                                                            <i class="fas fa-check-circle text-success"></i>
                                                        <?php } else { ?>
                                                            <i class="far fa-circle text-primary"></i>
                                                        <?php } ?>
                                                    </span>
                                                    <span class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="<?= $_tgl_; ?>" id="myCheck_set_<?= $_tgl_; ?>" onclick="pilihan_set_kbm(this.value)" <?= $cek_box_checked; ?>>
                                                        <label class="form-check-label" for="flexCheckDefault">
                                                            Set Full Day
                                                        </label>
                                                    </span>
                                                </div>
                                                <?php

                                                ?>
                                                <div id="select_set_<?= $_tgl_; ?>" style="<?= $check_box_display; ?>">
                                                    <select id="pilih_ruang" name="set_pilih_ruang" class="form-select form-select-sm" aria-label=".form-select-sm example" onchange="set_pilih_ruang(<?= $_tgl_; ?>, this.value, <?= $kelas; ?>);">
                                                        <option>- Pilih Ruangan -</option>
                                                        <?php
                                                        for ($ruang = 0; $ruang < $jumlah_datadaftarruang; $ruang++) {
                                                            if ($cek_ruangan_jad == $data_daftarruang[$ruang]['kode']) {
                                                                $select_ruangan_jad = 'selected';
                                                            } else {
                                                                $select_ruangan_jad = '';
                                                            }
                                                        ?>
                                                            <option <?= $select_ruangan_jad; ?> value="<?= $set_bulan . '#' . $data_daftarruang[$ruang]['kode']; ?>">[<?= $data_daftarruang[$ruang]['keterangan']; ?>]&nbsp;<?= $data_daftarruang[$ruang]['inforuang']; ?></option>
                                                        <?php } ?>
                                                        <option value="<?= $set_bulan; ?>">- Tidak diset -</option>
                                                    </select>

                                                    <select id="pilih_guru" class="form-select form-select-sm mt-1" aria-label=".form-select-sm example" onchange="set_pilih_guru(<?= $_tgl_; ?>, this.value, <?= $kelas; ?>);">
                                                        <option>- Pilih Guru/Instruktur -</option>
                                                        <?php
                                                        for ($guru_ = 0; $guru_ < $jumlah_datadataguru; $guru_++) {
                                                            if (@$cek_nick_jad_1 == $data_dataguru[$guru_]['nick']) {
                                                                $select_guru_jad = 'selected';
                                                            } else {
                                                                $select_guru_jad = '';
                                                            }
                                                        ?>
                                                            <option <?= $select_guru_jad; ?> value="<?= $set_bulan . '#' . $data_dataguru[$guru_]['nick']; ?>">[<?= $data_dataguru[$guru_]['info']; ?>]&nbsp;<?= $data_dataguru[$guru_]['nama']; ?></option>
                                                        <?php } ?>
                                                        <option value="<?= $set_bulan; ?>">- Tidak diset -</option>
                                                    </select>

                                                    <select id="pilih_guru" class="form-select form-select-sm mt-1" aria-label=".form-select-sm example" onchange="set_pilih_guru(<?= $_tgl_; ?>, this.value, <?= $kelas; ?>);">
                                                        <option>- Pilih Guru/Instruktur -</option>
                                                        <?php
                                                        for ($guru_ = 0; $guru_ < $jumlah_datadataguru; $guru_++) {
                                                            if (@$cek_nick_jad_2 == $data_dataguru[$guru_]['nick']) {
                                                                $select_guru_jad = 'selected';
                                                            } else {
                                                                $select_guru_jad = '';
                                                            }
                                                        ?>
                                                            <option <?= $select_guru_jad; ?> value="<?= $set_bulan . '#' . $data_dataguru[$guru_]['nick']; ?>">[<?= $data_dataguru[$guru_]['info']; ?>]&nbsp;<?= $data_dataguru[$guru_]['nama']; ?></option>
                                                        <?php } ?>
                                                        <option value="<?= $set_bulan; ?>">- Tidak diset -</option>
                                                    </select>
                                                </div>

                                                <div id="button_set_<?= $_tgl_; ?>" style="<?= $button_display; ?>">
                                                    <!-- Button trigger modal -->
                                                    <button type="button" class="mb-3 btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#set_kbm_jadwal_<?= $_tgl_; ?>">
                                                        Setting Perjam
                                                    </button>

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="set_kbm_jadwal_<?= $_tgl_; ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="set_kbm_jadwal_<?= $_tgl_; ?>Label" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h2 class="modal-title fs-5" id="set_kbm_jadwal_<?= $_tgl_; ?>Label">Seting Jadwal KBM - Tanggal : <?= $_tgl_; ?>&nbsp;<?= $array_bulan[(int)$set_bulan]; ?>&nbsp;<?= date('Y'); ?></h2>
                                                                    <button class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <?php
                                                                    $jam_kbm = 0;
                                                                    for ($data_jam = 0; $data_jam < $jumlah_datajampelajaran; $data_jam++) {
                                                                        if ($data_jampelajaran[$data_jam]['info'] != '' && $data_jampelajaran[$data_jam]['info'] != 'istirahat') {
                                                                            $jam_kbm++;
                                                                    ?>
                                                                            <h6 style="background-color: cornflowerblue;"><?= @$data_jampelajaran[$data_jam]['info']; ?></h6>
                                                                            <select id="pilih_ruang" name="set_pilih_ruang" class="form-select form-select-sm" aria-label=".form-select-sm example" onchange="set_pilih_ruang(<?= $_tgl_; ?>, this.value, <?= $kelas; ?>);">
                                                                                <option>- Pilih Ruangan -</option>
                                                                                <?php
                                                                                for ($ruang = 0; $ruang < $jumlah_datadaftarruang; $ruang++) {

                                                                                    $hasil_cari_ruangan = cari_diarray($jam_kbm, $data_jadwalkbm, 'mulai_jamke');

                                                                                    if (@$hasil_cari_ruangan[0]['ruangan'] == $data_daftarruang[$ruang]['kode']) {
                                                                                        $select_ruangan_jad_modal = 'selected';
                                                                                    } else {
                                                                                        $select_ruangan_jad_modal = '';
                                                                                    }
                                                                                ?>
                                                                                    <option <?= $select_ruangan_jad_modal; ?> value="<?= $set_bulan . '#' . $data_daftarruang[$ruang]['kode'] . '#' . $jam_kbm; ?>">[<?= $data_daftarruang[$ruang]['keterangan']; ?>]&nbsp;<?= $data_daftarruang[$ruang]['inforuang']; ?></option>
                                                                                <?php } ?>
                                                                                <option value="<?= $set_bulan; ?>">- Tidak diset -</option>
                                                                            </select>

                                                                            <select id="pilih_guru" class="form-select form-select-sm" aria-label=".form-select-sm example" onchange="set_pilih_guru(<?= $_tgl_; ?>, this.value, <?= $kelas; ?>);">
                                                                                <option>- Pilih Guru/Instruktur -</option>
                                                                                <?php
                                                                                for ($guru_ = 0; $guru_ < $jumlah_datadataguru; $guru_++) {
                                                                                    $hasil_cari_nick_guru = cari_diarray($jam_kbm, $data_jadwalkbm, 'mulai_jamke');
                                                                                    if (@$hasil_cari_nick_guru[0]['nick'] == $data_dataguru[$guru_]['nick']) {
                                                                                        $select_guru_jad_modal = 'selected';
                                                                                    } else {
                                                                                        $select_guru_jad_modal = '';
                                                                                    }
                                                                                ?>
                                                                                    <option <?= $select_guru_jad_modal; ?> value="<?= $set_bulan . '#' . $data_dataguru[$guru_]['nick'] . '#' . $jam_kbm; ?>">[<?= $data_dataguru[$guru_]['info']; ?>]&nbsp;<?= $data_dataguru[$guru_]['nama']; ?></option>
                                                                                <?php } ?>
                                                                                <option value="<?= $set_bulan; ?>">- Tidak diset -</option>
                                                                            </select>
                                                                    <?php }
                                                                    } ?>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">Selesai</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                    <?php
                                            }
                                            echo "</h1>";
                                        } else {
                                            echo "<h1 id='displaynone_tgl'>-</h1>";
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($na != 'NA') { ?>
                <div class="row">
                    <div class="col-lg-12 connectedSortable">
                        <div class="card elevation-5">
                            <div class="card-header bg-gradient-navy">
                                <h3 class="card-title">
                                    <i class="fas fa-cog fa-spin"></i></i>&nbsp;
                                    Pembagian Kelompok Siswa
                                </h3>
                                <div class="card-tools">
                                    <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Mengubah Setting Aplikasi, dan mengatur Admin"></i>
                                    <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <!-- <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button> -->
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div class="btn-group btn-sm">
                                            <a href="setting_kbm.php?jur=TE&t=X" class="btn btn-sm btn-danger border-0 <?= $disabled_x; ?>"><?= $icon_x; ?>&nbsp;
                                                X TE
                                            </a>
                                            <a href="setting_kbm.php?jur=TE&t=XI" class="btn btn-sm btn-warning border-0 <?= $disabled_xi; ?>"><?= $icon_xi; ?>&nbsp;
                                                XI TE
                                            </a>
                                            <a href="setting_kbm.php?jur=TE&t=XII" class="btn btn-sm btn-success border-0 <?= $disabled_xii; ?>"><?= @$icon_xii; ?>&nbsp;
                                                XII TE
                                            </a>
                                        </div>
                                        <label for="" class="text-danger">Pilih Kelas</label>
                                    </div>
                                    <form action="app/generate_kelompok.php" method="post">
                                        <input type="hidden" name="jur" value="<?= $jur; ?>">
                                        <input type="hidden" name="t" value="<?= $tingkat; ?>">
                                        <div class="d-flex">
                                            <div>
                                                <select class="form-select form-select-sm" name="mode_gen" aria-label=".form-select-sm example">
                                                    <option value="0" selected>Default</option>
                                                    <option value="1">Random</option>
                                                    <option value="2">Urut Nama</option>
                                                    <option value="3">Urut Kelas</option>
                                                    <option value="4">Urut NIS</option>
                                                </select>
                                            </div>
                                            <div>
                                                <button class="btn btn-danger btn-sm border-0" type="submit" name="generate" value="gen">Generate</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <?php if (@$jur) { ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>NIS</th>
                                                    <th>Nama</th>
                                                    <th>Kelas</th>
                                                    <th>Kelompok</th>
                                                    <th>*</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $nno = 0;
                                                while ($data_siswa = mysqli_fetch_array($query_siswa)) {
                                                    $nno++;
                                                    $data_s = $data_siswa['nis'];
                                                ?>
                                                    <tr>
                                                        <td><?= $nno; ?></td>
                                                        <td><?= $data_s; ?></td>
                                                        <td><?= $data_siswa['nama']; ?></td>
                                                        <td><?= $data_siswa['kelas']; ?></td>
                                                        <td class="d-flex">
                                                            <span class="mr-1" id="check_set_<?= $data_s; ?>">
                                                                <?php
                                                                $d_kelompok = @$data_siswa['kelompok'];

                                                                if ($d_kelompok) {
                                                                ?>
                                                                    <i class="fas fa-check-circle text-success"></i>
                                                                <?php } else { ?>
                                                                    <i class="fas fa-question-circle text-warning"></i>
                                                                <?php } ?>
                                                            </span>

                                                            <select class="form-select form-select-sm col-6" name="set_kelompok_<?= $data_s; ?>" aria-label=".form-select-sm example" onchange="set_kelompok(<?= $data_s; ?>, this.value);">
                                                                <option>-</option>
                                                                <?php
                                                                for ($i = 1; $i <= 10; $i++) {
                                                                    $d_sel = ($d_kelompok == $i) ? 'selected' : '';
                                                                    echo '<option value="' . $i . '" ' . $d_sel . '>Kel. ' . $i . '</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </td>
                                                        <td>-</td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>
<?php } ?>

<?php if (@$_GET['set'] == 'jam') { ?>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6 connectedSortable">
                    <div class="card elevation-5">
                        <div class="card-header bg-gradient-navy">
                            <h3 class="card-title">
                                <i class="fas fa-cog fa-spin"></i></i>&nbsp;
                                Setting Jam
                            </h3>
                            <div class="card-tools">
                                <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Mengubah Setting Jam"></i>
                                <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <!-- <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button> -->
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <h5>Jam KBM Normal</h5>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Jam ke - </th>
                                            <th>Set Jam Normal</th>
                                            <!-- <th>Set Jam Jumat</th>
                                            <th>Set Jam Upacara</th> -->
                                        </tr>
                                    </thead>
                                    <?php
                                    $sql_data_jam_pljrn = "SELECT * FROM jampelajaran WHERE mode = '5-45-NOR' AND jamke = ''";
                                    $query_data_jam_pljrn = mysqli_query($konek, $sql_data_jam_pljrn);
                                    $data_jam_pljrn = mysqli_fetch_array($query_data_jam_pljrn);
                                    ?>
                                    <tbody>
                                        <td>Mulai</td>
                                        <td>
                                            <input type="time" name="mulaijam_nor" class="form-control" id="mulaijam_nor" value="<?= @$data_jam_pljrn['waktu_masuk']; ?>" onchange="update_waktu_masuk_1(this.value, '5-45-NOR');">
                                        </td>
                                        <!-- <td>
                                            <input type="time" name="mulaijam_jum" class="form-control" id="mulaijam_jum" value="07:00:00">
                                        </td>
                                        <td>
                                            <input type="time" name="mulaijam_upa" class="form-control" id="mulaijam_upa" value="07:45:00">
                                        </td> -->
                                        <?php
                                        $jam = 1;
                                        $_jamke = 0;
                                        for ($jam = 1; $jam <= 12; $jam++) {

                                            $sql_data_jam_pljrn = "SELECT * FROM jampelajaran WHERE mode = '5-45-NOR' AND jamke = '$jam'";
                                            $query_data_jam_pljrn = mysqli_query($konek, $sql_data_jam_pljrn);
                                            $data_jam_pljrn = mysqli_fetch_array($query_data_jam_pljrn);
                                            if ($data_jam_pljrn['info'] != 'istirahat' && $data_jam_pljrn['info'] != '') {
                                                $_jamke++;
                                                $jamke = $_jamke;
                                            } else {
                                                $jamke = '-&nbsp;&nbsp;';
                                            }
                                        ?>
                                            <tr>
                                                <th class="d-flex">
                                                    <div id="jamke<?= $jam; ?>">
                                                        <?= $jamke; ?>
                                                    </div>
                                                    &nbsp;
                                                    <div class="">
                                                        <span class="badge <?= (@$data_jam_pljrn['info'] == 'istirahat') ? 'bg-info' : 'bg-secondary'; ?>">
                                                            <div id="mulaijam_<?= $jam; ?>">
                                                                <?= @$data_jam_pljrn['mulai'] ? $data_jam_pljrn['mulai'] : '--:--'; ?>
                                                            </div>
                                                        </span>
                                                        <span class="badge <?= (@$data_jam_pljrn['info'] == 'istirahat') ? 'bg-info' : 'bg-secondary'; ?>">
                                                            <div id="sampaijam_<?= $jam; ?>">
                                                                <?= @$data_jam_pljrn['selesai'] ? $data_jam_pljrn['selesai'] : '--:--'; ?>
                                                            </div>
                                                        </span>
                                                    </div>
                                                </th>
                                                <th>
                                                    <div class="d-flex">
                                                        <?php
                                                        $pilihan = @$data_jam_pljrn['keterangan'];
                                                        if ($pilihan == 'jamkbm#00:30:00') {
                                                            $select_jam1 = 'selected';
                                                            $select_jam2 = '';
                                                            $select_jam3 = '';
                                                            $select_jam4 = '';
                                                            $select_jam5 = '';
                                                            $select_jam6 = '';
                                                            $select_jam7 = '';
                                                            $select_jam8 = '';
                                                        } elseif ($pilihan == 'jamkbm#00:35:00') {
                                                            $select_jam1 = '';
                                                            $select_jam2 = 'selected';
                                                            $select_jam3 = '';
                                                            $select_jam4 = '';
                                                            $select_jam5 = '';
                                                            $select_jam6 = '';
                                                            $select_jam7 = '';
                                                            $select_jam8 = '';
                                                        } elseif ($pilihan == 'jamkbm#00:40:00') {
                                                            $select_jam1 = '';
                                                            $select_jam2 = '';
                                                            $select_jam3 = 'selected';
                                                            $select_jam4 = '';
                                                            $select_jam5 = '';
                                                            $select_jam6 = '';
                                                            $select_jam7 = '';
                                                            $select_jam8 = '';
                                                        } elseif ($pilihan == 'jamkbm#00:45:00') {
                                                            $select_jam1 = '';
                                                            $select_jam2 = '';
                                                            $select_jam3 = '';
                                                            $select_jam4 = 'selected';
                                                            $select_jam5 = '';
                                                            $select_jam6 = '';
                                                            $select_jam7 = '';
                                                            $select_jam8 = '';
                                                        } elseif ($pilihan == 'rehat#00:15:00') {
                                                            $select_jam1 = '';
                                                            $select_jam2 = '';
                                                            $select_jam3 = '';
                                                            $select_jam4 = '';
                                                            $select_jam5 = 'selected';
                                                            $select_jam6 = '';
                                                            $select_jam7 = '';
                                                            $select_jam8 = '';
                                                        } elseif ($pilihan == 'rehat#00:30:00') {
                                                            $select_jam1 = '';
                                                            $select_jam2 = '';
                                                            $select_jam3 = '';
                                                            $select_jam4 = '';
                                                            $select_jam5 = '';
                                                            $select_jam6 = 'selected';
                                                            $select_jam7 = '';
                                                            $select_jam8 = '';
                                                        } elseif ($pilihan == 'rehat#00:45:00') {
                                                            $select_jam1 = '';
                                                            $select_jam2 = '';
                                                            $select_jam3 = '';
                                                            $select_jam4 = '';
                                                            $select_jam5 = '';
                                                            $select_jam6 = '';
                                                            $select_jam7 = 'selected';
                                                            $select_jam8 = '';
                                                        } elseif ($pilihan == 'rehat#01:00:00') {
                                                            $select_jam1 = '';
                                                            $select_jam2 = '';
                                                            $select_jam3 = '';
                                                            $select_jam4 = '';
                                                            $select_jam5 = '';
                                                            $select_jam6 = '';
                                                            $select_jam7 = '';
                                                            $select_jam8 = 'selected';
                                                        } else {
                                                            $select_jam1 = '';
                                                            $select_jam2 = '';
                                                            $select_jam3 = '';
                                                            $select_jam4 = '';
                                                            $select_jam5 = '';
                                                            $select_jam6 = '';
                                                            $select_jam7 = '';
                                                            $select_jam8 = '';
                                                        }
                                                        ?>
                                                        <div id="cek_jam_update_1_<?= $jam; ?>">
                                                            <?php if ($pilihan) { ?>
                                                                <?= (@$data_jam_pljrn['info'] == 'istirahat') ? '<i class="fas fa-info-circle text-info"></i>' : '<i class="fas fa-check-circle text-success"></i>'; ?>

                                                            <?php } else { ?>
                                                                <i class="fas fa-question-circle text-warning mt-1 mr-1"></i>
                                                            <?php } ?>
                                                        </div>
                                                        <select onchange="set_jam_1(<?= $jam; ?>, this.value, '5-45-NOR');" class="form-select form-select-sm" aria-label=".form-select-sm example">
                                                            <option value="">-</option>
                                                            <option value="jamkbm#00:30:00" <?= $select_jam1; ?>>+ 30 Menit</option>
                                                            <option value="jamkbm#00:35:00" <?= $select_jam2; ?>>+ 35 Menit</option>
                                                            <option value="jamkbm#00:40:00" <?= $select_jam3; ?>>+ 40 Menit</option>
                                                            <option value="jamkbm#00:45:00" <?= $select_jam4; ?>>+ 45 Menit</option>
                                                            <option value="rehat#00:15:00" <?= $select_jam5; ?>>Rehat + 15 m</option>
                                                            <option value="rehat#00:30:00" <?= $select_jam6; ?>>Rehat + 30 m</option>
                                                            <option value="rehat#00:45:00" <?= $select_jam7; ?>>Rehat + 45 m</option>
                                                            <option value="rehat#01:00:00" <?= $select_jam8; ?>>Rehat + 60 m</option>
                                                        </select>
                                                    </div>
                                                </th>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>

<script type="text/javascript">
    function pilihan_set_kbm(tanggal) {
        // Get the checkbox
        // var checkBox = document.getElementById("myCheck_set_" + tanggal);
        // // Get the output text
        // var select_set = document.getElementById("select_set_" + tanggal);
        // var button_set = document.getElementById("button_set_" + tanggal);

        // If the checkbox is checked, display the output text
        if (document.getElementById("myCheck_set_" + tanggal).checked == true) {
            document.getElementById("select_set_" + tanggal).style.display = "block";
            document.getElementById("button_set_" + tanggal).style.display = "none";
        } else {
            document.getElementById("select_set_" + tanggal).style.display = "none";
            document.getElementById("button_set_" + tanggal).style.display = "block";
        }
    }

    function set_pilih_ruang(tgl_set, ruangan, kel) {
        $.ajax({
            url: "app/set_pilih_ruang.php",
            type: "POST",
            data: {
                tgl_set: tgl_set,
                kel: kel,
                ruangan: ruangan
            },
            success: function(data) {
                document.getElementById("icon_pilih_ruang_" + tgl_set).innerHTML = '<i class="fas fa-check-circle text-primary"></i>';
                // alert(data);
            },
            error: function() {
                alert('ERROR! ' + data);
            }
        });
    }

    function set_pilih_guru(tgl_set, guru, kel) {
        $.ajax({
            url: "app/set_pilih_guru.php",
            type: "POST",
            data: {
                tgl_set: tgl_set,
                guru: guru,
                kel: kel
            },
            success: function(data) {
                document.getElementById("icon_pilih_guru_" + tgl_set).innerHTML = '<i class="fas fa-check-circle text-primary"></i>';
                // alert(data);
            },
            error: function() {
                alert('ERROR! ' + data);
            }
        });
    }

    function update_jadwal(hari, nomorruang, kelompok) {
        $.ajax({
            data: {
                hari: hari,
                nomorruang: nomorruang,
                kelompok: kelompok
            },
            success: function() {
                document.getElementById("jadwalnya" + hari + nomorruang).innerHTML = '<i class="fas fa-check-circle text-primary"></i>';
                // alert(data);
            },
            error: function() {
                alert('error!');
            }
        });
    }

    function update1() {
        $.ajax({
            success: function() {
                document.getElementById("tanggal_jadwal").innerHTML = '<i class="fas fa-check-circle text-primary"></i> Tanggal hari Senin dipilih';
            }
        });
    }

    function update2() {
        $.ajax({
            success: function() {
                document.getElementById("tiap_minggu").innerHTML = '<i class="fas fa-check-circle text-primary"></i> Pengulangan dipilih';
            }
        });
    }

    function update3(tanggal_) {
        $.ajax({
            data: {
                tanggal_: tanggal_
            },
            success: function() {
                var tmp_sen = new Date(tanggal_);
                var tmp_sen_p = new Date(tmp_sen);
                var tmp_sel = new Date(tmp_sen_p.setDate(tmp_sen_p.getDate() + 1));
                var tmp_sel_p = new Date(tmp_sel);
                var tmp_rab = new Date(tmp_sel_p.setDate(tmp_sel_p.getDate() + 1));
                var tmp_rab_p = new Date(tmp_rab);
                var tmp_kam = new Date(tmp_rab_p.setDate(tmp_rab_p.getDate() + 1));
                var tmp_kam_p = new Date(tmp_kam);
                var tmp_jum = new Date(tmp_kam_p.setDate(tmp_kam_p.getDate() + 1));
                var sen = tmp_sen.getDate() + "/" + tmp_sen.getMonth() + "/" + tmp_sen.getFullYear();
                var sel = tmp_sel.getDate() + "/" + tmp_sel.getMonth() + "/" + tmp_sel.getFullYear();
                var rab = tmp_rab.getDate() + "/" + tmp_rab.getMonth() + "/" + tmp_rab.getFullYear();
                var kam = tmp_kam.getDate() + "/" + tmp_kam.getMonth() + "/" + tmp_kam.getFullYear();
                var jum = tmp_jum.getDate() + "/" + tmp_jum.getMonth() + "/" + tmp_jum.getFullYear();

                document.getElementById("tanggal_jadwal_3").innerHTML = '<i class="fas fa-check-circle text-primary"></i>&nbsp;Pengulangan dipilih ';
                document.getElementById("tanggal-jadwal-sen").innerHTML = '<i class="fas fa-check-circle text-primary"></i>&nbsp;' + sen;
                document.getElementById("tanggal-jadwal-sel").innerHTML = '<i class="fas fa-check-circle text-primary"></i>&nbsp;' + sel;
                document.getElementById("tanggal-jadwal-rab").innerHTML = '<i class="fas fa-check-circle text-primary"></i>&nbsp;' + rab;
                document.getElementById("tanggal-jadwal-kam").innerHTML = '<i class="fas fa-check-circle text-primary"></i>&nbsp;' + kam;
                document.getElementById("tanggal-jadwal-jum").innerHTML = '<i class="fas fa-check-circle text-primary"></i>&nbsp;' + jum;
            }
        });
    }

    function set_kelompok(nis, kelompok) {
        $.ajax({
            url: "app/set_kelompok.php",
            type: "POST",
            data: {
                nis: nis,
                kelompok: kelompok
            },
            success: function(data) {
                document.getElementById("check_set_" + nis).innerHTML = '<i class="fas fa-check-circle text-primary"></i>';
                // alert(data);
            },
            error: function() {
                alert('ERROR! ' + data);
            }
        });
    }

    function update_waktu_masuk_1(waktu_masuk, mode) {
        $.ajax({
            url: "app/set_jam_1.php",
            type: "POST",
            data: {
                waktu_masuk: waktu_masuk,
                mode: mode
            },
            success: function(data) {
                // alert(data);
            },
            error: function() {
                alert('ERROR!');
            }

        });
    }

    function set_jam_1(jam, setwaktu, mode) {
        $.ajax({
            url: "app/set_jam_1.php",
            type: "POST",
            data: {
                jam: jam,
                setwaktu: setwaktu,
                mode: mode
            },
            success: function(data) {
                var str = setwaktu;
                var res = str.split("#");
                var info_set = res[0];

                if (info_set == 'jamkbm') {
                    // data_info = 'Jam Pelajaran';
                    document.getElementById("cek_jam_update_1_" + jam).innerHTML = '<i class="fas fa-check-circle text-primary"></i>';
                } else if (info_set == 'rehat') {
                    // data_info = 'Istirahat';
                    document.getElementById("cek_jam_update_1_" + jam).innerHTML = '<i class="fas fa-info-circle text-info"></i>';
                }
                // alert(data);
            },
            error: function() {
                alert('ERROR!');
            }
        });
    }
</script>

<style>
    .fff {
        font-size: 10px;
        display: flex;
        flex-direction: column;
    }
</style>
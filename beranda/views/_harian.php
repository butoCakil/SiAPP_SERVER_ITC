<style>
    #waicon_01 {
        color: #fff;
        /* background-color: lime; */
        padding: 5px 7px;
        border-radius: 50% 50% 50% 10%;
        background: linear-gradient(to bottom, #55e36f, #1ea237);
        color: white;
    }

    thead {
        background-color: deepskyblue;
    }
</style>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div id="header_rekap" class="card bg-primary bg-gradient-primary elevation-2" style="border: none;">
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div style="display: flex; justify-content: baseline; justify-content: space-between;">
                            <div>
                                <a class="nav-link bg-light bg-gradient-light elevation-2" style="border-radius: 5px;" href="<?= $link_datab; ?><?= $tanggal_pilih_min; ?>">
                                    <div style="display: flex; gap: 10px;">
                                        <i class="fas fa-angle-double-left"></i>
                                        <span>Sebelumnya</span>
                                    </div>
                                </a>
                            </div>
                            <div style="display: flex; flex-direction: column; text-align: center;">
                                <div>
                                    <h4 class="mt-2"><b><?= $hari_indo; ?>, </h4>
                                </div>
                                <div>
                                    <h4><?= $tanggal_indo_pilih; ?></h4>
                                </div>
                            </div>
                            <div>
                                <a class="nav-link elevation-2 bg-gradient-light bg-light<?= $disabled; ?>" style="border-radius: 5px;" href="<?= $tanggal_pilih_plus; ?>">
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

        <div class="row">
            <div class="col-12">
                <div id="daftarhadirgtk_001" class="card elevation-2" style="border: none;">
                    <div class="card-header bg-primary bg-gradient-primary">
                        <div class="card-tools">
                            <!-- help -->
                            <i class="fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Menampilkan Catatan Presensi Harian kelas <?= $ket_akses_login; ?>"></i>
                            <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                        <h3 class="card-title">
                            <i class="fas fa-calendar-alt"></i>&nbsp;
                            Rekap Harian&nbsp;
                            <?= $ket_akses_login; ?>
                        </h3>
                    </div>
                    <div class="card-body mb-5">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr style="text-align: center; position: sticky;">
                                    <th>No.</th>
                                    <th>Tanggal</th>
                                    <th>Nama</th>
                                    <th>Foto</th>
                                    <th>Kelas</th>
                                    <th>Masuk</th>
                                    <th>Pulang</th>
                                    <th>Status</th>
                                    <th>Info</th>
                                    <th>Keterangan</th>
                                    <th>Kontak</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 0;
                                while ($no < $hit_datasiswa) {

                                    // $tanggal_pilih = '2022-02-10';
                                    // $tanggal_pilih_dmY = '10-02-2022';

                                    $nokartu_siswa = $hasil_datasiswa[$no]['nokartu'];
                                    $hasil_cari_presensi = cari_data_presensi($nokartu_siswa, $hasil_datapresensi);
                                    $hasil_cari_tanggal = cari_tanggal($tanggal_pilih, $hasil_cari_presensi);

                                    $hct_waktumasuk = @$hasil_cari_tanggal[0]['waktumasuk'] ? $hasil_cari_tanggal[0]['waktumasuk'] : '-';
                                    $hct_ketmasuk = @$hasil_cari_tanggal[0]['ketmasuk'] ? $hasil_cari_tanggal[0]['ketmasuk'] : '-';
                                    $hct_a_time = @$hasil_cari_tanggal[0]['a_time'] ? $hasil_cari_tanggal[0]['a_time'] : '-';
                                    $hct_waktupulang = @$hasil_cari_tanggal[0]['waktupulang'] ? $hasil_cari_tanggal[0]['waktupulang'] : '-';
                                    $hct_ketpulang = @$hasil_cari_tanggal[0]['ketpulang'] ? $hasil_cari_tanggal[0]['ketpulang'] : '-';
                                    $hct_b_time = @$hasil_cari_tanggal[0]['b_time'] ? $hasil_cari_tanggal[0]['b_time'] : '-';
                                    $hct_keterangan = @$hasil_cari_tanggal[0]['keterangan'] ? $hasil_cari_tanggal[0]['keterangan'] : '-';

                                    if ($hct_ketmasuk == 'MSK') {
                                        $hct_ketmasuk = '<span class="badge badge-success">On Time</span>';
                                    } elseif ($hct_ketmasuk == 'TLT') {
                                        $hct_ketmasuk = '<span class="badge badge-warning">Terlambat</span>';
                                    }

                                    if ($hct_ketpulang == 'PLG') {
                                        $hct_ketpulang = '<span class="badge badge-success">Pulang</span>';
                                    } elseif ($hct_ketpulang == "PA") {
                                        $hct_ketpulang = '<span class="badge badge-warning">Pulang Awal</span>';
                                    }

                                    if ($hct_keterangan != '-' && $hct_keterangan != '') {
                                        $hct_bg_keterangan = 'class = "bg-dark"';
                                    } else {
                                        $hct_bg_keterangan = '';
                                    }
                                ?>
                                    <tr <?= $hct_bg_keterangan; ?> style="text-align: center;">
                                        <td style="width: 5%;"><?= $no + 1; ?></td>
                                        <td>
                                            <?= $nama_hari_singkat_pilih; ?>,
                                            <?= $tanggal_pilih_dmY; ?>
                                        </td>
                                        <td style="text-align: left;"><?= $hasil_datasiswa[$no]['nama']; ?></td>
                                        <td><img src="../img/user/<?= $hasil_datasiswa[$no]['foto'] ? $hasil_datasiswa[$no]['foto'] : 'default.jpg'; ?>" style="height: 50px; width: 50px; border-radius: 100%; object-fit: cover; object-position: top;"></td>
                                        <td><?= @$hasil_datasiswa[$no]['kelas'] ? $hasil_datasiswa[$no]['kelas'] : (@$hasil_datasiswa[$no]['jabatan'] ? $hasil_datasiswa[$no]['jabatan'] : '-'); ?></td>

                                        <td>
                                            <?= $hct_waktumasuk; ?>
                                            <?= $hct_ketmasuk; ?>
                                            <?= $hct_a_time; ?>
                                        </td>
                                        <td>
                                            <?= $hct_waktupulang; ?>
                                            <?= $hct_ketpulang; ?>
                                            <?= $hct_b_time; ?>
                                        </td>

                                        <?php
                                        $sql_presensikelas = mysqli_query($konek, "SELECT * FROM presensikelas WHERE nis = '" . $hasil_datasiswa[$no]['nis'] . "' AND " . "tanggal = '" . $tanggal_pilih . "'");

                                        $data_hadir_kelas = array();
                                        $info_hadir_kelas = array();
                                        while ($data_presensikelas = mysqli_fetch_array($sql_presensikelas)) {
                                            $data_hadir_kelas[] = $data_presensikelas['status'];
                                            $info_hadir_kelas[] = '<span class="badge badge-secondary">[' . $data_presensikelas['mulai_jamke'] . '-' . $data_presensikelas['sampai_jamke'] . ']</span><br>' . $data_presensikelas['catatan'];
                                        }
                                        ?>

                                        <td>
                                            <?php
                                            if ($data_hadir_kelas) {
                                                $i = 0;
                                                foreach ($data_hadir_kelas as $value_data) {
                                                    if ($value_data[$i] == 'H') {
                                                        $bg_ = 'success';
                                                    } elseif ($value_data[$i] == 'I') {
                                                        $bg_ = 'primary';
                                                    } elseif ($value_data[$i] == 'S') {
                                                        $bg_ = 'info';
                                                    } elseif ($value_data[$i] == 'T') {
                                                        $bg_ = 'warning';
                                                    } elseif ($value_data[$i] == 'A') {
                                                        $bg_ = 'danger';
                                                    } else {
                                                        $bg_ = 'dark';
                                                    }


                                                    echo "<pre class='badge badge-" . $bg_ . "'>";
                                                    echo ($value_data);
                                                    echo "</pre>";
                                                    $i++;
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($info_hadir_kelas) {
                                                $i = 0;
                                                foreach ($info_hadir_kelas as $value_info) {
                                                    echo "<p>";
                                                    echo ($value_info);
                                                    echo "</p>";
                                                    $i++;
                                                }
                                            }
                                            ?>
                                        </td>

                                        <td><?= $hct_keterangan; ?></td>
                                        <td>
                                            <!-- whatsapp -->
                                            <a id="waicon_01" href="https://api.whatsapp.com/send?phone=&text=Halo%20<?= $hasil_datasiswa[$no]['nama']; ?>%2C%20Saya%20ingin%20tanya%20tentang%20presensi%20anda%20hari%20ini%20%3A%20<?= $tanggal_pilih_dmY; ?>" target="_blank">
                                                <i class="fab fa-whatsapp"></i>
                                                <!-- <img src="../img/app/message-circle_w.svg"> -->
                                            </a>
                                        </td>
                                    </tr>
                                    <?php $no++; ?>
                                <?php } ?>
                            </tbody>
                        </table>
                        <!-- <div id="footer_print" class="btn-group mb-1 mt-3">
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
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
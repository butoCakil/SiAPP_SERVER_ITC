<?php
// echo 'ket akses login: ' . $ket_akses_login;
?>
<section class="content">
    <div class="container-fluid">
        <div class="card bg-primary bg-gradient-primary elevation-2" style="border: none;">
            <!-- /.card-header -->
            <div id="header_rekap" class="card-body">
                <div style="display: flex; justify-content: baseline; justify-content: space-between;">
                    <div>
                        <a class="nav-link bg-light bg-gradient-light elevation-2" style="border-radius: 5px;" href="<?= $link_datab; ?><?= $tahun_bulan_pilih_min; ?>">
                            <div style="display: flex; gap: 10px;">
                                <i class="fas fa-angle-double-left"></i>
                                <span>Sebelumnya</span>
                            </div>
                        </a>
                    </div>
                    <div style="display: flex; flex-direction: column; text-align: center;">
                        <div>
                            <h4 class="mt-2"><b><?= $bulan_indo_pilih; ?></h4>
                        </div>
                        <div>
                            <h4><?= $tahun_pilih; ?></h4>
                        </div>
                    </div>
                    <div>
                        <a class="nav-link bg-light bg-gradient-light elevation-2 btn btn-secondary <?= $disabled_plus; ?>" style="border-radius: 5px;" href="<?= $tahun_bulan_pilih_plus; ?>">
                            <div style="display: flex; gap: 10px;">
                                <span>Berikutnya</span>
                                <i class="fas fa-angle-double-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <style>
            #tabelbulanan1 tbody #pp {
                height: 30px;
                width: 30px;
                border-radius: 50%;
                object-fit: cover;
                object-position: top;
                box-shadow: 0px 1px 2px 0px #000000;
            }
        </style>

        <div id="daftarhadirgtk_001" class="card mb-5 elevation-2">
            <div class="card-header bg-primary bg-gradient-primary">
                <div class="card-tools">
                    <i class="fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Menampilkan Catatan Presensi selama satu Bulan"></i>

                    <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
                <h3 class="card-title">
                    Rekap Presensi&nbsp;
                    <?= $ket_akses_login; ?>
                </h3>
            </div>

            <style>
                /* (A) TABLE WRAPPER */
                #demoCW {
                    width: 100%;
                    height: 450px;
                    overflow: auto;
                }

                /* (B) STICKY HEADERS */
                #tabelbulanan1 th {
                    position: sticky;
                    left: 0;
                    z-index: 1;
                }

                #tabelbulanan1 thead {
                    position: sticky;
                    top: 0;
                    z-index: 2;
                }

                #tabelbulanan1 th,
                #tabelbulanan1 thead {
                    background: #eeefff;
                }
            </style>

            <div class="card-body">
                <div class="table table-responsive">
                    <div id="demoCW" class="card">
                        <table id="tabelbulanan1" class="table table-bordered text-xs w-auto">
                            <thead style="text-align: center;">
                                <th style="vertical-align: middle;">No.</th>
                                <th style="vertical-align: middle;">Nama</th>
                                <td style="vertical-align: middle;">Foto</td>
                                <?php
                                $jumlah_hari = jmlhari($tahun_bulan_pilih);
                                $har = 0;
                                while ($har < $jumlah_hari) {
                                    $har++;
                                    $tanggal_pilih_1 = $tahun_bulan_pilih . '-' . sprintf('%02d', $har);

                                    $hari_singkat_indo = hariSingkatIndo(date('l', strtotime($tanggal_pilih_1)));
                                    $tgl_Ymd = date('Ymd', strtotime($tanggal_pilih_1));
                                    $liburnas = tanggalMerah($tgl_Ymd);
                                    if ($liburnas == '') {
                                        $harilibur = limaharikerja($tanggal_pilih_1);
                                        if ($harilibur == true) {
                                            $bg_hari = ' class="bg-danger"';
                                        } else {
                                            $bg_hari = '';
                                        }
                                    } else {
                                        $bg_hari = ' class="bg-purple"';
                                        $harilibur = true;
                                    }

                                ?>
                                    <td style="text-align: center; vertical-align: middle;" <?= $bg_hari; ?>>
                                        <div>
                                            <?= $hari_singkat_indo; ?>,
                                        </div>
                                        <div class="badge bg-light bg-gradient-light">
                                            <?= $har; ?>
                                        </div>
                                    </td>
                                <?php } ?>
                            </thead>
                            <?php
                            $no = 1;
                            while ($no < $hit_data) {
                                $foto_siswa = (@$hasil_data[$no]['foto']) ? $hasil_data[$no]['foto'] : 'default.jpg';
                            ?>
                                <tbody>
                                    <th style="text-align: center; vertical-align: middle;"><?= $no; ?></th>
                                    <th style="vertical-align: middle; min-width: 150px;"><?= $hasil_data[$no]['nama']; ?></th>
                                    <td style="width: fit-content;"><img id="pp" src="../img/user/<?= $foto_siswa; ?>"></td>

                                    <?php
                                    $hariha = 0;
                                    while ($hariha < $jumlah_hari) {
                                        $hariha++;
                                        $tanggal_pilih_ini = $tahun_bulan_pilih . '-' . sprintf('%02d', $hariha);

                                        $tgl_Ymd = date('Ymd', strtotime($tanggal_pilih_ini));
                                        $liburnas = tanggalMerah($tgl_Ymd);
                                        if ($liburnas == '') {
                                            $liburnas = '<i>' . $liburnas . '</i>';
                                            $harilibur = limaharikerja($tanggal_pilih_ini);
                                        } else {
                                            $harilibur = true;
                                        }

                                        if ($harilibur == false) {
                                            // cari data di array
                                            $nokartu_siswa = $hasil_data[$no]['nokartu'];
                                            $nama_siswa = $hasil_data[$no]['nama'];
                                            // $tanggal_pilih_ini = '2022-02-10';
                                            $hasil_cari_presensi = cari_data_presensi($nokartu_siswa, $hasil_datapresensi);
                                            $hasil_cari_tanggal = cari_tanggal($tanggal_pilih_ini, $hasil_cari_presensi);

                                            $waktumasuk = @$hasil_cari_tanggal[0]['waktumasuk'];
                                            $waktupulang = @$hasil_cari_tanggal[0]['waktupulang'];
                                            $ketmasuk = @$hasil_cari_tanggal[0]['ketmasuk'];
                                            $ketpulang = @$hasil_cari_tanggal[0]['ketpulang'];
                                            $keterangan = @$hasil_cari_tanggal[0]['keterangan'];

                                            if ($keterangan == 'Dinas Luar') {
                                                $keterangan = 'DL';
                                            }

                                            if ($ketmasuk == 'MSK') {
                                                $keterangan_hari_ini_1 = '<div class="badge badge-success">' . $waktumasuk . '</div>';
                                            } else if ($ketmasuk == 'TLT') {
                                                $keterangan_hari_ini_1 = '<div class="badge badge-warning">' . $waktumasuk . '</div>';
                                            } else if ($ketmasuk == '') {
                                                $keterangan_hari_ini_1 = '';
                                            } else {
                                                $keterangan_hari_ini_1 = '<div class="badge badge-danger">' . $waktupulang . '</div>';
                                            }

                                            if ($ketpulang == 'PLG') {
                                                $keterangan_hari_ini_2 = '<div class="badge badge-success">' . $waktupulang . '</div>';
                                            } else if ($ketpulang == "PA") {
                                                $keterangan_hari_ini_2 = '<div class="badge badge-warning">' . $waktupulang . '</div>';
                                            } else if ($ketpulang == '') {
                                                $keterangan_hari_ini_2 = '';
                                            } else {
                                                $keterangan_hari_ini_2 = '<div class="badge badge-danger">' . $waktupulang . '</div>';
                                            }

                                            if ($keterangan) {
                                                $keterangan_hari_ini_2 = '<div class="badge badge-info">' . $keterangan . '</div>';
                                            }

                                            $sql_presensikelas = mysqli_query($konek, "SELECT * FROM presensikelas WHERE nis = '" . $hasil_data[$no]['nis'] . "' AND " . "tanggal = '" . $tanggal_pilih_ini . "'");

                                            $data_hadir_kelas = array();
                                            $info_hadir_kelas = array();
                                            $_H = 0;
                                            $_I = 0;
                                            $_T = 0;
                                            $_S = 0;
                                            $_A = 0;
                                            while ($data_presensikelas = mysqli_fetch_array($sql_presensikelas)) {
                                                $data_hadir_kelas[] = $data_presensikelas['status'];
                                                $info_hadir_kelas[] = $data_presensikelas['catatan'];
                                                if ($data_presensikelas['status'] == 'H') {
                                                    $_H++;
                                                } elseif ($data_presensikelas['status'] == 'I') {
                                                    $_I++;
                                                } elseif ($data_presensikelas['status'] == 'T') {
                                                    $_T++;
                                                } elseif ($data_presensikelas['status'] == 'S') {
                                                    $_S++;
                                                } elseif ($data_presensikelas['status'] == 'A') {
                                                    $_A++;
                                                }
                                            }

                                            $cek_data = array_count_values($data_hadir_kelas);

                                            $_cek = '';
                                            $keterangan_kelas = '';
                                            foreach ($data_hadir_kelas as $dd) {
                                                $_cek = $dd;
                                            }

                                            if ($_H && !$_A && !$_I && !$_S && !$_T) {
                                                $_bg_ = 'success';
                                            } elseif ($_H && $_A && !$_I && !$_S && $_T) {
                                                $_bg_ = 'warning';
                                            } elseif (($_H && !$_A && $_I && $_S && !$_T) || ($_I || $_S)) {
                                                $_bg_ = 'info';
                                            } elseif (!$_H && $_A) {
                                                $_bg_ = 'danger';
                                            } else {
                                                $_bg_ = 'secondary';
                                            }

                                            if ($_cek) {
                                                $keterangan_kelas = '<span class="bg-' . $_bg_ . ' text-light p-2 mb-2">' . $_cek . '</span>';
                                            }

                                            $class_bg_hari = '';
                                        } else {
                                            $keterangan_kelas = '';
                                            $class_bg_hari = ' class="bg-danger"';
                                            if ($liburnas && $no == 1) {
                                                $label_libur = explode(' ', $liburnas);
                                                $keterangan_hari_ini_1 = @$label_libur[3] . ' ' . @$label_libur[4];
                                                $keterangan_hari_ini_2 = @$label_libur[5] . ' ' . @$label_libur[6] . ' ' . @$label_libur[7];
                                                $class_bg_hari = ' class="bg-danger" style = "font-size: 8px; text-align: center;"';
                                                // hilangkan string berisi huruf (a, i, u, e,) 
                                                // $keterangan_hari_ini_1 = preg_replace('/[a,i,u,e,o]/', '', $keterangan_hari_ini_1);
                                                // $keterangan_hari_ini_2 = preg_replace('/[a,i,u,e,o]/', '', $keterangan_hari_ini_2);
                                            } else {
                                                $keterangan_hari_ini_1 = '';
                                                $keterangan_hari_ini_2 = '';
                                            }
                                        }
                                    ?>
                                        <td <?= $class_bg_hari; ?> style="text-align: center; vertical-align: middle;">
                                            <?= $keterangan_hari_ini_1; ?>
                                            <?= $keterangan_hari_ini_2; ?>
                                            <?= $keterangan_kelas; ?>
                                        </td>
                                    <?php } ?>
                                    <?php $no++; ?>
                                <?php } ?>
                                </tbody>
                        </table>
                    </div>
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
</section>
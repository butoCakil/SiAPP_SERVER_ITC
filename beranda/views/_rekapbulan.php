<?php
$nokartu_login = @$_SESSION['nokartu_login'] ? $_SESSION['nokartu_login'] : '';

if ($bulan_tahun_pilihplus == '#') {
    $bulan_tahun_pilihplus = '#';
    $disable = ' disabled btn btn-secondary';
    $ket = 'masuk disable';
} else {
    $bulan_tahun_pilihplus = 'rekapbulan.php?bulan=' . $bulan_tahun_pilihplus;
    $disable = '';
    $ket = 'tidak masuk disable';
}

// test

$kelasjur = @$_GET['kelasjur'];

$kelasjur = $kelasjur ? $kelasjur : str_replace(" ", "", $ket_akses_login);

if ($kelasjur || $akses_login == "Wali Kelas") {
    $pilih_q = $kelasjur;
} else {
    // ============ sinkronkan (belum)
    if ($akses_login == "Wali Kelas") {
        $pilih_q = str_replace(' ', '', $pilih_q); // Menghapus semua spasi
    } else {
        $pilih_q = "";
    }
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
} ?>

<?php include('views/header.php'); ?>
<!-- overlayScrollbars -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div id="header_rekap" class="card bg-gradient-primary" style="z-index: 1;">
                    <div class="card-body">
                        <div style="display: flex; justify-content: baseline; justify-content: space-between;">
                            <a class="nav-link bg-light bg-gradient-light elevation-2" style="border-radius: 5px;"
                                href="rekapbulan.php?bulan=<?= $bulan_tahun_pilihmin; ?>&kelasjur=<?= $kelasjur; ?>"><i
                                    class="fas fa-angle-double-left"></i>
                                <span>Bulan Sebelumnya</span>
                            </a>
                            <h4 class="mt-2"><b><?= $nama_bulan_indo_pilih . ' </b> ' . $tahun_pilih; ?></h4>
                            <a class="nav-link elevation-2 bg-gradient-light bg-light<?= $disable; ?>"
                                style="border-radius: 5px;"
                                href="<?= $bulan_tahun_pilihplus; ?>&kelasjur=<?= $kelasjur; ?>">
                                <span>Bulan Berikutnya</span>
                                <i class="fas fa-angle-double-right"></i></a>
                        </div>
                    </div>
                </div>

                <div id="header_rekap_pilih" class="card" style="margin-top: -20px; z-index: 1;">
                    <ul class="pagination pagination-sm pagination-month"
                        style="justify-content: center; margin: auto;">

                        <?php
                        $bul = 1;
                        $int_bul_ini = (int) (date('m', strtotime($tanggal)));
                        for ($i = 0; $i < 12; $i++) {
                            $bulan_short_indo = bulansingkatindo(date('F', strtotime($tahun_pilih . '-' . $bul)));

                            $bul_pil = bulansingkatindo(date('F', strtotime($bulan_tahun_pilih)));
                            $int_bul_pil = (int) (date('m', strtotime($tahun_pilih . '-' . $bul)));

                            if ($bul_pil == $bulan_short_indo) {
                                $active = 'active';
                            } else {
                                $active = '';
                            }

                            if ($int_bul_ini < $int_bul_pil && $tahun == $tahun_pilih) {
                                $disable_pager = ' disabled';
                            } else {
                                $disable_pager = '';
                                $bulan_short_indo = '<b>' . $bulan_short_indo . '</b>';
                            }

                            $bulan_pager = 'rekapbulan.php?bulan=' . $tahun_pilih . '-' . $bul . "&kelasjur=" . $kelasjur;

                            ?>
                            <li class="page-item <?= $active; ?><?= $disable_pager; ?>">
                                <a href="<?= $bulan_pager; ?>" class="page-link elevation-1"
                                    style="font-size: 10px; border-radius: 5px;"><?= $bulan_short_indo; ?></a>
                            </li>
                            <?php
                            $bul++;
                        } ?>
                    </ul>
                </div>

                <div id="pilihkelasrekapsemua" class="col-12"
                    style="display: block; margin-left: auto; margin-right: auto; margin-top: -20px;">
                    <div class="card">
                        <div class="card-body">
                            <div class="btn-group">
                                <a href="?jur=AT&bulan=<?= $bulan_tahun_pilih; ?>&kelas=" type="button"
                                    class="disabled btn btn-success bg-gradient-success">AT</a>
                                <button
                                    class="elevation-3 btn btn-success bg-gradient-success dropdown-toggle dropdown-toggle-split"
                                    type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                    aria-expanded="false"></button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                    <li><a class="dropdown-item disabled text-dark"
                                            href="?jur=AT&kelas=X&bulan=<?= $bulan_tahun_pilih; ?>">Kelas X AT
                                            &nbsp;&nbsp;&raquo;</a>
                                        <ul class="submenu dropdown-menu">
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XAT1&bulan=<?= $bulan_tahun_pilih; ?>"> X AT 1 </a>
                                            </li>
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XAT2&bulan=<?= $bulan_tahun_pilih; ?>"> X AT 2 </a>
                                            </li>
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XAT3&bulan=<?= $bulan_tahun_pilih; ?>"> X AT 3 </a>
                                            </li>
                                            <!-- <li><a class="dropdown-item" href="?kelasjur=XAT4&bulan=<?= $bulan_tahun_pilih; ?>"> X AT 4 </a></li> -->
                                        </ul>
                                    </li>
                                    <li><a class="dropdown-item disabled text-dark"
                                            href="?jur=AT&kelas=XI&bulan=<?= $bulan_tahun_pilih; ?>">Kelas XI AT
                                            &nbsp;&raquo;</a>
                                        <ul class="submenu dropdown-menu">
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XIAT1&bulan=<?= $bulan_tahun_pilih; ?>"> XI AT 1
                                                </a></li>
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XIAT2&bulan=<?= $bulan_tahun_pilih; ?>"> XI AT 2
                                                </a></li>
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XIAT3&bulan=<?= $bulan_tahun_pilih; ?>"> XI AT 3
                                                </a></li>
                                        </ul>
                                    </li>
                                    <li><a class="dropdown-item disabled text-dark"
                                            href="?jur=AT&kelas=XII&bulan=<?= $bulan_tahun_pilih; ?>">Kelas XII AT
                                            &raquo;</a>
                                        <ul class="submenu dropdown-menu">
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XIIAT1&bulan=<?= $bulan_tahun_pilih; ?>"> XII AT 1
                                                </a></li>
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XIIAT2&bulan=<?= $bulan_tahun_pilih; ?>"> XII AT 2
                                                </a></li>
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XIIAT3&bulan=<?= $bulan_tahun_pilih; ?>"> XII AT 3
                                                </a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="btn-group">
                                <a href="?jur=DKV&bulan=<?= $bulan_tahun_pilih; ?>&kelas=" type="button"
                                    class="btn btn-primary bg-gradient-primary disabled">DKV</a>
                                <button
                                    class="elevation-3 btn btn-primary bg-gradient-primary dropdown-toggle dropdown-toggle-split"
                                    type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                    aria-expanded="false"></button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                    <li><a class="dropdown-item disabled text-dark"
                                            href="?jur=DKV&kelas=X&bulan=<?= $bulan_tahun_pilih; ?>">Kelas X
                                            DKV&nbsp;&nbsp;&nbsp;&raquo;</a>
                                        <ul class="submenu dropdown-menu">
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XDKV1&bulan=<?= $bulan_tahun_pilih; ?>"> X DKV 1
                                                </a></li>
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XDKV2&bulan=<?= $bulan_tahun_pilih; ?>"> X DKV 2
                                                </a></li>
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XDKV3&bulan=<?= $bulan_tahun_pilih; ?>"> X DKV 3
                                                </a></li>
                                            <!-- <li><a class="dropdown-item" href="?kelasjur=XDKV4&bulan=<?= $bulan_tahun_pilih; ?>"> X DKV 4 </a></li> -->
                                        </ul>
                                    </li>
                                    <li><a class="dropdown-item disabled text-dark"
                                            href="?jur=DKV&kelas=XI&bulan=<?= $bulan_tahun_pilih; ?>">Kelas XI
                                            DKV&nbsp;&nbsp;&raquo;</a>
                                        <ul class="submenu dropdown-menu">
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XIDKV1&bulan=<?= $bulan_tahun_pilih; ?>"> XI DKV 1
                                                </a></li>
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XIDKV2&bulan=<?= $bulan_tahun_pilih; ?>"> XI DKV 2
                                                </a></li>
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XIDKV3&bulan=<?= $bulan_tahun_pilih; ?>"> XI DKV 3
                                                </a></li>
                                        </ul>
                                    </li>
                                    <li><a class="dropdown-item disabled text-dark"
                                            href="?jur=DKV&kelas=XII&bulan=<?= $bulan_tahun_pilih; ?>">Kelas XII
                                            DKV&nbsp;&raquo;</a>
                                        <ul class="submenu dropdown-menu">
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XIIDKV1&bulan=<?= $bulan_tahun_pilih; ?>"> XII DKV 1
                                                </a></li>
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XIIDKV2&bulan=<?= $bulan_tahun_pilih; ?>"> XII DKV 2
                                                </a></li>
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XIIDKV3&bulan=<?= $bulan_tahun_pilih; ?>"> XII DKV 3
                                                </a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="btn-group">
                                <a href="?jur=TE&bulan=<?= $bulan_tahun_pilih; ?>&kelas=" type="button"
                                    class="btn btn-warning bg-gradient-warning disabled">TE</a>
                                <button
                                    class="elevation-3 btn btn-warning bg-gradient-warning dropdown-toggle dropdown-toggle-split"
                                    type="button" data-bs-toggle="dropdown"></button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                    <li><a class="dropdown-item disabled text-dark"
                                            href="?jur=TE&kelas=X&bulan=<?= $bulan_tahun_pilih; ?>">Kelas X
                                            TE&nbsp;&nbsp;&nbsp;&raquo;</a>
                                        <ul class="submenu dropdown-menu">
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XTE1&bulan=<?= $bulan_tahun_pilih; ?>"> X TE 1 </a>
                                            </li>
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XTE2&bulan=<?= $bulan_tahun_pilih; ?>"> X TE 2 </a>
                                            </li>
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XTE3&bulan=<?= $bulan_tahun_pilih; ?>"> X TE 3 </a>
                                            </li>
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XTE4&bulan=<?= $bulan_tahun_pilih; ?>"> X TE 4 </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li><a class="dropdown-item disabled text-dark"
                                            href="?jur=TE&kelas=XI&bulan=<?= $bulan_tahun_pilih; ?>">Kelas XI
                                            TE&nbsp;&nbsp;&raquo;</a>
                                        <ul class="submenu dropdown-menu">
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XITE1&bulan=<?= $bulan_tahun_pilih; ?>"> XI TE 1
                                                </a></li>
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XITE2&bulan=<?= $bulan_tahun_pilih; ?>"> XI TE 2
                                                </a></li>
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XITE3&bulan=<?= $bulan_tahun_pilih; ?>"> XI TE 3
                                                </a></li>
                                        </ul>
                                    </li>
                                    <li><a class="dropdown-item disabled text-dark"
                                            href="?jur=TE&kelas=XII&bulan=<?= $bulan_tahun_pilih; ?>">Kelas XII
                                            TE&nbsp;&raquo; </a>
                                        <ul class="submenu dropdown-menu">
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XIITE1&bulan=<?= $bulan_tahun_pilih; ?>"> XII TE 1
                                                </a></li>
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XIITE2&bulan=<?= $bulan_tahun_pilih; ?>"> XII TE 2
                                                </a></li>
                                            <li><a class="dropdown-item"
                                                    href="?kelasjur=XIITE3&bulan=<?= $bulan_tahun_pilih; ?>"> XII TE 3
                                                </a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <a href=""
                                    class="btn btn-dark bg-gradient-dark <?= $elevation3_smw; ?> no-border<?= $bbtn_kls_smw; ?>">
                                    <i class="fas fa-retweet"></i>
                                    <span>
                                        &nbsp;Refresh
                                    </span>
                                </a>
                            </div>
                        </div>
                        <?php if (!@$_GET['kelasjur']) { ?>
                            <div id="" class="form-text text-center mb-3 mt-0"><span
                                    class="text-danger m-0">*</span>&nbsp;Pilih Kelas dan Bulan untuk ditampilkan.</div>
                        <?php } ?>
                    </div>
                </div>

            </div>
        </div>

        <?php if (@$_GET['kelasjur'] || $akses_login == "Wali Kelas") { ?>
            <div class="row">
                <div class="col-12">
                    <div class="card bg-gradient-primary" style="z-index: 1;">
                        <div class="d-flex justify-content-end m-2">
                            <a target="_blank"
                                href="print_rekapbulan.php?kelasjur=<?= $kelasjur; ?>&bulan=<?= $bulan_tahun_pilih; ?>&a=print"
                                class="btn rounded-0 btn-dark"><i class="fas fa-print"></i>&nbsp;Print</a>
                            <a target="_blank"
                                href="print_rekapbulan.php?kelasjur=<?= $kelasjur; ?>&bulan=<?= $bulan_tahun_pilih; ?>&a=excel"
                                class="btn rounded-0 btn-success"><i class="fas fa-file-excel"></i>&nbsp;Excel</a>
                        </div>
                    </div>
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
                        </table>

                        <?php
                        $tanggal = "$tahun_pilih-$bulan_pilih-1";
                        $jumlah_hari_kerja = hitung_hari_kerja($tanggal);
                        $jumlah_hari = hitung_hari($tanggal);
                        ?>

                        <style>
                            .perharipertanggal td {
                                display: table-cell;
                                vertical-align: middle;
                                text-align: center;
                            }

                            .scrollable-table {
                                width: 100%;
                                height: 600px;
                                border-color: black;
                                /* Tinggi tabel yang bisa digulir */
                                overflow-y: auto;
                                /* Aktifkan scroll vertikal jika isi melebihi tinggi tabel */
                            }

                            .scrollable-table thead {
                                position: sticky;
                                border: 1px;
                                border-color: black;
                                text-align: center;
                                background-color: white;
                                color: black;
                                /* Warna latar belakang untuk baris judul */
                                top: 0;
                                /* Tetap di bagian atas saat tabel digulir */
                                z-index: 2;
                                /* Pastikan di atas konten lain */
                            }

                            table .freez1 {
                                /* background-color: cornflowerblue; */
                                background-color: white;
                                color: black;
                                position: sticky;
                                left: 0;
                                z-index: 1;
                            }
                        </style>

                        <table id="" class="table-bordered table-striped table table-responsive scrollable-table">
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
                                            echo "<th class='bg-$bg_col'>$i<br>$hari_singkat</th>";
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
                                            <a href="rekappersiswa.php?n=<?= $datasiswa[$j]['nick']; ?>&bulan=<?= $bulan_ini; ?>"
                                                class="text-decoration-none">
                                                <?= $nama_datasiswa; ?>
                                            </a>
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
                                                    $keterangan = "<span class='badge bg-danger'>A</span>";
                                                    $alpa_A++;
                                                } else {
                                                    $keterangan = "<span class='badge $bg_masuk'>" . ($keterangan_masuk ? $keterangan_masuk : "-") . "</span><span class='badge $bg_pulang'>" . ($keterangan_pulang ? $keterangan_pulang : "-") . "</span>";
                                                }
                                                ?>

                                                <td id="setketerangan<?= $nis_datasiswa; ?><?= $tanggal_loop; ?>">
                                                    <a type="button"
                                                        onclick="updateModal('<?= $nis_datasiswa; ?>', '<?= $nama_datasiswa; ?>', '<?= $kelas_datasiswa; ?>', '<?= $tanggal_loop; ?>', '<?= $keterangan_masuk; ?>', '<?= $keterangan_pulang; ?>');"
                                                        class="text-decoration-none" data-bs-toggle="modal"
                                                        data-bs-target="#editdetail">
                                                        <?= $keterangan; ?>
                                                    </a>
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
                            </tbody>
                        </table>
                    </div>

                    <table>
                        <tr>
                            <td>&nbsp;</td>
                            <td>Total Masuk</td>
                            <td>&nbsp;:&nbsp;</td>
                            <td><?= $total_masuk; ?></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>Total Apla</td>
                            <td>&nbsp;:&nbsp;</td>
                            <td><?= $total_alpa; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        <?php } else { ?>
            <h6>- Tidak ada kelas yang ditampilkan -<br>
                - Silakah Pilih Kelas -</h6>
        <?php } ?>
    </div>
</section>
<!-- Page specific script -->
<!-- overlayScrollbars -->
<?php include('views/footer.php'); ?>

<script>
    function formatTanggal(tanggal) {
        // Mendapatkan nama hari dalam bahasa Indonesia
        var hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
        // Mendapatkan nama bulan dalam bahasa Indonesia
        var bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        var tanggalObj = new Date(tanggal);
        var namaHari = hari[tanggalObj.getDay()];
        var tanggalBulan = tanggalObj.getDate();
        var namaBulan = bulan[tanggalObj.getMonth()];
        var tahun = tanggalObj.getFullYear();

        return namaHari + ', ' + tanggalBulan + ' ' + namaBulan + ' ' + tahun;
    }

    function updateModal(_nis, _nama, _kelas, _tgl, _ketmasuk, _ketpulang) {
        // alert(_nis + "\n" + _nama + "\n" + _tgl + "\n" + _ketmasuk + "\n" + _ketpulang);
        document.getElementById('modal-nis').value = _nis;
        document.getElementById('modal-nama').value = _nama;
        document.getElementById('modal-kelas').value = _kelas;
        document.getElementById('modal-tanggal').value = _tgl;
        // document.getElementById('modal-keteranganmasuk').value = _ketmasuk;
        // document.getElementById('modal-keteranganpulang').value = _ketpulang;

        var tanggalYangDiubah = formatTanggal(_tgl);

        // document.getElementById('title-detail').textContent = _nama;
        document.getElementById('title-detail2').textContent = tanggalYangDiubah;
        // document.getElementById('title-detail3').textContent = _kelas;

        // Mengatur nilai pada select keterangan masuk
        var selectKeteranganMasuk = document.getElementById('modal-keteranganmasuk');
        var options = selectKeteranganMasuk.options;
        if (_ketmasuk === '') {
            selectKeteranganMasuk.selectedIndex = 0;
        } else {
            for (var i = 0; i < options.length; i++) {
                if (options[i].value === _ketmasuk) {
                    selectKeteranganMasuk.selectedIndex = i;
                    break;
                }
            }
        }

        // Mengatur nilai pada select keterangan pulang
        var selectKeteranganpulang = document.getElementById('modal-keteranganpulang');
        var options = selectKeteranganpulang.options;
        if (_ketpulang === '') {
            selectKeteranganpulang.selectedIndex = 0;
        } else {
            for (var i = 0; i < options.length; i++) {
                if (options[i].value === _ketpulang) {
                    selectKeteranganpulang.selectedIndex = i;
                    break;
                }
            }
        }
    }
</script>
<!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script> -->
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<script src="plugins/jquery/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        $('#updateDetailPresensi-btn').click(function (event) {
            event.preventDefault();

            // Mengambil nilai dari input dan select di dalam modal
            var nis = $('#modal-nis').val();
            var nama = $('#modal-nama').val();
            var tanggal = $('#modal-tanggal').val();
            var keteranganMasuk = $('#modal-keteranganmasuk').val();
            var keteranganPulang = $('#modal-keteranganpulang').val();

            // Objek data yang akan dikirimkan ke server
            var data = {
                nis: nis,
                nama: nama,
                tanggal: tanggal,
                keteranganMasuk: keteranganMasuk,
                keteranganPulang: keteranganPulang
            };

            // Kirim data ke server menggunakan Ajax
            $.ajax({
                type: 'POST',
                url: 'app/proses_update_detail_presensi.php', // Ganti dengan URL Anda
                data: data,
                success: function (response) {
                    $('#editdetail').modal('hide');
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                    $('#editdetail').modal('hide');
                }
            });
        });
    });
</script>



<!-- Modal -->
<div class="modal fade" id="editdetail" tabindex="-1" aria-labelledby="editdetailLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editdetailLabel">Detail Presensi</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table>
                    <!-- <tr>
                        <td>Nama</td>
                        <td>&nbsp;:&nbsp;</td>
                        <td id="title-detail"></td>
                    </tr>
                    <tr>
                        <td>Kelas</td>
                        <td>&nbsp;:&nbsp;</td>
                        <td id="title-detail3"></td>
                    </tr> -->
                    <tr>
                        <td>Hari, Tanggal</td>
                        <td>&nbsp;:&nbsp;</td>
                        <td id="title-detail2"></td>
                    </tr>
                </table>
                <hr>
                <form action="" method="post">
                    <div class="mb-2">
                        <label for="modal-nis" class="form-label">NIS&nbsp;<b class="text-danger">*</b></label>
                        <input type="text" id="modal-nis" class="form-control" readonly>
                    </div>
                    <div class="mb-2">
                        <label for="modal-nama" class="form-label">Nama&nbsp;<b class="text-danger">*</b></label>
                        <input type="text" id="modal-nama" class="form-control" readonly>
                    </div>
                    <div class="mb-2">
                        <label for="modal-kelas" class="form-label">Kelas&nbsp;<b class="text-danger">*</b></label>
                        <input type="text" id="modal-kelas" class="form-control" readonly>
                    </div>
                    <div class="mb-2">
                        <label for="modal-tanggal" class="form-label">Tanggal&nbsp;<b class="text-danger">*</b></label>
                        <input type="date" id="modal-tanggal" class="form-control" readonly>
                    </div>
                    <div class="mb-2">
                        <label for="modal-keteranganmasuk" class="form-label">Keterangan Masuk&nbsp;<b
                                class="text-danger">*</b></label>
                        <!-- <input type="text" id="modal-keteranganmasuk" class="form-control"> -->
                        <select id="modal-keteranganmasuk" class="form-select" aria-label="Default select example"
                            <?= (($akses_login == "Wali Kelas") || ($akses_login == "admin")) ? "" : "disabled"; ?>>
                            <option selected value="-">- Tanpa Keterangan</option>
                            <option value="MSK">MSK - Masuk</option>
                            <option value="TLT">TLT - Terlambat Masuk</option>
                            <option value="I">I - Ijin</option>
                            <option value="S">S - Sakit</option>
                            <option value="TPM">TPM - Tidak Presensi Masuk</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label for="modal-keteranganpulang" class="form-label">Keterangan Pulang&nbsp;<b
                                class="text-danger">*</b></label>
                        <!-- <input type="text" id="modal-keteranganpulang" class="form-control"> -->
                        <select id="modal-keteranganpulang" class="form-select" aria-label="Default select example"
                            <?= (($akses_login == "Wali Kelas") || ($akses_login == "admin")) ? "" : "disabled"; ?>>
                            <option value="-" selected>- Tanpa Keterangan</option>
                            <option value="PLG">PLG - Pulang</option>
                            <option value="PA">PA - Pulang Awal</option>
                            <option value="I">I - Ijin</option>
                            <option value="S">S - Sakit</option>
                            <option value="TPP">TPP - Tidak Presensi Pulang</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <?php if (@$akses_login == "Wali Kelas" || @$akses_login == "admin") { ?>
                            <button type="submit" class="btn btn-primary" id="updateDetailPresensi-btn">Update</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <?php } ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Drop DOWN -->
<link href="plugins/jquery-ui-1.11.4/smoothness/jquery-ui.css" rel="stylesheet" />
<!-- <script src="plugins/jquery-ui-1.11.4/external/jquery/jquery.js"></script> -->
<!-- <script src="plugins/jquery-ui-1.11.4/jquery-ui.js"></script> -->
<script src="plugins/jquery-ui-1.11.4/jquery-ui.min.js"></script>
<link rel="stylesheet" href="plugins/jquery-ui-1.11.4/jquery-ui.theme.css">
<script>
    $(document).ready(function () {
        $("#tanggal").datepicker({})
    })
</script>

<style type="text/css">
    #waicon_01 {
        color: #fff;
        /* background-color: lime; */
        padding: 5px 7px;
        border-radius: 50% 50% 50% 10%;
        background: linear-gradient(to bottom, #55e36f, #1ea237);
        color: white;
    }

    /* ============ desktop view ============ */
    @media all and (min-width: 992px) {

        .dropdown-menu li {
            position: relative;
        }

        .dropdown-menu .submenu {
            display: none;
            position: absolute;
            left: 100%;
            top: -7px;
        }

        .dropdown-menu>li:hover {
            background-color: #f1f1f1
        }

        .dropdown-menu>li:hover>.submenu {
            display: block;
        }
    }

    /* ============ desktop view .end// ============ */

    /* ============ small devices ============ */
    @media (max-width: 991px) {

        .dropdown-menu .dropdown-menu {
            margin-left: 0rem;
            margin-right: 0rem;
            margin-bottom: .5rem;
        }

    }

    /* ============ small devices .end// ============ */
</style>


<script type="text/javascript">
    //	window.addEventListener("resize", function() {
    //		"use strict"; window.location.reload(); 
    //	});


    document.addEventListener("DOMContentLoaded", function () {


        /////// Prevent closing from click inside dropdown
        document.querySelectorAll('.dropdown-menu').forEach(function (element) {
            element.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        })



        // make it as accordion for smaller screens
        if (window.innerWidth < 992) {

            // close all inner dropdowns when parent is closed
            document.querySelectorAll('.navbar .dropdown').forEach(function (everydropdown) {
                everydropdown.addEventListener('hidden.bs.dropdown', function () {
                    // after dropdown is hidden, then find all submenus
                    this.querySelectorAll('.submenu').forEach(function (everysubmenu) {
                        // hide every submenu as well
                        everysubmenu.style.display = 'none';
                    });
                })
            });

            document.querySelectorAll('.dropdown-menu a').forEach(function (element) {
                element.addEventListener('click', function (e) {

                    let nextEl = this.nextElementSibling;
                    if (nextEl && nextEl.classList.contains('submenu')) {
                        // prevent opening link if link needs to open dropdown
                        e.preventDefault();
                        console.log(nextEl);
                        if (nextEl.style.display == 'block') {
                            nextEl.style.display = 'none';
                        } else {
                            nextEl.style.display = 'block';
                        }

                    }
                });
            })
        }
        // end if innerWidth

    });
    // DOMContentLoaded  end
</script>
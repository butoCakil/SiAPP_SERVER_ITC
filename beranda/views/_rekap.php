<?php

$nokartu_login = @$_SESSION['nokartu_login'] ? $_SESSION['nokartu_login'] : '';

if ($bulan_tahun_pilihplus == '#') {
    $bulan_tahun_pilihplus = '#';
    $disable = ' disabled btn btn-secondary';
    $ket = 'masuk disable';
} else {
    $bulan_tahun_pilihplus = 'rekap.php?bulan=' . $bulan_tahun_pilihplus;
    $disable = '';
    $ket = 'tidak masuk disable';
}

$title = $title . ' Bulan ' . $nama_bulan_indo_pilih . ' ' . $tahun_pilih;

?>

<?php include('views/header.php'); ?>
<!-- overlayScrollbars -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div id="header_rekap" class="card bg-gradient-primary" style="z-index: 1;">
                    <div class="card-body">
                        <div style="display: flex; justify-content: baseline; justify-content: space-between;">
                            <a class="nav-link bg-light bg-gradient-light elevation-2" style="border-radius: 5px;" href="rekap.php?bulan=<?= $bulan_tahun_pilihmin; ?>"><i class="fas fa-angle-double-left"></i>
                                <span>Sebelumnya</span>
                            </a>
                            <h4 class="mt-2"><b><?= $nama_bulan_indo_pilih . ' </b> ' . $tahun_pilih; ?></h4>
                            <a class="nav-link elevation-2 bg-gradient-light bg-light<?= $disable; ?>" style="border-radius: 5px;" href="<?= $bulan_tahun_pilihplus; ?>">
                                <span>Berikutnya</span>
                                <i class="fas fa-angle-double-right"></i></a>
                        </div>
                    </div>
                </div>

                <div id="header_rekap_pilih" class="card" style="margin-top: -20px;">
                    <ul class="pagination pagination-sm pagination-month" style="justify-content: center; margin: auto;">

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

                            $bulan_pager = 'rekap.php?bulan=' . $tahun_pilih . '-' . $bul;

                        ?>
                            <li class="page-item <?= $active; ?><?= $disable_pager; ?>">
                                <a href="<?= $bulan_pager; ?>" class="page-link elevation-1" style="font-size: 10px; border-radius: 5px;"><?= $bulan_short_indo; ?></a>
                            </li>
                        <?php
                            $bul++;
                        } ?>
                    </ul>
                </div>
                <?php
                ?>
                <div class="card">
                    <div class="card-body mb-5">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr style="text-align: center;">
                                    <th>Hari, Tgl</th>
                                    <th>Masuk</th>
                                    <th>Status</th>
                                    <th>Pulang</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan_pilih, $tahun_pilih);
                                $deskripsi = '';
                                $harilibur = '';
                                $tgl_Ymd = '';
                                $no = 0;
                                while ($no < $jumlah_hari) {
                                    $no++;
                                    $tanggal_pilih = $bulan_tahun_pilih . '-' . $no;
                                    $tgl_Ymd = date('Ymd', strtotime($tanggal_pilih));

                                    $hari = hariSingkatIndo(date('l', strtotime($tanggal_pilih)));
                                    $tgl = date('d-m-Y', strtotime($tanggal_pilih));

                                    $deskripsi = tanggalMerah($tgl_Ymd);

                                    if ($harikerja == '5') {
                                        $harilibur = limaharikerja($tanggal_pilih);
                                    } elseif ($harikerja == '6') {
                                        $harilibur = enamharikerja($tanggal_pilih);
                                    }

                                    if ($harilibur == false && !$deskripsi) {

                                        $sql = "SELECT * FROM datapresensi WHERE tanggal = '$tanggal_pilih' AND nokartu = '$nokartu_login'";
                                        $query = mysqli_query($konek, $sql);
                                        $cek = mysqli_num_rows($query);
                                        if ($cek > 0) {
                                            $data = mysqli_fetch_array($query);
                                            $kode = $data['kode'];
                                            $nama = $data['nama'];
                                            $nokartu = $data['nokartu'];
                                            $foto = $data['foto'];
                                            $foto = $data['info'];
                                            $waktu_masuk = $data['waktumasuk'];
                                            $status_masuk = $data['ketmasuk'];
                                            $selisih_masuk = $data['a_time'];
                                            $waktu_pulang = $data['waktupulang'];
                                            $status_pulang = $data['ketpulang'];
                                            $selisih_pulang = $data['b_time'];
                                            $keterangan = $data['keterangan'];
                                            $tanggal_P = $data['tanggal'];
                                        } else {
                                            $kode = '-';
                                            $nama = '-';
                                            $nokartu = '-';
                                            $foto = '-';
                                            $foto = '-';
                                            $waktu_masuk = '-';
                                            $status_masuk = 'Tanpa Keterangan';
                                            $selisih_masuk = '';
                                            $waktu_pulang = '-';
                                            $status_pulang = 'Tanpa Keterangan';
                                            $selisih_pulang = '';
                                            $keterangan = '-';
                                            $tanggal_P = '-';
                                        }


                                        if ($status_masuk == 'MSK') {
                                            $status_masuk = '<span class="badge badge-success">' . $status_masuk . '</span> <span class="badge badge-success">' . $selisih_masuk . '</span>';
                                        } elseif ($status_masuk == 'TLT') {
                                            $status_masuk = '<span class="badge badge-warning">' . $status_masuk . '</span> <span class="badge badge-warning">' . $selisih_masuk . '</span>';
                                        } else {
                                            $status_masuk = '<span class="badge badge-danger">' . $status_masuk . '</span> <span class="badge badge-danger">' . $selisih_masuk . '</span>';
                                        }

                                        if ($status_pulang == 'PLG') {
                                            $status_pulang = '<span class="badge badge-success">' . $status_pulang . '</span> <span class="badge badge-success">' . $selisih_pulang . '</span>';
                                        } elseif ($status_pulang == "PA") {
                                            $status_pulang = '<span class="badge badge-warning">' . $status_pulang . '</span> <span class="badge badge-warning">' . $selisih_pulang . '</span>';
                                        } else {
                                            $status_pulang = '<span class="badge badge-danger">' . $status_pulang . '</span> <span class="badge badge-danger">' . $selisih_pulang . '</span>';
                                        }

                                        if ($keterangan != '-' && $keterangan != '') {
                                            $keterangan = '<span class="badge badge-info">' . $keterangan . '</span>';
                                            $tanda_libur = 'class="bg-info" ';
                                        } else {
                                            $tanda_libur = '';
                                        }

                                        $tanda_libur = $tanda_libur . 'style="text-align: center;"';
                                        $tanda_libur_2 = '';
                                        $colspan_libur = '';
                                        $disable_libur = '';
                                    } else {
                                        $tanda_libur = 'class="bg-danger" style="text-align: center;"';
                                        $tanda_libur_2 = 'style="text-align: left;"';
                                        $colspan_libur = 'colspan="5"';
                                        $disable_libur = 'class = "d-none"';
                                        if ($deskripsi) {
                                            $tanda_libur = 'class="bg-warning" style="text-align: center;"';
                                            $tanda_libur_2 = 'style="text-align: left;"';
                                            $colspan_libur = 'colspan="5"';
                                            $disable_libur = 'class = "d-none"';
                                        }

                                        $waktu_masuk = $deskripsi;
                                        $status_masuk = '';
                                        $selisih_masuk = '';
                                        $waktu_pulang = '';
                                        $status_pulang = '';
                                        $selisih_pulang = '';
                                        $keterangan = '';
                                    }
                                ?>
                                    <tr <?= $tanda_libur; ?>>
                                        <td style="text-align: start;"><?= $tgl; ?>, <?= $hari; ?></td>
                                        <td <?= $colspan_libur; ?><?= $tanda_libur_2; ?>><b><?= $waktu_masuk; ?></b></td>
                                        <td <?= $disable_libur; ?>><?= $status_masuk; ?></td>
                                        <td <?= $disable_libur; ?>><b><?= $waktu_pulang; ?></b></td>
                                        <td <?= $disable_libur; ?>><?= $status_pulang; ?></td>
                                        <td <?= $disable_libur; ?>><?= $keterangan; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
<!-- Page specific script -->
<!-- overlayScrollbars -->
<?php include('views/footer.php'); ?>

<script>
    $(function() {
        $("#example1").DataTable({
            dom: 'flBtip',
            "autoWidth": false,
            "responsive": true,
            "lengthChange": true,
            "lengthMenu": [
                [7, 14, 21, -1],
                [7, 14, 21, "Semua"]
            ],
            "pagingType": "full",
            "language": {
                "emptyTable": "Tida ada data untuk tanggal yang dipilih.",
                "info": "Ditampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Ditampilkan 0 sampai 0 dari 0 data",
                "infoFiltered": "(Disaring dari _MAX_ total data)",
                "lengthMenu": "Tampilkan _MENU_ baris data",
                "loadingRecords": "Memuat...",
                "processing": "Memproses...",
                "search": "Cari:",
                "zeroRecords": "Tidak ditemukan data yang sesuai.",
                "paginate": {
                    "first": "<<",
                    "last": ">>",
                    "next": "lanjut >",
                    "previous": "< sebelum"
                },
            },
            "buttons": ["print", "pdf", "excel", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>
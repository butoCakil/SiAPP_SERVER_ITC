<?php
include('app/get_user.php');
$tanggal = date('Y-m-d');
$tanggal_dmY = date('d-m-Y');
$tahun = date('Y');

if (@$_GET['data'] == 'detail') {

    include("../config/konesi.php");
    $nick_guru = @$_GET['nick'];
    $nick_guru = mysqli_real_escape_string($konek, $nick_guru);

    $qu_data_detail_guru = mysqli_query($konek, "SELECT * FROM dataguru WHERE nick = '$nick_guru'");
    $jumlah_data_detail_guru = 0;
    $data_detail_guru = array();
    while ($hasil_data_detail_guru = mysqli_fetch_array($qu_data_detail_guru)) {
        $jumlah_data_detail_guru++;
        $data_detail_guru[] = $hasil_data_detail_guru;
    }

    // echo "Db jurnal";
    // echo "<br>";
    // echo "<pre>";
    // print_r($data_jurnal);
    // echo "</pre>";

    // ----------------------
    $title = 'Detail Guru';
    $navlink = 'Data Guru';
    $navlink_sub = 'dataguru';

    include('views/header.php');
    mysqli_close($konek);
?>
    <!-- Begin Content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card elevation-3">
                <div class="card-header border-transparent bg-gradient-primary">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i>&nbsp;
                        Detail Data Guru&nbsp;
                    </h3>

                    <div class="card-tools">
                        <i id="tooltip" class="fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Menampilkan Detail info Guru"></i>
                        <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times text-light"></i>
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0" style="max-height: 700px; overflow: auto;">
                    <button class="btn btn-sm btn-dark shadow bg-gradient-dark border-0 m-3" onclick="history.go(-1);"><i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Kembali</button>

                    <div class="card m-5">
                        <div class="row g-0">
                            <div class="col-md-4">
                                <img src="../img/user/<?= $data_detail_guru[0]['foto']; ?>" class="img-fluid rounded-start" style="width: 100%; height: 300px; object-fit: cover; object-position: center;">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <h5 class="card-title fw-bold"><?= $data_detail_guru[0]['nama']; ?></h5>
                                    <br>
                                    <hr>
                                    <p class="card-text">
                                        <?= $data_detail_guru[0]['jabatan']; ?>
                                    </p>
                                    <?php
                                    if ($data_detail_guru[0]['jabatan'] == "GURU" || $data_detail_guru[0]['jabatan'] == "TENDIK") {
                                    } else {
                                    ?>
                                        <div class="row">
                                            <div class="col-lg-3 col-6">
                                                <!-- small box -->
                                                <div class="small-box bg-info bg-gradient-info elevation-3">
                                                    <div class="inner">
                                                        <h4><?= @$data_user['poin'] ? $data_user['poin'] : '0'; ?></h4>
                                                        <p>Poin Presensi</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="ion ion-stats-bars"></i>
                                                    </div>
                                                    <a href="profil.php" class="small-box-footer">Info lengkap <i class="fas fa-arrow-circle-right"></i></a>
                                                </div>
                                            </div>
                                            <!-- ./col -->
                                            <div class="col-lg-3 col-6">
                                                <!-- small box -->
                                                <div class="small-box bg-success bg-gradient-success elevation-3">
                                                    <div class="inner">
                                                        <h4>99<sup style="font-size: 20px">%</sup></h4>
                                                        <p>% Kehadiran</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="ion ion-pie-graph"></i>
                                                    </div>
                                                    <a href="rekaptahun.php" class="small-box-footer">Info lengkap <i class="fas fa-arrow-circle-right"></i></a>
                                                </div>
                                            </div>
                                            <!-- ./col -->
                                            <div class="col-lg-3 col-6">
                                                <!-- small box -->
                                                <div class="small-box bg-warning bg-gradient-warning elevation-3">
                                                    <div class="inner">
                                                        <h4><?= @$data_user['t_waktu_telat'] ? $data_user['t_waktu_telat'] : '0'; ?></h4>
                                                        <p>
                                                            &sum;&nbsp;
                                                            Keterlambatan</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="ion ion-clock"></i>
                                                    </div>
                                                    <a href="profil.php" class="small-box-footer">Info lengkap <i class="fas fa-arrow-circle-right"></i></a>
                                                </div>
                                            </div>
                                            <!-- ./col -->
                                            <div class="col-lg-3 col-6">
                                                <!-- small box -->
                                                <div class="small-box bg-danger bg-gradient-danger elevation-3">
                                                    <div class="inner">
                                                        <h4>1</h4>
                                                        <p>Jumlah Alpha</p>
                                                    </div>
                                                    <div class="icon">
                                                        <i class="ion ion-calculator"></i>
                                                    </div>
                                                    <a href="rekaptahun.php" class="small-box-footer">Info lengkap <i class="fas fa-arrow-circle-right"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- EndContent -->
<?php
    include('views/footer.php');
} else {
    $_SESSION['pesan'] = 'Anda tidak memiliki akses ke halaman ini. Maaf! <br> Cek kembali URL / lokasi halaman <br>atau<br>kembali ke Home / Beranda. <i><br>(Kode: #JG101)</i>';
    include("404.php");
} ?>
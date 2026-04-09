<?php include('app/get_user.php'); ?>

<?php
if (@$_SESSION['level_login'] == 'superadmin') {
    echo "<script>window.location.href = 'statistik.php';</script>";
}
?>

<?php $title = 'Beranda'; ?>
<?php $navlink = 'Beranda'; ?>
<?php include 'views/header.php'; ?>


<script src="https://code.jquery.com/jquery-3.6.0.slim.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $("#list_hadir_guru").load("views/_daftargtk_hadir.php");
        setInterval(function() {
            $("#list_hadir_guru").load("views/_daftargtk_hadir.php");
        }, 1000 * 60);
    });
</script>


<?php

if ($datab_login == 'dataguru') {
    $datab = 'gtk';
} elseif ($datab_login == 'datasiswa') {
    $datab = 'siswa';
}

$sql = "SELECT * FROM datapresensi WHERE nokartu = '$nokartu_login' ORDER BY updated_at DESC";
$query = mysqli_query($konek, $sql);
?>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <div id="dashboard_header" class="row">
            <div class="col-12">
                <div class="card bg-gradient-primary elevation-3 border-0">
                    <div class="card-body">
                        <!-- <a href="../" class="image" style="text-decoration: none;">
                            <img src="../img/app/rfid-unscreen.gif" class="img-fluid elevation-0" style="height: 50px; width: 50px; border-radius: 100%;">
                        </a> -->

                        <!-- &nbsp;&nbsp;&nbsp;
                        <a id="button_dash_pjj" href="../wfh.php?data=<?= $datab; ?>&nick=<?= @$_SESSION['nick_login']; ?>" style="text-decoration: none;">
                            <button class="button_dash_01 btn btn-success bg-gradient-success elevation-2 mt-1">
                                <i class="fas fa-user-circle fa-1x"></i>&nbsp;
                                PJJ
                            </button>
                        </a>
                        &nbsp;&nbsp;&nbsp;
                        <a href="../formijin.php?data=<?= $datab; ?>&nick=<?= @$_SESSION['nick_login']; ?>">
                            <button class="button_dash_01 btn btn-primary bg-gradient-dark elevation-2 mt-1">
                                <i class="fas fa-pager fa-1x"></i>&nbsp;
                                IJIN
                            </button>
                        </a> -->
                        <span class="float-right">
                            <button id="dashboard_001" class="btn btn-dark bg-gradient-dark elevation-2 mr-3" style="border-radius: 50px; margin-top: 5px; border: none;">
                                &nbsp;
                                <i class="fas fa-compass fa-spin text-info" style="margin-left: 0px; z-index: 3;"></i>&nbsp;
                                <i class="fas fa-stroopwafel fa-spin text-warning" style="margin-left: -15px; z-index: 1;"></i>&nbsp;
                                <i class="fas fa-circle-notch fa-spin text-info" style="margin-left: -15px; z-index: 0;"></i>&nbsp;
                                <i class="fas fa-circle-notch fa-spin text-danger" style="margin-left: -15px; z-index: 1;"></i>&nbsp;
                                <i class="fas fa-circle-notch fa-spin text-warning" style="margin-left: -15px; z-index: 2;"></i>&nbsp;
                                <i class="fas fa-circle-notch fa-spin text-success" style="margin-left: -15px; z-index: 3;"></i>&nbsp;
                                <i class="fas fa-cog fa-spin text-light" style="margin-left: -15px; z-index: 4;"></i>&nbsp;
                            </button>
                            <button class="btn btn-danger bg-gradient-danger elevation-2" data-bs-toggle="modal" data-bs-target="#logout" style="border-radius: 50px; margin-top: 5px; border: none;">
                                <i class="fas fa-sign-out-alt fa-1x"></i>&nbsp;
                                Logout
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($datab_login == 'datasiswa') { ?>
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-6">

                    <!-- small box -->
                    <div class="small-box bg-info bg-gradient-info elevation-3">
                        <div class="inner">
                            <h3><?= @$data_user['poin'] ? $data_user['poin'] : '0'; ?></h3>
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
                            <h3>99<sup style="font-size: 20px">%</sup></h3>
                            <p>Persentase Kehadiran</p>
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
                            <h3><?= @$data_user['t_waktu_telat'] ? $data_user['t_waktu_telat'] : '0'; ?></h3>
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
                            <h3>1</h3>
                            <p>Jumlah Alpha</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-calculator"></i>
                        </div>
                        <a href="rekaptahun.php" class="small-box-footer">Info lengkap <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        <?php } ?>

        <!-- Main row -->
        <div class="row">
            <!-- Left col -->
            <section class="col-lg-8 connectedSortable">

                <?php if ($datab_login == 'datasiswa') { ?>
                    <!-- TABLE: LATEST ORDERS -->
                    <div class="card elevation-3">


                        <div class="card-header border-transparent bg-gradient-gray-dark">
                            <h3 class="card-title">
                                <i class="fas fa-history"></i>&nbsp;
                                Riwayat Presensi&nbsp;
                                <!-- help -->
                            </h3>

                            <div class="card-tools">
                                <i id="tooltip" class="fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Menampilkan Catatan Presensi"></i>
                                <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <!-- <button type="button" class="btn btn-tool" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                    </button> -->
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body p-0" style="max-height: 700px; overflow: auto;">
                            <div class="table-responsive-lg table-bordered table-striped">
                                <table class="table m-0">
                                    <thead>
                                        <tr>
                                            <th>No. </th>
                                            <th>Hari</th>
                                            <th>Tanggal</th>
                                            <th>Masuk</th>
                                            <th>Status</th>
                                            <th>Pulang</th>
                                            <th>Status</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 0;
                                        while ($data = mysqli_fetch_array($query)) {
                                            $i++;

                                            if (@$data['keterangan']) {
                                                if (@$data['keterangan'] == 'PJJ' || @$data['keterangan'] == 'WFH') {
                                                    $keterangan = '<span class="badge badge-success">' . @$data['keterangan'] . '</span>';
                                                } else {
                                                    $keterangan = '<span class="badge badge-info">' . @$data['keterangan'] . '</span>';
                                                }
                                            }

                                        ?>
                                            <tr>
                                                <td style="width: 5px;"><?= $i; ?></td>
                                                <td><?= hariIndo(date('l', strtotime($data['tanggal']))); ?></td>
                                                <td><?= $data['tanggal']; ?></td>
                                                <?php if ($data['ketmasuk'] == 'TLT') {
                                                    $waktumasuk = '<span class="badge badge-warning">' . $data['waktumasuk'] . '</span>';
                                                    $a_time = '<span class="badge badge-warning">+' . $data['a_time'] . '</span>';
                                                } else if ($data['ketmasuk'] == '') {
                                                    $waktumasuk = '<span class="badge badge-danger"></span>';
                                                    $a_time = '<span class="badge badge-danger"></span>';
                                                } else {
                                                    $waktumasuk = '<span class="badge badge-success">' . $data['waktumasuk'] . '</span>';
                                                    $a_time = '<span class="badge badge-success">-' . $data['a_time'] . '</span>';
                                                } ?>
                                                <td><?= $waktumasuk; ?></td>
                                                <td>
                                                    <?= $a_time; ?>
                                                </td>
                                                <?php
                                                if ($data['ketpulang'] == 'PLG') {
                                                    $waktupulang = '<span class="badge badge-success">' . $data['waktupulang'] . '</span>';
                                                    $b_time = '<span class="badge badge-success">+' . $data['b_time'] . '</span>';
                                                } else if ($data['ketpulang'] == '') {
                                                    $waktupulang = '<span class="badge badge-danger"></span>';
                                                    $b_time = '<span class="badge badge-danger"></span>';
                                                } else {
                                                    $waktupulang = '<span class="badge badge-warning">' . $data['waktupulang'] . '</span>';
                                                    $b_time = '<span class="badge badge-warning">-' . $data['b_time'] . '</span>';
                                                }
                                                ?>
                                                <td><?= $waktupulang; ?></td>
                                                <td>
                                                    <?= $b_time; ?>
                                                </td>


                                                <td><?= @$keterangan; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>


                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.card-body -->
                        <div id="footer_riwayat" class="card-footer clearfix">
                            <div>
                                <span class="badge badge-success">
                                    Hadir
                                </span>
                                <span class="badge badge-warning">
                                    Terlambat
                                </span>
                                <span class="badge badge-danger">
                                    Tidak Hadir
                                </span>
                                <span class="badge badge-info">
                                    Izin
                                </span>
                            </div>
                            <div>
                                <!-- <a href="javascript:void(0)" class="btn btn-sm btn-info float-left">Place New Order</a> -->
                                <a href="rekap.php" class="btn btn-sm btn-outline-dark float-right">Semua Rekap</a>
                            </div>
                        </div>
                        <!-- /.card-footer -->
                    </div>
                <?php } ?>

                <!-- Calendar -->
                <div class="card elevation-3">
                    <div class="card-header border-0 bg-gradient-dark">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">
                                <i class="fas fa-calendar-alt"></i>&nbsp;
                                Kalender
                            </h3>
                            <div class="card-tools">
                                <i class="fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Kalender Masehi, Hijiriah dan Jawa. Kalender ini juga menampilkan hari kerja dan hari libur serta status presensi anda setiap hari"></i>
                                <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <!-- <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button> -->
                            </div>
                        </div>
                    </div>

                    <!-- ambil info jadwal guru -->
                    <?php
                    $sql_jdwalguru = mysqli_query($konek, "SELECT * FROM jadwalkbm WHERE nick = '$nick_login'");
                    $data_jadwalguru = array();
                    while ($hasil_jadwalguru = mysqli_fetch_array($sql_jdwalguru)) {
                        $data_jadwalguru[] = $hasil_jadwalguru;
                    }

                    $sql_jurnalguru = mysqli_query($konek, "SELECT * FROM jurnalguru WHERE nick = '$nick_login'");
                    $data_jurnalguru = array();
                    while ($hasil_jurnalguru = mysqli_fetch_array($sql_jurnalguru)) {
                        $data_jurnalguru[] = $hasil_jurnalguru;
                    }

                    // echo "<pre>";
                    // print_r($data_jadwalguru);
                    // echo "</pre>";
                    ?>

                    <div class="card-body">
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <p>
                                Kalender ini menampilkan informasi disetiap tanggalnya. Informasi Jadwal kelas saya, info Hari libur nasional sampai informasi kehadiran. <br>
                                <i class="fas fa-info-circle mt-3" style="cursor: pointer;"></i>&nbsp;Tips:
                                <li class="mt-n3">
                                    Klik tanggal di kalender untuk melihat informasi di tanggal tersebut.
                                </li>
                                <li>
                                    Klik Tombol panah kanan atau kiri untuk mengganti tampilan bulan sebelumnya atau bulan berikutnya.
                                </li>
                            </p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php include 'views/_kalender.php'; ?>
                    </div>

                    <!-- go to kalender untuk halaman ini -->
                    <script>
                        function kalender_goto(tanggal) {
                            window.location.href = '?kalender_=' + tanggal;
                        }
                    </script>

                    <!-- <div class="card-header border-0">

                        <h3 class="card-title">
                            <i class="far fa-calendar-alt"></i>
                            Kalendar
                        </h3>
                        
                        <div class="card-tools">
                            
                            <div class="btn-group">
                                <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" data-offset="-52">
                                    <i class="fas fa-bars"></i>
                                </button>
                                <div class="dropdown-menu" role="menu">
                                    <a href="#" class="dropdown-item">Add new event</a>
                                    <a href="#" class="dropdown-item">Clear events</a>
                                    <div class="dropdown-divider"></div>
                                    <a href="#" class="dropdown-item">View calendar</a>
                                </div>
                            </div>
                            <button type="button" class="btn btn-success btn-sm" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-success btn-sm" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                                </button>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div id="calendar" style="width: 100%"></div>
                    </div> -->

                </div>
                <!-- /.card -->
            </section>
            <!-- /.Left col -->
            <!-- right col (We are only adding the ID to make the widgets sortable)-->
            <section class="col-lg-4 connectedSortable">
                <?php if (@$_SESSION['akses'] == 'Wali Kelas' || @$_SESSION['akses'] == 'BK') { ?>
                    <div class="card elevation-3">
                        <div class="card-header border-0 bg-gradient-dark">
                            <h3 class="card-title">
                                <i class="far fa-chart-bar"></i>&nbsp;
                                Kehadiran Kelas <?= $ket_akses_login; ?>
                            </h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus text-light"></i>
                                </button>
                                <!-- <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button> -->
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="d-flex flex-wrap">
                                <div class="col-6 text-center">
                                    <input type="text" class="knob" value="33" data-skin="tron" data-thickness="0.2" data-width="90" data-height="90" data-angleArc="1000" data-fgColor="#3c8dbc" data-readonly="true">

                                    <div class="knob-label">Hadir</div>
                                </div>

                                <div class="col-6 text-center">
                                    <input type="text" class="knob" value="0" data-skin="tron" data-thickness="0.2" data-angleOffset="0" data-width="90" data-height="90" data-fgColor="#00c0ef">

                                    <div class="knob-label">Izin</div>
                                </div>

                                <div class="col-6 text-center">
                                    <input type="text" class="knob" value="2" data-skin="tron" data-thickness="0.2" data-width="90" data-height="90" data-fgColor="#f56954" data-readonly="true">

                                    <div class="knob-label">Alpa</div>
                                </div>

                                <div class="col-6 text-center">
                                    <input type="text" class="knob" value="94" data-skin="tron" data-thickness="0.2" data-angleArc="250" data-angleOffset="-125" data-width="90" data-height="90" data-fgColor="#00a65a">

                                    <div class="knob-label">% Kehadiran <?= $ket_akses_login; ?></div>
                                </div>

                            </div>
                            <!-- /.row -->
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <a href="harian.php?datab=siswa" class="btn btn-sm btn-outline-dark float-right">Selengkapnya</a>
                        </div>
                    </div>
                    <!-- /.card -->
                <?php } ?>

                <div id="daftarhadirgtk_001" class="card elevation-3">
                    <div class="card-header border-0 bg-gradient-dark">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">
                                <i class="fas fa-sync-alt fa-spin" data-bs-toggle="tooltip" data-bs-placement="top" title="Menampilkan Daftar yang hadir di hari ini secara realtime"></i>&nbsp;
                                Data Hadir Guru
                                &nbsp;-&nbsp;
                                <?php
                                $bulanindo = bulanIndo(date('F'));
                                $hariindo = hariIndo(date('l'));
                                echo $hariindo . ', ' . date('d') . ' ' . $bulanindo . ' ' . date('Y');;
                                ?>
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>

                                <!-- <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button> -->
                            </div>
                        </div>
                    </div>

                    <style>
                        #list_hadir_guru {
                            overflow: auto;
                            max-height: 300px;
                        }
                    </style>

                    <div class="card-body">
                        <div id="list_hadir_guru"></div>
                    </div>
                    <div class="card-footer">
                        <span class="badge badge-success">
                            <i class="fas fa-check"></i>
                        </span>
                        &nbsp;Hadir
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="badge badge-info">
                            <i class="fas fa-info"></i>
                        </span>
                        &nbsp;Ijin
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <span class="badge badge-danger">
                            <i class="fas fa-times"></i>
                        </span>
                        &nbsp;Tidak Hadir
                    </div>
                </div>

                <!-- card start -->
                <!-- card end -->
            </section>
            <!-- right col -->
        </div>
        <!-- /.row (main row) -->
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<?php include 'views/footer.php'; ?>

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
            width: 200,
            height: 50,
            lineColor: '#92c1dc',
            endColor: '#92c1dc'
        })
        var sparkline2 = new Sparkline($('#sparkline-2')[0], {
            width: 200,
            height: 50,
            lineColor: '#f56954',
            endColor: '#f56954'
        })
        var sparkline3 = new Sparkline($('#sparkline-3')[0], {
            width: 200,
            height: 50,
            lineColor: '#3af221',
            endColor: '#3af221'
        })

        sparkline1.draw([1000, 1200, 920, 927, 931, 1027, 819, 930, 1021])
        sparkline2.draw([515, 519, 520, 522, 652, 810, 370, 627, 319, 630, 921])
        sparkline3.draw([15, 19, 20, 22, 33, 27, 31, 27, 19, 30, 21])

    })
</script>
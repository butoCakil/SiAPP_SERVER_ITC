<?php include('app/get_user.php');
$title = 'Jadwal Saya';
$navlink = 'Profil';
$navlink_sub = 'jadwal';
include('views/header.php'); ?>
<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline elevation-3">
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
        </div>
    </div>
</section>
<?php include('views/footer.php'); ?>
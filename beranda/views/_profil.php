<?php

$tentang = explode('#', @$data_user['tentang']);
$tentang_pendidikan = @$tentang[1];
$tentang_alamat = @$tentang[2];
$tentang_hobi = @$tentang[3];
$tentang_notes = @$tentang[4];
$tentang_nomorhp = @$tentang[5];
$tentang_ig = @$tentang[6];
$tentang_fb = @$tentang[7];
$tentang_twitter = @$tentang[8];
$tentang_line = @$tentang[9];
$tentang_wa = @$tentang[10];
$tentang_web = @$tentang[11];
$tentang_youtube = @$tentang[12];
?>

<style>
    a {
        cursor: pointer;
    }
</style>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- /.content -->
        <div class="row">
            <div class="col-md-6 connectedSortable">
                <!-- Profile Image -->
                <div id="profil_kartu" class="card card-primary card-outline elevation-3">
                    <div class="card-body box-profile" id="accordion_1">
                        <div id="btn_setting_profil">
                            <!-- edit -->
                            <a href="editprofil.php" class="btn" style="text-decoration: none; text-shadow: #838383 0px 0px 5px;">
                                <i class="fas fa-cog fa-spin"></i>
                                <h6 style="font-size: 10px;">Edit Profil</h6>
                            </a>
                        </div>
                        <div class="text-center">
                            <a data-bs-toggle="modal" data-bs-target="#tampilfotoprofil">
                                <img class="profile-user-img img-fluid img-circle elevation-1" src="../img/user/<?= @$data_user['foto'] ? @$data_user['foto'] : (@$foto_login ? $foto_login : 'default.jpg'); ?>" alt="User profile picture" style="height: 100px; width: 100px; border-radius: 50%; object-fit: cover; object-position: top;">
                            </a>
                        </div>


                        <div class="text-center">
                            <h3 class="profile-username text-center"><?= @$data_user['nama'] ? @$data_user['nama'] : (@$nama_login ? $nama_login : 'default.jpg'); ?></h3>

                            <p class="text-muted text-center">
                                <?php
                                if ($datab_login == 'dataguru') {
                                ?>
                                    NIP: <?= @$data_user['nip']; ?><br>
                                <?php
                                } else if ($datab_login == 'datasiswa') {
                                ?>
                                    NIS: <?= @$data_user['nis']; ?><br>
                                <?php
                                }
                                ?>

                                <?= ($datab_login == 'datasiswa') ? ('Siswa : ' . $info_login) : $info_login; ?>
                            </p>
                        </div>

                        <!-- social media icon -->
                        <style>
                            #sosmed_icon {
                                display: flex;
                                justify-content: center;
                                align-items: center;
                                flex-wrap: wrap;
                                gap: 10px;
                            }
                        </style>
                        <div id="sosmed_icon" class="text-center">

                            <?php if ($tentang_web) { ?>
                                <!-- wabsite -->
                                <a class="btn bg-dark bg-gradient-dark btn-sm elevation-2" data-toggle="collapse" href="#collapseWEB">
                                    <i class="iconify" data-icon="ion:earth"></i>
                                </a>
                            <?php } ?>

                            <?php if ($tentang_nomorhp) { ?>
                                <!-- telepon -->
                                <a class="btn bg-success bg-gradient-success btn-sm elevation-2" data-toggle="collapse" href="#collapseTelp">
                                    <i class="iconify" data-icon="ion:call"></i>
                                </a>
                            <?php } ?>

                            <?php if ($tentang_fb) { ?>
                                <!-- facebook -->
                                <a class="btn bg-primary bg-gradient-primary btn-sm elevation-2" data-toggle="collapse" href="#collapseFB">
                                    <i class="fab fa-facebook"></i>
                                </a>
                            <?php } ?>

                            <?php if ($tentang_ig) { ?>
                                <!-- Instagram -->
                                <a class="btn bg-danger bg-gradient-danger btn-sm elevation-2" data-toggle="collapse" href="#collapseIG">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            <?php } ?>

                            <?php if ($tentang_twitter) { ?>
                                <!-- twitter -->
                                <a class="btn btn-info btn-sm elevation-2" data-toggle="collapse" href="#collapseTW">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            <?php } ?>

                            <?php if ($tentang_youtube) { ?>
                                <!-- youtube -->
                                <a class="btn btn-danger bg-gradient-danger btn-sm elevation-2" data-toggle="collapse" href="#collapseYT">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            <?php } ?>

                            <!-- email -->
                            <a class="btn btn-warning bg-gradient-warning btn-sm elevation-2" data-toggle="collapse" href="#collapseEMAIL">
                                <i class="fas fa-envelope"></i>
                            </a>

                            <?php if ($tentang_line) { ?>
                                <!-- line -->
                                <a id="sosmed_line" class="btn btn-sm elevation-2" data-toggle="collapse" href="#collapseLINE">
                                    <i class="fab fa-line fa-border-none"></i>
                                </a>
                            <?php } ?>

                            <?php if ($tentang_wa) { ?>
                                <!-- whatsapp -->
                                <a id="sosmed_WA" class="btn btn-sm elevation-2" data-toggle="collapse" href="#collapseWA">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            <?php } ?>

                            <!-- infomase -->
                            <a class="btn btn-primary bg-gradient-navy btn-sm elevation-2" data-toggle="collapse" href="#collapseINFO">

                                <i class="fas fa-info-circle"></i>
                                <!-- <i class="fas fa-chevron-down"></i> -->
                            </a>
                        </div>

                        <!-- collapse -->

                        <div id="collapseINFO" class="collapse" data-parent="#accordion_1">
                            <div class="card-body">
                                <div class="border mb-2"></div>
                                <a href="#collapseINFO" class="float-right" data-parent="#accordion_1" data-toggle="collapse">
                                    <i class="fas fa-times"></i>
                                </a>

                                <table class="table table-borderless table-responsive-lg" style="text-align: left;">
                                    <tr>
                                        <td class="text-dark">
                                            <i class="fas fa-user-circle"></i>
                                        </td>
                                        <td>
                                            <?= @$data_user['nama'] ? @$data_user['nama'] : (@$nama_login ? $nama_login : 'default.jpg'); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-info">
                                            <i class="fas fa-info-circle"></i>
                                        </td>
                                        <td>
                                            <?= @$info_login;; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-danger">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </td>
                                        <td>
                                            <?= @$tentang_alamat ? $tentang_alamat : '-'; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-warning">
                                            <i class="fas fa-envelope"></i>
                                        </td>
                                        <td>
                                            <?= @$data_user['email'] ? @$data_user['email'] : (@$email_login ? $email_login : 'default.jpg'); ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-primary">
                                            <i class="fas fa-phone"></i>
                                        </td>
                                        <td>
                                            <?= @$tentang_nomorhp ? ("0" . $tentang_nomorhp) : '-'; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-success">
                                            <i class="fab fa-whatsapp"></i>
                                        </td>
                                        <td>
                                            <?= @$tentang_wa ? ("0" . $tentang_wa) : '-'; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-secondary">
                                            <i class="fas fa-globe"></i>
                                        </td>
                                        <td>
                                            <?= @$tentang_web ? 'www.' . $tentang_web : '-'; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-primary">
                                            <i class="fab fa-facebook"></i>
                                        </td>
                                        <td>
                                            <?= @$tentang_fb ? 'www.facebook.com/' . $tentang_fb : '-'; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-danger">
                                            <i class="fab fa-instagram"></i>
                                        </td>
                                        <td>
                                            <?= @$tentang_ig ? 'www.instagram.com/' . $tentang_ig : '-'; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-info">
                                            <i class="fab fa-twitter"></i>
                                        </td>
                                        <td>
                                            <?= @$tentang_twitter ? 'www.twitter.com/' . $tentang_twitter : '-'; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-danger">
                                            <i class="fab fa-youtube"></i>
                                        </td>
                                        <td>
                                            <?= @$tentang_youtube ? 'www.youtube.com/' . $tentang_youtube : '-'; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-success">
                                            <i class="fab fa-line"></i>
                                        </td>
                                        <td>
                                            <?= @$tentang_line ? 'line.me/ti/p/~' . $tentang_line : '-'; ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <style>
                            .tombol_chat {
                                border-radius: 30px 20px 20px 3px;
                                padding: 5px 15px;
                            }
                        </style>

                        <!-- collapse Telp -->
                        <div id="collapseTelp" class="collapse" data-parent="#accordion_1">
                            <div class="card-body">
                                <div class="border mb-2"></div>
                                <a href="tel:62<?= $tentang_nomorhp; ?>" class="btn btn-success bg-gradient-success btn-sm elevation-2" target="_blank" style="border: none;">
                                    <i class="fas fa-phone"></i>
                                    <span>&nbsp;
                                        telepon
                                    </span>
                                </a>&nbsp;
                                +62<?= $tentang_nomorhp; ?>
                                <a href="#collapseWA" class="float-right" data-parent="#accordion_1" data-toggle="collapse">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>

                        <!-- collapse WA -->
                        <div id="collapseWA" class="collapse" data-parent="#accordion_1">
                            <div class="card-body">
                                <div class="border mb-2"></div>
                                <a id="sosmed_WA" href="https://wa.me/62<?= $tentang_wa; ?>" class="tombol_chat btn btn-sm elevation-2" target="_blank" style="border: none;">
                                    <i class="fab fa-whatsapp"></i>
                                    <span>&nbsp;
                                        Chat
                                    </span>
                                </a>&nbsp;
                                +62<?= $tentang_wa; ?>
                                <a href="#collapseWA" class="float-right" data-parent="#accordion_1" data-toggle="collapse">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>

                        <!-- collapse LINE -->
                        <div id="collapseLINE" class="collapse" data-parent="#accordion_1">
                            <div class="card-body">
                                <div class="border mb-2"></div>
                                <a id="sosmed_line" href="https://line.me/ti/p/~<?= @$tentang_line; ?>" class="btn elevation-2" target="_blank">
                                    <i class="fab fa-line fa-border-none"></i>
                                </a>
                                &nbsp;
                                <?= $tentang_line; ?>
                                <a href="#collapseLINE" class="float-right" data-parent="#accordion_1" data-toggle="collapse">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>

                        <!-- collapse Email -->
                        <div id="collapseEMAIL" class="collapse" data-parent="#accordion_1">
                            <div class="card-body">
                                <div class="border mb-2"></div>
                                <a href="mailto:<?= @$data_user['email'] ? @$data_user['email'] : '-'; ?>" class="btn btn-warning bg-gradient-warning btn-sm elevation-2" target="_blank">
                                    <i class="fas fa-envelope"></i>
                                    &nbsp;
                                    Kirim Email ke : &nbsp;
                                </a>
                                &nbsp;
                                <?= @$data_user['email'] ? @$data_user['email'] : '-'; ?>
                                <a href="#collapseEMAIL" class="float-right" data-parent="#accordion_1" data-toggle="collapse">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>

                        <!-- collapse Youtube -->
                        <div id="collapseYT" class="collapse" data-parent="#accordion_1">
                            <div class="card-body">
                                <div class="border mb-2"></div>
                                <a href="https://www.youtube.com/<?= @$tentang_youtube; ?>" class="btn btn-danger bg-gradient-danger btn-sm elevation-2" target="_blank">
                                    <i class="fab fa-youtube"></i>
                                    &nbsp;Channel Youtube :&nbsp;
                                </a>
                                &nbsp;
                                <?= @$tentang_youtube; ?>
                                <a href="#collapseYT" class="float-right" data-parent="#accordion_1" data-toggle="collapse">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>

                        <!-- collapse Twitter -->
                        <div id="collapseTW" class="collapse" data-parent="#accordion_1">
                            <div class="card-body">
                                <div class="border mb-2"></div>
                                <a href="https://www.twitter.com/<?= @$tentang_twitter; ?>" class="btn btn-info btn-sm elevation-2" target="_blank">
                                    <i class="fab fa-twitter"></i>&nbsp;
                                    Twitter :&nbsp;
                                </a>
                                &nbsp;
                                <?= @$tentang_twitter; ?>
                                <a href="#collapseTW" class="float-right" data-parent="#accordion_1" data-toggle="collapse">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>

                        <!-- collapse Instagram -->
                        <div id="collapseIG" class="collapse" data-parent="#accordion_1">
                            <div class="card-body">
                                <div class="border mb-2"></div>
                                <a href="https://www.instagram.com/<?= @$tentang_ig; ?>" class="btn btn-danger bg-gradient-danger btn-sm elevation-2" target="_blank">
                                    <i class="fab fa-instagram"></i>&nbsp;
                                    Instagram :&nbsp;
                                </a>
                                &nbsp;
                                <?= @$tentang_ig; ?>
                                <a href="#collapseIG" class="float-right" data-parent="#accordion_1" data-toggle="collapse">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>

                        <!-- collapse Facebook -->
                        <div id="collapseFB" class="collapse" data-parent="#accordion_1">
                            <div class="card-body">
                                <div class="border mb-2"></div>
                                <a href="https://www.facebook.com/<?= @$tentang_fb; ?>" class="btn btn-primary bg-gradient-primary btn-sm elevation-2" target="_blank">
                                    <i class="fab fa-facebook"></i>&nbsp;
                                    Profil Facebook :&nbsp;
                                </a>
                                &nbsp;
                                <?= @$tentang_fb; ?>
                                <a href="#collapseFB" class="float-right" data-parent="#accordion_1" data-toggle="collapse">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>

                        <!-- collapse Website -->
                        <div id="collapseWEB" class="collapse" data-parent="#accordion_1">
                            <div class="card-body">
                                <div class="border mb-2"></div>
                                <a href="https://www.<?= $tentang_web; ?>" class="btn bg-dark bg-gradient-dark btn-sm elevation-2" target="_blank" data-toggle="tooltip" data-placement="top" title="www.<?= $tentang_web; ?>">
                                    <span class="iconify" data-icon="ion:earth"></span>&nbsp;
                                    Kunjungi Website :&nbsp;
                                </a>
                                &nbsp;
                                www.<?= @$tentang_web; ?>
                                <a href="#collapseWEB" class="float-right" data-parent="#accordion_1" data-toggle="collapse">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>


                        <!-- <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a> -->
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->

                <style>
                    #kartu_profil .card-body {
                        margin-bottom: -20px;
                    }

                    #kartu_profil .small-box {
                        height: 120px;
                    }

                    #kartu_profil .kartu_profil_sub {
                        max-width: 180px;
                    }

                    #kartu_profil .card-body .row {
                        display: flex;
                        justify-content: space-around;
                        flex-wrap: wrap;
                    }

                    @media only screen and (max-width: 767px) {
                        #kartu_profil .kartu_profil_sub {
                            max-width: 300px;
                        }
                    }

                    @media only screen and (max-width: 649px) {
                        #kartu_profil .kartu_profil_sub {
                            max-width: 250px;
                        }
                    }

                    @media only screen and (max-width: 549px) {
                        #kartu_profil .kartu_profil_sub {
                            max-width: 200px;
                        }
                    }

                    @media only screen and (max-width: 450px) {
                        #kartu_profil .kartu_profil_sub {
                            max-width: 100%;
                        }
                    }
                </style>

                <div id="kartu_profil" class="card elevation-3">
                    <div class="card-body card-primary card-outline">
                        <?php if (@$data_user['poin']) { ?>
                            <div class="row">
                                <div class="kartu_profil_sub kartu_profil_1">
                                    <!-- small box -->
                                    <div class="small-box bg-info elevation-2">
                                        <div class="inner">
                                            <p>Poin&nbsp;Presensi&nbsp;:</p>
                                            <h3><?= $data_user['poin']; ?></h3>
                                        </div>
                                        <div class="icon">
                                            <span class="iconify" data-icon="ion:create"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="kartu_profil_sub kartu_profil_2">
                                    <!-- small box -->
                                    <div class="small-box text-white bg-warning elevation-2">
                                        <div class="inner">
                                            <!-- icon sigma -->
                                            <p>&sum;&nbsp;&nbsp;Keterlambatan&nbsp;:</p>
                                            <h3><?= $data_user['t_waktu_telat']; ?></h3>
                                        </div>
                                        <div class="icon">
                                            <i class="ion ion-clock"></i>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                if ($data_user['keterangan']) { ?>
                                    <div class="kartu_profil_sub kartu_profil_3">
                                        <!-- small box -->
                                        <div class="small-box text-white bg-primary elevation-2">
                                            <div class="inner">
                                                <!-- icon sigma -->
                                                <p>Keterangan&nbsp;:</p>
                                                <h3 style="font-size: 18px;"><?= $data_user['keterangan']; ?></h3>
                                                <h6 style="font-size: 12px;"><?= date('d-m-Y', strtotime($data_user['updated_at'])); ?></h6>
                                            </div>
                                            <div class="icon">
                                                <span class="iconify" data-icon="ion:newspaper"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="kartu_profil_sub kartu_profil_4">
                                        <!-- small box -->
                                        <div class="small-box text-white bg-success elevation-2">
                                            <div class="inner">
                                                <!-- icon sigma -->
                                                <p>Terakhir Update:</p>
                                                <h6 id="inner_001"><?= date('d-m-Y', strtotime($data_user['updated_at'])); ?></h6>
                                                <h6 id="inner_002"><?= date('H:i:s', strtotime($data_user['updated_at'])); ?></h6>
                                            </div>
                                            <div class="icon">
                                                <span class="iconify" data-icon="ion:save-sharp"></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- About Me Box -->
                <div class="card card-primary elevation-3">
                    <div class="card-header bg-gradient-primary">
                        <h3 class="card-title"><i class="fas fa-info"></i>&nbsp;&nbsp;Tentang Saya</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <!-- <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button> -->
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <strong><i class="fas fa-book mr-1"></i> Pendidikan</strong>

                        <p class="text-muted">
                            <!-- B.S. in Computer Science from the University of Tennessee at Knoxville -->
                            <?= $tentang_pendidikan; ?>
                        </p>

                        <hr>

                        <strong><i class="fas fa-map-marker-alt mr-1"></i> Alamat Rumah</strong>

                        <p class="text-muted">
                            <?= $tentang_alamat; ?>
                        </p>

                        <hr>

                        <strong><i class="fas fa-pencil-alt mr-1"></i> Hobi</strong>

                        <p class="text-muted">
                            <!-- <span class="tag tag-danger">Membaca</span>
                            <span class="tag tag-success">Coding</span>
                            <span class="tag tag-info">Bernyanyi</span>
                            <span class="tag tag-warning">Olahraga</span>
                            <span class="tag tag-primary">Traveling</span> -->
                            <?= $tentang_hobi; ?>
                        </p>

                        <hr>

                        <strong><i class="far fa-file-alt mr-1"></i>
                            Bio
                        </strong>

                        <p class="text-muted">
                            <?= $tentang_notes;; ?>
                        </p>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>

            <div class="col-md-6 connectedSortable">
                <!--  -->
                <!-- <div class="card">
                    <div class="card-header bg-gradient-primary">
                        <h3 class="card-title">Detail Riwayat</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div> -->
                <!--  -->

                <!--  -->
                <div class="card">
                    <div class="card-header bg-gradient-primary">
                        <h3 class="card-title"><i class="fas fa-calendar-alt"></i>&nbsp;Kalender</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <!-- <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button> -->
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

                    <?php include '_kalender.php'; ?>

                    <!-- go to kalender untuk halaman ini -->
                    <script>
                        function kalender_goto(tanggal) {
                            window.location.href = '?kalender_=' + tanggal;
                        }
                    </script>
                </div>
            </div>
            <!-- /.col -->

        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<style>
    /* modal prev image */
    .tampilfotoprofil .modal-content {
        background-color: transparent;
        box-shadow: none;
        border: none;
    }

    .tampilfotoprofil .modal-content img {
        border-top: 4px solid blue;
        border-radius: 5px;
        max-height: 70%;
        max-width: 100%;
        margin: auto;
    }

    .modal .tombol_close_gambar {
        /* position: absolute; */
        background-color: #fff;
        border: none;
        border-radius: 50%;
        padding: 20px;
        bottom: 0;
        margin-top: 5px;
        display: block;
        margin-left: auto;
        margin-right: auto;

    }
</style>

<!-- Modal prev image -->
<div class="modal fade tampilfotoprofil" id="tampilfotoprofil" tabindex="-1" aria-labelledby="tampilfotoprofilLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <img class="elevation-5" src="../img/user/<?= @$data_user['foto'] ? @$data_user['foto'] : 'default.jpg'; ?>">
            <!-- <button type="button" class="btn-close tombol_close_gambar elevation-5" data-bs-dismiss="modal" aria-label="Close"></button> -->
            <button type="button" class="btn-close tombol_close_gambar elevation-5 mb-5" data-bs-toggle="modal" data-bs-target="#modaldetailtentang<?= $nomor; ?>"></button>
        </div>
    </div>
</div>
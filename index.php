<?php
session_start();
$akses_login = @$_SESSION['akses'];

if (@$_SESSION['pesan']) {
    $pesan = $_SESSION['pesan'];
    $bg_pesan = "success";
} elseif (@$_SESSION['pesan_error']) {
    $pesan = $_SESSION['pesan_error'];
    $bg_pesan = "danger";
} else {
    $pesan = '';
    $bg_pesan = '';
}

if (isset($_GET['f']) == 'ull') {
    $full_recent = true;
} else {
    $full_recent = false;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="img/app/logoInstitusi.png" type="image/x-icon">

    <link rel="stylesheet" href="css/style_3.css">
    <link rel="stylesheet" href="dist/bootstrap-5.2.3-dist/css/bootstrap.css">

    <!-- <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script> -->
    <script src="dist/jquery/jquery-3.7.1.min.js"></script>
    
    <script type="text/javascript">
        $(document).ready(function() {
            $("#cekkartu").load("baca.php");
            // $("#riwayat").load("recent.php");
            $("#cektag").load("cektag.php");
            setInterval(function() {
                $("#cekkartu").load("baca.php");
                // $("#riwayat").load("recent.php");
                $("#cektag").load("cektag.php");
            }, 2000);
        });

        // var auto_refresh = setInterval(
        //     (function() {
        //         $("#cekkartu").load("baca.php");
        //     }), 1000);
    </script>

    <title>&Sopf;&iopf;&Aopf;&Popf;&nbsp;Sistem Administrasi Pembelajaran & Presensi Siswa</title>
</head>

<body>
    <?php if (@$pesan) { ?>
        <div class="alert alert-<?= $bg_pesan; ?> alert-dismissible fade show text-center m-0" role="alert">
            <?= $pesan; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } ?>

    <section class="container">

        <?php
        // echo "<pre>";
        // print_r(@$_SESSION);
        // echo "</pre>";
        ?>

        <style>
            .header {
                height: 100%;
                /* padding: 20px; */
                border-radius: 0px 0px 30px 30px;
                margin-bottom: 20px;
                /* background-image: url(img/SMKNBansari.png); */
                background-position: top;
                background-position-x: 10%;
                background-repeat: no-repeat;
                background-size: 12%;
            }

            .logo_jur img,
            .logo_s img {
                overflow: auto;
                margin-top: -5px;
            }

            .logo_jur img {
                float: right;
                margin-right: 5%;
                width: 90px;
                height: 100px;
            }

            .logo_s img {
                float: left;
                margin-left: 5%;
                width: 100px;
                height: 100px;
            }

            @media only screen and (max-width: 768px) {
                .header {
                    background-size: 25%;
                }

                .logo_s img, .logo_jur img {
                    height: 50px;
                    width: 50px;
                }

                .tagline-hero {
                    display: none;
                }
            }
        </style>
        <div class="header">
            <div class="header-gradient">
                <div class="hero">
                    <div class="logo_s">
                        <img class="logos" src="img/logoInstitusi.png">
                    </div>
                    <div class="logo_jur">
                        <img class="logobos" src="img/SMKBOS.png">
                    </div>
                </div>
                <div>
                    <h1>&Sopf;&iopf;&Aopf;<b>&Popf;</b><b>&Popf;</b></h1>
                </div>
                <div class="tagline-hero">
                    <h4>Sistem Administrasi Pembelajaran dan Presensi Siswa</h4>
                </div>
            </div>
        </div>
    </section>

    <section class="container body">
        <style>
            .post {
                /* background-color: white; */
                border-radius: 20px;
                box-shadow: rgba(52, 49, 70, 0.774) 2px 2px 10px;
                background-image: url(img/bor.png);
                /* background-position: center; */
                background-size: 106% 110%;
                background-repeat: no-repeat;
                /* background-position-x: 0; */
                /* background-position-y: 0; */
                margin-left: auto;
                margin-right: auto;

                <?php if ($full_recent) { ?>
                    width: 100%;
                <?php } else { ?>
                    width: 68%;
                <?php } ?>
                height: max-content;
            }

            @media only screen and (max-width: 768px) {
                .post {
                    width: 100%;
                }
            }
        </style>

        <div class="post">
            <div class="post-grad">
                <div class="content-poost">
                    <!-- Halaman Presensi Live -->
                    <?php if (@$_GET['s'] == 'liverecord') { ?>
                        <div class="" style="height: 400px;">
                            <div id="cekkartu"></div>
                        </div>
                    <?php } elseif (@$_GET['s'] == 'livetag') { ?>
                        <div class="" style="height: 400px;">
                            <div id="cektag"></div>
                            <a href="/app/mqtt/log/" style="display: block; text-align: right; color: #eee6e6; font-style: italic; text-decoration: none;">lihat detail...</a>
                        </div>
                        <!-- Riwayat Presensi List -->
                    <?php } elseif (@$_GET['s'] == 'listrecord' || @$_GET['s'] == 'eventrecord') { ?>
                            <div class="" style="min-height: 400px;">
                                <div class="box recent shadow_1">
                                    <div class="recent-title">
                                        <div class="group">
                                            <h4 class="shadow_1">
                                                <span class="iconify" data-icon="clarity:history-line"></span>
                                                <?php if (@$_GET['s'] == 'listrecord'){ ?>
                                                Riwayat Presensi
                                                <?php } elseif(@$_GET['s'] == 'eventrecord'){ ?>
                                                Riwayat Sholat
                                                <?php } ?>
                                            </h4>
                                                <div style="display: flex; justify-content: center;">
                                                    <!-- <form action="<?= $_SERVER['PHP_SELF']; ?>" method="post"> -->
                                                    <form id="myForm" action="recent.php" method="post">
                                                        <div class="input-group input-group-sm">
                                                            <select name="tampilkelasriwayat" id="riwayatharipresensi" class="shadow_1">
                                                                <option value="" <?= ($tampilhari == "") ? "selected" : ""; ?>>-- Semua Kelas --</option>
                                                                <option value="XAT1" <?= ($tampilhari == "XAT1") ? "selected" : ""; ?>>X AT 1</option>
                                                                <option value="XAT2" <?= ($tampilhari == "XAT2") ? "selected" : ""; ?>>X AT 2</option>
                                                                <option value="XAT3" <?= ($tampilhari == "XAT3") ? "selected" : ""; ?>>X AT 3</option>

                                                                <option value="XDKV1" <?= ($tampilhari == "XDKV1") ? "selected" : ""; ?>>X DKV 1</option>
                                                                <option value="XDKV2" <?= ($tampilhari == "XDKV2") ? "selected" : ""; ?>>X DKV 2</option>
                                                                <option value="XDKV3" <?= ($tampilhari == "XDKV3") ? "selected" : ""; ?>>X DKV 3</option>

                                                                <option value="XTE1" <?= ($tampilhari == "XTE1") ? "selected" : ""; ?>>X TE 1</option>
                                                                <option value="XTE2" <?= ($tampilhari == "XTE2") ? "selected" : ""; ?>>X TE 2</option>
                                                                <option value="XTE3" <?= ($tampilhari == "XTE3") ? "selected" : ""; ?>>X TE 3</option>
                                                                <option value="XTE4" <?= ($tampilhari == "XTE4") ? "selected" : ""; ?>>X TE 4</option>

                                                                <option value="XIAT1" <?= ($tampilhari == "XIAT1") ? "selected" : ""; ?>>XI AT 1</option>
                                                                <option value="XIAT2" <?= ($tampilhari == "XIAT2") ? "selected" : ""; ?>>XI AT 2</option>
                                                                <option value="XIAT3" <?= ($tampilhari == "XIAT3") ? "selected" : ""; ?>>XI AT 3</option>

                                                                <option value="XIDKV1" <?= ($tampilhari == "XIDKV1") ? "selected" : ""; ?>>XI DKV 1</option>
                                                                <option value="XIDKV2" <?= ($tampilhari == "XIDKV2") ? "selected" : ""; ?>>XI DKV 2</option>
                                                                <option value="XIDKV3" <?= ($tampilhari == "XIDKV3") ? "selected" : ""; ?>>XI DKV 3</option>

                                                                <option value="XITE1" <?= ($tampilhari == "XITE1") ? "selected" : ""; ?>>XI TE 1</option>
                                                                <option value="XITE2" <?= ($tampilhari == "XITE2") ? "selected" : ""; ?>>XI TE 2</option>
                                                                <option value="XITE3" <?= ($tampilhari == "XITE3") ? "selected" : ""; ?>>XI TE 3</option>

                                                                <option value="XIIAT1" <?= ($tampilhari == "XIIAT1") ? "selected" : ""; ?>>XII AT 1</option>
                                                                <option value="XIIAT2" <?= ($tampilhari == "XIIAT2") ? "selected" : ""; ?>>XII AT 2</option>
                                                                <option value="XIIAT3" <?= ($tampilhari == "XIIAT3") ? "selected" : ""; ?>>XII AT 3</option>

                                                                <option value="XIIDKV1" <?= ($tampilhari == "XIIDKV1") ? "selected" : ""; ?>>XII DKV 1</option>
                                                                <option value="XIIDKV2" <?= ($tampilhari == "XIIDKV2") ? "selected" : ""; ?>>XII DKV 2</option>
                                                                <option value="XIIDKV3" <?= ($tampilhari == "XIIDKV3") ? "selected" : ""; ?>>XII DKV 3</option>

                                                                <option value="XIITE1" <?= ($tampilhari == "XIITE1") ? "selected" : ""; ?>>XII TE 1</option>
                                                                <option value="XIITE2" <?= ($tampilhari == "XIITE2") ? "selected" : ""; ?>>XII TE 2</option>
                                                                <option value="XIITE3" <?= ($tampilhari == "XIITE3") ? "selected" : ""; ?>>XII TE 3</option>
                                                            </select>
                                                            <!-- <select name="tampilhaririwayat" id="riwayatharipresensi" class="shadow_1">
                                                                <option value="0" <?= ($tampilhari == "0") ? "selected" : ""; ?>>Hari ini</option>
                                                                <option value="1" <?= ($tampilhari == "1") ? "selected" : ""; ?>>Kemarin</option>
                                                                <option value="2" <?= ($tampilhari == "2") ? "selected" : ""; ?>>2 hari lalu</option>
                                                                <option value="3" <?= ($tampilhari == "3") ? "selected" : ""; ?>>3 hari lalu</option>
                                                                <option value="4" <?= ($tampilhari == "4") ? "selected" : ""; ?>>4 hari lalu</option>
                                                                <option value="5" <?= ($tampilhari == "5") ? "selected" : ""; ?>>5 hari lalu</option>
                                                                <option value="6" <?= ($tampilhari == "6") ? "selected" : ""; ?>>Semua</option>
                                                            </select> -->
                                                            <!-- <select name="tampilketriwayat" id="riwayatketpresensi" class="shadow_1">
                                                                <option value="semua" <?= ($tampilket == "semua") ? "selected" : ""; ?>>Semua</option>
                                                                <option value="masuk" <?= ($tampilket == "masuk") ? "selected" : ""; ?>>Masuk</option>
                                                                <option value="pulang" <?= ($tampilket == "pulang") ? "selected" : ""; ?>>Pulang</option>
                                                                <option value="terlambat" <?= ($tampilket == "terlambat") ? "selected" : ""; ?>>Terlambat</option>
                                                                <option value="pulangawal" <?= ($tampilket == "pulangawal") ? "selected" : ""; ?>>Pulang Awal</option>
                                                                <option value="ijin" <?= ($tampilket == "ijin") ? "selected" : ""; ?>>Ijin</option>
                                                                <option value="sakit" <?= ($tampilket == "sakit") ? "selected" : ""; ?>>Sakit</option>
                                                                <option value="dinasluar" <?= ($tampilket == "dinasluar") ? "selected" : ""; ?>>Dinas Luar</option>
                                                                <option value="WFH" <?= ($tampilket == "WFH") ? "selected" : ""; ?>>WFH</option>
                                                                <option value="PJJ" <?= ($tampilket == "PJJ") ? "selected" : ""; ?>>PJJ</option>
                                                                <option value="lainnya" <?= ($tampilket == "lainnya") ? "selected" : ""; ?>>Lainnya</option>
                                                            </select>
                                                            <select name="tampiljmlriwayat" id="riwayatjmlpresensi" class="shadow_1">
                                                                <option value="10" <?= ($tampiljlm == "10") ? "selected" : ""; ?>>10</option>
                                                                <option value="20" <?= ($tampiljlm == "20") ? "selected" : ""; ?>>20</option>
                                                                <option value="30" <?= ($tampiljlm == "30") ? "selected" : ""; ?>>30</option>
                                                                <option value="50" <?= ($tampiljlm == "50") ? "selected" : ""; ?>>50</option>
                                                                <option value="100" <?= ($tampiljlm == "100") ? "selected" : ""; ?>>100</option>
                                                                <option value="semua" <?= ($tampiljlm == "semua") ? "selected" : ""; ?>>Semua</option>
                                                            </select> -->
                                                            <!-- <button id="tombol_set_home" type="submit" name="tampilanriwayat" value="OK" class="btn btn-dark border-0">
                                                                &#9989;&nbsp;SET
                                                            </button> -->
                                                            <label id="tombol_set_home" class="bg-dark border-0" disabled>
                                                                &nbsp;Pilih&nbsp;Kelas&nbsp;
                                                            </label>
                                                        </div>
                                                    </form>
                                                </div>
                                        </div>
                                    </div>
                                    <div class="tabel-riwayat">
                                        <div id="riwayat"></div>
                                    </div>
                                    <div class="ml-2 mt-2">
                                        <?php 
                                        if ($full_recent) { 
                                            if (@$_GET['s'] == 'listrecord'){
                                        ?>
                                            
                                            <a href="?s=listrecord" class="mt-1 text-decoration-none badge bg-primary mr-2"
                                                style="float: right; cursor: pointer;">Tampilkan normal</a>
                                        <?php 
                                            } elseif(@$_GET['s'] == 'eventrecord'){
                                        ?>
                                            <a href="?s=eventrecord" class="mt-1 text-decoration-none badge bg-primary mr-2"
                                                style="float: right; cursor: pointer;">Tampilkan normal</a>
                                        <?php   
                                            }
                                        } else { 
                                            if (@$_GET['s'] == 'listrecord'){
                                        ?>
                                            <a href="?s=listrecord&f=ull" class="mt-1 text-decoration-none badge bg-primary mr-2"
                                                style="float: right; cursor: pointer;">Tampilkan Lebar</a>

                                        <?php 
                                            } elseif(@$_GET['s'] == 'eventrecord'){
                                        ?>
                                            <a href="?s=eventrecord&f=ull" class="mt-1 text-decoration-none badge bg-primary mr-2"
                                                style="float: right; cursor: pointer;">Tampilkan Lebar</a>
                                        <?php
                                            }
                                        } 
                                        ?>
                                    </div>

                                    <!-- label keterangan -->
                                    <?php if (@$_GET['s'] == 'listrecord'){ ?>
                                    <div id="ket2" class="ml-2">
                                        <div id="ket2_2">
                                            <label>&nbsp;Keterangan: </label>
                                        </div>
                                        <div id="ket2_1">
                                            <label class="bg-warning badge">+Terlambat</label>
                                            <label class="bg-success badge">-Hadir</label>
                                            |
                                            <label class="bg-warning badge">-Pulang Awal</label>
                                            <label class="bg-success badge">+Pulang</label>

                                            |
                                            <label class="bg-primary badge">Ijin</label>
                                            <label class="bg-danger badge">Alpha</label>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <script>
                                $(document).ready(function(){
                                    // Fungsi untuk memuat konten dari recent.php dengan POST data form
                                    function loadContent() {
                                        $.ajax({
                                            <?php if (@$_GET['s'] == 'listrecord') { ?>
                                                url: 'recent.php',
                                            <?php } elseif (@$_GET['s'] == 'eventrecord') { ?>
                                                url: 'recentevent.php',
                                            <?php } ?>
                                            type: 'POST',
                                            data: $('#myForm').serialize(),  // Menyertakan data form
                                            success: function(response) {
                                                $('#riwayat').html(response);  // Menampilkan hasil di elemen #riwayat
                                            }
                                        });
                                    }

                                    // Memuat data setiap 2 detik (2000 ms)
                                    setInterval(loadContent, 2000);

                                    // Menangani submit form tanpa reload halaman
                                    $('#myForm').on('submit', function(e) {
                                        e.preventDefault();  // Menghindari reload halaman
                                        loadContent();       // Memuat konten saat form disubmit
                                    });
                                });
                            </script>

                            <style>
                                .recent {
                                    overflow: hidden;
                                    background-color: rgba(198, 221, 251, 0.3);
                                    border-radius: 50px 3px 50px 3px;
                                    margin-right: 10px;
                                    margin-left: 10px;
                                    margin-bottom: 20px;
                                    margin-top: 10px;
                                    width: 100%;
                                    min-height: 400px;
                                    max-height: 880px;
                                    font-size: 2ex;
                                }

                                .recent .badge {
                                    border-radius: 10px 3px 10px 3px;
                                    padding: 3px 8px;
                                }

                                .recent #riwayatharipresensi {
                                    border-radius: 20px 0px 0px 3px;
                                    padding-left: 20px;
                                }

                                .recent #tombol_set_home {
                                    border-radius: 0px 3px 20px 0px;
                                    padding-right: 10px;
                                }

                                .recent h4 {
                                    text-align: center;
                                    font-size: 20px;
                                    font-weight: 400;
                                    font-style: italic;
                                    font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
                                    /* background-color: rgba(0, 50, 100, 0.7); */
                                    background: linear-gradient(to bottom, rgba(1, 88, 174, 0.7) 0%, rgba(0, 17, 34, 0.7) 100%);
                                    color: #eee6e6;
                                    padding: 2px;
                                    width: 220px;
                                    justify-content: center;
                                    margin-left: auto;
                                    margin-right: auto;
                                    border-radius: 50px 5px 50px 5px;
                                    margin-top: 10px;
                                }

                                .recent h4 .iconify {
                                    padding-bottom: 4px;
                                }

                                .recent h6,
                                .recent a {
                                    display: flex;
                                    margin: auto;
                                }

                                #labet-kettabel {
                                    font-size: 5px;
                                }

                                <?php if ($full_recent) { ?>
                                    .tabel-riwayat {
                                        overflow: auto;
                                        height: 600px;
                                        margin-top: 0px;
                                        margin-right: -300px;
                                        padding-right: 300px;
                                    }

                                <?php } else { ?>
                                    .tabel-riwayat {
                                        overflow: auto;
                                        min-height: 200px;
                                        max-height: 440px;
                                        margin-top: 0px;
                                        margin-right: -300px;
                                        padding-right: 300px;
                                    }

                                <?php } ?>

                                #ket1 {
                                    margin-bottom: 0;
                                }

                                #ket2 {
                                    margin-top: -5px;
                                    margin-left: 20px;
                                }

                                #ket2 #ket2_2 label {
                                    font-size: 14px;
                                }

                                #ket2_1 {
                                    margin-top: 0px;
                                    margin-left: 10px;
                                }

                                .label-tombol {
                                    font-size: 10px;
                                    background-color: rgb(54, 120, 206);
                                    color: rgb(167, 189, 248);
                                    float: right;
                                    margin-bottom: 50px;
                                    margin-right: 20px;
                                }
                            </style>

                        <!-- defaultt home -->
                    <?php } else { ?>
                        <div class="header-post m-0">
                            <h1>SiAP (Sistem Administrasi Pembelajaran dan Presensi Siswa)</h1>
                            <p>
                                Aplikasi ini digunakan untuk memenuhi kebutuhan dalam proses belajar mengajar. Mencatat Presensi setiap Kelas sesuai jadwal yang telah disinkronkan. Mencatat Jurnal mengajar, mencatat Nilai di setiap pertemuan, menambahkan catatan setiap siswa selama Kegiatan Pembelajaran dikelas, serta pemantauan setiap kelas secara real-time.
                            </p>
                        </div>
                        <hr>
                        <div class="posting-view m-0">
                            <h1>Kelas Saya</h1>
                            <p>
                                Bapak/Ibu Guru dapat langsung membuka daftar peserta didik di setiap harinya sesuai jadwal yang ada di hari tersebut. Bapak/Ibu Guru dapat mengisi Jurnal, Presensi siswa di kelas, nilai harian, serta menambahkan catatan untuk setiap siswa.
                                Catatan tersebut dapat dipantau sehingga jika ada siswa yang mempunyai catatan khusus, Bapak/Ibu Guru yang lain dapat langsung mengetahuinnya.
                            </p>
                        </div>
                        <hr>
                        <div class="posting-view m-0">
                            <h1>Rekap Nilai dan pemantauan Kelas</h1>
                            <p>
                                Jurnal dan Rekap Nilai di setiap pertemuan akan tersimpan dan dapat di cetak / di download (file excel) sesuai kelas yang Bapak/Ibu ampu.

                                Semua kegiatan kelas dapat dipantau termasuk kehadiran siswa dan kegiatan belajar yang terjadi dikelas.
                            </p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>


        <style>
            .kartu_login .bingkai1 {
                background-color: #eee6e6;
                padding: 5px;
                border-radius: 50%;
                width: 160px;
                height: 160px;
                display: block;
                margin-left: auto;
                margin-right: auto;
            }

            .kartu_login #foto_profil {
                width: 150px;
                height: 150px;
                object-fit: cover;
                object-position: center;
                border-radius: 50%;
                display: block;
                margin-left: auto;
                margin-right: auto;
                box-shadow: 3px 0px 3px 0px #000;
            }

            .kartu_login h6 {
                text-align: center;
                margin-top: 10px;
            }

            .kartu_login .labeluser2 {
                font-size: 12px;
                font-weight: 400;
            }

            .kartu_login .labeluser2 {
                font-style: italic;
                margin-top: -5px;
            }

            .kartu_login .labeluser3 {
                font-size: 14px;
            }

            .kartu_login .labeluser4 {
                font-size: 18px;
                margin-top: -5px;
            }

            .login-form {
                color: white;
                width: 30%;
                border-radius: 20px;
                /* box-shadow: rgba(137, 118, 255, 0.616) 0px 0px 10px 5px; */
                box-shadow: rgba(52, 49, 70, 0.774) 2px 2px 10px;
                background-image: url(img/SMKBOS.png);
                background-position: top;
                background-repeat: no-repeat;
                background-size: 120%;
            }

            @media only screen and (max-width: 768px) {
                .login-form {
                    width: 100%;
                }
            }

            <?php
            if ($full_recent) {
                ?>
                .login-form {
                    display: none;
                }

                <?php
            }
            ?>
        </style>

        <div class="login-form">
            <div class="login-form-grad">
                <?php if (@$_SESSION['username_login']) { ?>
                    <div class="kartu_login">
                        <div class="bingkai1">
                            <img id=foto_profil src="img/user/<?= $_SESSION['foto_login'] ? $_SESSION['foto_login'] : 'default.jpg'; ?>">
                        </div>

                        <h6 for="foto_profil" class="labeluser1"><?= $_SESSION['username_login']; ?></h6>
                        <h6 for="foto_profil" class="labeluser2"><?= "(" . $_SESSION['email_login'] . ")"; ?></h6>
                        <h6 for="foto_profil" class="labeluser3"><?= $_SESSION['info_login']; ?></h6>
                        <h6 for="foto_profil" class="labeluser4"><?= $_SESSION['akses']; ?> - <?= $_SESSION['ket_akses']; ?></h6>

                        <div class="d-flex justify-content-around">
                            <a href="beranda" class="btn btn-primary" style="border-radius: 50px; margin-top: 5px; border: none;"><img src="img/app/solid_tachometer-alt.svg" style="width: 20px;">&nbsp;Beranda</a>

                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#logout" style="border-radius: 50px; margin-top: 5px; border: none;">
                                Logout <img src="img/app/log-out_w.svg" style="height: 20px; margin-left: 5px; margin-right: 0; margin-top: auto; margin-bottom: auto;">
                            </button>
                        </div>
                    </div>
                <?php } else { ?>
                    <form action="login.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Email</label>
                            <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="username" value="<?= @$_COOKIE['username_login'] ? $_COOKIE['username_login'] : ''; ?>" autofocus placeholder="contoh@smknbansari.sch.id">
                            <div id="emailHelp" class="form-text">Email yang digunakan adalah email sekolah - @smknbansari.sch.id</div>
                        </div>

                        <div class="mb-3">
                            <!-- <input type="password" id="typePasswordX" class="form-control form-control-lg" name="password" /> -->
                            <label class="form-label" for="typePasswordX">Password</label>
                            <div class="input-group">
                                <input type="password" id="typePasswordX" class="form-control" name="password" placeholder="Password" value="<?= @$_COOKIE['password_login'] ? $_COOKIE['password_login'] : ''; ?>" />
                                <div class="input-group-append">

                                    <!-- kita pasang onclick untuk merubah icon buka/tutup mata setiap diklik  -->
                                    <span id="myeyebtn" onclick="change()" class="input-group-text">

                                        <!-- icon mata bawaan bootstrap  -->
                                        <svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z" />
                                            <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="exampleCheck1" name="ingataku" value="ingataku">
                            <label class="form-check-label" for="exampleCheck1">Ingatkan saya</label>
                        </div>
                        <button type="submit" name="login" value="Masuk" class="btn btn-success .bg-gradient border-0 w-100">🔑&nbsp;Login</button>
                    </form>
                <?php } ?>
                <hr>

                <?php if (@!$_GET['s']) { ?>
                    <div class="mt-4 text-center">
                        <a href="?s=liverecord" class="btn btn-outline-light btn-sm rounded-pill">🔴&nbsp;Live&nbsp;Record</a>
                    </div>
                <?php } else { ?>
                    <div class="mt-4 text-center">
                        <?php if ($_GET['s'] == 'listrecord') { ?>
                            <a href="?s=eventrecord" class="btn btn-outline-warning btn-sm rounded-pill border-2">🗒️&nbsp;Tampil&nbsp;Rekap&nbsp;Sholat</a>
                        <?php } elseif ($_GET['s'] == 'eventrecord') { ?>
                            <a href="?s=listrecord" class="btn btn-outline-warning btn-sm rounded-pill border-2">🗒️&nbsp;Tampil&nbsp;Rekap&nbsp;Presensi</a>
                        <?php } ?>
                    </div>
                    <?php if ($_GET['s'] == 'liverecord') { ?>
                        <div class="mt-4 text-center">
                            <a href="?s=eventrecord" class="btn btn-outline-warning btn-sm rounded-pill border-2">🗒️&nbsp;Rekap&nbsp;Sholat</a>
                            <a href="?s=listrecord" class="btn btn-outline-warning btn-sm rounded-pill border-2">🗒️&nbsp;Rekap&nbsp;Presensi</a>
                        </div>
                    <?php } ?>
                    <hr>
                    <div class="mt-2 text-center">
                        <a href="../" class="btn btn-dark btn-sm border-0 rounded-pill">
                            << Kembali
                        </a>
                    </div>
                <?php } ?>
                <div class="d-flex justify-content-around mt-4">
                    <div class="d-flex flex-column">
                        <img src="img/at.png" style="width: 15px;">
                        <label style="font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif; font-size: 12px; text-align: center;">AT</label>
                    </div>
                    <div class="d-flex flex-column">
                        <img src="img/dkv.png" style="width: 35px;">
                        <label style="font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif; font-size: 12px; text-align: center;">DKV</label>
                    </div>
                    <div class="d-flex flex-column">
                        <img src="img/te.png" style="width: 25px;">
                        <label style="font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif; font-size: 12px; text-align: center;">TE</label>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="footer">
            <div>
                <img src="img/SMKNBansari.png" class="logo_2">
            </div>
            <div>
                <h5>SMK NEGERI BANSARI &#169;</i> 2022</h5>
                <h6>Smart Link IT</h6>
            </div>
            <div>
                <img src="img/SMKBOS.png" class="logo_3">
            </div>
        </div>
    </section>

    <script src="dist/bootstrap-5.2.3-dist/js/bootstrap.js"></script>
    <script src="dist/bootstrap-5.2.3-dist/js/bootstrap.bundle.js"></script>
    <script src="dist/bootstrap-5.2.3-dist/js/bootstrap.esm.js"></script>

    <script>
        // membuat fungsi change
        function change() {

            // membuat variabel berisi tipe input dari id='pass', id='pass' adalah form input password 
            var x = document.getElementById('typePasswordX').type;

            //membuat if kondisi, jika tipe x adalah password maka jalankan perintah di bawahnya
            if (x == 'password') {

                //ubah form input password menjadi text
                document.getElementById('typePasswordX').type = 'text';

                //ubah icon mata terbuka menjadi tertutup
                document.getElementById('myeyebtn').innerHTML = `<svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-eye-slash-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10.79 12.912l-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
                                                                    <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708l-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829z"/>
                                                                    <path fill-rule="evenodd" d="M13.646 14.354l-12-12 .708-.708 12 12-.708.708z"/>
                                                                    </svg>`;
            } else {

                //ubah form input password menjadi text
                document.getElementById('typePasswordX').type = 'password';

                //ubah icon mata terbuka menjadi tertutup
                document.getElementById('myeyebtn').innerHTML = `<svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                                    <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                                    </svg>`;
            }
        }

        var myModal = document.getElementById('login')
        var myInput = document.getElementById('username_login')

        myModal.addEventListener('shown.bs.modal', function() {
            myInput.focus()
        })
    </script>

    <!-- Log out -->
    <div class="modal fade" id="logout" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="margin-left: 40%;">Log out!</h5>
                    <a data-bs-dismiss="modal" aria-label="Close" style="cursor: pointer;"><i class="fas fa-times"></i></a>
                </div>
                <div class="modal-body">
                    <h6 style="text-align: center;">"<b><?= $_SESSION['username_login']; ?></b>", akan keluar dari akun. Yakin?</h6>
                    <div style="display: flex; justify-content: center;">
                        <form action="logout.php" method="post">
                            <div class="mb-3 form-check">
                                <div>
                                    <input type="checkbox" class="form-check-input" id="exampleCheck1" name="tetapingat" value="tetapingat">
                                    <label class="form-check-label" for="exampleCheck1">ingat saya!</label>
                                </div>
                                <div>
                                    <input type="checkbox" class="form-check-input" id="exampleCheck1" name="lupakan" value="lupakan">
                                    <label class="form-check-label" for="exampleCheck1">lupakan saya!</label>
                                </div>
                            </div>

                            <button type="button" class="btn btn-secondary mx-1" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger mx-1" name="logout" value="logout">Tetap Log out!<img src="img/app/log-out_w.svg" style="height: 20px; margin-left: 5px; margin-right: 0; margin-top: auto; margin-bottom: auto;"></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>

<?php
if (@$_SESSION['pesan']) {
    unset($_SESSION['pesan']);
    $bg_pesan = "";
}

if (@$_SESSION['pesan_error']) {
    unset($_SESSION['pesan_error']);
    $bg_pesan = "";
}
?>
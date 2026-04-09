<?php


if (@$_GET['cari']) {
    include('app/get_user.php');
    
    $katakunci = $_GET['katakunci'];
    $katakunci = mysqli_real_escape_string($konek, $katakunci);
    
    $query1 = "SELECT * FROM dataguru WHERE nama like '%" . $katakunci . "%' ORDER BY nama ASC";
    $result1 = mysqli_query($konek, $query1);
    $hitung1 = mysqli_num_rows($result1);
    
    $query2 = "SELECT * FROM datasiswa WHERE nama like '%" . $katakunci . "%' ORDER BY nama ASC";
    $result2 = mysqli_query($konek, $query2);
    $hitung2 = mysqli_num_rows($result2);
    
    if ($_GET['cari'] == 'gtk') {
        $datab = 'dataguru';
        $result = $result1;
        $hitung = $hitung1;
        $datab_info = 'jabatan';
        $db_info = 'Guru dan Tendik';
        $tmb1_aktif = 'disabled';
        $tmb2_aktif = '';
    } elseif ($_GET['cari'] == 'siswa') {
        $datab = 'datasiswa';
        $result = $result2;
        $hitung = $hitung2;
        $datab_info = 'kelas';
        $db_info = 'Siswa';
        $tmb1_aktif = '';
        $tmb2_aktif = 'disabled';
    }
    
    $title = 'Hasil Pencarian';
    $sub_title = 'Hasil Pencarian <i><b>"' . $db_info . '"</b></i>, kata kunci: <i><b>"' . $katakunci . '"</b></i>';
    
    $_SESSION['link_back'] = 'hasilcari.php?cari=' . $_GET['cari'] . '&katakunci=' . $katakunci;

    include('views/header.php');
?>

    <style>
        .tombol_header {
            display: flex;
            justify-content: center;
            margin-left: auto;
            margin-right: auto;
        }
    </style>

    <section class="content">
        <div class="container-fluid">
            <h5><?= $sub_title; ?></h5>
            <div class="row">
                <div class="col-md-12">
                    <!-- AREA CHART -->
                    <div class="card card-navy elevation-2">
                        <div class="card-header bg-gradient-navy rounded">
                            <a href="javascript:history.go(-1)" class="btn btn-default float-left"><i class="fa fa-arrow-left"></i></a>
                            <div class="tombol_header">
                                <div class="btn-group">
                                    <?php if ($tmb1_aktif) {
                                        $icon_1 = '<i class="fas fa-spinner fa-spin"></i>';
                                        $warna_bg = 'light';
                                    } else {
                                        $icon_1 = '<i class="fas fa-address-card"></i>';
                                        $warna_bg = 'success';
                                    } ?>
                                    <a href="?cari=gtk&katakunci=<?= $katakunci; ?>" class="btn btn-<?= $warna_bg; ?> bg-<?= $warna_bg; ?> bg-gradient-<?= $warna_bg; ?> elevation-3 <?= $tmb1_aktif; ?>">
                                        <?= $icon_1; ?>
                                        &nbsp;
                                        GTK
                                        &nbsp;
                                        <span class="right badge badge-danger"><?= $hitung1; ?></span>
                                    </a>

                                    <?php if ($tmb2_aktif) {
                                        $icon_2 = '<i class="fas fa-spinner fa-spin"></i>';
                                        $warna_bg = 'light';
                                    } else {
                                        $icon_2 = ' <i class="fas fa-address-card"></i>';
                                        $warna_bg = 'success';
                                    } ?>
                                    <a href="?cari=siswa&katakunci=<?= $katakunci; ?>" class="btn btn-<?= $warna_bg; ?> bg-<?= $warna_bg; ?> bg-gradient-<?= $warna_bg; ?> elevation-3 <?= $tmb2_aktif; ?>">
                                        <?= $icon_2; ?>
                                        &nbsp;
                                        Siswa
                                        &nbsp;
                                        <span class="right badge badge-danger"><?= $hitung2; ?></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if (!$hitung) { ?>
        <section class="content">
            <div class="container-fluid">
                <!-- alert -->
                <div class="alert alert-warning text-center" role="alert">
                    <?= $sub_title; ?>
                    <h5>
                        <i class="fas fa-exclamation-circle"></i>
                        &nbsp;Tidak ditemukan...
                    </h5>
                </div>
            </div>
        </section>
    <?php } else {
        // konten
    ?>
        <section class="content">
            <div class="container-fluid">
                <?php
                // include('views/_kartukontak.php'); 
                include('views/_kartuflip_kontak.php');
                ?>
            </div>
        </section>
<?php
    }
    include('views/footer.php');
} ?>
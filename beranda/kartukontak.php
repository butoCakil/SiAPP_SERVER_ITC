<?php

include('app/get_user.php');

unset($_SESSION['link_back']);

if ($level_login == 'user_siswa') {
    $_SESSION['pesan'] = 'Anda tidak memiliki akses ke halaman ini. <i>(Kode: #KAR001)</i>';
    header('location:' . $base_url . '404.php');
}

if (@$_GET['datab'] == 'GTK') {
    $datab = 'dataguru';
    $datab_info = 'jabatan';
    $info_title = 'Guru dan Tendik';
    $navlink = 'Data Guru';
    $sortir = "";
} else if (@$_GET['datab'] == 'siswa') {
    $datab = 'datasiswa';
    $datab_info = 'kelas';
    $info_title = 'Siswa kelas ' . $ket_akses_login;
    $navlink = 'Wali';
    $sortir = " WHERE kelas = '$ket_akses_login' ";
} else {
    $datab = 'dataguru';
    $datab_info = 'jabatan';
    $info_title = 'Guru dan Tendik';
    $sortir = "";
}

if (@$_GET['mode'] == 'lapse') {
    $load_kartu = '_kartukontak.php';
    $tombol_dis_1 = 'disabled';
    $tombol_dis_2 = '';
} elseif (@$_GET['mode'] == 'flip') {
    $load_kartu = '_kartuflip_kontak.php';
    $tombol_dis_1 = '';
    $tombol_dis_2 = 'disabled';
} else {
    $load_kartu = '_kartuflip_kontak.php';
    $tombol_dis_1 = '';
    $tombol_dis_2 = 'disabled';
}

include "../config/konesi.php";
$datab = mysqli_real_escape_string($konek, $datab);
$sql = "SELECT * FROM " . $datab . $sortir . " ORDER BY nama ASC";
$result = mysqli_query($konek, $sql);

$title = 'Kartu Kontak ' . $info_title;
$navlink_sub = 'kontak';
include('views/header.php');

if ($level_login == 'superadmin' || $level_login == 'admin') { ?>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header bg-gradient-navy rounded d-flex justify-content-between">
                    <div class="my-auto">
                        <button href="../tambahdata.php" class="btn bg-success bg-gradient-success" disabled>
                            <i class="fa fa-plus"></i>&nbsp;
                            Tambah Data
                        </button>
                    </div>
                    <div class="d-flex flex-column">
                        <label class="text-xs m-0 text-center">Pilih Tampilan Kartu</label>
                        <div class="btn-group">
                            <a href="?mode=lapse&datab=<?= @$_GET['datab']; ?>" class="btn btn-sm btn bg-warning bg-gradient-warning <?= $tombol_dis_1; ?>">
                                <i class="fa fa-list"></i>&nbsp;
                                Collapse
                            </a>
                            <a href="?mode=flip&datab=<?= @$_GET['datab']; ?>" class="btn btn btn-sm bg-primary bg-gradient-primary <?= $tombol_dis_2; ?>">
                                <i class="fa fa-share"></i>&nbsp;
                                Flip
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php } ?>

<section class="content">
    <div class="container-fluid">
        <?php
        // include('views/_kartukontak.php');
        ?>
        <?php
        // include('views/_kartuflip_kontak.php');
        include('views/' . $load_kartu);
        ?>
    </div>
</section>

<?php
include('views/footer.php');

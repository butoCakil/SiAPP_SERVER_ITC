<?php
if (@$_SESSION['ingataku']) {
    ini_set('session.cookie_lifetime', 2000000);
} else {
    ini_set('session.cookie_lifetime', 0);
}
session_start();

$dir = "";
if (@$subDir) {
    for ($i = 0; $i < $subDir; $i++) {
        $dir = $dir . "../";
    }
}

include @$dir . '../app/konversiwaktu.php';
include @$dir . '../config/konesi.php';

if (@$_SESSION['username_login']) {
    $nama_login = $_SESSION['username_login'];
    $email_login = $_SESSION['email_login'];
    $password_login = $_SESSION['password_login'];
    $foto_login = $_SESSION['foto_login'];
    $nick_login = @$_SESSION['nick_login'];
    $info_login = $_SESSION['info_login'];
    $level_login = $_SESSION['level_login'];
    $akses_login = @$_SESSION['akses'];
    $ket_akses_login = @$_SESSION['ket_akses'];
    $akses = @$_SESSION['akses'];
    isset($_SESSION['datab_login']) ? $datab_login = $_SESSION['datab_login'] : $datab_login = '';
    isset($_SESSION['nokartu_login']) ? $nokartu_login = $_SESSION['nokartu_login'] : $nokartu_login = '';
} else {
    // echo 'tidak ada';
    $link_login = '<a id="ketupat_tmbl_1" href="../" class="btn btn-success shadow_gradient_1" data-bs-toggle="modal" data-bs-target="#login" style="cursor: pointer;"><b><i>Log in</i></b></a>';
    $pesan = 'Telah keluar dari Akun Anda.' . '<br>' . 'Silahkan ' . $link_login;
    $_SESSION['pesan_error'] = $pesan;
    header("Location: $dir../");
}

if (@$nokartu_login) {
    $sql2 = "SELECT * FROM `$datab_login` WHERE nokartu = '$nokartu_login' ORDER BY id ASC";
    $query2 = mysqli_query($konek, $sql2);
    $data_user = mysqli_fetch_array($query2);
} else {
    if ($level_login != "superadmin") {
        header("Location: $dir../");
    }
}

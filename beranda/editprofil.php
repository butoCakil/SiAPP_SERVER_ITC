<?php
include('app/get_user.php');

if (($level_login == 'superadmin' || ($level_login == 'admin' && !$nick_login)) && !$_GET['datab'] && !$_GET['nick']) {
    // echo '<script>alert("klik edit pada kartu nama yang akan diubah.");</script>';
    header("Location: kartukontak.php?datab=GTK");
    // echo 'level login: ' . $level_login . '<br>';
    // alert
}


include('../config/konesi.php');

// redirect
// if (@$_POST) {
//     echo '<meta http-equiv="refresh" content="0; url=?page=profil">';
// }

// if(@$_POST){
//     $_POST = '';
//     unset($_POST);
header("Location: " . @$link_editprofil);
// }

if (@$_SESSION['pesan_error']) {
    $pesan_error = $_SESSION['pesan_error'];
    $alert_bg = $_SESSION['alert_bg'];
    unset($_SESSION['pesan_error']);
    unset($_SESSION['alert_bg']);
}

$link_tag = @$_GET['tag'];

if (@$_GET['datab']) {
    $datab = $_GET['datab'];
    $datab = mysqli_real_escape_string($konek, $datab);
    $nick = $_GET['nick'];
    $nick = mysqli_real_escape_string($konek, $nick);
    $link_editprofil = 'editprofil.php?datab=' . $datab . '&nick=' . $nick . '&linka=cocard';
    $link_editprofil_srt = 'datab=' . $datab . '&nick=' . $nick;

    $sql = "SELECT * FROM $datab WHERE nick = '$nick'";
    $result = mysqli_query($konek, $sql);
    $data = mysqli_fetch_assoc($result);

    $title = 'Edit Profil ' . $data['nama'];
} else {
    $datab = $datab_login;
    $nick = $nick_login;
    $data = $data_user;
    $link_editprofil = 'editprofil.php';
    $link_editprofil_srt = '';

    $title = 'Edit Profil Saya';
}


if (@$_GET['linka'] == 'cocard') {
    if ($datab == 'dataguru') {
        $datab_ = 'GTK';
    } else if ($datab == 'datasiswa') {
        $datab_ = 'siswa';
    } else {
        $datab_ = 'dataguru';
    }

    $link_back = 'kartukontak.php?datab=' . $datab_ . '#' . $link_tag;
    $ket_link = 'ke Kartu Kontak';
} else {
    $link_back = 'profil.php';
    $ket_link = 'ke Profil';
}

if (@$_SESSION['link_back']) {
    $link_back = $_SESSION['link_back'] . '#' . $link_tag;
    $ket_link = 'ke Hasil Cari';
}

$link_back_link = '<br><a href="' . $link_back . '" class="btn btn-dark bg-gradient-dark btn-sm mt-1 border-0 elevation-1" style="text-decoration: none;">
                   <i class="fas fa-arrow-left"></i>&nbsp;' . $ket_link . '</a>';

// echo 'POST: ';
// print_r($_POST);
// echo '<br>';
// echo 'datab : ' . $datab . '<br>';
// echo 'nick : ' . $nick . '<br>';
// echo 'data : ' . $data . '<br>';
// echo 'link_editprofil : ' . $link_editprofil . '<br>';

// die;
// form ubahprofil
if (@$_POST['ubahprofil']) {

    $emailbaru = strtolower($_POST['email']);
    $emaillama = strtolower($_POST['emaillama']);
    $fotobaru_ = $_FILES['foto']['name'];
    $fotobaru = @$_FILES['fotobaru']['name'];
    $fotolama = $_POST['fotolama'];


    if ($emailbaru == '') {
        $emailbaru = $emaillama;
        $pesan_email = ' <h6><i class="icon fa fa-ban"></i>&nbsp;Email tidak diubah</h6>';
        $update_email = false;
        // $_SESSION['pesan_error'] = '<h4><i class="icon fa fa-check"></i> Batal!</h4>Email Kosong<br>' . $pesan_email;
        // $_SESSION['alert_bg'] = 'alert-danger';
        // echo '<meta http-equiv="refresh" content="0; url=?' . $link_editprofil_srt . '">';
        // header("Location: " . $link_editprofil);
    } else if ($emailbaru == $emaillama) {
        $pesan_email = ' <h6><i class="icon fa fa-ban"></i>&nbsp;Email tetap</h6>';
        $update_email = false;
    } else {
        $pesan_email = ' <h6><i class="icon fa fa-check"></i>&nbsp;Email berubah.</h6><br><h5>Email baru ini akan dugunakan untuk login ke akun anda pada login selanjutnya => ' . $emailbaru . '</h5>';
        $update_email = true;
    }
    // echo 'Email: ' . $emailbaru . '<br>';
    // echo 'Foto baru: ' . $fotobaru . '<br>';
    // echo 'Foto baru__: ' . $fotobaru_ . '<br>';
    // echo 'Foto Lama: ' . $fotolama . '<br>';

    if ($fotobaru != '') {
        $update_foto = true;

        // echo 'Masuk, Jika ada foto<br>';

        $x = explode('.', $fotobaru);
        $ekstensi = strtolower(end($x));

        // echo 'Ekstensi: ' . $ekstensi . '<br>';

        $ekstensi_diperbolehkan    = array('png', 'jpg', 'jpeg', "gif");
        $file_tmp = $_FILES['fotobaru']['tmp_name'];

        // echo 'File Tmp: ' . $file_tmp . '<br>';

        $fotobaru = "_" . $nick . "_" . $fotobaru;

        // echo 'Foto Baru: ' . $fotobaru . '<br>';

        if (in_array($ekstensi, $ekstensi_diperbolehkan) == true) {

            // echo 'Masuk, Jika foto cocok!<br>';

            //Mengupload foto
            $foto_terupload = move_uploaded_file($file_tmp, '../img/user/' . $fotobaru);

            $pesan_foto = ' <h6><i class="icon fa fa-check"></i>&nbsp;Foto profil berhasil diubah. Efek akan berubah setelah log out dan log in kembali.</h6>';

            // if ($foto_terupload) {
            //     echo 'Foto Terupload!<br>' . '../img/user/' . $fotobaru;
            // } else {
            //     echo 'Foto Gagal Terupload!<br>';
            // }
        } else {

            // echo 'Masuk, Jika foto tidak cocok!<br>';
            $_SESSION['pesan_error'] = 'Ekstensi foto tidak diperbolehkan. Silahkan pilih foto lain.';
            $alert_bg = 'alert-danger';
            header("Location: " . $link_editprofil);
        }
    } else {
        // echo 'Masuk, Jika tidak ada foto<br>';
        $fotobaru = $fotolama;
        $pesan_foto = ' <h6><i class="icon fa fa-ban"></i>&nbsp;Foto profil tetap.</h6>';
        $update_foto = false;
    }

    // die;

    if (!$konek) {
        // echo '<script>alert("Koneksi gagal")</script>';
        $_SESSION['pesan_error'] = '<h4><i class="icon fa fa-times"></i> Koneksi gagal</h4>' . '<br>' . '<h6>' . mysqli_connect_error() . '</h6>';
        $_SESSION['alert_bg'] = 'alert-danger';
        // header("Location: ../error404_2");
    } else {

        if ($update_email == true) {
            $emailbaru = mysqli_real_escape_string($konek, $emailbaru);
            $sql = "SELECT * FROM `$datab` WHERE email = '$emailbaru'";
            $query = mysqli_query($konek, $sql);
            $cek = mysqli_num_rows($query);


            if ($cek > 0) {
                // echo '<script>alert("Email sudah digunakan")</script>';
                $_SESSION['pesan_error'] = '<h4><i class="icon fa fa-times"></i> Gagal! Ubah Profil</h4>Email sudah digunakan';
                $_SESSION['alert_bg'] = 'alert-danger';
            } else {

                if ($update_foto == true) {
                    $sql = "UPDATE `$datab` SET email = '$emailbaru', foto = '$fotobaru' WHERE nick = '$nick'";
                } elseif ($update_foto == false) {
                    $sql = "UPDATE `$datab` SET email = '$emailbaru' WHERE nick = '$nick'";
                }

                $query = mysqli_query($konek, $sql);
                // echo '<script>alert("Email berhasil diubah")</script>';
                $_SESSION['pesan_error'] = '<h4><i class="icon fa fa-check"></i>&nbsp;Berhasil!</h4><br>' . $pesan_foto . $pesan_email . ' ' . $link_back_link;
                $_SESSION['alert_bg'] = 'alert-success';
            }
        } else {
            if ($update_foto == true) {
                $sql = "UPDATE `$datab` SET foto = '$fotobaru' WHERE nick = '$nick'";

                $query = mysqli_query($konek, $sql);
                // echo '<script>alert("Email berhasil diubah")</script>';
                $_SESSION['pesan_error'] = '<h4><i class="icon fa fa-check"></i>&nbsp;Berhasil!</h4><br>' . $pesan_foto . $pesan_email . ' ' . $link_back_link;
                $_SESSION['alert_bg'] = 'alert-success';
            } elseif ($update_foto == false) {
                $_SESSION['pesan_error'] = '<h4><i class="icon fa fa-ban"></i>&nbsp;Batal!</h4>Tidak ada data yang diubah ' . $link_back_link;
                $_SESSION['alert_bg'] = 'alert-warning';
            }
        }
    }

    echo '<meta http-equiv="refresh" content="0; url=?' . $link_editprofil_srt . '">';
    header("Location: " . $link_editprofil);
}

// form gantipassword
if (@$_POST['gantipassword']) {

    $password_lama = md5($_POST['password_lama']);
    $password_baru = $_POST['password_baru'];
    $password_baru_ulang = $_POST['password_baru_ulang'];

    // echo 'Password Lama: ' . $_POST['password_lama'] . '<br>';
    // echo 'Password Lama (md5): ' . $password_lama . '<br>';
    // echo 'Password Baru: ' . $password_baru . '<br>';
    // echo 'Password Baru Ulang: ' . $password_baru_ulang . '<br>';

    // cek password lama
    $sql = "SELECT * FROM `$datab` WHERE nick = '$nick'";
    $query = mysqli_query($konek, $sql);
    $cek = mysqli_num_rows($query);

    // echo 'Cek: ' . $cek . '<br>';

    if ($cek > 0) {

        // echo 'Masuk, Jika ada data<br>';

        $data = mysqli_fetch_assoc($query);
        $password_lama_ = $data['password'];

        // echo 'Password Lama (md5): ' . $password_lama_ . '<br>';

        if ($password_lama == $password_lama_) {
            // cek password baru
            if ($password_baru == $password_baru_ulang) {
                // echo 'Password baru cocok<br>';

                // echo 'Password Baru: ' . $password_baru . '<br>';

                $password_baru = md5($password_baru);

                // echo 'Password Baru (md5): ' . $password_baru . '<br>';
                // die;

                $sql = "UPDATE `$datab` SET password = '$password_baru' WHERE nick = '$nick'";
                $query = mysqli_query($konek, $sql);
                // echo '<script>alert("Password berhasil diubah")</script>';
                $_SESSION['pesan_error'] = '<h4><i class="icon fa fa-check"></i> Berhasil! Ubah Password</h4>Password berhasil diubah' . $link_back_link;
                $_SESSION['alert_bg'] = 'alert-success';
                echo '<meta http-equiv="refresh" content="0; url=?' . $link_editprofil_srt . '">';
                header("Location: " . $link_editprofil);
            } else {
                // echo 'Password baru tidak cocok<br>';
                // die;

                $_SESSION['pesan_error'] = '<h4><i class="icon fa fa-times"></i> Gagal! Ubah Password</h4>Password baru tidak cocok ' . $link_back_link;
                $_SESSION['alert_bg'] = 'alert-danger';
                echo '<meta http-equiv="refresh" content="0; url=?' . $link_editprofil_srt . '">';
                header("Location: " . $link_editprofil);
            }
        } else {
            // echo 'Password lama tidak cocok<br>';
            // die;

            $_SESSION['pesan_error'] = '<h4><i class="icon fa fa-times"></i> Gagal! Ubah Password</h4>Password lama tidak cocok' . $link_back_link;
            $_SESSION['alert_bg'] = 'alert-danger';
            echo '<meta http-equiv="refresh" content="0; url=?' . $link_editprofil_srt . '">';
            header("Location: " . $link_editprofil);
        }
    } else {
        // echo 'Password lama tidak cocok<br>';
        // die;

        $_SESSION['pesan_error'] = '<h4><i class="icon fa fa-times"></i> Gagal! Ubah Password</h4>Password lama tidak cocok' . $link_back_link;
        $_SESSION['alert_bg'] = 'alert-danger';
        echo '<meta http-equiv="refresh" content="0; url=?' . $link_editprofil_srt . '">';
        header("Location: " . $link_editprofil);
    }
}

// form simpanbio
if (@$_POST['simpanbio']) {

    $bio_pendidikan = $_POST['pendidikan'];
    $bio_alamat = $_POST['alamat'];
    $bio_hobi = $_POST['hobi'];
    $bio_notes = $_POST['notes'];

    // ambil data tentang dari database
    $sql = "SELECT tentang FROM `$datab` WHERE nick = '$nick'";
    $query = mysqli_query($konek, $sql);
    $tentang = mysqli_fetch_assoc($query);

    

    $tentang_bio = array();
    $tentang_bio = explode('#', $tentang['tentang']);

    // ubah data tentang
    $tentang_bio[1] = $bio_pendidikan;
    $tentang_bio[2] = $bio_alamat;
    $tentang_bio[3] = $bio_hobi;
    $tentang_bio[4] = $bio_notes;

    // penggabungan array tentang
    $tentang_bio = implode('#', $tentang_bio);

    // update data tentang
    $sql = "UPDATE `$datab` SET tentang = '$tentang_bio' WHERE nick = '$nick'";
    $query = mysqli_query($konek, $sql);

    if ($query) {
        // echo 'Berhasil' . '<br>' . $sql . '<br>' . $tentang_bio;
        $_SESSION['pesan_error'] = '<h4><i class="icon fa fa-check"></i> Berhasil! Ubah Bio</h4>Bio Telah diubah' . $link_back_link;
        $_SESSION['alert_bg'] = 'alert-success';
        echo '<meta http-equiv="refresh" content="0; url=?' . $link_editprofil_srt . '">';
        header("Location: " . $link_editprofil);
    } else {
        // echo 'Gagal' . '<br>' . $sql . '<br>' . $tentang_bio . '<br>' . mysqli_error($konek);
        $_SESSION['pesan_error'] = '<h4><i class="icon fa fa-times"></i> Gagal! Ubah Bio</h4>Bio Gagal diubah' . $link_back_link;
        $_SESSION['alert_bg'] = 'alert-danger';
        echo '<meta http-equiv="refresh" content="0; url=?' . $link_editprofil_srt . '">';
        header("Location: " . $link_editprofil);
    }
}

// form templatepesan
if (@$_POST['templatepesan']) {

    $template_pesan = $_POST['temp_pesan'];

    $sql = "UPDATE `$datab` SET template_pesan = '$template_pesan' WHERE nick = '$nick'";
    $query = mysqli_query($konek, $sql);

    if ($query) {
        // echo 'Berhasil' . '<br>' . $sql . '<br>' . $template_pesan;
        $_SESSION['pesan_error'] = '<h4><i class="icon fa fa-check"></i> Berhasil! Ubah Template Pesan</h4>Template Pesan Telah diubah' . $link_back_link;
        $_SESSION['alert_bg'] = 'alert-success';
        echo '<meta http-equiv="refresh" content="0; url=?' . $link_editprofil_srt . '">';
        header("Location: " . $link_editprofil);
    } else {
        // echo 'Gagal' . '<br>' . $sql . '<br>' . $template_pesan . '<br>' . mysqli_error($konek);
        $_SESSION['pesan_error'] = '<h4><i class="icon fa fa-times"></i> Gagal! Ubah Template Pesan</h4>Template Pesan Gagal diubah' . $link_back_link;
        $_SESSION['alert_bg'] = 'alert-danger';
        echo '<meta http-equiv="refresh" content="0; url=?' . $link_editprofil_srt . '">';
        header("Location: " . $link_editprofil);
    }
}

// form kontak
if (@$_POST['kontak']) {

    $nomorhp = @$_POST['nomorhp'];
    $website = @$_POST['website'];
    $instagram = @$_POST['instagram'];
    $facebook = @$_POST['facebook'];
    $twitter = @$_POST['twitter'];
    $line = @$_POST['line'];
    $youtube = @$_POST['youtube'];
    $whatsapp = @$_POST['whatsapp'];

    // ambil data tentang dari database
    $sql = "SELECT tentang FROM `$datab` WHERE nick = '$nick'";
    $query = mysqli_query($konek, $sql);
    $tentang = mysqli_fetch_assoc($query);

    $tentang_bio = array();
    $tentang_bio = explode('#', $tentang['tentang']);

    // mengambil hanya angka
    $nomorhp = preg_replace('/[^0-9]/', '', @$nomorhp);
    $whatsapp = preg_replace('/[^0-9]/', '', @$whatsapp);

    // menghilangkan angka 0 jika ada angka 0 di depan
    if (substr(@$nomorhp, 0, 1) == 0) {
        $nomorhp = substr(@$nomorhp, 1);
    }

    if (substr(@$whatsapp, 0, 1) == 0) {
        $whatsapp = substr(@$whatsapp, 1);
    }

    // $nomorhp = @$nomorhp ? '62' . $nomorhp : '';
    // $whatsapp = @$whatsapp ? '62' . $whatsapp : '';

    // ubah data tentang
    $tentang_bio[5] = $nomorhp;
    $tentang_bio[6] = $instagram;
    $tentang_bio[7] = $facebook;
    $tentang_bio[8] = $twitter;
    $tentang_bio[9] = $line;
    $tentang_bio[10] = $whatsapp;
    $tentang_bio[11] = $website;
    $tentang_bio[12] = $youtube;

    $tentang_bio = implode('#', $tentang_bio);

    // update data tentang
    $sql = "UPDATE `$datab` SET tentang = '$tentang_bio' WHERE nick = '$nick'";
    $query = mysqli_query($konek, $sql);

    if ($query) {
        // echo 'Berhasil' . '<br>' . $sql . '<br>' . $tentang_bio;
        $_SESSION['pesan_error'] = '<h4><i class="icon fa fa-check"></i> Berhasil! Ubah Kontak</h4>Kontak Telah diubah' . $link_back_link;
        $_SESSION['alert_bg'] = 'alert-success';
        echo '<meta http-equiv="refresh" content="0; url=?' . $link_editprofil_srt . '">';
        header("Location: " . $link_editprofil);
    } else {
        // echo 'Gagal' . '<br>' . $sql . '<br>' . $tentang_bio . '<br>' . mysqli_error($konek);
        $_SESSION['pesan_error'] = '<h4><i class="icon fa fa-times"></i> Gagal! Ubah Kontak</h4>Kontak Gagal diubah' . $link_back_link;
        $_SESSION['alert_bg'] = 'alert-danger';
        echo '<meta http-equiv="refresh" content="0; url=?' . $link_editprofil_srt . '">';
        header("Location: " . $link_editprofil);
    }
}

$tentang = explode('#', @$data['tentang']);
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

// echo 'tentang : <br>';
// print_r($tentang);
// echo '<br>';

// echo 'tentang_pendidikan : ' . $tentang_pendidikan . '<br>';
// echo 'tentang_alamat : ' . $tentang_alamat . '<br>';
// echo 'tentang_hobi : ' . $tentang_hobi . '<br>';
// echo 'tentang_notes : ' . $tentang_notes . '<br>';
// echo 'tentang_nomorhp : ' . $tentang_nomorhp . '<br>';
// echo 'tentang_ig : ' . $tentang_ig . '<br>';
// echo 'tentang_fb : ' . $tentang_fb . '<br>';
// echo 'tentang_twitter : ' . $tentang_twitter . '<br>';
// echo 'tentang_line : ' . $tentang_line . '<br>';
// echo 'tentang_wa : ' . $tentang_wa . '<br>';
// echo 'tentang_web : ' . $tentang_web . '<br>';
// echo 'tentang_youtube : ' . $tentang_youtube . '<br>';

// die;


// $title = 'Edit Profil';
$navlink = 'Profil';
$navlink_sub = 'editprofil';
include('views/header.php');
include('views/_formeditprofil.php');
mysqli_close($konek);
include('views/footer.php');

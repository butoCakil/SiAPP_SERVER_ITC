<?php
include('app/get_user.php');
// include "../config/konesi.php";

// $disabel_preload = true;

if ($level_login == 'admin' || $level_login == 'superadmin') {

    // --- tangkap POST --- //
    if (@$_POST['setting1']) {
        $info = $_POST['pilihhari'];
        $waktumasuk = $_POST['waktumasuk'];
        $waktupulang = $_POST['waktupulang'];

        $sql = "UPDATE statusnya SET waktumasuk='$waktumasuk', waktupulang='$waktupulang', info='$info'";
        $update = mysqli_query($konek, $sql);

        if ($update) {
            // echo '<script>alert("Berhasil mengubah setting 1");</script>';
            $pesan = "Berhasil update pengaturan waktu masuk dan waktu pulang";
        } else {
            echo '<script>alert("Gagal mengubah setting 1 ' . mysqli_error($konek) . '");</script>';
            $pesan = ("Error: "  . $pesan . $sql . "<br>" . mysqli_error($konek));
        }
    }

    if (@$_POST['setting2']) {
        $wa = $_POST['bukamasuk'];
        $wta = $_POST['tutupmasuk'];
        $wtp = $_POST['bukapulang'];
        $wp = $_POST['tutuppulang'];

        $sql = "UPDATE statusnya SET wa='$wa', wta='$wta', wtp='$wtp', wp='$wp'";

        $update = mysqli_query($konek, $sql);

        if ($update) {
            // echo '<script>alert("Berhasil mengubah setting 2");</script>';
            $pesan = "Berhasil update pengaturan Batas waktu akses";
        } else {
            echo '<script>alert("Gagal mengubah setting 2 ' . mysqli_error($konek) . '");</script>';
            $pesan = ("Error: "  . $pesan . $sql . "<br>" . mysqli_error($konek));
        }
    }

    if (@$_POST['set_admin'] == 'set') {

        $nick_user = $_POST['nick_user'];
        $level_login_user = @$_POST['level_login'];
        $akses_user = $_POST['akses'];
        $ket_akses_user = $_POST['ket_akses'];

        if ($ket_akses_user == '-') {
            $ket_akses_user = '';
        }

        $query_set_admin = "UPDATE dataguru SET level_login = '$level_login_user', akses = '$akses_user', ket_akses = '$ket_akses_user' WHERE nick = '$nick_user'";
        $sql_set_admin = mysqli_query($konek, $query_set_admin);

        if ($sql_set_admin) {
            // echo '<script>alert("Berhasil mengubah data admin");</script>';
            echo '<script>location.href="setting.php";</script>';
        } else {
            echo '<script>alert("Gagal mengubah data admin");</script>';
            echo '<script>location.href="setting.php";</script>';
        }
    } else if (@$_POST['set_admin'] == 'del') {

        if (@$_POST['cekbox'] == 'cek') {
            $nick_user = $_POST['nick_user'];

            $query_set_admin = "UPDATE dataguru SET level_login = NULL, akses = NULL, ket_akses = NULL WHERE nick = '$nick_user'";
            $sql_set_admin = mysqli_query($konek, $query_set_admin);

            if ($sql_set_admin) {
                // echo '<script>alert("Berhasil menghapus akses admin");</script>';
                echo '<script>location.href="setting.php";</script>';
            } else {
                echo '<script>alert("Gagal menghapus akses admin");</script>';
                echo '<script>location.href="setting.php";</script>';
            }
        }
    } else {
    }
    // -------------------- //

    if (@$_POST['tambahadmin']) {
        $username_admin = $_POST['usernameadmin'];
        $email_admin = $_POST['emailadmin'];
        $password_admin = md5($_POST['passwordadmin']);
        $kontak_admin = $_POST['kontakadmin'];
        $foto = $_POST['foto'];
        $status = 'logout';

        $query_tambah_admin = "INSERT INTO admin (username, email, password, status, wa, foto) VALUES ('$username_admin', '$email_admin', '$password_admin', '$status', '$kontak_admin', '$foto')";
        $sql_tambah_admin = mysqli_query($konek, $query_tambah_admin);

        if ($sql_tambah_admin) {
            echo '<script>alert("Berhasil menambah admin, ' . $username_admin . '");</script>';
            echo '<script>location.href="setting.php";</script>';
        } else {
            echo '<script>alert("Gagal menambah admin, ' . $username_admin . '");</script>';
            echo '<script>location.href="setting.php";</script>';
        }
    }

    // -----uabah admin----- //

    if (@$_POST['ubahdataadmin']) {
        $usernameadminlama = $_POST['usernameadminlama'];
        $usernameadminbaru = $_POST['usernameadminbaru'];
        $emailadminlama = $_POST['emailadminlama'];
        $emailadminbaru = $_POST['emailadminbaru'];
        $passwordadminlama = $_POST['passwordadminlama'];
        $passwordadminbaru = $_POST['passwordadminbaru'];
        $passwordadminbaru_ulang = $_POST['passwordadminbaru_ulang'];

        if ($passwordadminbaru == $passwordadminbaru_ulang) {
            $passwordadminbaru = md5($passwordadminbaru);

            $nama_login = $_SESSION['username_login'] = $usernameadminbaru;
            $email_login = $_SESSION['email_login'] = $emailadminbaru;
            $password_login = $_SESSION['password_login'] = $passwordadminbaru_ulang;

            $query_ubah_admin = "UPDATE admin SET username = '$usernameadminbaru', email = '$emailadminbaru', password = '$passwordadminbaru' WHERE username = '$usernameadminlama' AND email = '$emailadminlama'";
            $sql_ubah_admin = mysqli_query($konek, $query_ubah_admin);

            if ($sql_ubah_admin) {
                // echo '<script>alert("Berhasil mengubah data admin, ' . $usernameadminlama . '");</script>';
                echo '<script>location.href="setting.php";</script>';
            } else {
                echo '<script>alert("Gagal mengubah data admin, ' . $usernameadminlama . '");</script>';
                echo '<script>location.href="setting.php";</script>';
            }
        } else {
            echo '<script>alert("Password baru tidak sama");</script>';
            echo '<script>location.href="setting.php";</script>';
        }
    }

    // --------------------- //


    // -----hapus admin----- //

    if (@$_POST['hapusadmin']) {
        $id_admin = $_POST['id_hapus'];

        $query_hapus_admin = "DELETE FROM admin WHERE id = '$id_admin'";
        $sql_hapus_admin = mysqli_query($konek, $query_hapus_admin);

        if ($sql_hapus_admin) {
            // echo '<script>alert("Berhasil menghapus admin");</script>';
            echo '<script>location.href="setting.php";</script>';
        } else {
            echo '<script>alert("Gagal menghapus admin <br>' . mysqli_error($konek) . '");</script>';
            echo '<script>location.href="setting.php";</script>';
        }
    }
    // --------------------- //


    // -------------------- //
    $sql = mysqli_query($konek, "SELECT * FROM statusnya");
    $data = mysqli_fetch_array($sql);

    $info = $data['info'];
    $waktumasuk = $data['waktumasuk'];
    $waktupulang = $data['waktupulang'];
    $wa = $data['wa'];
    $wta = $data['wta'];
    $wtp = $data['wtp'];
    $wp = $data['wp'];

    if ($info == 5) {
        $harikerja = "Senin - Jum'at";
    } elseif ($info == 6) {
        $harikerja = "Senin - Sabtu";
    } elseif ($info == 7) {
        $harikerja = "Semua Hari";
    } else {
        $harikerja = "";
    }

    $title = 'Setting';
    $navlink = 'Form Ijin';

    include('views/header.php');
    include('views/_setting.php');
    mysqli_close($konek);
    include('views/footer.php');
} else {
    $pesan = "Maaf bossku,<br>Anda tidak memiliki akses ke menu ini!";
    $_SESSION['pesan'] = $pesan;
    header('location: 404.php');
}

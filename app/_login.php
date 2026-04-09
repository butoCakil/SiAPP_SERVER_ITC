<?php
date_default_timezone_set('Asia/Jakarta');
$timestamp = date('Y-m-d H:i:s');

include('config/konesi.php');

error_reporting(0);
ini_set('session.cookie_lifetime', 2000000);
session_start();

if ($_POST['login']) {

    $email = $_POST['username'];
    $oripass = $_POST['password'];
    $password = md5($_POST['password']);

    if (!$email) {
        $_SESSION['pesan_error'] = 'Email tidak boleh kosong!';
        header("Location: ../");
    } elseif (!$oripass) {
        $_SESSION['pesan_error'] = 'Password tidak boleh kosong!';
        header("Location: ../");
    } elseif (strlen($oripass) < 4) {
        $_SESSION['pesan_error'] = 'Password minimal 4 karakter!';
        header("Location: ../");
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['pesan_error'] = 'Email tidak valid!';
            header("Location: ../");
        } else {

            // Prepare the SELECT statement to retrieve data from the 'admin' table
            $query_select_admin = "SELECT * FROM admin WHERE username = ? OR email = ?";
            $stmt_select_admin = mysqli_stmt_init($konek);
            mysqli_stmt_prepare($stmt_select_admin, $query_select_admin);
            mysqli_stmt_bind_param($stmt_select_admin, "ss", $email, $email);
            mysqli_stmt_execute($stmt_select_admin);
            $result_select_admin = mysqli_stmt_get_result($stmt_select_admin);

            if (mysqli_num_rows($result_select_admin) == 1) {
                $row = mysqli_fetch_assoc($result_select_admin);
                if ($password == $row['password']) {
                    $_SESSION['username_login'] = $row['username'];
                    $_SESSION['email_login'] = $row['email'];
                    $_SESSION['password_login'] = $_POST['password'];
                    $_SESSION['foto_login'] = $row['foto'];
                    $_SESSION['info_login'] = 'Super Admin';
                    $_SESSION['level_login'] = 'superadmin';
                    $_SESSION['akses'] = 'admin';
                    $_SESSION['ket_akses'] = 'superadmin';
                    $_SESSION['datab_login'] = '';

                    // Prepare the UPDATE statement to update the 'admin' table
                    $query_update_admin = "UPDATE admin SET status = 'login' WHERE email = ?";
                    $stmt_update_admin = mysqli_stmt_init($konek);
                    mysqli_stmt_prepare($stmt_update_admin, $query_update_admin);
                    mysqli_stmt_bind_param($stmt_update_admin, "s", $row['email']);
                    $login = mysqli_stmt_execute($stmt_update_admin);

                    $_SESSION['pesan_login'] = 'Anda login sebagai super admin : ' . $row['username'];

                    header("Location: beranda/statistik.php");
                } else {
                    $_SESSION['pesan_error'] = 'Password salah!';
                    header("Location: ../");
                }
            } else {
                // Handle the case for non-admin users

                // Prepare the SELECT statement to retrieve data from the 'dataguru' table
                $query_select_dataguru = "SELECT * FROM dataguru WHERE email = ?";
                $stmt_select_dataguru = mysqli_stmt_init($konek);
                mysqli_stmt_prepare($stmt_select_dataguru, $query_select_dataguru);
                mysqli_stmt_bind_param($stmt_select_dataguru, "s", $email);
                mysqli_stmt_execute($stmt_select_dataguru);
                $result_select_dataguru = mysqli_stmt_get_result($stmt_select_dataguru);

                if (mysqli_num_rows($result_select_dataguru) == 1) {
                    $row = mysqli_fetch_assoc($result_select_dataguru);
                    if ($password == $row['password']) {
                        $_SESSION['nokartu_login'] = $row['nokartu'];
                        $_SESSION['username_login'] = $row['nama'];
                        $_SESSION['nick_login'] = $row['nick'];
                        $_SESSION['email_login'] = $row['email'];
                        $_SESSION['password_login'] = $_POST['password'];
                        $_SESSION['foto_login'] = $row['foto'];
                        $_SESSION['info_login'] = $row['jabatan'];
                        $_SESSION['level_login'] = @$row['level_login'] ? $row['level_login'] : 'user_gtk';
                        $_SESSION['akses'] = @$row['akses'] ? $row['akses'] : 'GTK';
                        $_SESSION['ket_akses'] = @$row['ket_akses'] ? $row['ket_akses'] : '';
                        $_SESSION['datab_login'] = 'dataguru';

                        // Prepare the UPDATE statement to update the 'dataguru' table
                        $query_update_dataguru = "UPDATE dataguru SET login = 'login' WHERE email = ?";
                        $stmt_update_dataguru = mysqli_stmt_init($konek);
                        mysqli_stmt_prepare($stmt_update_dataguru, $query_update_dataguru);
                        mysqli_stmt_bind_param($stmt_update_dataguru, "s", $row['email']);
                        $login = mysqli_stmt_execute($stmt_update_dataguru);

                        $_SESSION['pesan_login'] = 'Anda login sebagai GTK : ' . $row['nama'];

                        header("Location: beranda");
                    } else {
                        $_SESSION['pesan_error'] = $row['nama'] . ', Password Anda salah!';
                        header("Location: ../");
                    }
                } else {

                    // Prepare the SELECT statement to retrieve data from the 'datasiswa' table
                    $query_select_datasiswa = "SELECT * FROM datasiswa WHERE email = ?";
                    $stmt_select_datasiswa = mysqli_stmt_init($konek);
                    mysqli_stmt_prepare($stmt_select_datasiswa, $query_select_datasiswa);
                    mysqli_stmt_bind_param($stmt_select_datasiswa, "s", $email);
                    mysqli_stmt_execute($stmt_select_datasiswa);
                    $result_select_datasiswa = mysqli_stmt_get_result($stmt_select_datasiswa);

                    if (mysqli_num_rows($result_select_datasiswa) == 1) {
                        $row = mysqli_fetch_assoc($result_select_datasiswa);
                        if ($password == $row['password']) {
                            $_SESSION['nokartu_login'] = $row['nokartu'];
                            $_SESSION['username_login'] = $row['nama'];
                            $_SESSION['nick_login'] = $row['nick'];
                            $_SESSION['email_login'] = $row['email'];
                            $_SESSION['password_login'] = $_POST['password'];
                            $_SESSION['foto_login'] = $row['foto'];
                            $_SESSION['info_login'] = $row['kelas'];
                            $_SESSION['level_login'] = 'user_siswa';
                            $_SESSION['akses'] = '';
                            $_SESSION['ket_akses'] = '';
                            $_SESSION['datab_login'] = 'datasiswa';

                            // Prepare the UPDATE statement to update the 'datasiswa' table
                            $query_update_datasiswa = "UPDATE datasiswa SET login = 'login' WHERE email = ?";
                            $stmt_update_datasiswa = mysqli_stmt_init($konek);
                            mysqli_stmt_prepare($stmt_update_datasiswa, $query_update_datasiswa);
                            mysqli_stmt_bind_param($stmt_update_datasiswa, "s", $row['email']);
                            $login = mysqli_stmt_execute($stmt_update_datasiswa);

                            $_SESSION['pesan_login'] = 'Anda login sebagai siswa : ' . $row['nama'];

                            header("Location: beranda");
                        } else {
                            $_SESSION['pesan_error'] = $row['nama'] . ', Passwordmu salah!';
                            header("Location: ../");
                        }
                    } else {
                        $_SESSION['pesan_error'] = 'Username tidak ditemukan!';
                        header("Location: ../");
                    }
                }
            }

            if ($_POST['ingataku']) {
                $_SESSION['ingataku'] = $_POST['ingataku'];
                setcookie('username_login', $_POST['username'], time() + (60 * 60 * 24 * 30), "/");
                setcookie('password_login', $_POST['password'], time() + (60 * 60 * 24 * 30), "/");
                ini_set('session.cookie_lifetime', 2000000);
            } else {
                setcookie('username_login', '', time() - (60 * 60), "/");
                setcookie('password_login', '', time() - (60 * 60), "/");
                ini_set('session.cookie_lifetime', 0);
            }

            // Close the prepared statements
            mysqli_stmt_close($stmt_select_admin);
            mysqli_stmt_close($stmt_select_dataguru);
            mysqli_stmt_close($stmt_select_datasiswa);
            mysqli_stmt_close($stmt_update_admin);
            mysqli_stmt_close($stmt_update_dataguru);
            mysqli_stmt_close($stmt_update_datasiswa);
        }
    }

    mysqli_close($konek);
} else {
    $_SESSION['pesan_error'] = 'Silahkan login terlebih dahulu!';
    header("Location: ../");
}

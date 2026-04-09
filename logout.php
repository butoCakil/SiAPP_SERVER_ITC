<?php
session_start();

if (@$_POST['tetapingat'] == 'tetapingat') {
    setcookie('username_login', $_SESSION['email_login'], time() + (60 * 60 * 24 * 30), "/");
    setcookie('password_login', $_SESSION['password_login'], time() + (60 * 60 * 24 * 30), "/");
} elseif (@$_POST['lupakan'] == 'lupakan') {
    setcookie('username_login', '', time() - (60 * 60), "/");
    setcookie('password_login', '', time() - (60 * 60), "/");
}

include('config/konesi.php');

if (@$_SESSION['datab_login'] == '') {
    if (@$_SESSION['level_login'] == 'superadmin') {
        $email = $_SESSION['email_login'];
        $info_logout = 'Admin';

        // Prepare the UPDATE statement to update the 'admin' table
        $query_update_admin = "UPDATE admin SET status = 'logout' WHERE email = ?";
        $stmt_update_admin = mysqli_stmt_init($konek);
        mysqli_stmt_prepare($stmt_update_admin, $query_update_admin);
        mysqli_stmt_bind_param($stmt_update_admin, "s", $email);
        $status = mysqli_stmt_execute($stmt_update_admin);

        mysqli_stmt_close($stmt_update_admin);
    }
} elseif (@$_SESSION['datab_login'] == 'dataguru') {
    $nokartu = $_SESSION['nokartu_login'];
    $info_logout = 'GTK';

    // Prepare the UPDATE statement to update the 'dataguru' table
    $query_update_dataguru = "UPDATE dataguru SET login = 'logout' WHERE nokartu = ?";
    $stmt_update_dataguru = mysqli_stmt_init($konek);
    mysqli_stmt_prepare($stmt_update_dataguru, $query_update_dataguru);
    mysqli_stmt_bind_param($stmt_update_dataguru, "s", $nokartu);
    $status = mysqli_stmt_execute($stmt_update_dataguru);

    mysqli_stmt_close($stmt_update_dataguru);
} elseif (@$_SESSION['datab_login'] == 'datasiswa') {
    $nokartu = $_SESSION['nokartu_login'];
    $info_logout = 'Siswa';

    // Prepare the UPDATE statement to update the 'datasiswa' table
    $query_update_datasiswa = "UPDATE datasiswa SET login = 'logout' WHERE nokartu = ?";
    $stmt_update_datasiswa = mysqli_stmt_init($konek);
    mysqli_stmt_prepare($stmt_update_datasiswa, $query_update_datasiswa);
    mysqli_stmt_bind_param($stmt_update_datasiswa, "s", $nokartu);
    $status = mysqli_stmt_execute($stmt_update_datasiswa);

    mysqli_stmt_close($stmt_update_datasiswa);
} else {
    // Handle the case for unknown user type
}

mysqli_close($konek);

$pesan = @$_SESSION['username_login'] . ', Anda telah keluar dari akun anda. Sebagai ' . $info_logout . '.';
session_destroy();
session_start();
$_SESSION['pesan'] = $pesan;

header("Location: ../");

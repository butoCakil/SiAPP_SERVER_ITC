<?php
$navlink_active_dash = '';
$navlink_active_1 = '';
$navlink_active_2 = '';
$navlink_active_3 = '';
$navlink_active_4 = '';
$navlink_active_5 = '';
$navlink_active_5x = '';
$navlink_active_5y = '';
$navlink_active_6 = '';
$navlink_active_7 = '';
$navlink_active_8 = '';
$menu_open_dash = '';
$menu_open_1 = '';
$menu_open_2 = '';
$menu_open_3 = '';
$menu_open_4 = '';
$menu_open_5 = '';
$menu_open_5x = '';
$menu_open_6 = '';
$menu_open_7 = '';
$menu_open_8 = '';
if (@$navlink == 'Beranda') {
    $navlink_active_1 = 'active';
    $menu_open_1 = 'menu-open';
} elseif (@$navlink == 'dashdevice') {
    $navlink_active_dash = 'active';
    $menu_open_dash = 'menu-open';
} elseif (@$navlink == 'Rekap') {
    if ($navlink_sub == 'bulanan') {
        $navlink_sub1_active_1 = 'active';
        $navlink_sub1_active_2 = '';
    } elseif ($navlink_sub == 'tahunan') {
        $navlink_sub1_active_1 = '';
        $navlink_sub1_active_2 = 'active';
    } else {
        $navlink_sub1_active_1 = '';
        $navlink_sub1_active_2 = '';
    }

    $navlink_active_2 = 'active';
    $menu_open_2 = 'menu-open';
} elseif (@$navlink == 'statistik') {
    $navlink_active_3 = 'active';
    $menu_open_3 = 'menu-open';

    if (@$_GET['page'] == 'chart') {
        $navlink_sub4_active_1 = 'active';
        $navlink_sub4_active_2 = '';
    } else if (@$_GET['page'] == 'table') {
        $navlink_sub4_active_1 = '';
        $navlink_sub4_active_2 = 'active';
    } else {
        $navlink_sub4_active_1 = '';
        $navlink_sub4_active_2 = '';
    }
} elseif (@$navlink == 'Wali') {
    $navlink_sub2_active_0 = '';
    $navlink_sub2_active_1 = '';
    $navlink_sub2_active_2 = '';
    $navlink_sub2_active_3 = '';
    $navlink_sub2_active_4 = '';
    $navlink_sub2_active_5 = '';
    $navlink_sub2_active_6 = '';
    $navlink_sub2_active_7 = '';
    $navlink_sub2_active_8 = '';
    $navlink_sub2_active_9 = '';

    if ($navlink_sub == 'harian') {
        $navlink_sub2_active_1 = 'active';
    } elseif ($navlink_sub == 'bulanan') {
        $navlink_sub2_active_2 = 'active';
    } elseif ($navlink_sub == 'tahunan') {
        $navlink_sub2_active_3 = 'active';
    } elseif ($navlink_sub == 'kontak') {
        $navlink_sub2_active_5 = 'active';
    }

    if ($navlink_sub == 'semuakelas') {
        $menu_open_sub_1 = '';
        $menu_open_sub_2 = 'menu-open';
        $navlink_sub2_active_0 = 'active';
    } elseif ($navlink_sub == 'kegiatan') {
        $menu_open_sub_1 = '';
        $menu_open_sub_2 = 'menu-open';
        $navlink_sub2_active_8 = 'active';
    } else if ($navlink_sub == 'nilaikelas') {
        $menu_open_sub_1 = '';
        $menu_open_sub_2 = 'menu-open';
        $navlink_sub2_active_7 = 'active';
    } else if ($navlink_sub != '' && $navlink_sub != 'semuakelas') {
        $menu_open_sub_1 = 'menu-open';
        $menu_open_sub_2 = '';
        $navlink_sub2_active_0 = '';
        $navlink_sub2_active_7 = '';
        $navlink_sub2_active_6 = '';
    } else if ($navlink_sub == '') {
        $menu_open_sub_1 = '';
        $menu_open_sub_2 = '';
        $navlink_sub2_active_0 = '';
        $navlink_sub2_active_7 = '';
        $navlink_sub2_active_6 = '';
    }

    $navlink_active_4 = 'active';
    $menu_open_4 = 'menu-open';
} elseif (@$navlink == 'Form Ijin') {
    $navlink_active_5 = 'active';
    // $menu_open_5 = 'menu-open';
} elseif (@$navlink == 'Profil') {
    if ($navlink_sub == 'profil') {
        $navlink_sub3_active_1 = 'active';
        $navlink_sub3_active_2 = '';
        $navlink_sub3_active_3 = '';
    } elseif ($navlink_sub == 'jadwal') {
        $navlink_sub3_active_1 = '';
        $navlink_sub3_active_2 = '';
        $navlink_sub3_active_3 = 'active';
    } elseif ($navlink_sub == 'editprofil') {
        $navlink_sub3_active_1 = '';
        $navlink_sub3_active_2 = 'active';
        $navlink_sub3_active_3 = '';
    } else {
        $navlink_sub3_active_1 = '';
        $navlink_sub3_active_2 = '';
        $navlink_sub3_active_3 = '';
    }

    $navlink_active_6 = 'active';
    $menu_open_6 = 'menu-open';
} elseif (@$navlink == 'Data Guru') {
    $navlink_active_7 = 'active';
    $menu_open_7 = 'menu-open';

    if ($navlink_sub == 'harian') {
        $navlink_sub3_active_1 = 'active';
        $navlink_sub3_active_2 = '';
        $navlink_sub3_active_3 = '';
        $navlink_sub3_active_4 = '';
        $navlink_sub2_active_6 = '';
        $navlink_sub2_active_9 = '';
    } elseif ($navlink_sub == 'bulanan') {
        $navlink_sub3_active_1 = '';
        $navlink_sub3_active_2 = 'active';
        $navlink_sub3_active_3 = '';
        $navlink_sub3_active_4 = '';
        $navlink_sub2_active_6 = '';
        $navlink_sub2_active_9 = '';
    } elseif ($navlink_sub == 'tahunan') {
        $navlink_sub3_active_1 = '';
        $navlink_sub3_active_2 = '';
        $navlink_sub3_active_3 = 'active';
        $navlink_sub3_active_4 = '';
        $navlink_sub2_active_6 = '';
        $navlink_sub2_active_9 = '';
    } elseif ($navlink_sub == 'kontak') {
        $navlink_sub3_active_1 = '';
        $navlink_sub3_active_2 = '';
        $navlink_sub3_active_3 = '';
        $navlink_sub3_active_4 = 'active';
        $navlink_sub2_active_6 = '';
        $navlink_sub2_active_9 = '';
    } elseif ($navlink_sub == 'dataguru') {
        $navlink_sub3_active_1 = '';
        $navlink_sub3_active_2 = '';
        $navlink_sub3_active_3 = '';
        $navlink_sub3_active_4 = '';
        $navlink_sub2_active_6 = 'active';
    } elseif ($navlink_sub == 'datawalikelas') {
        $navlink_sub3_active_1 = '';
        $navlink_sub3_active_2 = '';
        $navlink_sub3_active_3 = '';
        $navlink_sub3_active_4 = '';
        $navlink_sub2_active_6 = '';
        $navlink_sub2_active_9 = 'active';
    } else {
        $navlink_sub3_active_1 = '';
        $navlink_sub3_active_2 = '';
        $navlink_sub3_active_3 = '';
        $navlink_sub3_active_4 = '';
        $navlink_sub2_active_6 = '';
        $navlink_sub2_active_9 = '';
    }
} elseif (@$navlink == 'Kelas') {
    $navlink_active_8 = 'active';
    $menu_open_8 = 'menu-open';
} elseif (@$navlink == 'setID') {
    $navlink_active_5x = 'active';
    $menu_open_5x = 'menu-open';
} elseif (@$navlink == 'setKBM') {
    $navlink_active_5y = 'active';
    // $menu_open_5x = 'menu-open';
}

?>

<!-- Preloader -->
<!-- <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="<?= @$dir; ?>../img/logoInstitusi.png" alt="AdminLTELogo" height="60" width="60">
</div> -->

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light fixed-top">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="<?= @$dir; ?>../" class="nav-link">
                <!-- <i class="fas fa-home mr-1 text-cyan"></i> -->
                <img src="<?= @$dir; ?>../img/app/nfc.png" class="img-circle elevation" style="opacity: .8; height: 25px; width: 25px; margin-top: -5px;">
                Home
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="#" class="nav-link">
                <i class="fas fa-info-circle mr-1 text-indigo"></i>
                About
            </a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Navbar Search -->
        <li class="nav-item">
            <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                <i class="fas fa-search"></i>
            </a>
            <div class="navbar-search-block">
                <form action="<?= @$dir; ?>hasilcari.php" class="form-inline" method="GET">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" type="search" placeholder="Cari" aria-label="Search" name="katakunci">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit" name="cari" value="gtk">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>


        <!-- Notifications Dropdown Menu -->
        <!-- <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                    <span class="badge badge-warning navbar-badge">15</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item dropdown-header">15 Notifications</span>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-envelope mr-2"></i> 4 new messages
                        <span class="float-right text-muted text-sm">3 mins</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-users mr-2"></i> 8 friend requests
                        <span class="float-right text-muted text-sm">12 hours</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-file mr-2"></i> 3 new reports
                        <span class="float-right text-muted text-sm">2 days</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
                </div>
            </li> -->

        <li class="nav-item ">
            <!-- <a class="nav-link" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#" role="button">
                    <i class="fas fa-th-large"></i>
                </a> -->
            <a data-toggle="dropdown" href="#" class="nav-link nav-link" data-toggle="dropdown">
                <div style="display: flex;">
                    <div class="user-panel" style="display: flex; margin-bottom: -20px; margin-top: -10px;">
                        <div class="image mr-2">
                            <img src="<?= @$dir; ?>../img/user/<?= @$foto_login ? $foto_login : 'default.jpg'; ?>" class="elevation-2" alt="User Image" style="height: 40px; width: 40px; border-radius: 50%; object-fit: cover; object-position: top;">
                        </div>
                        <div style="margin-top: -10px; text-decoration: none;" class="info mr-3">
                            <?= @$nama_login; ?>
                        </div>
                    </div>
                    <div id="info_user_001">
                        <i class="fas fa-caret-down mr-2"></i>
                        <!-- <i class="far fa-bell"></i> -->
                        <!-- <i class="fas fa-bell fa-shake"></i> -->
                        <!-- <i class="fa-solid fa-bell fa-shake"></i> -->
                        <!-- <span class="badge badge-success">
                            1
                        </span> -->
                    </div>
                </div>
                <div id="info_user_002" class="mt-n3">
                    <p for="">
                        <?= @$info_login; ?>
                    </p>
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <!-- <span class="dropdown-item dropdown-header">Profil</span> -->
                <a href="<?= @$dir; ?>profil.php" class="dropdown-item">
                    <i class="fas fa-user mr-2 text-info"></i>
                    Profil Saya
                    <!-- <span class="float-right text-muted text-sm">3 menit</span> -->
                </a>
                <!-- <div class="dropdown-divider"></div> -->
                <!-- <a href="#" class="dropdown-item">
                    <i class="fas fa-envelope mr-2"></i> 4 Pesan
                    <span class="float-right text-muted text-sm">3 menit</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-users mr-2"></i> 8 Permintaan
                    <span class="float-right text-muted text-sm">12 jam</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-file mr-2"></i> 3 laporan
                    <span class="float-right text-muted text-sm">2 hari</span>
                </a> -->
                <a href="<?= @$dir; ?>jadwal.php" class="dropdown-item">
                    <i class="fas fa-list mr-2 text-warning"></i>
                    Jadwal
                    <span class="badge badge-success">1</span>
                    <!-- <span class="float-right text-muted text-sm">2 hari</span> -->
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer" data-bs-toggle="modal" data-bs-target="#logout">
                    <!-- <i class="fas fa-power-off mr-2 text-danger"></i> -->
                    <i class="fas fa-sign-out-alt mr-2 text-danger"></i>
                    Log Out
                </a>
            </div>
        </li>

        <li class="nav-item dropdown">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
    </ul>
</nav>
<!-- /.navbar -->

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <div class="brand-link">
        <a href="<?= @$dir; ?>../" style="text-decoration: none; color: aliceblue;">
            <img src="<?= @$dir; ?>../img/app/logoInstitusi.png" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light">&Sopf;&iopf;&Aopf;&Popf;</span></a>
        <!-- <img src="../img/app/nfc.png" class="img-circle elevation-3" style="opacity: .8; height: 35px; width: 35px; margin-top: -5px;"> -->
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <a href="<?= @$dir; ?>profil.php">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="<?= @$dir; ?>../img/user/<?= @$foto_login ? $foto_login : 'default.jpg'; ?>" class="elevation-2" alt="User Image" style="height: 50px; width: 50px; border-radius: 50%; object-fit: cover; object-position: top; margin-left: -5px;">
                </div>
                <div class="info d-block" style="margin-top: -7px; margin-bottom: -20px;">
                    <a href="<?= @$dir; ?>profil.php" style="text-decoration: none;"><?= @$nama_login; ?></a>
                    <p for="" style="color: dimgray;"><?= @$info_login; ?></p>
                </div>
            </div>
        </a>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

                <?php if (@$_SESSION['akses'] != 'superadmin') { ?>
                    <li class="nav-item <?= $menu_open_1; ?>">
                        <a href="<?= @$dir; ?>../beranda" class="nav-link <?= @$navlink_active_1; ?>">
                            <i class="nav-icon fas fa-tachometer-alt text-info"></i>
                            <p>
                                Beranda
                                <!-- <span class="right badge badge-danger">New</span> -->
                                <!-- <i class="right fas fa-angle-left"></i> -->
                            </p>
                        </a>
                    </li>
                <?php } ?>

                <?php if (@$_SESSION['akses'] != 'superadmin') { ?>
                    <li class="nav-item <?= $menu_open_dash; ?>">
                        <a href="<?= @$dir; ?>../beranda/dashdevice.php" class="nav-link <?= @$navlink_active_dash; ?>">
                            <i class="nav-icon fas fa-mobile-alt text-light"></i>
                            <p>
                                Devices
                                <span class="right badge badge-success">Online</span>
                            </p>
                        </a>
                    </li>
                <?php } ?>

                <?php if (@$_SESSION['akses'] != 'admin' && @$_SESSION['akses'] != 'superadmin') { ?>
                    <li class="nav-item <?= $menu_open_2; ?>">
                        <a href="#" class="nav-link <?= @$navlink_active_2; ?>">
                            <i class="nav-icon fas fa-th text-fuchsia"></i>
                            <p>
                                Rekap Presensi
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= @$dir; ?>rekap.php" class="nav-link <?= $navlink_sub1_active_1; ?>">
                                    &nbsp;
                                    <i class="far fa-calendar nav-icon text-cyan"></i>
                                    <p>Bulanan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= @$dir; ?>rekaptahun.php" class="nav-link <?= $navlink_sub1_active_2; ?>">
                                    &nbsp;
                                    <i class="fas fa-book nav-icon text-success"></i>
                                    <p>Tahunan</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php } ?>

                <?php if (@$_SESSION['level_login'] == 'admin' || @$_SESSION['level_login'] == 'superadmin') { ?>
                    <li class="nav-item <?= $menu_open_3; ?>">
                        <a href="<?= @$dir; ?>statistik.php" class="nav-link <?= @$navlink_active_3; ?>">
                            <i class="nav-icon fas fa-chart-pie text-danger"></i>
                            <p>
                                Statistik
                                <!-- <i class="fas fa-angle-left right"></i> -->
                                &nbsp;
                                <span class="badge badge-warning bg-warning"><?= @$_SESSION['username_login']; ?></span>
                            </p>
                        </a>
                        <!-- <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= @$dir; ?>statistik.php?page=chart" class="nav-link <?= $navlink_sub4_active_1; ?>">
                                    &nbsp;
                                    <i class="fas fa-chart-bar nav-icon text-warning"></i>
                                    <p>Grafik</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= @$dir; ?>statistik.php?page=table" class="nav-link <?= $navlink_sub4_active_2; ?>">
                                    &nbsp;
                                    <i class="fas fa-table nav-icon text-success"></i>
                                    <p>Tabel</p>
                                </a>
                            </li>
                        </ul> -->
                    </li>
                <?php } ?>

                <?php if (@$_SESSION['level_login'] == 'user_gtk' || @$_SESSION['level_login'] == 'admin' || @$_SESSION['level_login'] == 'superadmin') { ?>
                    <li class="nav-item <?= $menu_open_7; ?>">
                        <a href="#" class="nav-link <?= @$navlink_active_7; ?>">
                            <i class="nav-icon fas fa-th text-purple"></i>
                            <p>
                                Data GTK
                                <i class="fas fa-angle-left right"></i>
                                <span class="badge badge-warning bg-warning text-dark"><?= @$_SESSION['username_login']; ?></span>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <!-- <li class="nav-item">
                                <a href="harian.php?datab=GTK" class="nav-link <?= $navlink_sub3_active_1; ?>">
                                    &nbsp;
                                    <i class="fas fa-font nav-icon text-success"></i>
                                    <p>Harian</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="bulanan.php?datab=GTK" class="nav-link <?= $navlink_sub3_active_2; ?>">
                                    &nbsp;
                                    <i class="fas fa-calendar nav-icon text-warning"></i>
                                    <p>Bulanan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="tahunan.php?datab=GTK" class="nav-link <?= $navlink_sub3_active_3; ?>">
                                    &nbsp;
                                    <i class="far fa-calendar nav-icon text-primary"></i>
                                    <p>Tahunan</p>
                                </a>
                            </li> -->
                            <li class="nav-item">
                                <a href="<?= @$dir; ?>kartukontak.php?datab=GTK" class="nav-link <?= $navlink_sub3_active_4; ?>">
                                    &nbsp;
                                    <i class="far fa-address-card nav-icon text-info"></i>
                                    <p>Kontak GTK</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= @$dir; ?>dataguru.php" class="nav-link <?= $navlink_sub2_active_6; ?>">
                                    &nbsp;
                                    <i class="far fa-file nav-icon text-indigo"></i>
                                    <p>Data Guru</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= @$dir; ?>datawalikelas.php" class="nav-link <?= $navlink_sub2_active_9; ?>">
                                    &nbsp;
                                    <i class="far fa-eye nav-icon text-lime"></i>
                                    <p>Data Wali Kelas</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php } ?>

                <li class="nav-item <?= $menu_open_4; ?>">
                    <a href="#" class="nav-link <?= @$navlink_active_4; ?>">
                        <i class="nav-icon fas fa-copy text-warning"></i>
                        <p>
                            Data Siswa
                            <i class="right fas fa-angle-left"></i>
                            <?php if (@$akses_login == 'Wali Kelas') { ?>
                                <span class="badge badge-success bg-success text-light"><?= $akses_login; ?></span>
                            <?php } ?>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <?php if (@$_SESSION['akses'] == 'Wali Kelas') { ?>
                            <li class="nav-item <?= $menu_open_sub_1; ?>">
                                <a href="#" class="nav-link">
                                    &nbsp;
                                    <i class="far fa-eye nav-icon text-lime"></i>
                                    <p>
                                        Wali Kelas
                                        <i class="right fas fa-angle-left"></i>
                                        <span class="badge badge-secondary bg-secondary"><?= $ket_akses_login; ?></span>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="<?= @$dir; ?>harian.php?datab=siswa" class="nav-link <?= $navlink_sub2_active_1; ?>">
                                            &nbsp;
                                            &nbsp;
                                            <i class="fas fa-font nav-icon text-warning"></i>
                                            <p>Harian</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= @$dir; ?>rekapbulan.php?datab=siswa" class="nav-link <?= $navlink_sub2_active_2; ?>">
                                            &nbsp;
                                            &nbsp;
                                            <i class="fas fa-calendar nav-icon text-info"></i>
                                            <p>Bulanan</p>
                                        </a>
                                    </li>
                                    <!-- <li class="nav-item">
                                        <a href="<?= @$dir; ?>tahunan.php?datab=siswa" class="nav-link <?= $navlink_sub2_active_6; ?>">
                                            &nbsp;
                                            &nbsp;
                                            <i class="far fa-calendar nav-icon text-success"></i>
                                            <p>Tahunan</p>
                                        </a>
                                    </li> -->

                                    <li class="nav-item">
                                        <a href="<?= @$dir; ?>kartukontak.php?datab=siswa" class="nav-link <?= $navlink_sub2_active_6; ?>">
                                            &nbsp;
                                            &nbsp;
                                            <i class="far fa-address-card nav-icon text-info"></i>
                                            <p>Kontak
                                                <span class="badge badge-secondary">
                                                    <?= $ket_akses_login; ?>
                                            </p>
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php } ?>
                        <li class="nav-item">
                            <a href="<?= @$dir; ?>semuakelas.php" class="nav-link <?= $navlink_sub2_active_0; ?>">
                                &nbsp;<i class="far fa-folder-open nav-icon text-warning"></i>
                                <p>Kehadiran Siswa</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= @$dir; ?>rekapbulan.php" class="nav-link <?= $navlink_sub2_active_0; ?>">
                                &nbsp;<i class="far fa-folder-open nav-icon text-success"></i>
                                <p>Rekap Kelas Perbulan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= @$dir; ?>event.php" class="nav-link <?= $navlink_sub2_active_8; ?>">
                                &nbsp;<i class="far fa-folder-open nav-icon text-info"></i>
                                <p>Kegiatan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= @$dir; ?>nilaikelas.php" class="nav-link <?= $navlink_sub2_active_7; ?>">
                                &nbsp;<i class="fas fa-list nav-icon text-fuchsia"></i>
                                <p>Nilai Kelas</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php if (@$level_login != 'superadmin' && @$_SESSION['akses'] != 'user_siswa' && @$_SESSION['akses'] != '') { ?>
                    <li class="nav-item">
                        <a href="<?= @$dir; ?>kelas.php?datab=siswa" class="nav-link <?= $navlink_active_8; ?>">
                            <i class="far fa-address-card nav-icon text-info"></i>
                            <p>Kelas Saya</p>
                            </span>
                        </a>
                    </li>
                <?php } ?>
                <?php if (@$_SESSION['level_login'] != 'superadmin') { ?>
                    <li class="nav-item <?= $menu_open_6; ?>">
                        <a href="#" class="nav-link <?= @$navlink_active_6; ?>">
                            <i class="nav-icon fas fa-user text-teal"></i>
                            <p>
                                Profil
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="<?= @$dir; ?>profil.php" class="nav-link <?= $navlink_sub3_active_1; ?>">
                                    &nbsp;
                                    <i class="far fa-user nav-icon text-blue"></i>
                                    <p>Profil Saya</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= @$dir; ?>editprofil.php" class="nav-link <?= $navlink_sub3_active_2; ?>">
                                    &nbsp;
                                    <i class="far fa-edit nav-icon text-warning"></i>
                                    <p>Edit Profil</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= @$dir; ?>jadwal.php" class="nav-link <?= $navlink_sub3_active_3; ?>">
                                    &nbsp;
                                    <i class="far fa-calendar nav-icon text-success"></i>
                                    <p>Jadwal Saya</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php } ?>

                <?php if (@$_SESSION['level_login'] == 'superadmin' && @$_SESSION['level_login'] != 'admin' && strtolower(@$_SESSION['username_login']) != 'piket') { ?>
                    <li class="nav-item <?= $menu_open_5; ?>">
                        <a href="<?= @$dir; ?>setting.php" class="nav-link <?= @$navlink_active_5; ?>">
                            <i class="nav-icon fas fa-cog fa-spin"></i>
                            <!-- <i class="nav-icon fas fa-edit text-purple"></i> -->
                            <p>
                                Setting
                                &nbsp;
                                &nbsp;
                                <span class="badge badge-warning bg-warning text-dark">
                                    <i class="fas fa-lock-open"></i>
                                </span>
                            </p>
                        </a>
                    </li>
                    <li class="nav-item <?= $menu_open_5x; ?>">
                        <a href="<?= @$dir; ?>setting_id.php" class="nav-link <?= @$navlink_active_5x; ?>">
                            <i class="nav-icon fas fa-edit text-light"></i>
                            <p>
                                Setting ID
                                &nbsp;
                                &nbsp;
                                <span class="badge badge-warning bg-warning text-dark">
                                    <i class="fas fa-lock-open"></i>
                                </span>
                            </p>
                        </a>
                    </li>
                <?php }

                if (@$_SESSION['level_login'] == 'admin' || @$_SESSION['level_login'] == 'superadmin' && strtolower(@$_SESSION['username_login']) != 'piket') { ?>
                    <li class="nav-item <?= $menu_open_5; ?>">
                        <a href="<?= @$dir; ?>setting_kbm.php" class="nav-link <?= @$navlink_active_5y; ?>">
                            <i class="nav-icon fas fa-edit text-purple"></i>
                            <p>
                                Setting KBM
                                &nbsp;
                                &nbsp;
                                <span class="badge badge-warning bg-warning text-dark">
                                    <i class="fas fa-lock-open"></i>
                                </span>
                            </p>
                        </a>
                    </li>
                <?php } ?>

                <li class="border-bottom border-secondary"></li>
                <li class="nav-item">
                    <a href="#" class="nav-link" style="font-weight: 400; text-shadow: 0 0 3px black;" data-bs-toggle="modal" data-bs-target="#logout">
                        <!-- <i class="nav-icon fas fa-window-close text-danger"></i> -->
                        <i class="nav-icon fas fa-sign-out-alt text-danger"></i>
                        <p>
                            Log Out!
                            <!-- <i class="fas fa-angle-left right"></i> -->
                        </p>
                    </a>
                </li>
                <li class="border-bottom border-secondary"></li>

                <li class="nav-item">
                    <a class="nav-link" href="<?= @$dir; ?>../">
                        <img src="<?= @$dir; ?>../img/app/nfc.png" class="img-circle elevation mx-n1 p-0" style="opacity: .8; height: 30px; width: 30px;">
                        &nbsp;
                        <p>
                            Home
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:history.go(-1)">
                        <i class="nav-icon fas fa-reply-all"></i>
                        <p>
                            Kembali
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <style>
            #cuaca table,
            #cuaca h6 {
                font-size: 12px;
                color: grey;
                margin: 0;
            }
        </style>
        <!-- <div id="cuaca">
            <?php
            $sql_cuaca = mysqli_query($konek, "SELECT * FROM cuaca");
            $ataaa = array();
            foreach ($sql_cuaca as $dtcuaca) {
                $ataaa[] = $dtcuaca;
                $jam_cuaca = $dtcuaca['jam_cuaca'];
                $kondisi_cuaca = $dtcuaca['kondisi_cuaca'];
                $suhu_cuaca = $dtcuaca['suhu_cuaca'];
                $kelembapan_cuaca = $dtcuaca['kelembapan_cuaca'];
                $kecepatan_angin = $dtcuaca['kecepatan_angin'];
                $arah_angin = $dtcuaca['arah_angin'];
                $timestamp = $dtcuaca['timestamp'];
            }
            ?>

            <table>
                <tbody>
                    <tr>
                        <td>Suhu / Cuaca</td>
                        <td>:&nbsp;</td>
                        <td><?= $suhu_cuaca; ?> (<?= $kondisi_cuaca; ?>)</td>
                    </tr>
                    <tr>
                        <td>Kelembaban</td>
                        <td>:&nbsp;</td>
                        <td><?= $kelembapan_cuaca; ?></td>
                    </tr>
                    <tr>
                        <td>Angin</td>
                        <td>:&nbsp;</td>
                        <td><?= $kecepatan_angin; ?> (<?= $arah_angin; ?>)</td>
                    </tr>
                </tbody>
            </table>
            <h6>Teakhir Update <?= $jam_cuaca; ?> (BMKG)</h6>
        </div> -->
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

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
                    <form action="<?= @$dir; ?>../logout.php" method="post">
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
                        <button type="submit" class="btn btn-danger mx-1" name="logout" value="logout">Tetap Log out!<img src="<?= @$dir; ?>../img/app/log-out_w.svg" style="height: 20px; margin-left: 5px; margin-right: 0; margin-top: auto; margin-bottom: auto;"></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
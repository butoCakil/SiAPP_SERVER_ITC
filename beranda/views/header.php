<?php
if (@$_SESSION['ingataku']) {
    // ini_set('session.cookie_lifetime', 2000000);
} else {
    // ini_set('session.cookie_lifetime', 0);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- favicon -->
    <link rel="shortcut icon" href="<?= @$dir; ?>dist/img/logoInstitusi.png" type="image/x-icon">

    <title><?= @$title ? $title : 'Dashboard'; ?></title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= @$dir; ?>plugins/fontawesome-free/css/all.min.css">
    <!-- fullCalendar -->
    <link rel="stylesheet" href="<?= @$dir; ?>plugins/fullcalendar/main.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="<?= @$dir; ?>plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?= @$dir; ?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="<?= @$dir; ?>plugins/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= @$dir; ?>dist/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?= @$dir; ?>plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="<?= @$dir; ?>plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="<?= @$dir; ?>plugins/summernote/summernote-bs4.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css.css">
    <link rel="stylesheet" href="<?= @$dir; ?>plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= @$dir; ?>plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= @$dir; ?>plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!--  -->
    <link rel="stylesheet" href="<?= @$dir; ?>plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="<?= @$dir; ?>css/responsive400px.css">

    <!-- multilevel dropdown -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script> -->

    <!-- <script src="https://code.jquery.com/jquery-3.6.0.slim.js" type="text/javascript"></script> -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>

    <!-- CSS internal -->


    <!-- JS chart -->
    <script type="text/javascript" src="<?= @$dir; ?>dist/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?= @$dir; ?>dist/js/Chart.min.js"></script>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-footer-fixed">
    
    <div class="wrapper mt-1">
        <?php include(@$dir . "views/navbar.php"); ?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2 mt-5">
                        <div class="col-sm-6">
                            <h1 id="judul_header" class="m-0"><?= @$title ? $title : 'Dashboard'; ?></h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">
                                    <a href="<?= @$dir; ?>../" style="text-decoration: none;">
                                        <!-- <i class="fas fa-home mr-2"></i> -->
                                        <img src="<?= @$dir; ?>../img/app/rfid-unscreen.gif" class="img-fluid elevation-0" style="height: 30px; width: 30px; border-radius: 100%;">
                                        Home
                                    </a>
                                </li>
                                <?php if ($title != 'Beranda') { ?>
                                    <li class="breadcrumb-item">
                                        <a href="<?= @$dir; ?>../beranda" style="text-decoration: none;">
                                            <!-- <i class="nav-icon fas fa-tachometer-alt"></i>&nbsp; -->
                                            Beranda
                                        </a>
                                    </li>
                                <?php } ?>
                                <li class="breadcrumb-item active"><?= @$title ? $title : 'Dashboard'; ?></li>

                                <?php
                                // echo "<pre>";
                                // print_r($_SESSION);
                                // echo "</pre>";
                                ?>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->
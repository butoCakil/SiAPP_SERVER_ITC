<?php
include('app/get_user.php');
if ($level_login == 'superadmin') {
    header("Location: statistik.php");
}

$title = 'Profil';
$navlink = 'Profil';
$navlink_sub = 'profil';
include('views/header.php');
include('views/_profil.php');
include('views/footer.php');
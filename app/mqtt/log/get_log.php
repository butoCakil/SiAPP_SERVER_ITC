<?php
$subDir = 2;
include "../../../beranda/app/get_user.php";
if (@$_SESSION['level_login'] == 'superadmin') {

    date_default_timezone_set('Asia/Jakarta');
    $tanggal = date('Y-m-d');
    $getTanggal = @$_GET['date'];

    $tanggal = $getTanggal ? $getTanggal : $tanggal;

    $logFile = "log/tag_$tanggal.log";
    $logContent = file_get_contents($logFile);
    echo nl2br($logContent);
} else { ?>
    <script>
        alert("ilegal akses");
        window.location.href = "../../../";
    </script>
<?php } ?>
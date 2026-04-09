<?php
include "config/konesi.php";

$query_select_tmprfid = "SELECT nokartu_admin FROM tmprfid";
$stmt_select_tmprfid = mysqli_stmt_init($konek);
mysqli_stmt_prepare($stmt_select_tmprfid, $query_select_tmprfid);
mysqli_stmt_execute($stmt_select_tmprfid);
$result_select_tmprfid = mysqli_stmt_get_result($stmt_select_tmprfid);

$tex = "";
if ($row = mysqli_fetch_assoc($result_select_tmprfid)) {
    $tex = $row['nokartu_admin'];
}

// Tutup prepared statement
mysqli_stmt_close($stmt_select_tmprfid);

// Tutup koneksi database
mysqli_close($konek);
?>

<div class="m-3">
    <input id="nokartuInput" type="text" name="" value="<?= @$tex; ?>" class="form-control w-75 mx-auto" placeholder="SCAN KARTU, UNTUK MENDAPATKAN NOMOR" style="text-align: center; font-size: 24px; font-weight: 900;" disabled>
</div>

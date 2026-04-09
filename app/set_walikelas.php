<?php
include '../config/konesi.php';

$kelas = $_POST['kelas'] ?? '';
$guru_id = (int)($_POST['guru_id'] ?? 0);

if ($kelas && $guru_id) {

    // hapus wali lama di kelas ini
    mysqli_query($konek, "
        UPDATE dataguru 
        SET akses=NULL, ket_akses=NULL 
        WHERE ket_akses='{$kelas}'
    ");

    // set wali baru
    mysqli_query($konek, "
        UPDATE dataguru 
        SET akses='Wali Kelas', ket_akses='{$kelas}' 
        WHERE id={$guru_id}
    ");
}

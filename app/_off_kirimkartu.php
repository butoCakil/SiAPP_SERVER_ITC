<?php
include("../config/konesi.php");

// Baca nomor kartu dari NodeMCU
$nokartu = $_GET['nokartu'];

// Validasi karakter aneh
if (!preg_match('/^[a-fA-F0-9]+$/', $nokartu)) {
    die("Karakter aneh terdeteksi dalam nomor kartu.");
}

// Persiapan prepared statement
$stmtDelete = mysqli_prepare($konek, "DELETE FROM tmprfid");
$stmtInsert = mysqli_prepare($konek, "INSERT INTO tmprfid(nokartu) VALUES(?)");

if ($stmtDelete && $stmtInsert) {
    // Eksekusi prepared statement DELETE
    mysqli_stmt_execute($stmtDelete);

    // Eksekusi prepared statement INSERT
    mysqli_stmt_bind_param($stmtInsert, "s", $nokartu);
    $result = mysqli_stmt_execute($stmtInsert);

    if ($result) {
        echo "Berhasil";
    } else {
        echo "Gagal";
    }

    // Tutup prepared statement
    mysqli_stmt_close($stmtDelete);
    mysqli_stmt_close($stmtInsert);
} else {
    echo "Gagal mempersiapkan prepared statement.";
}

mysqli_close($konek);


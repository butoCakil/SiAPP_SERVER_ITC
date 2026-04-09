<?php

// echo "hahahahaha\n";
// print_r($_POST);

if (@$_POST) {
    $nis = $_POST['nis'];
    $data_kelompok = $_POST['kelompok'];

    include "../../config/konesi.php";

    $sq = "UPDATE datasiswa SET kelompok = '$data_kelompok' WHERE nis = '$nis'";
    $q_up = mysqli_query($konek, $sq);

    mysqli_close($konek);

    if ($q_up) {
        echo "Berhasil Update " . $nis;
    } else {
        echo "Gagal Update " . $nis . "\n" . mysqli_error($konek);
    }
} else {
    echo "Tidak ada data";
}

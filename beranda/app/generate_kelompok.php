<?php
if (@$_POST['generate'] == 'gen') {

    include '../../config/konesi.php';

    $mode = $_POST['mode_gen'];
    $jur = $_POST['jur'];
    $tingkat = $_POST['t'];

    $get_kode = "SELECT * FROM kodeinfo WHERE jur = '$jur' AND tingkat = '$tingkat' ORDER BY info ASC";
    $q_kode = mysqli_query($konek, $get_kode);

    $data_kelas = array();
    $i = 0;
    $query_kelas = "";
    while ($data_kode = mysqli_fetch_array($q_kode)) {
        $data_kelas[$i] = @$data_kode['info'];
        $i++;

        $query_kelas = $query_kelas . ($query_kelas ? " OR kelas = '" : " WHERE (kelas = '") . @$data_kode['info'] . "'";
    }

    $query_kelas = $query_kelas . ")";

    $sql_siswa = "SELECT * FROM datasiswa" . @$query_kelas;
    $query_siswa = mysqli_query($konek, $sql_siswa);
    $jumlah_data = mysqli_num_rows($query_siswa);

    $perkelompok = round($jumlah_data / 5);

    $data_siswa = array();
    $nomor = 1;
    $set_ke = 1;
    while ($hasil_siswa = mysqli_fetch_array($query_siswa)) {
        $data_siswa = @$hasil_siswa['nis'];

        if ($nomor % $perkelompok == 0) {
            $set_ke++;
        }

        // echo "NIS : " . $hasil_siswa['nis'] . "<br>";
        // echo "Kelompok : " . $set_ke . "<br>";

        $sql_up = "UPDATE datasiswa SET kelompok = '$set_ke' WHERE nis = '$data_siswa'";
        $update_set = mysqli_query($konek, $sql_up);

        $nomor++;
    }

    mysqli_close($konek);

    // echo "<pre>";
    // print_r($data_siswa);
    // echo "</pre>";
    // echo "<br>";
    // echo ($jumlah_data);
    // echo "<br>";
    // echo "kelompok 1: " . $perkelompok;
    // echo "<br>";
}

header("Location: ../setting_kbm.php?jur=" . $jur . "&t=" . $tingkat);

<?php

if (@$_POST) {
    include '../../config/konesi.php';

    // print_r($_POST);

    // $id = @$_POST['id'];
    $nis = @$_POST['nis'];
    $nokartu = @$_POST['nokartu'];
    $_masuk = @$_POST['masuk'];

    $_masuk_pecah = array();
    $_masuk_pecah = explode("#", $_masuk);

    $masuk = $_masuk_pecah[0];
    $mulai_jamke = @$_masuk_pecah[1];
    $sampai_jamke = @$_masuk_pecah[2];
    $ruangan = @$_masuk_pecah[3];
    $nick_guru = @$_masuk_pecah[4];

    echo "\nNokartu : " . $nokartu;
    echo "\nMasuk : " . $masuk;
    echo "\nMasuk jamke : " . $mulai_jamke;
    echo "\nSelesai : " . $sampai_jamke;
    echo "\nRUangan : " . $ruangan;
    echo "\nGuru : " . $nick_guru;
    echo "\n";

    $tanggal = @$_POST['tanggal'];
    $tanggal = date('Y-m-d', strtotime($tanggal));

    // NAma guru
    $cek_guru_kelas = "SELECT * FROM dataguru WHERE nick = '$nick_guru'";
    $query_cek_guru = mysqli_query($konek, $cek_guru_kelas);
    $hasil_cek_guru = mysqli_num_rows($query_cek_guru);
    $nama_guru_kelas = mysqli_fetch_array($query_cek_guru)['nama'];

    // cek Siswa ()
    $cek_presesnsi_kelas = "SELECT * FROM presensikelas WHERE nis = '$nis' AND mulai_jamke = '$mulai_jamke' AND nick_guru = '$nick_guru' AND tanggal LIKE '%$tanggal%'";
    $query_cek = mysqli_query($konek, $cek_presesnsi_kelas);
    $hasil_cek = mysqli_num_rows($query_cek);

    if ($hasil_cek) {
        // update ststus presensi
        $update_status = "UPDATE presensikelas SET ruangan = '$ruangan', status = '$masuk', mulai_jamke = '$mulai_jamke', sampai_jamke = '$sampai_jamke', nick_guru = '$nick_guru', nama_guru = '$nama_guru_kelas' WHERE nis = '$nis' AND tanggal LIKE '%$tanggal%'";
        $update_query = mysqli_query($konek, $update_status);

        if (!$update_query) {
            echo "Gagal update status";
        }
    } else {
        // echo "Tidak ada data\n";

        $cek_datasiswa = "SELECT * FROM datasiswa WHERE nis = '$nis'";
        $que_datasiswa = mysqli_query($konek, $cek_datasiswa);
        $hasil_datasiswa = mysqli_fetch_assoc($que_datasiswa);

        $nama_datasiswa = $hasil_datasiswa['nama'];
        $kelas_datasiswa = $hasil_datasiswa['kelas'];

        $insert_presensikelas = "INSERT INTO `presensikelas`(`nokartu`, `nis`, `nama`, `ruangan`, `kelas`, `mulai_jamke`, `sampai_jamke`, `status`, `nick_guru`, `nama_guru`, `tanggal`) VALUES ('$nokartu', '$nis', '$nama_datasiswa', '$ruangan', '$kelas_datasiswa', '$mulai_jamke', '$sampai_jamke', '$masuk', '$nick_guru', '$nama_guru_kelas', '$tanggal')";

        // echo $insert_presensikelas . "\n";
        $que_insert = mysqli_query($konek, $insert_presensikelas);

        echo $nama_datasiswa;

        if (!$que_insert) {
            echo "\nGagal Tambahkan Presensi " . $masuk;
        }
    }

    mysqli_close($konek);
}

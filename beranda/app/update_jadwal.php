<?php

if (@$_POST['simpanjadwal']) {

    include "../../config/konesi.php";

    $jurusan = @$_POST['simpanjadwal'];
    $tingkat = @$_POST['tingkat'];
    $tanggal_jadwal = date('Y-m-d', strtotime(@$_POST['tanggal_jadwal']));
    $tiap_minggu = @$_POST['tiap_minggu'];

    $jadwalte = array();

    for ($hari = 0; $hari < 5; $hari++) {
        for ($ruang = 0; $ruang < 5; $ruang++) {
            $jadwalte[$hari][$ruang] = @$_POST['jadwalte' . $hari . $ruang];
        }
    }

    for ($hari = 0; $hari < 5; $hari++) {
        if ($hari == 0) {
            $kode_hari = "sen";
        } else if ($hari == 1) {
            $kode_hari = "sel";
        } else if ($hari == 2) {
            $kode_hari = "rab";
        } else if ($hari == 3) {
            $kode_hari = "kam";
        } else if ($hari == 4) {
            $kode_hari = "jum";
        } else {
            $kode_hari = "";
        }

        for ($ruang = 0; $ruang < 5; $ruang++) {

            if ($ruang == 0) {
                $kode_ruang = 'TE_HA';
            } else if ($ruang == 1) {
                $kode_ruang = 'TE_AV';
            } else if ($ruang == 2) {
                $kode_ruang = 'TE_AS';
            } else if ($ruang == 3) {
                $kode_ruang = 'TE_NT';
            } else if ($ruang == 4) {
                $kode_ruang = 'TE_FA';
            } else {
                $kode_ruang = '';
            }

            $jadwalte_ = @$_POST['jadwalte' . $hari . $ruang];

            $ambilnickguru = "SELECT * FROM jadwalgurujur WHERE ruangan = '$kode_ruang'";
            $q_ambilnickguru = mysqli_query($konek, $ambilnickguru);

            $data_guru = array();
            while ($hasil_ambilnickguru = mysqli_fetch_array($q_ambilnickguru)) {
                $data_guru[] = @$hasil_ambilnickguru;
            }

            $nick_guru1 = @$data_guru[0][3];
            $nick_guru2 = @$data_guru[1][3];
            $nick_guru3 = @$data_guru[2][3];

            // echo "<br><pre>";
            // print_r($_POST);
            // echo "</pre>";
            // echo "<br><pre> Guru 1: ";
            // print_r($nick_guru1);
            // echo "</pre>";
            // echo "<br><pre> Guru 2: ";
            // print_r($nick_guru2);
            // echo "</pre>";
            // echo "<br><pre> Guru 3: ";
            // print_r(@$nick_guru3);
            // echo "</pre>";
            // die;

            $cek_jadwal = "SELECT * FROM jadwaljurusan WHERE ruangan = '$kode_ruang' AND tingkat = '$tingkat'";
            $que_cek_jadwal = mysqli_query($konek, $cek_jadwal);
            $hasil_cek_jadwal = mysqli_num_rows($que_cek_jadwal);

            if ($hasil_cek_jadwal > 0) {
                $update_jadwal = "UPDATE `jadwaljurusan` SET `nick_guru1`='$nick_guru1', `nick_guru2`='$nick_guru2', `nick_guru3`='$nick_guru3', `$kode_hari`='$jadwalte_', `tingkat`='$tingkat', `tanggal` = '$tanggal_jadwal', `tiap_minggu` = '$tiap_minggu' WHERE `ruangan`='$kode_ruang' AND tingkat = '$tingkat'";
                $que_update = mysqli_query($konek, $update_jadwal);

                if ($que_update) {
                    echo "Update " . $kode_ruang . " OK<br>";
                } else {
                    echo "Update " . $kode_ruang . " ERROR<br>" . mysqli_error($konek) . "<br>";
                }
            } else {

                $sql_jadwal = "INSERT INTO `jadwaljurusan`(`ruangan`, `nick_guru1`, `nick_guru2`, `nick_guru3`, `$kode_hari`, `tingkat`, `tanggal`, `tiap_minggu`) VALUES ('$kode_ruang', '$nick_guru1', '$nick_guru2', '$nick_guru3', '$jadwalte_', '$tingkat', '$tanggal_jadwal', '$tiap_minggu')";
                $que_jadwal = mysqli_query($konek, $sql_jadwal);

                if ($que_jadwal) {
                    echo "Tambahkan " . $kode_ruang . " OK<br>";
                } else {
                    echo "Tambahkan " . $kode_ruang . " ERROR<br>" . mysqli_error($konek) . "<br>";
                }
            }
        }
    }

    mysqli_close($konek);
}

header('Location: ../setting_kbm.php?jur=' . $jurusan . '&t=' . $tingkat);

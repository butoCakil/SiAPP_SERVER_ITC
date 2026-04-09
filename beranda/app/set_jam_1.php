<?php
if (@$_POST) {

    // print_r($_POST);

    if (@$_POST['waktu_masuk']) {
        $waktu_masuk = $_POST['waktu_masuk'];
        $mode = $_POST['mode'];

        include "../../config/konesi.php";

        $sql = "SELECT * FROM jampelajaran WHERE mode = '$mode'";
        $query = mysqli_query($konek, $sql);
        $hasil = mysqli_num_rows($query);

        if ($hasil) {
            $sql_update = "UPDATE jampelajaran SET `waktu_masuk`='$waktu_masuk'";
            $query_update = mysqli_query($konek, $sql_update);

            if ($query_update) {
                echo "Berhasil Update Waktu Masuk :\n" . $waktu_masuk;
            } else {
                echo "GAGAL Update Waktu Masuk!\n" . mysqli_error($konek);
            }
        } else {
            $sql_insert = "INSERT INTO jampelajaran (`mode`, `waktu_masuk`, `jamke`, `mulai`, `selesai`, `info`) VALUES ('$mode','$waktu_masuk','','','','')";
            $query_insert = mysqli_query($konek, $sql_insert);

            if ($query_insert) {
                echo "Berhasil Tambahkan Waktu Masuk :\n" . $waktu_masuk;
            } else {
                echo "GAGAL Tambahkan Waktu Masuk!\n" . mysqli_error($konek);
            }
        }

        mysqli_close($konek);
    } else {
        include "../../config/konesi.php";

        $mode = @$_POST['mode'];
        $jamke = @$_POST['jam'];
        $data_set = @$_POST['setwaktu'];

        $_data_set = explode('#', $data_set);

        $info_set = $_data_set[0];
        $waktu_set = $_data_set[1];

        // cek sesuai mode jadwal
        $sql_cek = "SELECT * FROM jampelajaran WHERE mode = '$mode'";
        $query_cek = mysqli_query($konek, $sql_cek);
        $hasil_cek = mysqli_num_rows($query_cek);

        if ($hasil_cek) {
            $data_cek = mysqli_fetch_array($query_cek);
            $waktu_masuk = $data_cek['waktu_masuk'];

            if ($jamke == 1) {
                $mulai = $waktu_masuk;
            } else {
                $jamke_sebelumnya = $jamke - 1;

                $sql_ = "SELECT * FROM jampelajaran WHERE jamke = '$jamke_sebelumnya'";
                $query_ = mysqli_query($konek, $sql_);
                $hasil_ = mysqli_fetch_array($query_);

                $waktu_selesai = $hasil_['selesai'];
                $info_jam_sebelumnya = @$hasil['info'];
                $mulai = $waktu_selesai;
            }

            $time1_unix = strtotime(date('Y-m-d') . ' ' . $mulai);
            $time2_unix = strtotime(date('Y-m-d') . ' ' . $waktu_set);

            $begin_day_unix = strtotime(date('Y-m-d') . ' 00:00:00');

            $selesai = date('H:i:s', ($time1_unix + ($time2_unix - $begin_day_unix)));

            // echo "\njumlah time : " . $selesai;

            if ($mulai != '' || $mulai != '00:00:00' || $mulai != 0) {

                if ($info_set == 'jamkbm') {
                    $info_ = 'jam_ke-' . $jamke;
                } elseif ($info_set == 'rehat') {
                    $info_ = 'istirahat';
                }

                $sql_cek_jamke = "SELECT * FROM jampelajaran WHERE jamke = '$jamke'";
                $query_cek_jamke = mysqli_query($konek, $sql_cek_jamke);
                $hasil_cek_jamke = mysqli_num_rows($query_cek_jamke);

                if ($hasil_cek_jamke) {
                    $sql_update_set = "UPDATE jampelajaran SET jamke = '$jamke', mulai = '$mulai', selesai = '$selesai', info = '$info_', keterangan = '$data_set' WHERE jamke = '$jamke'";
                    $query_update_set = mysqli_query($konek, $sql_update_set);

                    if ($query_update_set) {
                        echo "\nBerhasil Update set Jam ke : " . $jamke;
                    } else {
                        echo "\nGAGAL Update set Jam ke : " . $jamke . "\n" . mysqli_error($konek);
                    }
                } else {


                    $sql_insert_set = "INSERT INTO jampelajaran (`mode`, `waktu_masuk`, `jamke`, `mulai`, `selesai`, `info`, `keterangan`) VALUES ('$mode', '$waktu_masuk', '$jamke','$mulai','$selesai','$info_', '$data_set')";
                    $query_insert_set = mysqli_query($konek, $sql_insert_set);

                    if ($query_insert_set) {
                        echo "\nBerhasil Insert Set jam ke : " . $jamke;
                    } else {
                        echo "\nGAGAL! Insert Set jam ke : " . $jamke . "\n" . mysqli_error($konek);
                    }
                }
            } else {
                echo "SET Waktu masuk!";
            }
        } else {
            echo "SET Waktu masuk!";
        }

        mysqli_close($konek);
    }
}

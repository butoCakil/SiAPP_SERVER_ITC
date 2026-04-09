<?php

echo 'Set data Guru';
echo "\n";
echo "\n";

// print_r(@$_POST);

if (@$_POST) {
    $_kelompokkelas = @$_POST['kel'];
    if ($_kelompokkelas) {
        $tgl_set = $_POST['tgl_set'];

        $_data_guru = $_POST['guru'];
        $__data_guru = explode('#', $_data_guru);

        $bulan_set = @$__data_guru[0];
        $nick_guru = @$__data_guru[1];
        $jamke = @$__data_guru[2];

        if ($jamke) {
            $mulai_jam = $jamke;
            $selesai_jam = $jamke + 1;
        } else {
            $mulai_jam = 1;
            $selesai_jam = 11;
        }

        $tanggal = date('Y') . '-' . sprintf('%02d', $bulan_set) . '-' . sprintf('%02d', $tgl_set);

        // echo "\ntanggal : " . $tanggal;
        // echo "\nnick_guru : " . $nick_guru;
        // echo "\nmulai_jam : " . $mulai_jam;
        // echo "\nselesai_jam : " . $selesai_jam;

        include '../../config/konesi.php';

        $qu_kel = mysqli_query($konek, "SELECT * FROM kelompokkelas WHERE id = '$_kelompokkelas'");
        $hhasil_kel = mysqli_fetch_array($qu_kel);

        $kelompokkelas = @$hhasil_kel['kode'];
        $jurusan = @$hhasil_kel['jurusan'];
        $kelompok = @$hhasil_kel['kelompok'];

        if (!@$nick_guru) {
            $q_hapus = "DELETE FROM `jadwalkbm` WHERE kelas = '$kelompokkelas' AND tanggal = '$tanggal'";
            $hapus_ruang = mysqli_query($konek, $q_hapus);
            if ($hapus_ruang) {
                echo "\Guru telah dikosongkan\n" . $q_hapus;
                // header("location: setting_kbm.php?jur=" . $jurusan . "&bulanset=" . $bulan . "&kel=" . $kelompokkelas);

                // echo "<script>" .
                //     "window.location.assign('" . "setting_kbm.php?jur=" . $jurusan . "&bulanset=" . $bulan . "&kel=" . $kelompokkelas . "')" .
                //     "</script>";
                header("Refresh:0; url=setting_kbm.php?jur=" . $jurusan . "&bulanset=" . $bulan . "&kel=" . $kelompokkelas);
            } else {
                echo "\nGagal kosongkan ruang\n" . mysqli_error($konek);
            }
        }


        // echo "\nkelompokkelas : " . $kelompokkelas;

        $cek_jadwal = mysqli_query($konek, "SELECT * FROM jadwalkbm WHERE kelas = '$kelompokkelas' AND tanggal = '$tanggal' AND mulai_jamke = '$mulai_jam'");
        $hasil_cek_jadwal = mysqli_num_rows($cek_jadwal);
        $hasil_data_cek_jadwal = mysqli_fetch_array($cek_jadwal);
        $nick_cek_jadwal = @$hasil_data_cek_jadwal['nick'];
        if ($hasil_cek_jadwal > 0) {

            echo "\nHasil Cek Ada";
            echo "\nhasil cek nick : " . @$hasil_data_cek_jadwal['nick'];

            if (($nick_guru != $nick_cek_jadwal) && $nick_cek_jadwal) {
                echo "\nNick sudah ada";
                $ruangan_cek_jadwal = $hasil_data_cek_jadwal['ruangan'];
                $info_cek_jadwal = $hasil_data_cek_jadwal['info'];
                $tingkat_cek_jadwal = $hasil_data_cek_jadwal['tingkat'];

                echo "\nRuangan cek : " . $ruangan_cek_jadwal;
                echo "\nInfo Ruangan cek : " . $info_cek_jadwal;
                echo "\nTingkat cek : " . $tingkat_cek_jadwal;

                $insert_jadwal = mysqli_query($konek, "INSERT INTO jadwalkbm (`ruangan`, `info`,`kelas`, `kelompok`, `tingkat`, `jur`, `nick`, `tanggal`, `mulai_jamke`, `sampai_jamke`) VALUES ('$ruangan_cek_jadwal', '$info_cek_jadwal', '$kelompokkelas', '$kelompok', '$tingkat_cek_jadwal', '$jurusan', '$nick_guru', '$tanggal', '$mulai_jam', '$selesai_jam')");

                if ($insert_jadwal) {
                    echo "\nBerhasil tambah jadwal Guru\n";
                } else {
                    echo "\nGAGAL tambah jadwal Guru\n" .  mysqli_error($konek);
                }
            } else {
                echo "\nNick belum ada";
                $update_jadwal = mysqli_query($konek, "UPDATE jadwalkbm SET nick = '$nick_guru' WHERE kelas = '$kelompokkelas' AND tanggal = '$tanggal' AND mulai_jamke = '$mulai_jam'");

                if ($update_jadwal) {
                    echo "\nBErhasil Update jadwal Guru\n";
                } else {
                    echo "\nGagal Update Jadwal Guru\n" . mysqli_error($konek);
                }
            }
        } else {
            $insert_jadwal = mysqli_query($konek, "INSERT INTO jadwalkbm (`kelas`, `kelompok`, `jur`, `nick`, `tanggal`, `mulai_jamke`, `sampai_jamke`) VALUES ('$kelompokkelas', '$kelompok', '$jurusan', '$nick_guru', '$tanggal', '$mulai_jam', '$selesai_jam')");

            if ($insert_jadwal) {
                echo "\nBerhasil tambah jadwal Guru\n";
            } else {
                echo "\nGAGAL tambah jadwal Guru\n" .  mysqli_error($konek);
            }
        }

        mysqli_close($konek);
    } else {
        echo "WARNING! Pilih Kelompok Kelas terlebih dahulu!";
    }
}

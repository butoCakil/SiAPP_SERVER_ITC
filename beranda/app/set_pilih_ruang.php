<?php

echo "Set data ruang";
echo "\n";
echo "\n";

// print_r(@$_POST);

if (@$_POST) {
    $_kelompokkelas = @$_POST['kel'];

    if ($_kelompokkelas) {
        $tgl_set = $_POST['tgl_set'];
        $bulan_ruangan = $_POST['ruangan'];
        $_bulan_ruangan = explode('#', $bulan_ruangan);
        $bulan = $_bulan_ruangan[0];
        $ruangan = @$_bulan_ruangan[1];
        $jamke = @$_bulan_ruangan[2];
        $tanggal = date('Y') . '-' . sprintf('%02d', $bulan) . '-' . sprintf('%02d', $tgl_set);
        include '../../config/konesi.php';

        $qu_kel = mysqli_query($konek, "SELECT * FROM kelompokkelas WHERE id = '$_kelompokkelas'");
        $hhasil_kel = mysqli_fetch_array($qu_kel);

        $kelompokkelas = @$hhasil_kel['kode'];
        $dicari = "_";

        // echo "KElompok kelas : " . $kelompokkelas . "\n";

        // ambil tingkat kelas dari kelompok kelas
        if (preg_match("/$dicari/i", $kelompokkelas)) {

            // echo "Data " . $dicari . " Ditemukan";

            $_pecah_kel = explode($dicari, $kelompokkelas);

            // echo "data 1: " . $_pecah_kel[0] . "\n";
            // echo "data 2: " . $_pecah_kel[1] . "\n";
            // echo "data 3: " . $_pecah_kel[2] . "\n";

            $tingkat = @$_pecah_kel[1];
        } else {
            $dicari = "XII";
            if (preg_match("/$dicari/i", $kelompokkelas)) {
                $tingkat = $dicari;
            } else {
                $dicari = "XI";
                if (preg_match("/$dicari/i", $kelompokkelas)) {
                    $tingkat = $dicari;
                } else {
                    $dicari = "X";
                    if (preg_match("/$dicari/i", $kelompokkelas)) {
                        $tingkat = $dicari;
                    } else {
                        $tingkat = '';
                    }
                }
            }
            // echo "\nTingkat : " . $tingkat;
        }

        $jurusan = @$hhasil_kel['jurusan'];
        $kelompok = @$hhasil_kel['kelompok'];

        if (!@$ruangan) {
            $q_hapus = "DELETE FROM `jadwalkbm` WHERE kelas = '$kelompokkelas' AND tanggal = '$tanggal'";
            $hapus_ruang = mysqli_query($konek, $q_hapus);
            if ($hapus_ruang) {
                echo "\nRuang telah dikosongkan\n" . $q_hapus;
                // header("location: setting_kbm.php?jur=" . $jurusan . "&bulanset=" . $bulan . "&kel=" . $kelompokkelas);

                // echo "<script>" .
                //     "window.location.assign('" . "setting_kbm.php?jur=" . $jurusan . "&bulanset=" . $bulan . "&kel=" . $kelompokkelas . "')" .
                //     "</script>";
                header("Refresh:0; url=setting_kbm.php?jur=" . $jurusan . "&bulanset=" . $bulan . "&kel=" . $kelompokkelas);
            } else {
                echo "\nGagal kosongkan ruang\n" . mysqli_error($konek);
            }
        } else {

            $qu_ruangan = mysqli_query($konek, "SELECT * FROM daftarruang WHERE kode = '$ruangan'");
            $hasil_ruangan = mysqli_fetch_array($qu_ruangan);

            $inforuang = @$hasil_ruangan['inforuang'];
            $keterangan_ruang = @$hasil_ruangan['keterangan'];

            // echo "\nbulan ke : " . $bulan;
            // echo "\nruangan : " . $ruangan;
            // echo "\njamke : " . $jamke;

            if ($jamke) {
                $mulai_jam = $jamke;
                $selesai_jam = $jamke + 1;
            } else {
                $mulai_jam = 1;
                $selesai_jam = 11;
            }

            $cek_ruangan_ = mysqli_query($konek, "SELECT * FROM jadwalkbm WHERE ruangan = '$ruangan' AND tanggal = '$tanggal'");
            $hasil_cek_ruangan = mysqli_num_rows($cek_ruangan_);
            $data_cek_ruangan_ = mysqli_fetch_array($cek_ruangan_);

            if ($hasil_cek_ruangan > 0) {
                $cek_kelas_ruan = @$data_cek_ruangan_['kelas'];

                echo "\nRuangan Sudah terisi kelas / kelompok Lain : " . $cek_kelas_ruan;
                echo "\nData Tidak ditambahkan! Silakan pilih ruangan lain!";
            } else {
                $cek_jadwal = mysqli_query($konek, "SELECT * FROM jadwalkbm WHERE kelas = '$kelompokkelas' AND tanggal = '$tanggal' AND mulai_jamke = '$mulai_jam'");
                $data_cek_jadwal = mysqli_fetch_array($cek_jadwal);
                $nick_guru = @$data_cek_jadwal['nick'];

                if ($nick_guru) {
                    $hapus_jam = mysqli_query($konek, "DELETE FROM `jadwalkbm` WHERE kelas = '$kelompokkelas' AND tanggal = '$tanggal'");
                    if ($hapus_jam) {
                        echo "\nDeleted DB sampah";

                        $insert_nick = mysqli_query($konek, "INSERT INTO jadwalkbm (`ruangan`, `info`, `kelas`, `kelompok`, `tingkat`, `jur`, `nick`, `tanggal`, `mulai_jamke`, `sampai_jamke`) VALUES ('$ruangan', '$inforuang', '$kelompokkelas', '$kelompok', '$tingkat', '$jurusan', '$nick_guru', '$tanggal', '$mulai_jam', '$selesai_jam')");

                        if ($insert_nick) {
                            echo "\nTetap tambahkan nick";
                        } else {
                            echo "\nGagal tambahkan nick" . mysqli_error($konek);
                        }
                    } else {
                        echo "\nGagal Delete DB" . mysqli_error($konek);
                    }
                }

                // echo "\nmulai_jam : " . $mulai_jam;
                // echo "\nselesai_jam : " . $selesai_jam;
                // echo "\n";
                // echo 'info ruang : ' . $inforuang;
                // echo "\n";
                // echo 'keterangan : ' . $keterangan_ruang;
                // echo "\n";
                // echo 'Kelompok kelas : ' . $kelompokkelas;
                // echo "\n";

                $cek_jadwal = mysqli_query($konek, "SELECT * FROM jadwalkbm WHERE kelas = '$kelompokkelas' AND tanggal = '$tanggal' AND mulai_jamke = '$mulai_jam'");
                $hasil_cek_jadwal = mysqli_num_rows($cek_jadwal);

                if ($hasil_cek_jadwal > 0) {
                    $update_jadwal = mysqli_query($konek, "UPDATE jadwalkbm SET ruangan = '$ruangan', info = '$inforuang', sampai_jamke = '$selesai_jam' WHERE kelas = '$kelompokkelas' AND tanggal = '$tanggal' AND mulai_jamke = '$mulai_jam'");

                    if ($update_jadwal) {
                        echo "\nBErhasil Update jadwal\n";
                    } else {
                        echo "\nGagal Update Jadwal\n" . mysqli_error($konek);
                    }
                } else {
                    $insert_jadwal = mysqli_query($konek, "INSERT INTO jadwalkbm (`ruangan`, `info`, `kelas`, `kelompok`, `tingkat`, `jur`, `tanggal`, `mulai_jamke`, `sampai_jamke`) VALUES ('$ruangan', '$inforuang', '$kelompokkelas', '$kelompok', '$tingkat', '$jurusan', '$tanggal', '$mulai_jam', '$selesai_jam')");

                    if ($insert_jadwal) {
                        echo "\nBerhasil tambah jadwal\n";
                    } else {
                        echo "\nGAGAL tambah jadwal\n" .  mysqli_error($konek);
                    }
                }
            }
        }
        mysqli_close($konek);
    } else {
        echo "WARNING! Pilih Kelompok Kelas terlebih dahulu!";
    }
}

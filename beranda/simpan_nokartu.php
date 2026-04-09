<?php
if (@$_POST['key'] == 'updatenokartu$123') {

    include "../config/konesi.php";

    $nokartu = @$_POST['nokartu'];

    if ($nokartu) {
        // cari nokartu sudah ada belum 
        $cari = mysqli_query($konek, "SELECT nokartu FROM dataguru WHERE nokartu = '$nokartu'");

        if (mysqli_num_rows($cari) == 0) {
            $cari = mysqli_query($konek, "SELECT nokartu FROM datasiswa WHERE nokartu = '$nokartu'");
            if (mysqli_num_rows($cari) == 0) {
                $getID = @$_POST['id'];
                $getdb = @$_POST['db'];
                if ($getdb) {
                    $insert_nokartu = mysqli_query($konek, "UPDATE `$getdb` SET `nokartu`='$nokartu' WHERE `id`='$getID'");

                    if ($insert_nokartu) {
                        echo "Berhasil ubah nokartu";
                    } else {
                        echo "Gagal ubah nokartu";
                    }
                }
            } else {
                echo "Nomor Sudah digunakan oleh Siswa";
            }
        } else {
            echo "Nomor Sudah digunakan oleh GTK";
        }
    }

    // menghapus nomorkartu di tmprfid
    $sql = mysqli_query($konek, "DELETE FROM tmprfid");

    mysqli_close(($konek));
} else {
    echo "Akses tidak diijinkan";
}

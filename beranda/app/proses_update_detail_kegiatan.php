<?php
// Aktifkan error reporting untuk debugging
// ini_set('display_errors', 1);   // Menampilkan error di browser
// error_reporting(E_ALL);         // Menampilkan semua jenis error

// proses_update_detail_presensi.php
$status = "";
$error = "";
$ruang = "masjid";
$keterangan = "DZUHUR";
// header('Content-Type: application/json');

if (@$_POST) {
    // Lakukan pembaruan data di database

    // Include file koneksi
    include '../../config/konesi.php';

    // Mendapatkan data dari permintaan POST
    $nis = @$_POST['nis'];
    $nama = @$_POST['nama'];
    $tanggal = @$_POST['tanggal'];
    $keteranganMasuk = @$_POST['keteranganMasuk'];
    $waktuH = "12:12:12";

    if($keteranganMasuk == "-" || empty($keteranganMasuk)){
        echo "keteranganMasuk: KOSONG\n";
        
        // Membuat pola LIKE untuk tanggal
        $tanggal_like = "%" . $tanggal . "%";
        
        // Query DELETE
        $query_delete = "DELETE FROM presensiEvent WHERE nis = ? AND tanggal LIKE ?";
        
        // Menyiapkan statement untuk DELETE
        $stmt_delete = mysqli_stmt_init($konek);
        if (!mysqli_stmt_prepare($stmt_delete, $query_delete)) {
            echo "Gagal menyiapkan query: " . mysqli_error($konek);
            exit();
        }

        // Bind parameter untuk DELETE: 'ss' untuk string
        if (!mysqli_stmt_bind_param($stmt_delete, "ss", $nis, $tanggal_like)) {
            echo "Gagal bind parameter: " . mysqli_error($konek);
            exit();
        }

        // Eksekusi statement DELETE
        if (mysqli_stmt_execute($stmt_delete)) {
            echo "Data berhasil dihapus.\n";
        } else {
            echo "Gagal menghapus data: " . mysqli_error($konek);
        }

        // Tutup statement DELETE
        mysqli_stmt_close($stmt_delete);

        // Membuat pola LIKE untuk tanggal
        $tanggal_like = "%" . $tanggal . "%";
        
        // Query DELETE
        $infoM = "Perijinan Masjid";
        $query_delete = "DELETE FROM daftarijin WHERE nis = ? AND info = ? AND tanggalijin LIKE ?";
        
        // Menyiapkan statement untuk DELETE
        $stmt_delete = mysqli_stmt_init($konek);
        if (!mysqli_stmt_prepare($stmt_delete, $query_delete)) {
            echo "Gagal menyiapkan query: " . mysqli_error($konek);
            exit();
        }

        // Bind parameter untuk DELETE: 'ss' untuk string
        if (!mysqli_stmt_bind_param($stmt_delete, "sss", $nis, $infoM, $tanggal_like)) {
            echo "Gagal bind parameter: " . mysqli_error($konek);
            exit();
        }

        // Eksekusi statement DELETE
        if (mysqli_stmt_execute($stmt_delete)) {
            echo "Data berhasil dihapus.\n";
        } else {
            echo "Gagal menghapus data: " . mysqli_error($konek);
        }

        // Tutup statement DELETE
        mysqli_stmt_close($stmt_delete);
    } elseif($keteranganMasuk == "M"){ 
         // Cek data di dalam tabel datapresensi berdasarkan nis dan tanggal
        $infoM = "Perijinan Masjid";
        $query_select_datasiswa = "SELECT * FROM daftarijin WHERE nis = ? AND tanggalijin = ? AND info = ?";
        $stmt_select_datasiswa = mysqli_stmt_init($konek);
        mysqli_stmt_prepare($stmt_select_datasiswa, $query_select_datasiswa);
        mysqli_stmt_bind_param(
            $stmt_select_datasiswa,
            "sss",
            $nis,
            $tanggal,
            $infoM
        );

        mysqli_stmt_execute($stmt_select_datasiswa);
        $result_select_datasiswa = mysqli_stmt_get_result($stmt_select_datasiswa);

        if (mysqli_num_rows($result_select_datasiswa) > 0) {

        } else {
            $query_select_datasiswa = "SELECT * FROM datasiswa WHERE nis = ?";
            $stmt_select_datasiswa = mysqli_stmt_init($konek);
            mysqli_stmt_prepare($stmt_select_datasiswa, $query_select_datasiswa);
            mysqli_stmt_bind_param(
                $stmt_select_datasiswa,
                "s",
                $nis
            );
            mysqli_stmt_execute($stmt_select_datasiswa);
            $result_select_datasiswa = mysqli_stmt_get_result($stmt_select_datasiswa);

            $data_nokartu = "";
            $data_kode = "";
            $data_info = "";
            $data_foto = "";
            foreach ($result_select_datasiswa as $dts) {
                $data_nokartu = $dts['nokartu'];
                $data_nama = $dts['nama'];
                $data_kode = $dts['kode'];
                $data_info = $dts['kelas'];
                $data_foto = $dts['foto'];
            }

            mysqli_stmt_close($stmt_select_datasiswa);

            // INSERT INTO `daftarijin`(`id`, `nokartu`, `nis`, `nama`, `info`, `jam_keluar`, `jam_kembali`, `tanggalijin`, `fotodoc`, `kode`, `timestamp`) VALUES ('[value-1]','[value-2]','[value-3]','[value-4]','[value-5]','[value-6]','[value-7]','[value-8]','[value-9]','[value-10]','[value-11]')
            
            $kodeM = "IJIN";
            $query_insert_datasiswa = "INSERT INTO daftarijin (nokartu, nis, nama, info, jam_keluar, tanggalijin, kode) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert_datasiswa = mysqli_stmt_init($konek);
            mysqli_stmt_prepare($stmt_insert_datasiswa, $query_insert_datasiswa);
            mysqli_stmt_bind_param(
                $stmt_insert_datasiswa,
                "sssssss",
                $data_nokartu,
                $nis,
                $data_nama,
                $infoM,
                $waktuH,
                $tanggal,
                $kodeM
            );

            if (mysqli_stmt_execute($stmt_insert_datasiswa)) {
                $status = "Data berhasil disimpan.";
            } else {
                $error = "Error: " . mysqli_stmt_error($stmt_insert_datasiswa);
            }

            mysqli_stmt_close($stmt_insert_datasiswa);
        }
    } else {
        // echo "keteranganMasuk: $keteranganMasuk\n";

        // Cek data di dalam tabel datapresensi berdasarkan nis dan tanggal
        $query_select_datasiswa = "SELECT * FROM presensiEvent WHERE nis = ? AND tanggal = ?";
        $stmt_select_datasiswa = mysqli_stmt_init($konek);
        mysqli_stmt_prepare($stmt_select_datasiswa, $query_select_datasiswa);
        mysqli_stmt_bind_param(
            $stmt_select_datasiswa,
            "ss",
            $nis,
            $tanggal
        );
        mysqli_stmt_execute($stmt_select_datasiswa);
        $result_select_datasiswa = mysqli_stmt_get_result($stmt_select_datasiswa);

        if (mysqli_num_rows($result_select_datasiswa) > 0) {
            // Data sudah ada, lakukan update
            // Misalnya:
            $query_update_datasiswa = "UPDATE presensiEvent SET mulai = ?, ruang = ? WHERE nis = ? AND tanggal = ?";
            $stmt_update_datasiswa = mysqli_stmt_init($konek);
            mysqli_stmt_prepare($stmt_update_datasiswa, $query_update_datasiswa);
            mysqli_stmt_bind_param(
                $stmt_update_datasiswa,
                "ssss",
                $keteranganMasuk,
                $nis,
                $ruang,
                $tanggal
            );
            if (mysqli_stmt_execute($stmt_update_datasiswa)) {
                $status = "Data berhasil diperbarui.";
            } else {
                $error = "Error: " . mysqli_stmt_error($stmt_update_datasiswa);
            }
            mysqli_stmt_close($stmt_update_datasiswa);
        } else {
            // Data belum ada, lakukan insert

            // Ambiil data dari data siswa
            // Prepare the SELECT statement to check nokartu in the 'datasiswa' table
            $query_select_datasiswa = "SELECT * FROM datasiswa WHERE nis = ?";
            $stmt_select_datasiswa = mysqli_stmt_init($konek);
            mysqli_stmt_prepare($stmt_select_datasiswa, $query_select_datasiswa);
            mysqli_stmt_bind_param(
                $stmt_select_datasiswa,
                "s",
                $nis
            );
            mysqli_stmt_execute($stmt_select_datasiswa);
            $result_select_datasiswa = mysqli_stmt_get_result($stmt_select_datasiswa);

            $data_nokartu = "";
            $data_kode = "";
            $data_info = "";
            $data_foto = "";
            foreach ($result_select_datasiswa as $dts) {
                $data_nokartu = $dts['nokartu'];
                $data_kode = $dts['kode'];
                $data_info = $dts['kelas'];
                $data_foto = $dts['foto'];
            }

            mysqli_stmt_close($stmt_select_datasiswa);

            if($keteranganMasuk == "S"){
                $waktuH = $keteranganMasuk;
            } 

            // Lakukan tambah data
            // INSERT INTO `presensiEvent`(`id`, `nokartu`, `nis`, `ruang`, `mulai`, `selesai`, `jam`, `tanggal`, `timestamp`, `keterangan`) VALUES ('[value-1]','[value-2]','[value-3]','[value-4]','[value-5]','[value-6]','[value-7]','[value-8]','[value-9]','[value-10]')

            $query_insert_datasiswa = "INSERT INTO presensiEvent (nokartu, nis, ruang, mulai, jam, tanggal, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert_datasiswa = mysqli_stmt_init($konek);
            mysqli_stmt_prepare($stmt_insert_datasiswa, $query_insert_datasiswa);
            mysqli_stmt_bind_param(
                $stmt_insert_datasiswa,
                "sssssss",
                $data_nokartu,
                $nis,
                $ruang,
                $waktuH,
                $waktuH,
                $tanggal,
                $keterangan
            );

            if (mysqli_stmt_execute($stmt_insert_datasiswa)) {
                $status = "Data berhasil disimpan.";
            } else {
                $error = "Error: " . mysqli_stmt_error($stmt_insert_datasiswa);
            }

            mysqli_stmt_close($stmt_insert_datasiswa);
        }
    }

    mysqli_close($konek);

    print_r($_POST);

    // Membuat array dengan data yang disebutkan
    $data_array = array(
        'nis' => $nis,
        'ruang' => $ruang,
        'waktuH' => @$waktuH,
        'tanggal' => $tanggal,
        'keterangan' => $keterangan
    );

    // Menampilkan array dalam format yang mudah dibaca
    print_r($data_array);
} else {
    echo "<script>alert('Akses tidak diijinkan!');</script>";
    header("Location: ../");
}

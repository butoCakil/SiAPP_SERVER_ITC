<?php
// proses_update_detail_presensi.php
$status = "";
$error = "";
header('Content-Type: application/json');

if (@$_POST) {
    // Lakukan pembaruan data di database

    // Include file koneksi
    include '../../config/konesi.php';

    // Mendapatkan data dari permintaan POST
    $nis = @$_POST['nis'];
    $nama = @$_POST['nama'];
    $tanggal = @$_POST['tanggal'];
    $keteranganMasuk = @$_POST['keteranganMasuk'];
    $keteranganPulang = @$_POST['keteranganPulang'];

    // Cek data di dalam tabel datapresensi berdasarkan nis dan tanggal
    $query_select_datasiswa = "SELECT nomorinduk, nama, tanggal FROM datapresensi WHERE nomorinduk = ? AND tanggal = ?";
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
        $query_update_datasiswa = "UPDATE datapresensi SET ketmasuk = ?, ketpulang = ? WHERE nomorinduk = ? AND tanggal = ?";
        $stmt_update_datasiswa = mysqli_stmt_init($konek);
        mysqli_stmt_prepare($stmt_update_datasiswa, $query_update_datasiswa);
        mysqli_stmt_bind_param(
            $stmt_update_datasiswa,
            "ssss",
            $keteranganMasuk,
            $keteranganPulang,
            $nis,
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

        $waktumasuk = "07:00:00";
        $waktupulang = "15:00:00";
        $a_time = "00:00:00";
        $b_time = "00:00:00";

        // Lakukan tambah data
        $query_insert_datasiswa = "INSERT INTO datapresensi (nokartu, nomorinduk, nama, info, foto, waktumasuk, ketmasuk, a_time, waktupulang, ketpulang, b_time, tanggal, kode) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert_datasiswa = mysqli_stmt_init($konek);
        mysqli_stmt_prepare($stmt_insert_datasiswa, $query_insert_datasiswa);
        mysqli_stmt_bind_param(
            $stmt_insert_datasiswa,
            "sssssssssssss",
            $data_nokartu,
            $nis,
            $nama,
            $data_info,
            $data_foto,
            $waktumasuk,
            $keteranganMasuk,
            $a_time,
            $waktupulang,
            $keteranganPulang,
            $b_time,
            $tanggal,
            $data_kode
        );
        if (mysqli_stmt_execute($stmt_insert_datasiswa)) {
            $status = "Data berhasil disimpan.";
        } else {
            $error = "Error: " . mysqli_stmt_error($stmt_insert_datasiswa);
        }
        mysqli_stmt_close($stmt_insert_datasiswa);
    }

    mysqli_stmt_close($stmt_select_datasiswa);
    mysqli_close($konek);

    // Kirim tanggapan ke klien
    echo json_encode([
        'message' => "$status",
        'error' => $error,
        'nis' => "$nis",
        'nama' => "$nama",
        'tanggal' => "$tanggal",
        'keteranganMasuk' => "$keteranganMasuk",
        'keteranganPulang' => "$keteranganPulang"
    ]);
} else {
    echo "<script>alert('Akses tidak diijinkan!');</script>";
    header("Location: ../");
}

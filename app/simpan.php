<?php

session_start();
/* tangkap variable yang dikirimkan oleh POST Input */
$post = $_POST;

// ambil data nokartu di DB tmprfid
include("../config/konesi.php");
$sql = mysqli_query($konek, "SELECT * FROM tmprfid");
$data = mysqli_fetch_array($sql);
$nokartu = isset($data['nokartu']) ? $data['nokartu'] : "";

if (!$nokartu) {
    $nokartu = isset($data['nokartu_admin']) ? $data['nokartu_admin'] : "";
}

// tangkap data yang di POST oleh form
$nama = $_POST['nama'];
$kode = $_POST['pilihkatagori'];
$info = $_POST['info'];
// dapatkan nama file foto yang diupload
$foto = $_FILES['foto']['name'];

// jika ada nomor kartu yang ditag dan nama
if ($nokartu && $nama) {
    // Hapus variabel sesi yang tidak digunakan
    unset($_SESSION['nama']);
    unset($_SESSION['info']);
    unset($_SESSION['pilihkatagori']);
    unset($_SESSION['foto']);
    unset($_SESSION['tambah_data']);

    // ekstensi yang diperbolehkan
    $ekstensi_diperbolehkan = array('png', 'jpg');
    // dapatkan ekstensi file foto
    $xten = explode('.', $foto);
    $ekstensi = strtolower(end($xten));
    // dapatkan alamat file foto di folder temporary
    $file_tmp = $_FILES['foto']['tmp_name'];

    // Persiapan insert ke database

    // ambil waktu sekarang
    date_default_timezone_set('Asia/Jakarta');
    $timestamp = date('Y-m-d H:i:s');

    // generate NICK
    // memecah kata dari input nama berdasarkan spasi " "
    $namadepan = explode(" ", $nama);
    // memeriksa apakah ada nama ke-2
    if (empty($namadepan[1])) {
        // jika tidak ada nama ke-2, maka akan mengambil 4 digit dari nomor kartu
        $scndNick = substr($nokartu, 0, 2);
    } else {
        $scndNick = ($namadepan[1]);
    }
    // hasil nick adalah penggabungan nama depan dengan nama kata ke-2 atau 4 digit nomor kartu
    $nick = strtolower($namadepan[0]) . strtolower($scndNick) . substr($nokartu, -2);
    // menghapus karakter koma ","
    $nick = str_replace(',', '', $nick);
    $nick = str_replace('%', '', $nick);
    $nick = str_replace('.', '', $nick);

    // Pindah file foto dari temporary ke folder
    $foto = $nick . "_" . $foto;
    move_uploaded_file($file_tmp, '../img/user/' . $foto);

    if ($kode == "SW") {
        $link = "../datasiswa.php";
        $msg = "Siswa";

        $namatmp = ucwords(strtolower($nama));
        $_kode = str_replace(' ', '', $info);
        $_kode = strtoupper($_kode);
        $info = strtoupper($info);

        // Persiapan prepared statement
        $sql = "INSERT INTO datasiswa (nokartu, nama, nick, kelas, foto, created_at, updated_at, kode) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($konek, $sql);

        if ($stmt) {
            // Binding parameter
            mysqli_stmt_bind_param($stmt, "ssssssss", $nokartu, $namatmp, $nick, $info, $foto, $timestamp, $timestamp, $_kode);

            // Eksekusi prepared statement
            $result = mysqli_stmt_execute($stmt);

            if ($result) {
                $_SESSION['pesan'] = "Data baru " . $msg . " berhasil ditambahkan. Nama : " . $namatmp;
            } else {
                $_SESSION['pesan'] = "Error: Gagal menambahkan data. " . mysqli_error($konek);
            }

            // Tutup prepared statement
            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['pesan'] = "Gagal mempersiapkan prepared statement.";
        }
    } else {
        $link = "../datagtk.php";
        $msg = "GTK";

        // Membuat huruf kapital awal kata
        $info = ucwords(strtolower($info));

        // Membuat nama dan gelar otomatis
        $namatmp = ucwords(strtolower($nama));
        foreach (array('-', '\'', '.') as $delimiter) {
            if (strpos($namatmp, $delimiter) !== FALSE) {
                $namatmp = implode($delimiter, array_map('ucfirst', explode($delimiter, $namatmp)));
            }
        }

        // Persiapan prepared statement
        $sql = "INSERT INTO dataguru (nokartu, nama, nick, status, foto, created_at, updated_at, kode, jabatan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($konek, $sql);

        if ($stmt) {
            // Binding parameter
            mysqli_stmt_bind_param($stmt, "sssssssss", $nokartu, $namatmp, $nick, $info, $foto, $timestamp, $timestamp, $kode, $info);

            // Eksekusi prepared statement
            $result = mysqli_stmt_execute($stmt);

            if ($result) {
                $_SESSION['pesan'] = "Data baru " . $msg . " berhasil ditambahkan. Nama : " . $namatmp;
            } else {
                $_SESSION['pesan'] = "Error: Gagal menambahkan data. " . mysqli_error($konek);
            }

            // Tutup prepared statement
            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['pesan'] = "Gagal mempersiapkan prepared statement.";
        }
    }

    // Menghapus nomor kartu di tmprfid
    $sql = mysqli_query($konek, "DELETE FROM tmprfid");

    mysqli_close($konek);

    header("location:" . $link);
} else {
    // Redirect kembali ke halaman form
    $username['key'] = $_SESSION['username'];
    $pesan_admin['key'] = $_SESSION['pesan_admin'];
    $_SESSION = $post;
    $_SESSION['username'] = $username['key'];
    $_SESSION['pesan_admin'] = $pesan_admin['key'];
    header("location: " . $_SERVER['HTTP_REFERER']);
}

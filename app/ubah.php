<?php
if ($_POST['ubah_data']) {

    session_start();
    /* tangkap variable yang dikirimkan oleh POST Input */
    include("../config/konesi.php");
    $sql = mysqli_query($konek, "SELECT * FROM tmprfid");
    $data = mysqli_fetch_array($sql);
    $nokartu = isset($data['nokartu_admin']) ? $data['nokartu_admin'] : "";

    // tangkap data yang di POST oleh form
    $kode = $_POST['pilihkatagori'];
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $nick = $_POST['nick'];
    $info = $_POST['info'];
    $nama_tmp = $_POST['nama_tmp'];
    $foto_tmp = $_POST['foto_tmp'];
    
    if ($_FILES['foto']['name']) {
        $foto = $_FILES['foto']['name'];
        // ekstensi yang diperbolehkan
        $ekstensi_diperbolehkan = array('png', 'jpg', 'jpeg', 'gif');
        // dapatkan ekstensi file foto
        $xten = explode('.', $foto);
        $ekstensi = strtolower(end($xten));
        // dapatkan alamat file foto di folder temporary
        $file_tmp = $_FILES['foto']['tmp_name'];
        // pindah file foto dari temporary ke folder
        $foto = $nick . "_" . $foto;
        move_uploaded_file($file_tmp, '../img/user/' . $foto);
    } else {
        $foto = $foto_tmp;
    }

    // persiapan update ke database

    // ambil waktu sekarang
    date_default_timezone_set('Asia/Jakarta');
    $timestamp = date('Y-m-d H:i:s');

    if ($nama != $nama_tmp) {
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
    }

    if ($kode == "SW") {
        $link = "datasiswa.php";
        $msg = "Siswa";

        $namatmp = ucwords(strtolower($nama));
        $kode = str_replace(' ', '', $info);
        $kode = strtoupper($kode);
        $info = strtoupper($info);

        $sql = "UPDATE datasiswa SET nokartu = ?, nama = ?, nick = ?, kelas = ?, foto = ?, updated_at = ?, kode = ? WHERE id = ?";
    } else {
        $link = "datagtk.php";
        $msg = "GTK";

        // membuat huruf kapital awal kata
        $info = ucwords(strtolower($info));

        // membuat nama dan gelar otomatis
        $namatmp = ucwords(strtolower($nama));
        foreach (array('-', '\'', '.') as $delimiter) {
            if (strpos($namatmp, $delimiter) !== FALSE) {
                $namatmp = implode($delimiter, array_map('ucfirst', explode($delimiter, $namatmp)));
            }
        }

        $sql = "UPDATE dataguru SET nokartu = ?, nama = ?, nick = ?, status = ?, foto = ?, updated_at = ?, kode = ? WHERE id = ?";
    }

    $stmt = mysqli_prepare($konek, $sql);

    if ($stmt) {
        // Binding parameter
        if ($kode == "SW") {
            mysqli_stmt_bind_param($stmt, "sssssssi", $nokartu, $namatmp, $nick, $info, $foto, $timestamp, $kode, $id);
        } else {
            mysqli_stmt_bind_param($stmt, "sssssssi", $nokartu, $namatmp, $nick, $info, $foto, $timestamp, $kode, $id);
        }

        // Eksekusi prepared statement
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            $_SESSION['pesan'] = "Data dari " . $namatmp . " (" . $msg . ") berhasil diubah.";
        } else {
            $_SESSION['pesan'] = "Error: Gagal mengubah data. " . mysqli_error($konek);
        }

        unset($_SESSION['ubah']);
        $_SESSION['link2'] = $link;

        // Tutup prepared statement
        mysqli_stmt_close($stmt);

        // menghapus nomor kartu di tmprfid
        $sql = mysqli_query($konek, "DELETE FROM tmprfid");

        mysqli_close($konek);

        header("location:" . "../detail.php?nick=" . $nick);
    } else {
        $_SESSION['pesan'] = "Gagal mempersiapkan prepared statement.";
    }
} else {
    $_SESSION['nama'] = $nama;
    $_SESSION['info'] = $info;
    $_SESSION['pilihkatagori'] = $kode;
    $_SESSION['ubah_data'] = true;

    mysqli_close($konek);

    header("location: " . $_SERVER['HTTP_REFERER']);
}

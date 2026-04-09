<?php
session_start();
include('../config/konesi.php');
date_default_timezone_set('Asia/Jakarta');
$timestamp = date('Y-m-d H:i:s');
$tanggal = date('Y-m-d');

if ($_POST) {
    $fotoselfie = $_FILES['selfie']['name'];
    // ekstensi yang diperbolehkan
    $ekstensi_diperbolehkan    = array('png', 'jpg', 'jpeg', 'gif');

    $xten = explode('.', $fotoselfie);
    $ekstensi = strtolower(end($xten));


    if ($fotoselfie) {
        $datab = $_POST['datab'];
        $nokartu = $_POST['nokartu'];
        $nama = $_POST['nama'];
        $nick  = $_POST['nick'];
        $info  = $_POST['info'];
        $fotopp = $_POST['fotopp'];
        $jam   = $_POST['jam'];
        $kode   = $_POST['kode'];

        // echo "tangkap nokartu: " . $nokartu . "<br>";

        if ($datab == "dataguru") {
            $keterangan    = "WFH";
            $fotodoc = "docdis";
            $tglwal = "tglawaldispo";
            $tglakhir = "tglakhirdispo";
        } else {
            $keterangan    = "PJJ";
            $fotodoc = "fotodoc";
            $tglwal = "tglawal";
            $tglakhir = "tglakhir";
        }

        // proses fotoselfie
        $file_tmp = $_FILES['selfie']['tmp_name'];
        $fotoselfie = $keterangan . "_" . $nick . "_" . $tanggal . "." . $ekstensi;
        move_uploaded_file($file_tmp, '../img/user/' . $fotoselfie);
        // echo "datab: ";
        // echo $datab;
        // echo ", fotoselfie: ";
        // echo $fotoselfie;
        // echo ", nick: ";
        // echo $nick;
        // echo ", jam: ";
        // echo $jam;
        // echo ", info: ";
        // echo $info;
        // echo ", nokartu: ";
        // echo $nokartu;
        // echo ", keterangan: ";
        // echo $keterangan;
        // echo ", fotodoc: ";
        // echo $fotodoc;
        // echo ", tanggal: ";
        // echo $tanggal;


        /* rekam presensi */

        // ambil data dari tabel statusnya
        $sql = mysqli_query($konek, "SELECT * FROM statusnya");
        $data = mysqli_fetch_array($sql);
        // ambil data "mode"
        $mode = $data['mode'];
        // ambil waktu dari statusnya
        $waktumasuk = $data['waktumasuk'];
        $waktupulang = $data['waktupulang'];

        echo "<br>";
        echo "nokartu: " . $nokartu;
        // cek nokartu di data presensi
        $stmt = mysqli_prepare($konek, "SELECT nokartu FROM datapresensi WHERE nokartu = ? AND tanggal = ?");
        mysqli_stmt_bind_param($stmt, 'ss', $nokartu, $tanggal);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        $hasilCek = isset($row['nokartu']) ? $row['nokartu'] : "";

        if (!$hasilCek) {
            echo "<br>belum absen hari ini<br>";
            echo "nokartynya masihkah? " . $nokartu;

            if ($jam <= $waktumasuk) {
                $ket = "MSK";

                $w_awal = strtotime($jam);
                $w_akhir = strtotime($waktumasuk);

                // Proses konversi
                $sel_wa = $w_akhir - $w_awal;
                $temp = $sel_wa;
                $selisihjam = sprintf("%02d", (int)($temp / (60 * 60)));
                $selisihmenit = sprintf("%02d", (int)(($temp / 60) - (60 * $selisihjam)));
                $selisihdetik = $temp % 60;
                // penggabungan
                $selisihWaktu = $selisihjam . ":" . $selisihmenit . ":" . $selisihdetik;
            } else {
                $ket = "TLT";
                // menghitung selisih jam
                $w_awal = strtotime($waktumasuk);
                $w_akhir = strtotime($jam);
                // Proses konversi
                $sel_wa = $w_akhir - $w_awal;
                $temp = $sel_wa;
                $selisihjam = sprintf("%02d", (int)($temp / (60 * 60)));
                $selisihmenit = sprintf("%02d", (int)(($temp / 60) - (60 * $selisihjam)));
                $selisihdetik = $temp % 60;
                // penggabungan
                $selisihWaktu = $selisihjam . ":" . $selisihmenit . ":" . $selisihdetik;
            }

            // echo "nokartu " . $nokartu . ", nama " . $nama . ", info " . $info . " , foto " . $fotopp . " , waktumasuk " . $jam . ", ketmasuk " . $ket . ", a_time " . $selisihWaktu . ", tanggal " . $tanggal . ", keterangan " . $keterangan . ", updated_at " . $timestamp . " <br>";

            // buat data di database datapresensi
            // Persiapan prepared statement
            $sql = "INSERT INTO datapresensi (nokartu, nama, info, foto, waktumasuk, ketmasuk, a_time, tanggal, keterangan, updated_at, kode) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($konek, $sql);

            if ($stmt) {
                // Bind parameter
                mysqli_stmt_bind_param($stmt, "isssssssssi", $nokartu, $nama, $info, $fotopp, $jam, $ket, $selisihWaktu, $tanggal, $keterangan, $timestamp, $kode);

                // Eksekusi prepared statement
                $result = mysqli_stmt_execute($stmt);

                if ($result) {
                    // echo "berhasil presensi";
                    $pesan = $nama . ", berhasil melakukan presensi " . $keterangan;
                } else {
                    // echo "Gagal presensi: " . $sql . "<br>" . mysqli_error($konek);
                    $pesan = $nama . ", presensi Anda GAGAL. Tolong coba lagi. Atau hubungi " . "<a href='admin.php'>Admin</a>";
                }

                // Tutup prepared statement
                mysqli_stmt_close($stmt);
            } else {
                $pesan = "Gagal mempersiapkan prepared statement.";
            }

            // update di databsa keterangan dan foto selfie-nya
            // Persiapan prepared statement
            $sql = "UPDATE `$datab` SET keterangan = ?, `$tglwal` = ?, `$tglakhir` = ?, `$fotodoc` = ? WHERE nokartu = ?";
            $stmt = mysqli_prepare($konek, $sql);

            if ($stmt) {
                // Bind parameter
                mysqli_stmt_bind_param($stmt, "ssssi", $keterangan, $tanggal, $tanggal, $fotoselfie, $nokartu);

                // Eksekusi prepared statement
                $result = mysqli_stmt_execute($stmt);

                if ($result) {
                    // echo "data diupdate";
                    $pesan = $nama . ", berhasil melakukan presensi " . $keterangan;
                } else {
                    // echo "Gagal update: " . $sql . "<br>" . mysqli_error($konek);
                    $pesan = $nama . ", presensi kamu GAGAL. Tolong coba lagi. Atau hubungi " . "<a href='admin.php'>Admin</a>";
                }

                // Tutup prepared statement
                mysqli_stmt_close($stmt);
            } else {
                $pesan = "Gagal mempersiapkan prepared statement.";
            }
        } else {
            echo "sudah absen hari ini";
            $pesan = $nama . ", Anda Sudah melakukan Presensi.<br>Terima Kasih :)";
        }

        unset($_SESSION['harusuploadselfie']);
        $_SESSION['direct'] = "index.php";
        $_SESSION['pesan'] = $pesan;
        header("location: ../detail.php?nick=" . $nick);
    } else {
        $_SESSION['harusuploadselfie'] = "Harus upload foto selfie";
        header("location: " . $_SERVER['HTTP_REFERER']);
    }
} else {
    include('error404_2.php');
}

mysqli_close($konek);

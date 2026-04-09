<?php

include("../config/konesi.php");

$kode_error = '';
$detail_error = '';

if ($_POST) {
    session_start();
    date_default_timezone_set('Asia/Jakarta');
    $timestamp = date('Y-m-d H:i:s');
    $tanggal = date('Y-m-d');

    // ambil data dari tabel statusnya
    $sql = mysqli_query($konek, "SELECT * FROM statusnya");
    $data = mysqli_fetch_array($sql);
    $kode_harikerja = $data['info'];

    $datab = $_POST['datab'];

    if ($datab == "dataguru") {
        $_tglawal = "tglawaldispo";
        $_tglakhir = "tglakhirdispo";
        $_fotodoc = "docdis";
        $in_info = "jabatan";
    } else {
        $_tglawal = "tglawal";
        $_tglakhir = "tglakhir";
        $_fotodoc = "fotodoc";
        $in_info = "kelas";
    }

    $nick = $_POST['nick'];
    $keterangan = $_POST['keterangan'];
    $tglawal = $_POST['tglawal'];
    $tglakhir = $_POST['tglakhir'];

    if ($tglakhir < $tglawal) {
        $tglakhir = "tglawal";
    }

    // hapus spasi keterangan
    $ket_terangan = str_replace(" ", "", $keterangan);

    if ($_FILES['fotodoc']['name']) {
        $fotodoc = "doc_ijin_" . $tanggal . '_' . $ket_terangan . '_' . $nick . '_' . $_FILES['fotodoc']['name'];

        $file_tmp = $_FILES['fotodoc']['tmp_name'];
        move_uploaded_file($file_tmp, '../img/user/' . $fotodoc);
    } else {
        $fotodoc = "docs.png";
    }

    // echo "nick : ";
    // echo $nick;
    // echo "keterangan : ";
    // echo $keterangan;
    // echo ", tglawal : ";
    // echo $tglawal;
    // echo ", tglakhir : ";
    // echo $tglakhir;
    // echo ", fotodoc : ";
    // echo $fotodoc;
    // echo ", database ; ";
    // echo $datab;

    // die;

    // cek apakah tanggal awal dan tanggal akhir adalah hari libur
    $libur_awal = harilibur($kode_harikerja, $tglawal);
    $libur_akhir = harilibur($kode_harikerja, $tglakhir);

    if ($libur_awal == true) {
        // tambah 1 hari
        $tglawal = date('Y-m-d', strtotime($tglawal . ' +1 day'));
        $libur_awal = harilibur($kode_harikerja, $tglawal);
        if ($libur_awal == true) {
            // tambah 1 hari lagi
            $tglawal = date('Y-m-d', strtotime($tglawal . ' +1 day'));
        }
    }

    if ($libur_akhir == true) {
        // kurangi 1 hari
        $tglakhir = date('Y-m-d', strtotime($tglakhir . ' -1 day'));
        $libur_akhir = harilibur($kode_harikerja, $tglakhir);
        if ($libur_akhir == true) {
            // kurangi 1 hari lagi
            $tglakhir = date('Y-m-d', strtotime($tglakhir . ' -1 day'));
        }
    }

    // tglawal harus lebih kecil sama dengan tglakhir
    if ($tglawal > $tglakhir) {
        $pesan = 'Periksa kembali tanggal ijin, pastikan bukan berada pada hari libur';
        $_SESSION['direct'] = "index.php";
        $_SESSION['pesan'] = $pesan;
        header("location: " . $_SERVER['HTTP_REFERER']);
    } else {

        // updete query

        $sql = ("UPDATE `$datab` SET keterangan ='$keterangan', `$_tglawal`='$tglawal', `$_tglakhir`='$tglakhir', `$_fotodoc` = '$fotodoc' WHERE nick='$nick'");
        $update = mysqli_query($konek, $sql);
        if ($update) {
            // echo "berhasil tambahkan ijin " . $keterangan;
            $pesan = 'Berhasil tambahkan ijin "' . $keterangan . '"';
        } else {
            $kode_error = "003";
            $detail_error = "Error: Gagal menambahkan data. " . $sql . "<br>" . mysqli_error($konek);
            echo "Error: Gagal menambahkan data. " . $sql . "<br>" . mysqli_error($konek);
            include('error404_2.php');
            // die;
        }
    }


    $sql = mysqli_query($konek, "SELECT * FROM `$datab` WHERE nick = '$nick'");

    $data = mysqli_fetch_array($sql);
    $nokartu = $data['nokartu'];
    $nama = $data['nama'];
    $foto = $data['foto'];
    $info = $data[$in_info];
    $kode = $data['kode'];

    $settgl = $tglawal;

    for ($i = $tglawal; $i <= $tglakhir; $i++) {

        // echo 'masuk luping for ke: ' . (string)$i . '<br>';

        $harilibur = harilibur($kode_harikerja, $i);


        if ($harilibur == false) {
            // echo 'masuk bukan hari libur' . '<br>';
            // catat di presensi
            // cek nokartu di data presensi
            $sql = mysqli_query($konek, "SELECT nokartu FROM datapresensi WHERE nokartu = '$nokartu' AND tanggal = '$settgl'");
            $data = mysqli_fetch_array($sql);
            $hasilCek = isset($data['nokartu']) ? ($data['nokartu']) : "";

            // echo 'cek di daftarpresensi' . '<br>';
            // echo 'hasil cek : ' . $hasilCek . '<br>';

            $sql = mysqli_query($konek, "SELECT nokartu FROM daftarijin WHERE nokartu = '$nokartu' AND tanggalijin = '$settgl'");
            $data = mysqli_fetch_array($sql);
            $hasilCek2 = isset($data['nokartu']) ? ($data['nokartu']) : "";

            // echo 'cek di daftar ijin' . '<br>';
            // echo 'hasil cek : ' . $hasilCek2 . '<br>';

            if ($hasilCek2) {
                $sql2 = mysqli_query($konek, "UPDATE daftarijin SET keterangan = '$keterangan', fotodoc = '$fotodoc', timestamp = '$timestamp' WHERE nokartu = '$nokartu' AND tanggalijin = '$settgl'");

                // $aa = 'update';
                if (!$sql2) {
                    $kode_error = "004";
                    $detail_error = "Error: Gagal menambahkan data. " . $sql2 . "<br>" . mysqli_error($konek);
                    echo "Error: Gagal menambahkan data. " . $sql2l . "<br>" . mysqli_error($konek);
                    include('error404_2.php');
                    // die;
                }
            } else {
                $sql2 = mysqli_query($konek, " INSERT INTO daftarijin (nokartu, nama, info, foto, tanggalijin, keterangan, fotodoc, kode, timestamp) VALUES('$nokartu', '$nama', '$info', '$foto', '$settgl', '$keterangan', '$fotodoc', '$kode', '$timestamp')");

                // $aa = 'insert';
                if (!$sql2) {
                    $kode_error = "004";
                    $detail_error = "Error: Gagal menambahkan data. " . $sql2 . "<br>" . mysqli_error($konek);
                    echo "Error: Gagal menambahkan data. " . $sql2l . "<br>" . mysqli_error($konek);
                    include('error404_2.php');
                    // die;
                }
            }
            

            // if ($sql2) {
            //     // echo "berhasil tambahkan ijin " . $keterangan;
            //     $pesan = '<br>Berhasil ' . $aa . ' ijin "' . $keterangan . '"';
            // } else {
            //     echo "Error: Gagal " . $aa . " data. " . $sql2 . "<br>" . mysqli_error($konek);
            //     include('error404_2.php');
            //     // die;
            // }

            // echo 'nama : ' . $nama . '<br>';
            // echo 'nokartu : ' . $nokartu . '<br>';
            // echo 'info : ' . $info . '<br>';
            // echo 'foto : ' . $foto . '<br>';
            // echo 'tanggal : ' . $settgl . '<br>';
            // echo 'keterangan : ' . $keterangan . '<br>';
            // echo 'fotodoc : ' . $fotodoc . '<br>';
            // echo 'kode : ' . $kode . '<br>';
            // echo $pesan;
            // echo 'sampai disini';
            // die;

            if (!$hasilCek) {
                // echo 'masuk di buat data di tabel datapresensi';
                // die;
                // buat data di database datapresensi
                $sql = mysqli_query($konek, "INSERT INTO datapresensi (nokartu, nama, info, foto, tanggal, keterangan, updated_at, kode) VALUES ($nokartu, '$nama', '$info', '$foto', '$settgl', '$keterangan', '$timestamp', '$kode')");


                if ($sql) {
                    $x = "OK";
                    // echo "berhasil Tambahkan ke datapresensi";
                    // $pesan = $pesan . ", " . $nama . ". Telah ditambahkan ke datapresensi";
                } else {
                    $x = "";
                    $kode_error = "001";
                    $detail_error = "Error: Gagal menambahkan data. " . $sql . "<br>" . mysqli_error($konek) . " Tanggal : " . $settgl;
                    echo "Error: Gagal menambahkan data. " . $sql . "<br>" . mysqli_error($konek);
                    include('error404_2.php');
                    // die;
                }
            } else {
                // echo 'masuk di "sudah ada data di tabel daftarpresensi"';
                // die;
                // update catatan presensi
                $sql = mysqli_query($konek, "UPDATE datapresensi SET keterangan = '$keterangan' WHERE nokartu = '$nokartu' AND tanggal = '$settgl'");

                if ($sql) {
                    $x = "OK";
                    // echo "berhasil terupdate data presensi " . $nama;
                    // $pesan = $pesan . ", " . $nama . ". Telah diperbarui di datapresensi";
                } else {
                    $x = "";
                    $kode_error = "002";
                    $detail_error = "Error: Gagal menambahkan data. " . $sql . "<br>" . mysqli_error($konek);
                    echo "Error: Gagal menambahkan data. " . $sql . "<br>" . mysqli_error($konek);
                    include('error404_2.php');
                    // die;
                }
            }
        } else {
            $x = "Hari libur, tidak dapat memperbarui datapresensi, Coba kembali saat hari kerja.";
            // echo $x;
            // die;
        }
        $settgl++;
    }

    if ($x) {
        if ($x == 'OK') {
            $pesan = $pesan . ", " . $nama . ". Telah diperbarui di datapresensi. " . $x;
        } else {
            $pesan = $x;
        }
    } else {
        $pesan = 'Kode error: ' . $kode_error . ' <i style="color: red;"><span class="iconify" data-icon="emojione-v1:warning"></span>&nbsp;Update data bermasalah. Coba ulangi, atau laporkan bug ke <a class="btn btn-sm btn-danger" href="admin.php">Admin</a></i><br>' . $detail_error;
    }

    $_SESSION['direct'] = "index.php";
    $_SESSION['pesan'] = $pesan;
    header("location: ../detail.php?nick=" . $nick);
} else {
    include('error404_2.php');
}

mysqli_close($konek);

// hari libur
function enamharikerja($tanggal)
{
    $tanggal = strtotime($tanggal);
    $tanggal = date('l', $tanggal);
    $tanggal = strtolower($tanggal);
    if ($tanggal == "sunday") {
        return true;
    } else {
        return false;
    }
}

function limaharikerja($tanggal)
{
    $tanggal = strtotime($tanggal);
    $tanggal = date('l', $tanggal);
    $tanggal = strtolower($tanggal);
    if ($tanggal == "sunday" || $tanggal == "saturday") {
        return true;
    } else {
        return false;
    }
}

function harilibur($kode, $tanggal)
{
    if ($kode) {
        if ($kode == "5") {
            $tanggal = strtotime($tanggal);
            $tanggal = date('l', $tanggal);
            $tanggal = strtolower($tanggal);
            if ($tanggal == "sunday" || $tanggal == "saturday") {
                return true;
            } else {
                return false;
            }
        } elseif ($kode == "6") {
            $tanggal = strtotime($tanggal);
            $tanggal = date('l', $tanggal);
            $tanggal = strtolower($tanggal);
            if ($tanggal == "sunday") {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

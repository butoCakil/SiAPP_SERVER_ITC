<?php
include('app/get_user.php');

if ($level_login == 'admin' || $level_login == 'superadmin') {
    $title = 'Setting KBM';
    $navlink = 'setKBM';

    if (@$_GET['bulanset']) {
        $set_bulan = $_GET['bulanset'];
        $set_bulan = mysqli_real_escape_string($konek, $set_bulan);
    } else {
        $set_bulan = date('m');
    }

    if (!@$_GET['set']) {
        $kelompokkelas = @$_GET['kel'];
        $kelompokkelas = mysqli_real_escape_string($konek, $kelompokkelas);

        if (@$_GET['jur']) {
            $jur = $_GET['jur'];
            $jur = mysqli_real_escape_string($konek, $jur);
            $tingkat = @$_GET['t'];
            $tingkat = mysqli_real_escape_string($konek, $tingkat);
            $na = @$_GET['na'];
            $na = mysqli_real_escape_string($konek, $na);

            if ($tingkat) {
                $sortir_jur = "WHERE jur = '$jur' AND tingkat = '$tingkat'";
            } else {
                $sortir_jur = "WHERE jur = '$jur'";
            }

            // $sortir_jur = mysqli_real_escape_string($konek, $sortir_jur);
            $get_kode = "SELECT * FROM kodeinfo " . $sortir_jur . " ORDER BY info ASC";
            $q_kode = mysqli_query($konek, $get_kode);

            $data_kelas = array();
            $i = 0;
            $query_kelas = "";
            while ($data_kode = mysqli_fetch_array($q_kode)) {
                $data_kelas[$i] = @$data_kode['info'];
                $i++;

                $query_kelas = $query_kelas . ($query_kelas ? " OR kelas = '" : " WHERE kelas = '") . @$data_kode['info'] . "'";
            }

            $jur = mysqli_real_escape_string($konek, $jur);
            $sql_daftarruang = "SELECT * FROM daftarruang WHERE keterangan = '$jur'";
            $q_daftarruang = mysqli_query($konek, $sql_daftarruang);

            $jumlah_daftarruang = 0;
            $data_ruang = array();
            while ($data_daftarruang = mysqli_fetch_array($q_daftarruang)) {
                $data_ruang[] = $data_daftarruang;
                $jumlah_daftarruang++;
            }
        } else {
            $jur = '';
            $tingkat = '';
        }

        // echo '<pre>';
        // print_r($data_kelas);
        // echo '</pre>';
        // echo '<br>';
        // echo '<br>';
        // echo '<pre>';
        // print_r($query_kelas);
        // echo '</pre>';

        $sql_siswa = "SELECT * FROM datasiswa" . @$query_kelas . " ORDER BY kelas ASC";
        $query_siswa = mysqli_query($konek, $sql_siswa);

        // while($data_siswa = mysqli_fetch_array($query_siswa)){
        //     echo "Nama Siswa : " . $data_siswa['nama'] . "<br>";
        // }

        // $sql_jadwalguru = "SELECT * FROM jadwalgurujur";
        // $q_jadwalguru = mysqli_query($konek, $sql_jadwalguru);

        // $jumlah_jadwalgurujur = 0;
        // $data_jadwal = array();
        // while ($data_jadwalguru = mysqli_fetch_array($q_jadwalguru)) {
        //     $data_jadwal[] = $data_jadwalguru;
        //     $jumlah_jadwalgurujur++;
        // }

        if (@$jur) {
            $sql_data_kelompok = mysqli_query($konek, "SELECT * FROM kelompokkelas WHERE jurusan = '$jur' ORDER BY info ASC");
        } else {
            $sql_data_kelompok = mysqli_query($konek, "SELECT * FROM kelompokkelas ORDER BY info ASC");
        }
        $data_kelompok = array();
        $jumlah_datakelompok = 0;
        while ($hasil_data_kelompok = mysqli_fetch_array($sql_data_kelompok)) {
            $data_kelompok[] = $hasil_data_kelompok;
            $jumlah_datakelompok++;
        }

        // echo '<br>';
        // echo '<pre>';
        // print_r($data_kelompok);
        // echo '</pre>';

        // die;

        $sql_data_daftarruang = mysqli_query($konek, "SELECT * FROM daftarruang");
        $data_daftarruang = array();
        $jumlah_datadaftarruang = 0;
        while ($hasil_data_daftarruang = mysqli_fetch_array($sql_data_daftarruang)) {
            $data_daftarruang[] = $hasil_data_daftarruang;
            $jumlah_datadaftarruang++;
        }

        // echo '<br>';
        // echo '<pre>';
        // print_r($data_daftarruang);
        // echo '</pre>';

        // die;

        $sql_data_dataguru = mysqli_query($konek, "SELECT * FROM dataguru");
        $data_dataguru = array();
        $jumlah_datadataguru = 0;
        while ($hasil_data_dataguru = mysqli_fetch_array($sql_data_dataguru)) {
            $data_dataguru[] = $hasil_data_dataguru;
            $jumlah_datadataguru++;
        }

        // echo '<br>';
        // echo '<pre>';
        // print_r($data_dataguru);
        // echo '</pre>';

        // die;

        $sql_data_jampelajaran = mysqli_query($konek, "SELECT * FROM jampelajaran");
        $data_jampelajaran = array();
        $jumlah_datajampelajaran = 0;
        while ($hasil_data_jampelajaran = mysqli_fetch_array($sql_data_jampelajaran)) {
            $data_jampelajaran[] = $hasil_data_jampelajaran;
            $jumlah_datajampelajaran++;
        }

        // echo '<br>';
        // echo '<pre>';
        // print_r($data_jampelajaran);
        // echo '</pre>';

        // die;

        $sql_data_jadwalkbm = mysqli_query($konek, "SELECT * FROM jadwalkbm WHERE kelas = '$kelompokkelas' AND tanggal LIKE '%" . date('Y') . '-' . sprintf('%02d', $set_bulan) . "%'");
        $data_jadwalkbm = array();
        $jumlah_datajadwalkbm = 0;
        while ($hasil_data_jadwalkbm = mysqli_fetch_array($sql_data_jadwalkbm)) {
            $data_jadwalkbm[] = $hasil_data_jadwalkbm;
            $jumlah_datajadwalkbm++;
        }

        // echo '<br>';
        // echo '<pre>';
        // print_r($data_jadwalkbm);
        // echo '</pre>';
        // echo '<br>';

        // die;

        // $id = id_cek_array_db($data_jadwalkbm[], 'tanggal', '2022-10-04');

        // echo "<br>ID nya : ";
        // echo "<pre>";
        // print_r($id);
        // echo "</pre>";

        // die;
    }

    $jumlahhari_bulan = cal_days_in_month(CAL_GREGORIAN, $set_bulan, date('Y'));
    $first = date("l", strtotime(date('Y') . '-' . $set_bulan . '-' . '01'));

    $angka_hari = hari_ke_angka($first);

    include('views/header.php');

    if (@$_GET['jadwal_semster']) {
        include "views/_jadwalSemester.php";
    } else {
        include('views/_setting_kbm.php');
    }

    include('views/footer.php');
} else {
    $pesan = "Maaf bossku,<br>Anda tidak memiliki akses ke menu ini!";
    $_SESSION['pesan'] = $pesan;
    header('location: 404.php');
}

function hari_ke_angka($hari__)
{
    if ($hari__ == 'Monday') {
        $__angka_hari = '1';
    } elseif ($hari__ == 'Tuesday') {
        $__angka_hari = '2';
    } elseif ($hari__ == 'Wednesday') {
        $__angka_hari = '3';
    } elseif ($hari__ == 'Thursday') {
        $__angka_hari = '4';
    } elseif ($hari__ == 'Friday') {
        $__angka_hari = '5';
    } elseif ($hari__ == 'Saturday') {
        $__angka_hari = '6';
    } else {
        $__angka_hari = '7';
    }

    return $__angka_hari;
}

// cek data (array dari DB, nama tabel, isi data tabel)
function cek_data_array($_array, $_datatbl, $_isidata)
{
    // $_jumlah_data = count($_array);
    // for ($_i = 0; $_i < $_jumlah_data; $_i) {
    $_data = @$_array[$_datatbl];
    // if (@$_data) {
    if ($_data == $_isidata) {
        return true;
    } else {
        return false;
    }
    // }
}

// // cari berdasarkan tanggal di hasil cari presensi
function cari_diarray($data_dicari, $hasil_array_db, $_namatabel)
{
    $hasil_cari = array();
    foreach ($hasil_array_db as $dtp) {
        if ($dtp[$_namatabel] == $data_dicari) {
            $hasil_cari[] = $dtp;
        }
    }
    return $hasil_cari;
}

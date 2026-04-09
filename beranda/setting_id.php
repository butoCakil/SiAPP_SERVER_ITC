<?php
include('app/get_user.php');
// die;
if ($level_login == 'admin' || $level_login == 'superadmin') {
    $title = 'Setting ID';
    $navlink = 'setID';

    include('views/header.php');
?>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#inputkartu").load("../readtmprfid.php");
            setInterval(function() {
                $("#inputkartu").load("../readtmprfid.php");
            }, 1000);
        });
    </script>
<?php
    // koding disini

    $getdb = @$_GET['db'];

    if ($getdb == "GTK") {
        $db = "dataguru";
        $ni = "nip";
        $info = "info";
        $urut = "info";
    } elseif ($getdb == "SISWA") {
        $db = "datasiswa";
        $ni = "nis";
        $info = "kelas";
        $urut = "kode";
    } else {
        $db = "";
        $ni = "";
        $info = "";
        $urut = "";
    }

    include "../config/konesi.php";

    // menghapus nomorkartu di tmprfid
    $sql = mysqli_query($konek, "DELETE FROM tmprfid");
    if ($db) {

        $sql_db = mysqli_query($konek, "
            SELECT `id`, `nokartu`, `$ni`, `nama`, `kode`, `$info` 
            FROM `$db` 
            ORDER BY `kode` ASC, `nama` ASC
        ");

        mysqli_close($konek);
    }

    include('views/_setting_id.php');
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

<?php
include('app/get_user.php');
$tanggal = date('Y-m-d');
$tanggal_dmY = date('d-m-Y');
$tahun = date('Y');

if (@$_GET['tanggal']) {
    $tanggal_get = $_GET['tanggal'];
    $tanggal_pilih = date('Y-m-d', strtotime($tanggal_get));
    $tanggal_pilih_dmY = date('d-m-Y', strtotime($tanggal_get));
    $nama_hari_pilih = hariIndo(date('l', strtotime($tanggal_get)));
    $nama_hari_singkat_pilih = hariSingkatIndo(date('l', strtotime($tanggal_get)));
    $tahun_bulan_pilih = date('Y-m', strtotime($tanggal_get));
    $bulan_tahun_pilih = date('m-Y', strtotime($tanggal_get));
    $bulanIndoPilih = bulanIndo(date('F', strtotime($tanggal_get)));
} else {
    $tanggal_pilih = date('Y-m-d');
    $tanggal_pilih_dmY = date('d-m-Y');
    $nama_hari_pilih = hariIndo(date('l', strtotime($tanggal_pilih)));
    $nama_hari_singkat_pilih = hariSingkatIndo(date('l', strtotime($tanggal_pilih)));
    $tahun_bulan_pilih = date('Y-m');
    $bulan_tahun_pilih = date('m-Y');
    $bulanIndoPilih = bulanIndo(date('F', strtotime($tanggal_pilih)));
}

$tanggal_indo_pilih = date('d', strtotime($tanggal_pilih)) . " " . $bulanIndoPilih . " " . date('Y', strtotime($tanggal_pilih));
$hari_indo = hariIndo(date('l', strtotime($tanggal_pilih)));

include('../config/konesi.php');

if (@$_GET['datab']) {
    $datab = $_GET['datab'];
    if ($datab == 'siswa') {
        $title = 'Wali Kelas ' . $ket_akses_login . ' - ' . $tanggal_indo_pilih;
        $navlink = 'Wali';
        $navlink_sub = 'harian';

        if ($akses_login != 'Wali Kelas') {
            header('location: semuakelas.php');
        } else {
            // echo 'S = 1';
            // $databasis = 'datasiswa';
            // $where_sql = "WHERE kelas = '" . $ket_akses_login . "'";
            // $where_sql_2 = "WHERE info = '" . $ket_akses_login . "'";
            $link_datab = 'harian.php?datab=siswa&tanggal=';
            $sql1 = "SELECT * FROM datasiswa WHERE kelas='" . $ket_akses_login . "' ORDER BY nama ASC";
            $sql2 = "SELECT * FROM datapresensi WHERE info='" . $ket_akses_login . "' ORDER BY nama ASC";
        }
    } else if ($datab == 'GTK') {
        $title = 'Data Guru dan Tendik - ' . $tanggal_indo_pilih;
        $navlink = 'Data Guru';
        $navlink_sub = 'harian';
        $ket_akses_login = 'Guru dan Tenaga Pendidikan';

        // echo 'S = 2';
        if ($level_login == 'admin' || $level_login == 'superadmin') {

            // $databasis = 'dataguru';
            // $where_sql = '';
            // $where_sql_2 = "WHERE (info = 'GR' OR info = 'KR')";
            $link_datab = 'harian.php?datab=GTK&tanggal=';
            $sql1 = "SELECT * FROM dataguru ORDER BY nama ASC";
            $sql2 = "SELECT * FROM datapresensi WHERE (kode = 'GR' OR kode = 'KR')";
        } else {
            header('location: semuakelas.php');
        }
    } else {
        // echo 'S = 3';
        if ($level_login == 'admin' || $level_login == 'superadmin') {
            header('location:harian.php?datab=GTK');
        } else if ($level_login == 'user_gtk') {
            header('location:harian.php?datab=siswa');
        } else {
            $_SESSION['pesan'] = 'Anda tidak memiliki akses ke halaman ini. Maaf! <br> Silakan kembali ke Home / Beranda. <i>(Kode: #HA002)</i>';
            header('location: 404.php');
        }
    }
} else {
    // echo 'S = 4';
    if ($level_login == 'admin' || $level_login == 'superadmin') {
        // echo 'S = 5';
        header('location:harian.php?datab=GTK');
    } else if ($level_login == 'user_gtk') {
        // echo 'S = 6';
        header('location:harian.php?datab=siswa');  
    } else {
        // echo 'S = 7';
        $_SESSION['pesan'] = 'Anda tidak memiliki akses ke halaman ini. Maaf! <br> Silakan kembali ke Home / Beranda. <i>(Kode: #HA001)</i>';
        header('location: 404.php');
    }
}

// echo 'S = 8';
// echo '<br>';
// echo $databasis;
// echo '<br>';
// echo $where_sql;
// echo '<br>';
// echo $where_sql_2;
// echo '<br>';
// echo $link_datab;
// echo '<br>';
// echo $sql1;

$sql3 = "SELECT * FROM statusnya";
$query3 = mysqli_query($konek, $sql3);
$status = mysqli_fetch_assoc($query3);
$harikerja = $status['info'];

$tanggal_pilih_plus = date('Y-m-d', strtotime($tanggal_pilih . ' +1 day'));

$cek_libur_tanggal_plus = limaharikerja($tanggal_pilih_plus);
if ($cek_libur_tanggal_plus == true) {
    $tanggal_pilih_plus = date('Y-m-d', strtotime($tanggal_pilih_plus . ' +1 day'));
    $cek_libur_tanggal_plus = limaharikerja($tanggal_pilih_plus);
    if ($cek_libur_tanggal_plus == true) {
        $tanggal_pilih_plus = date('Y-m-d', strtotime($tanggal_pilih_plus . ' +1 day'));
    }
}

$tanggal_pilih_min = date('Y-m-d', strtotime($tanggal_pilih . ' -1 day'));

$cek_libur_tanggal_min = limaharikerja($tanggal_pilih_min);
if ($cek_libur_tanggal_min == true) {
    $tanggal_pilih_min = date('Y-m-d', strtotime($tanggal_pilih_min . ' -1 day'));
    $cek_libur_tanggal_min = limaharikerja($tanggal_pilih_min);
    if ($cek_libur_tanggal_min == true) {
        $tanggal_pilih_min = date('Y-m-d', strtotime($tanggal_pilih_min . ' -1 day'));
    }
}

if ($tanggal_pilih_plus >= (date('Y-m-d', strtotime($tanggal . ' +1 day')))) {
    $tanggal_pilih_plus = '#';
    $disabled = ' disabled btn btn-secondary';
} else {
    $tanggal_pilih_plus = $link_datab . $tanggal_pilih_plus;
    $disabled = '';
}

// $sql1 = "SELECT * FROM `$databasis` `$where_sql` ORDER BY nama ASC";
$query1 = mysqli_query($konek, $sql1);
$hit_datasiswa = mysqli_num_rows($query1);

// $sql2 = "SELECT * FROM datapresensi `$where_sql_2` ORDER BY nama ASC";
$query2 = mysqli_query($konek, $sql2);

$hasil_datasiswa = array();
while ($datasiswa = mysqli_fetch_array($query1)) {
    $hasil_datasiswa[] = $datasiswa;
}

$hasil_datapresensi = array();
while ($datapresensi = mysqli_fetch_array($query2)) {
    $hasil_datapresensi[] = $datapresensi;
}

// cari data di array
// $nama_siswa = $hasil_datasiswa[5]['nama'];
// $nokartu_siswa = $hasil_datasiswa[5]['nokartu'];

// $hasil_cari_presensi = cari_data_presensi($nokartu_siswa, $hasil_datapresensi);


// $hasil_cari_tanggal = cari_tanggal('2022-02-10', $hasil_cari_presensi);
// $hitung_hasil_cari_tanggal = count($hasil_cari_tanggal);

// $tanggal_presensi_siswa = $hasil_cari_tanggal[0]['tanggal'];
// $nama_presensi_siswa = $hasil_cari_tanggal[0]['nama'];
// $waktumasuk_presensi_siswa = $hasil_cari_tanggal[0]['waktumasuk'];
// $ketmasuk_presensi_siswa = $hasil_cari_tanggal[0]['ketmasuk'];


// print_r('nama siswa: ' . $nama_siswa);
// print_r('<br>');
// print_r('nokartu siswa: ' . $nokartu_siswa);
// print_r('<br>');
// print_r('<br>');

// // print_r('hasil dari cari : ');
// // print_r('<br>');
// // print_r('hitung hasil cari presensi: ' . $hitung_hasil_cari_tanggal);
// // print_r('<br> tanggal presensi siswa : ');
// // print_r($tanggal_presensi_siswa);
// // print_r('<br> nama presensi siswa : ');
// // print_r($nama_presensi_siswa);
// // print_r('<br> waktumasuk presensi siswa : ');
// // print_r($waktumasuk_presensi_siswa);
// // print_r('<br> ketmasuk presensi siswa : ');
// // print_r($ketmasuk_presensi_siswa);

// // print_r('<br>');
// // print_r('<br>');
// // print_r('hasil_cari_tanggal: ');
// // printf("<pre>%s</pre>", print_r($hasil_cari_tanggal, true));
// print_r('<br>');
// print_r('hasil_cari_presensi: ');
// printf("<pre>%s</pre>", print_r($hasil_cari_presensi, true));
// print_r('<br>');

// // print_r('<br>');
// // print_r('data_array_hasil_datasiswa: ');
// // print_r('<br>');
// // printf('<pre>%s</pre>', );



// print_r(' Data Siswa: ');
// printf("<pre>%s</pre>", print_r($hasil_datasiswa, true));
// print_r('<br>');
// print_r(' Data Presensi: ');
// printf("<pre>%s</pre>", print_r($hasil_datapresensi, true));
// die;

if ($tanggal_pilih > $tanggal) {
    $pesan = 'Hari esok belum datang, mari kita bersiap membuat hari esok menjadi lebih baik';
    $tanggal_pilih = $tanggal;
}

$cek_libur_tanggal = limaharikerja($tanggal_pilih);
if ($cek_libur_tanggal == true) {
    $tanggal_kurangi_1 = date('Y-m-d', strtotime('-1 day', strtotime($tanggal)));
    $cek_libur_tanggal = limaharikerja($tanggal_kurangi_1);
    if ($cek_libur_tanggal == true) {
        $tanggal_kurangi_2 = date('Y-m-d', strtotime('-1 day', strtotime($tanggal_kurangi_1)));
        $cek_libur_tanggal = limaharikerja($tanggal_kurangi_2);
        if ($cek_libur_tanggal == true) {
            $tanggal_kurangi_3 = date('Y-m-d', strtotime('-1 day', strtotime($tanggal_kurangi_2)));
            $cek_libur_tanggal = limaharikerja($tanggal_kurangi_3);
            if ($cek_libur_tanggal == false) {
                $pesan = 'Hari ini Libur ya...';
                header('location: ' . $link_datab . $tanggal_kurangi_3);
            }
        } else {
            $pesan = 'Hari ini Libur ya...';
            header('location: ' . $link_datab . $tanggal_kurangi_2);
        }
    } else {
        $pesan = 'Hari ini Libur ya...';
        header('location: ' . $link_datab . $tanggal_kurangi_1);
    }
}

// die;

include('views/header.php');
include('views/_harian.php');
mysqli_close($konek);
include('views/footer.php');

?>
<script>
    $(function() {
        $("#example1").DataTable({
            // dom: 'Bflitp',
            dom: 'flBtip',
            // buttons: ['print', 'excel', 'csv', 'pdf'],
            "autoWidth": false,
            "responsive": true,
            "lengthChange": true,
            "lengthMenu": [
                [30, 50, -1],
                [30, 50, "Semua---"]
            ],
            "pagingType": "full",
            "language": {
                "emptyTable": "Tida ada data untuk tanggal yang dipilih.",
                "info": "Ditampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "infoEmpty": "Ditampilkan 0 sampai 0 dari 0 data",
                "infoFiltered": "(Disaring dari _MAX_ total data)",
                "lengthMenu": "Tampilkan _MENU_ baris data",
                "loadingRecords": "Memuat...",
                "processing": "Memproses...",
                "search": "Cari:",
                "zeroRecords": "Tidak ditemukan data yang sesuai.",
                "paginate": {
                    "first": "<<",
                    "last": ">>",
                    "next": "lanjut >",
                    "previous": "< sebelum"
                },
            },
            "buttons": ["print", "pdf", "excel", "colvis"]
            // "buttons": ["excel", "pdf", "print"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>

<?php
// // cari berdasarkan tanggal di hasil cari presensi
function cari_tanggal($tanggal_pilih, $hasil_cari_presensi)
{
    $hasil_cari_tanggal = array();
    foreach ($hasil_cari_presensi as $dtp) {
        if ($dtp['tanggal'] == $tanggal_pilih) {
            $hasil_cari_tanggal[] = $dtp;
        }
    }
    return $hasil_cari_tanggal;
}
function cari_data_presensi($nokartu_, $hasil_datapresensi_)
{
    $hasil_cari_presensi_ = array();
    foreach ($hasil_datapresensi_ as $dtp) {
        if ($dtp['nokartu'] == $nokartu_) {
            $hasil_cari_presensi_[] = $dtp;
        }
    }

    return $hasil_cari_presensi_;
}

?>
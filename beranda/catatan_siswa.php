<?php
if (@$_GET) {
    include "../config/konesi.php";
    $nis = mysqli_real_escape_string($konek, @$_GET['nis']);
    $info = mysqli_real_escape_string($konek, @$_GET['info']);
    $db = mysqli_real_escape_string($konek, @$_GET['db']);


    $qu_data = mysqli_query($konek, "SELECT * FROM datasiswa WHERE nis = '$nis'");
    $data_siswa = array();
    $_hit_data_siswa = 0;
    while ($hasil_info_ = mysqli_fetch_array($qu_data)) {
        $data_siswa[] = $hasil_info_;
        $_hit_data_siswa++;
    }
    
    $qu_info = mysqli_query($konek, "SELECT * FROM presensikelas WHERE nis = '$nis'");
    $data_info = array();
    $_hit_data_info = 0;
    while ($hasil_info_ = mysqli_fetch_array($qu_info)) {
        $data_info[] = $hasil_info_;
        $_hit_data_info++;
    }

    // echo "<pre>";
    // print_r($data_info);
    // echo "</pre>";
    // die;
}

$title = 'Catatan Siswa "' . @$data_siswa[0]['nama'] . ' (' . @$data_siswa[0]['kelas'] . ')"';
$navlink = 'Kelas';
include('app/get_user.php');
include('views/header.php');
include('views/_catatan_siswa.php');
mysqli_close($konek);
include('views/footer.php');
?>
<script>
    $(function() {
        $("#tabel_catatan").DataTable({
            dom: 'flBtip',
            "autoWidth": false,
            "responsive": true,
            "lengthChange": true,
            "lengthMenu": [
                [50, 100, -1],
                [50, 100, "Semua"]
            ],
            "pagingType": "full",
            "language": {
                "emptyTable": "Tida ada data.",
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
            "buttons": ["print", "excel", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>
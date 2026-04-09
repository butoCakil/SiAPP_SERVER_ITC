<?php
include('app/get_user.php');
$tanggal = date('Y-m-d');
$tanggal_dmY = date('d-m-Y');
$tahun = date('Y');

include("../config/konesi.php");

$qu_data_dataguru = mysqli_query($konek, "SELECT * FROM dataguru ORDER BY kode ASC, nama ASC");
$jumlah_data_dataguru = 0;
$data_dataguru = array();
while ($hasil_data_dataguru = mysqli_fetch_array($qu_data_dataguru)) {
    $jumlah_data_dataguru++;
    $data_dataguru[] = $hasil_data_dataguru;
}

// echo "Db jurnal";
// echo "<br>";
// echo "<pre>";
// print_r($data_jurnal);
// echo "</pre>";

// ----------------------
$title = 'Data Guru';
$navlink = 'Data Guru';
$navlink_sub = 'dataguru';

include('views/header.php');
include('views/_dataguru.php');
mysqli_close($konek);
include('views/footer.php');
?>

<script>
    $(function() {
        $("#datagururekap").DataTable({
            dom: 'fltip',
            "autoWidth": false,
            "responsive": true,
            "lengthChange": true,
            "lengthMenu": [
                [20, 50, 100, -1],
                [20, 50, 100, "Semua"]
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
            "buttons": ["print", "excel", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>
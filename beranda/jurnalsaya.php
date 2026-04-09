<?php
include "app/get_user.php";
$title = 'Jurnal Saya';
$navlink = 'Kelas';
include "views/header.php";

include "../config/konesi.php";
?>
<section class="content">
    <div class="container-fluid">
        <div class="card elevation-3 bg-primary bg-gradient-primary border-0" style="z-index: 1;">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i>&nbsp;
                    Jurnal Mengajar Saya
                </h3>
                <div class="card-tools">
                    <i class="fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Menampilkan Catatan Presensi Semua siswa dan dapat memilih perkelas serta perjurusan"></i>
                    &nbsp;
                </div>
            </div>
        </div>

        <div class="col-12" style="margin-top: -20px;">
            <div class="card elevation-3">
                <div class="card-body mb-5">
                    <div class="table-responsive">
                        <table id="example1" class="table">
                            <thead>
                                <th>No.</th>
                                <th>Hari, Tanggal</th>
                                <th>Ruangan</th>
                                <th>Kelas/Kelompok</th>
                                <th>Jurnal</th>
                                <th>Jam Ke</th>
                                <th>Jml Siswa</th>
                                <th>Hadir</th>
                                <th>I</th>
                                <th>S</th>
                                <th>A</th>
                            </thead>
                            <tbody>
                                <?php
                                $nom = 0;
                                $qu_jurnal = mysqli_query($konek, "SELECT * FROM jurnalguru WHERE nick = '$nick_login'");
                                while ($data_jurnal = mysqli_fetch_array($qu_jurnal)) {
                                    $nom++;
                                    $ruangan_jurnal = @$data_jurnal['ruangan'];
                                ?>
                                    <tr>
                                        <td><?= $nom; ?></td>
                                        <td><?= $data_jurnal['tanggal']; ?></td>
                                        <td><?= $ruangan_jurnal; ?></td>
                                        <td><?= $data_jurnal['kelas'] ? $data_jurnal['kelas'] : ($data_jurnal['kelompok'] . $data_jurnal['jur'] . $data_jurnal['tingkat']); ?></td>
                                        <td><?= $data_jurnal['jurnal']; ?></td>
                                        <td><?= $data_jurnal['jamke'] . ' - ' . $data_jurnal['sampai_jamke']; ?></td>

                                        <?php
                                        $kelas_jurnal = $data_jurnal['kelas'];
                                        $kelompok_jurnal = $data_jurnal['kelompok'];
                                        $tingkat_jurnal = $data_jurnal['tingkat'];
                                        $jur_jurnal = $data_jurnal['jur'];

                                        $qu_jumlah_siswa = mysqli_query($konek, "SELECT * FROM datasiswa WHERE kelas = '$kelas_jurnal'");
                                        $jml_siswa = mysqli_num_rows($qu_jumlah_siswa);

                                        if ($jml_siswa == 0) {
                                            $qu_jumlah_siswa = mysqli_query($konek, "SELECT * FROM datasiswa WHERE kelompok = '$kelompok_jurnal' AND tingkat = '$tingkat_jurnal' AND jur = '$jur_jurnal'");
                                            $jml_siswa = mysqli_num_rows($qu_jumlah_siswa);
                                        }
                                        ?>
                                        <td><?= $jml_siswa; ?></td>

                                        <?php
                                        $jamke = @$data_jurnal['jamke'];
                                        $tanggal_jurnal = $data_jurnal['tanggal'];

                                        $qu_hadir = mysqli_query($konek, "SELECT * FROM presensikelas WHERE (status = 'H' OR status = 'T') AND tanggal = '$tanggal_jurnal' AND ruangan = '$ruangan_jurnal' AND mulai_jamke = '$jamke'");
                                        $jml_hadir = mysqli_num_rows($qu_hadir);
                                        ?>

                                        <td><?= $jml_hadir; ?></td>

                                        <?php
                                        $qu_ijin = mysqli_query($konek, "SELECT * FROM presensikelas WHERE status = 'I' AND tanggal = '$tanggal_jurnal' AND ruangan = '$ruangan_jurnal' AND mulai_jamke = '$jamke'");
                                        $jml_ijin = mysqli_num_rows($qu_ijin);
                                        ?>

                                        <td><?= $jml_ijin; ?></td>

                                        <?php
                                        $qu_sakit = mysqli_query($konek, "SELECT * FROM presensikelas WHERE status = 'S' AND tanggal = '$tanggal_jurnal' AND ruangan = '$ruangan_jurnal' AND mulai_jamke = '$jamke'");
                                        $jml_sakit = mysqli_num_rows($qu_sakit);
                                        ?>

                                        <td><?= $jml_sakit; ?></td>

                                        <?php
                                        $qu_alpa = mysqli_query($konek, "SELECT * FROM presensikelas WHERE status = 'A' AND tanggal = '$tanggal_jurnal' AND ruangan = '$ruangan_jurnal' AND mulai_jamke = '$jamke'");
                                        $jml_alpa = mysqli_num_rows($qu_alpa);
                                        ?>

                                        <td><?= $jml_alpa; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <button class="btn btn-dark btn-sm" onclick="window.close();"><i class="fas fa-arrow-alt-circle-left"></i> Kembali</button>
                </div>
            </div>
        </div>
</section>

<?php mysqli_close($konek); ?>

<script>
    $(function() {
        $("#example1").DataTable({
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

<?php
include "views/footer.php";


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
?>
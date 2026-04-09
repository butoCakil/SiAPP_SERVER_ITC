<?php

// echo 'cek --------------------------------';
// echo '<br>';
// echo '<br>';

include('../../config/konesi.php');
// set date time lokasi
date_default_timezone_set('Asia/Jakarta');
$tanggal = date('Y-m-d');
// $tanggal = date('Y-m-d', strtotime('2022-02-17'));

$sql = "SELECT * FROM dataguru WHERE kode = 'GR'";
$query1 = mysqli_query($konek, $sql);
$hitung1 = mysqli_num_rows($query1);

$data_guru = array();
while ($data = mysqli_fetch_assoc($query1)) {
    $data_guru[] = $data;
}

// $nama_guru = $data_guru[0]['nama'];
// $nokartu_guru = $data_guru[0]['nokartu'];
// $foto_guru = $data_guru[0]['foto'];
// $info_guru = $data_guru[0]['jabatan'];


$sql = "SELECT * FROM datapresensi WHERE kode = 'GR' AND tanggal = '$tanggal'";
$query2 = mysqli_query($konek, $sql);

$data_presensi_guru = array();
while ($data2 = mysqli_fetch_assoc($query2)) {
    $data_presensi_guru[] = $data2;
}

mysqli_close($konek);

// $hasil_cari_presensi = cari_data_presensi($nokartu_guru, $data_presensi_guru);
// $tanggal_presensi = $hasil_cari_presensi[0]['tanggal'];
// $ketmasuk_presensi = $hasil_cari_presensi[0]['ketmasuk'];
// $keterangan_presensi = $hasil_cari_presensi[0]['keterangan'];

// // print_r('<br>');
// // print_r('nama_guru: ');
// // print_r($nama_presensi);
// // print_r('<br>');
// // print_r('foto_guru: ');
// // print_r($foto_presensi);
// // print_r('<br>');
// // print_r('info_guru: ');
// // print_r($info_presensi);
// // print_r('<br>');
// // print_r('tanggal_presensi: ');
// // print_r($tanggal_presensi);
// // print_r('<br>');
// print_r('ketmasuk_presensi: ');
// print_r($ketmasuk_presensi);
// print_r('<br>');
// print_r('keterangan_presensi: ');
// print_r($keterangan_presensi);


// print_r('<br>');
// print_r('<br>');
// print_r('Data hasil_cari_presensi');
// printf('<pre>%s</pre>', print_r($hasil_cari_presensi, true));


// die;

?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

<div class="table">
    <table class="table table-responsive-xxl table-bordered table-striped">
        <tr class="bg-secondary bg-gradient-secondary">
            <th>No</th>
            <th style="text-align: center;">
                <i class="fa fa-user-circle"></i>
            </th>
            <th>Nama</th>
            <th>info</th>
            <th>Ket.</th>
            <!-- <th>CP</th> -->
        </tr>
        <?php
        $no = 0;
        while ($no < $hitung1) {
            $nokartu_guru = $data_guru[$no]['nokartu'];
            $nama_guru = $data_guru[$no]['nama'];
            $foto_guru = $data_guru[$no]['foto'];
            $info_guru = $data_guru[$no]['jabatan'];

            // mencari nokartu di data_presensi_guru
            $hasil_cari_presensi = cari_data_presensi($nokartu_guru, $data_presensi_guru);
            // $tanggal_presensi = $hasil_cari_presensi[0]['tanggal'];
            $ketmasuk_presensi = @$hasil_cari_presensi[0]['ketmasuk'];
            $keterangan_presensi = @$hasil_cari_presensi[0]['keterangan'];

            if ($keterangan_presensi == '') {
                if ($ketmasuk_presensi == '') {
                    // $keterangan = 'Tidak Hadir';
                    $keterangan = '<span class="badge badge-danger"><i class="fas fa-times"></i></span>';
                } else {
                    // $keterangan = 'Hadir';
                    $keterangan = '<span class="badge badge-success"><i class="fas fa-check"></i></span>';
                }
            } else {
                // $keterangan = $keterangan_presensi;
                $keterangan = '<span class="badge badge-primary"><i class="fas fa-info"></i></span>';
            }
        ?>
            <tr>
                <td><?= $no + 1; ?></td>
                <td><img class="elevation-2" src="../img/user/<?= $foto_guru; ?>" style="height: 30px; width: 30px; border-radius: 50%; object-fit: cover; object-position: top;"></td>
                <td><?= $nama_guru; ?></td>
                <td style="font-size: 12px;"><?= $info_guru; ?></td>
                <td><?= $keterangan; ?></td>
                <!-- <td>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-primary bg-gradient-primary border-0 dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-mobile-alt"></i>
                            <i class="fas fa-down"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item d-flex justify-content-between" href="tel:+6282241863393"><i class="fas fa-phone"></i>&nbsp;Telepon</a>
                            <a class="dropdown-item d-flex justify-content-between" href="https://api.whatsapp.com/send?phone=6282241863393&text=Selamat%20Pagi%0ASaya"><span class="iconify" data-icon="ion:logo-whatsapp" style="color: aliceblue; background-color: limegreen; border-radius: 50%; padding: 2px; font-size: 25px;"></span>&nbsp;Whatsapp</a>
                        </div>
                    </div>
                </td> -->
            </tr>
        <?php $no++;
        } ?>
    </table>
</div>

<?php
function cari_data_presensi($nokartu_siswa, $hasil_datapresensi)
{
    $hasil_cari_presensi = array();
    foreach ($hasil_datapresensi as $dtp) {
        if ($dtp['nokartu'] == $nokartu_siswa) {
            $hasil_cari_presensi[] = $dtp;
        }
    }

    return $hasil_cari_presensi;
}

// // cari berdasarkan tanggal di hasil cari presensi
function cari_guru($tanggal_pilih, $hasil_cari_presensi)
{
    $hasil_cari_tanggal = array();
    foreach ($hasil_cari_presensi as $dtp) {
        if ($dtp['nokartu'] == $tanggal_pilih) {
            $hasil_cari_tanggal[] = $dtp;
        }
    }
    return $hasil_cari_tanggal;
}
?>
<?php
function gasalgenap($sekarang)
{
    // Mendapatkan tanggal sekarang
    // $sekarang = date('Y-m-d');

    // Membagi tanggal menjadi tahun, bulan, dan hari
    list($tahun, $bulan, $hari) = explode('-', $sekarang);

    // Menentukan semester berdasarkan bulan
    if ($bulan >= 7 && $bulan <= 12) {
        $semester = 'Gasal';
    } else {
        $semester = 'Genap';
    }

    return $semester;
}

include('app/get_user.php');
$tanggal = date('Y-m-d');
$tanggal_dmY = date('d-m-Y');
$tahun = date('Y');

if (@$_GET['data'] == 'jurnal') {

    include("../config/konesi.php");

    $nick_guru = @$_GET['nick'];
    $nick_guru = mysqli_real_escape_string($konek, $nick_guru);

    $cek_guru = mysqli_query($konek, "SELECT * FROM dataguru WHERE nick = '$nick_guru'");
    $hasil_data_guru = mysqli_fetch_array($cek_guru);

    $qu_data_jurnal = mysqli_query($konek, "SELECT * FROM jurnalguru WHERE nick = '$nick_guru'");
    $jumlah_data_jurnal = 0;
    $data_jurnal = array();
    while ($hasil_data_jurnal = mysqli_fetch_array($qu_data_jurnal)) {
        $jumlah_data_jurnal++;
        $data_jurnal[] = $hasil_data_jurnal;
    }

    // echo "Db jurnal";
    // echo "<br>";
    // echo "<pre>";
    // print_r($data_jurnal);
    // echo "</pre>";
    // die;
    // ----------------------
    $title = 'Jurnal Guru';
    $navlink = 'Data Guru';
    $navlink_sub = 'dataguru';

    include('views/header.php');
    mysqli_close($konek);
?>
    <!-- Begin Content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card elevation-3 bg-primary bg-gradient-primary border-0" style="z-index: 1;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>&nbsp;
                        Jurnal Guru - <?= @$hasil_data_guru['nama']; ?>
                    </h3>
                    <!-- <div class="card-tools"> -->
                    <!-- <i class="fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Menampilkan Catatan Presensi Semua siswa dan dapat memilih perkelas serta perjurusan"></i> -->
                    <!-- &nbsp; -->
                    <!-- </div> -->
                </div>
            </div>

            <style>
                #tabeljurnalguru thead th {
                    text-align: center;
                }
            </style>

            <div class="col-12" style="margin-top: -20px;">
                <div class="card elevation-3">
                    <div class="card-body mb-5">
                        <button class="btn btn-sm btn-dark shadow bg-gradient-dark border-0 m-1" onclick="history.go(-1);"><i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Kembali</button>

                        <?php if ($jumlah_data_jurnal > 0) { ?>
                            <div>
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td>Nama</td>
                                            <td>:</td>
                                            <td><?= @$hasil_data_guru['nama']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>NIP</td>
                                            <td>:</td>
                                            <td><?= @$hasil_data_guru['nip']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Guru</td>
                                            <td>:</td>
                                            <td><?= @$hasil_data_guru['info']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Semester</td>
                                            <td>:</td>
                                            <td>
                                                <?php
                                                $semester = gasalgenap($tanggal);

                                                echo $semester;
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Tahun Ajaran</td>
                                            <td>:</td>
                                            <td>
                                                <?php
                                                if ($semester == 'Gasal') {
                                                    $tahunAjaran = $tahun . "/" . ($tahun + 1);
                                                } else {
                                                    $tahunAjaran = ($tahun - 1) . "/" . $tahun;
                                                }

                                                echo $tahunAjaran;
                                                ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="table-responsive">
                                <table id="tabeljurnalguru" class="table table-hover table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Tanggal</th>
                                            <th>Kelas</th>
                                            <th>Ruangan</th>
                                            <th>Jamke</th>
                                            <th style="width: 40%;">Jurnal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        for ($jj = 0; $jj < $jumlah_data_jurnal; $jj++) {
                                            if (gasalgenap($data_jurnal[$jj]['tanggal']) == gasalgenap($tanggal)) {
                                        ?>
                                                <tr>
                                                    <td><?= $jj + 1; ?></td>
                                                    <td><?= $data_jurnal[$jj]['tanggal']; ?></td>
                                                    <td><?= $data_jurnal[$jj]['kelas']; ?></td>
                                                    <td><?= "[" . $data_jurnal[$jj]['ruangan'] . "] " . $data_jurnal[$jj]['info']; ?></td>
                                                    <td><?= $data_jurnal[$jj]['jamke'] . " - " . $data_jurnal[$jj]['sampai_jamke']; ?></td>
                                                    <td>
                                                        <textarea class="form-control" style="width: 100%;" rows="2" disabled><?= $data_jurnal[$jj]['jurnal']; ?></textarea>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            <a href="" class="btn btn-sm border-0 shadow btn-warning bg-gradient-warning mb-1">
                                                                <i class="fas fa-edit"></i>&nbsp;Edit
                                                            </a>
                                                            <a href="" class="btn btn-sm border-0 shadow btn-danger bg-gradient-danger" onclick="return confirm('Data jurnal ini akan di hapus! Yakin?');">
                                                                <i class="fas fa-trash-alt"></i>&nbsp;Hapus
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <h5 style="text-align: center;">Tidak ada data</h5>
                        <?php } ?>
                    </div>
                    <!-- EndContent -->
                </div>
            </div>
        </div>
    </section>
<?php
    include('views/footer.php');
} else {
    $_SESSION['pesan'] = 'Anda tidak memiliki akses ke halaman ini. Maaf! <br> Cek kembali URL / lokasi halaman <br>atau<br>kembali ke Home / Beranda. <i><br>(Kode: #JG101)</i>';
    include("404.php");
}
?>

<style>
    @media only screen and (max-width: 768px) {

        #example1,
        #example1 #masuk {
            font-size: 12px;
        }
    }
</style>
<style>
    .header {
        text-align: center;
    }

    #table .noborder td,
    #table .noborder tr {
        border: none;
    }

    #table,
    #table th,
    #table td {
        border: 1px solid black;
        border-collapse: collapse;
    }

    table {
        font: 11pt "Tahoma";
        font-size: 14px;
    }
</style>
<style type="text/css">
    /* Kode CSS Untuk PAGE ini dibuat oleh http://jsfiddle.net/2wk6Q/1/ */
    body {
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
        background-color: #FAFAFA;
        font: 11pt "Tahoma";
    }

    * {
        box-sizing: border-box;
        -moz-box-sizing: border-box;
    }

    .page {
        width: 210mm;
        min-height: 297mm;
        padding: 20mm 20mm 20mm 30mm;
        margin: 10mm auto;
        border: 1px #D3D3D3 solid;
        /* border-radius: 5px; */
        background: white;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }

    .subpage {
        padding: 1cm;
        border: 5px red solid;
        height: 257mm;
        outline: 2cm #FFEAEA solid;
    }

    @page {
        size: A4;
        margin: 0;
    }

    @media print {

        html,
        body {
            width: 210mm;
            height: 297mm;
        }

        .page {
            margin: 0;
            border: initial;
            border-radius: initial;
            width: initial;
            min-height: initial;
            box-shadow: initial;
            background: initial;
            page-break-after: always;
        }
    }
</style>
<?php
// include "views/footer.php";
?>

<script>
    function tableHtmlToExcel(tableID, filename = '') {
        var downloadLink;
        var dataType = 'application/vnd.ms-excel';
        var tableSelect = document.getElementById(tableID);
        var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

        filename = filename ? filename + '.xls' : 'excel_data.xls';

        downloadLink = document.createElement("a");

        document.body.appendChild(downloadLink);

        if (navigator.msSaveOrOpenBlob) {
            var blob = new Blob(['\ufeff', tableHTML], {
                type: dataType
            });
            navigator.msSaveOrOpenBlob(blob, filename);
        } else {
            downloadLink.href = 'data:' + dataType + ', ' + tableHTML;

            downloadLink.download = filename;

            downloadLink.click();
        }
    }
</script>
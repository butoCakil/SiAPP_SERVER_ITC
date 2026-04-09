<?php
include('app/get_user.php');
include("../config/konesi.php");
$tanggal = date('Y-m-d');
$tanggal_dmY = date('d-m-Y');
$tahun = date('Y');

if (@$_GET['tanggal']) {
    $tanggal_get = $_GET['tanggal'];

    if ($tanggal_get > $tanggal) {
        $tanggal_get = date('Y-m-d', strtotime($tanggal));
    }

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


if (@$_GET['kelas']) {
    $kelas_get = $_GET['kelas'];
    $kelas_pilih = $kelas_get;
    $link_kelas_pilih = '&kelas=' . $kelas_pilih;
} else {
    $kelas_pilih = "";
    $link_kelas_pilih = "";
}

if (@$_GET['jur']) {
    $jur_get = $_GET['jur'];
    $jur_pilih = $jur_get;
    $link_jur_pilih = '&jur=' . $jur_pilih;
} else {
    $jur_pilih = "";
    $link_jur_pilih = "";
}

$link_pilih = $link_kelas_pilih . $link_jur_pilih;

if ($jur_pilih && $kelas_pilih) {
    $sortir = " kode = '" . $kelas_pilih . $jur_pilih . "1'";
    $sortir = $sortir . " OR kode = '" . $kelas_pilih . $jur_pilih . "2'";
    $sortir = $sortir . " OR kode = '" . $kelas_pilih . $jur_pilih . "3'";
    $query_sortir = " WHERE (" . $sortir . ")";
} elseif ($jur_pilih && !$kelas_pilih) {
    $sortir = " kode = 'X" . $jur_pilih . "1'";
    $sortir = $sortir . " OR kode = 'X" . $jur_pilih . "2'";
    $sortir = $sortir . " OR kode = 'X" . $jur_pilih . "3'";
    $sortir = $sortir . " OR kode = 'XI" . $jur_pilih . "1'";
    $sortir = $sortir . " OR kode = 'XI" . $jur_pilih . "2'";
    $sortir = $sortir . " OR kode = 'XI" . $jur_pilih . "3'";
    $sortir = $sortir . " OR kode = 'XII" . $jur_pilih . "1'";
    $sortir = $sortir . " OR kode = 'XII" . $jur_pilih . "2'";
    $sortir = $sortir . " OR kode = 'XII" . $jur_pilih . "3'";
    $query_sortir = " WHERE (" . $sortir . ")";
} else if (@$_GET['kelasjur']) {
    $kelasjur = $_GET['kelasjur'];
    $link_pilih = '&kelasjur=' . $kelasjur;

    $query_sortir = " WHERE kode = '" . $kelasjur . "'";
} else {
    $query_sortir = "";
}

$sql2 = "SELECT * FROM statusnya";
$query2 = mysqli_query($konek, $sql2);
$status = mysqli_fetch_assoc($query2);
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
    $tanggal_pilih_plus = 'nilaikelas.php?tanggal=' . $tanggal_pilih_plus . $link_pilih;
    $disabled = '';
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
                header('location: nilaikelas.php?tanggal=' . $tanggal_kurangi_3);
            }
        } else {
            $pesan = 'Hari ini Libur ya...';
            header('location: nilaikelas.php?tanggal=' . $tanggal_kurangi_2);
        }
    } else {
        $pesan = 'Hari ini Libur ya...';
        header('location: nilaikelas.php?tanggal=' . $tanggal_kurangi_1);
    }
}

$query_sortir = mysqli_real_escape_string($konek, $query_sortir);
$sql1 = "SELECT * FROM datasiswa" . $query_sortir . " ORDER BY kelas ASC";
$query1 = mysqli_query($konek, $sql1);
$hit_datasiswa = mysqli_num_rows($query1);

$sql2 = "SELECT * FROM datapresensi";
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
$nama_siswa = $hasil_datasiswa[5]['nama'];
$nis_siswa = $hasil_datasiswa[5]['nis'];
$hasil_cari_presensi = cari_data_presensi($nis_siswa, $hasil_datapresensi);

// $sql_nilai_semua = mysqli_query($konek, "SELECT * FROM nilaisiswa");
// $data_nilai_semua = array();

// while ($hasil_nilai_semua = mysqli_fetch_array($sql_nilai_semua)) {
//     $data_nilai_semua[] = $hasil_nilai_semua;
// }

$sql_nilai_semua = mysqli_query($konek, "SELECT * FROM presensikelas");
$data_nilai_semua = array();

while ($hasil_nilai_semua = mysqli_fetch_array($sql_nilai_semua)) {
    $data_nilai_semua[] = $hasil_nilai_semua;
}

// echo "<br>";
// echo "<pre>";
// print_r($data_nilai_semua);
// echo "</pre>";
// die;

$title = 'Nilai Per Kelas';
$navlink = 'Wali';
$navlink_sub = 'nilaikelas';

include 'views/header.php';
include 'views/navbar.php';
mysqli_close($konek);
?>

<!-- konten start -->
<style type="text/css">
    #waicon_01 {
        color: #fff;
        /* background-color: lime; */
        padding: 5px 7px;
        border-radius: 50% 50% 50% 10%;
        background: linear-gradient(to bottom, #55e36f, #1ea237);
        color: white;
    }

    /* ============ desktop view ============ */
    @media all and (min-width: 992px) {

        .dropdown-menu li {
            position: relative;
        }

        .dropdown-menu .submenu {
            display: none;
            position: absolute;
            left: 100%;
            top: -7px;
        }

        .dropdown-menu>li:hover {
            background-color: #f1f1f1
        }

        .dropdown-menu>li:hover>.submenu {
            display: block;
        }
    }

    /* ============ desktop view .end// ============ */

    /* ============ small devices ============ */
    @media (max-width: 991px) {

        .dropdown-menu .dropdown-menu {
            margin-left: 0rem;
            margin-right: 0rem;
            margin-bottom: .5rem;
        }

    }

    /* ============ small devices .end// ============ */
</style>


<script type="text/javascript">
    //	window.addEventListener("resize", function() {
    //		"use strict"; window.location.reload(); 
    //	});


    document.addEventListener("DOMContentLoaded", function() {


        /////// Prevent closing from click inside dropdown
        document.querySelectorAll('.dropdown-menu').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        })



        // make it as accordion for smaller screens
        if (window.innerWidth < 992) {

            // close all inner dropdowns when parent is closed
            document.querySelectorAll('.navbar .dropdown').forEach(function(everydropdown) {
                everydropdown.addEventListener('hidden.bs.dropdown', function() {
                    // after dropdown is hidden, then find all submenus
                    this.querySelectorAll('.submenu').forEach(function(everysubmenu) {
                        // hide every submenu as well
                        everysubmenu.style.display = 'none';
                    });
                })
            });

            document.querySelectorAll('.dropdown-menu a').forEach(function(element) {
                element.addEventListener('click', function(e) {

                    let nextEl = this.nextElementSibling;
                    if (nextEl && nextEl.classList.contains('submenu')) {
                        // prevent opening link if link needs to open dropdown
                        e.preventDefault();
                        console.log(nextEl);
                        if (nextEl.style.display == 'block') {
                            nextEl.style.display = 'none';
                        } else {
                            nextEl.style.display = 'block';
                        }

                    }
                });
            })
        }
        // end if innerWidth

    });
    // DOMContentLoaded  end
</script>
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script> -->
<section class="content">
    <div class="container-fluid">
        <div class="card bg-primary bg-gradient-primary elevation-3" style="border: none; z-index: 1;">
            <div id="header_rekap" class="card-body">
                <div style="display: flex; justify-content: baseline; justify-content: space-between;">
                    <div>
                        <a class="nav-link bg-light elevation-3" style="border-radius: 5px;" href="nilaikelas.php?tanggal=<?= $tanggal_pilih_min; ?><?= $link_pilih; ?>">
                            <div style="display: flex; gap: 10px;">
                                <i class="fas fa-angle-double-left"></i>
                                <span>Sebelumnya</span>
                            </div>
                        </a>
                    </div>
                    <div style="display: flex; flex-direction: column; text-align: center;">
                        <div>
                            <h4 class="mt-2"><b><?= $hari_indo; ?></b>, </h4>
                        </div>
                        <div>
                            <h4><?= $tanggal_indo_pilih; ?></h4>
                        </div>
                    </div>
                    <div>
                        <a class="nav-link elevation-3 bg-light<?= $disabled; ?>" style="border-radius: 5px;" href="<?= $tanggal_pilih_plus; ?>">
                            <div style="display: flex; gap: 10px;">
                                <span>Berikutnya</span>
                                <i class="fas fa-angle-double-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div id="pilihkelasrekapsemua" class="col-12" style="display: block; margin-left: auto; margin-right: auto; margin-top: -20px;">
            <div class="card elevation-3">
                <div class="card-body">
                    <div>
                        <a href="?tanggal=<?= $tanggal_pilih; ?>" class="btn btn-dark bg-gradient-dark elevation-3 no-border">
                            <i class="fas fa-database"></i>
                            <span>
                                &nbsp;Semua
                            </span>
                        </a>
                    </div>
                    <div class="btn-group">
                        <a href="?jur=AT&tanggal=<?= $tanggal_pilih; ?>&kelas=" type="button" class="btn btn-success bg-gradient-success elevation-3">AT</a>
                        <button class="elevation-3 btn btn-success bg-gradient-success dropdown-toggle dropdown-toggle-split" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"></button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item" href="?jur=AT&kelas=X&tanggal=<?= $tanggal_pilih; ?>">Kelas X AT &nbsp;&nbsp;&raquo;</a>
                                <ul class="submenu dropdown-menu">
                                    <li><a class="dropdown-item" href="?kelasjur=XAT1&tanggal=<?= $tanggal_pilih; ?>"> X AT 1 </a></li>
                                    <li><a class="dropdown-item" href="?kelasjur=XAT2&tanggal=<?= $tanggal_pilih; ?>"> X AT 2 </a></li>
                                    <li><a class="dropdown-item" href="?kelasjur=XAT3&tanggal=<?= $tanggal_pilih; ?>"> X AT 3 </a></li>
                                </ul>
                            </li>
                            <li><a class="dropdown-item" href="?jur=AT&kelas=XI&tanggal=<?= $tanggal_pilih; ?>">Kelas XI AT &nbsp;&raquo;</a>
                                <ul class="submenu dropdown-menu">
                                    <li><a class="dropdown-item" href="?kelasjur=XIAT1&tanggal=<?= $tanggal_pilih; ?>"> XI AT 1 </a></li>
                                    <li><a class="dropdown-item" href="?kelasjur=XIAT2&tanggal=<?= $tanggal_pilih; ?>"> XI AT 2 </a></li>
                                    <li><a class="dropdown-item" href="?kelasjur=XIAT3&tanggal=<?= $tanggal_pilih; ?>"> XI AT 3 </a></li>
                                </ul>
                            </li>
                            <li><a class="dropdown-item" href="?jur=AT&kelas=XII&tanggal=<?= $tanggal_pilih; ?>">Kelas XII AT &raquo;</a>
                                <ul class="submenu dropdown-menu">
                                    <li><a class="dropdown-item" href="?kelasjur=XIIAT1&tanggal=<?= $tanggal_pilih; ?>"> XII AT 1 </a></li>
                                    <li><a class="dropdown-item" href="?kelasjur=XIIAT2&tanggal=<?= $tanggal_pilih; ?>"> XII AT 2 </a></li>
                                    <li><a class="dropdown-item" href="?kelasjur=XIIAT3&tanggal=<?= $tanggal_pilih; ?>"> XII AT 3 </a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="btn-group">
                        <a href="?jur=DKV&tanggal=<?= $tanggal_pilih; ?>&kelas=" type="button" class="btn btn-primary bg-gradient-primary elevation-3">DKV</a>
                        <button class="elevation-3 btn btn-primary bg-gradient-primary dropdown-toggle dropdown-toggle-split" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"></button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item" href="?jur=DKV&kelas=X&tanggal=<?= $tanggal_pilih; ?>">Kelas X DKV&nbsp;&nbsp;&nbsp;&raquo;</a>
                                <ul class="submenu dropdown-menu">
                                    <li><a class="dropdown-item" href="?kelasjur=XDKV1&tanggal=<?= $tanggal_pilih; ?>"> X DKV 1 </a></li>
                                    <li><a class="dropdown-item" href="?kelasjur=XDKV2&tanggal=<?= $tanggal_pilih; ?>"> X DKV 2 </a></li>
                                    <li><a class="dropdown-item" href="?kelasjur=XDKV3&tanggal=<?= $tanggal_pilih; ?>"> X DKV 3 </a></li>
                                </ul>
                            </li>
                            <li><a class="dropdown-item" href="?jur=DKV&kelas=XI&tanggal=<?= $tanggal_pilih; ?>">Kelas XI DKV&nbsp;&nbsp;&raquo;</a>
                                <ul class="submenu dropdown-menu">
                                    <li><a class="dropdown-item" href="?kelasjur=XIDKV1&tanggal=<?= $tanggal_pilih; ?>"> XI DKV 1 </a></li>
                                    <li><a class="dropdown-item" href="?kelasjur=XIDKV2&tanggal=<?= $tanggal_pilih; ?>"> XI DKV 2 </a></li>
                                    <li><a class="dropdown-item" href="?kelasjur=XIDKV3&tanggal=<?= $tanggal_pilih; ?>"> XI DKV 3 </a></li>
                                </ul>
                            </li>
                            <li><a class="dropdown-item" href="?jur=DKV&kelas=XII&tanggal=<?= $tanggal_pilih; ?>">Kelas XII DKV&nbsp;&raquo;</a>
                                <ul class="submenu dropdown-menu">
                                    <li><a class="dropdown-item" href="?kelasjur=XIIDKV1&tanggal=<?= $tanggal_pilih; ?>"> XII DKV 1 </a></li>
                                    <li><a class="dropdown-item" href="?kelasjur=XIIDKV2&tanggal=<?= $tanggal_pilih; ?>"> XII DKV 2 </a></li>
                                    <li><a class="dropdown-item" href="?kelasjur=XIIDKV3&tanggal=<?= $tanggal_pilih; ?>"> XII DKV 3 </a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="btn-group">
                        <a href="?jur=TE&tanggal=<?= $tanggal_pilih; ?>&kelas=" type="button" class="btn btn-warning bg-gradient-warning elevation-3">TE</a>
                        <button class="elevation-3 btn btn-warning bg-gradient-warning dropdown-toggle dropdown-toggle-split" type="button" data-bs-toggle="dropdown"></button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item" href="?jur=TE&kelas=X&tanggal=<?= $tanggal_pilih; ?>">Kelas X TE&nbsp;&nbsp;&nbsp;&raquo;</a>
                                <ul class="submenu dropdown-menu">
                                    <li><a class="dropdown-item" href="?kelasjur=XTE1&tanggal=<?= $tanggal_pilih; ?>"> X TE 1 </a></li>
                                    <li><a class="dropdown-item" href="?kelasjur=XTE2&tanggal=<?= $tanggal_pilih; ?>"> X TE 2 </a></li>
                                    <li><a class="dropdown-item" href="?kelasjur=XTE3&tanggal=<?= $tanggal_pilih; ?>"> X TE 3 </a></li>
                                </ul>
                            </li>
                            <li><a class="dropdown-item" href="?jur=TE&kelas=XI&tanggal=<?= $tanggal_pilih; ?>">Kelas XI TE&nbsp;&nbsp;&raquo;</a>
                                <ul class="submenu dropdown-menu">
                                    <li><a class="dropdown-item" href="?kelasjur=XITE1&tanggal=<?= $tanggal_pilih; ?>"> XI TE 1 </a></li>
                                    <li><a class="dropdown-item" href="?kelasjur=XITE2&tanggal=<?= $tanggal_pilih; ?>"> XI TE 2 </a></li>
                                    <li><a class="dropdown-item" href="?kelasjur=XITE3&tanggal=<?= $tanggal_pilih; ?>"> XI TE 3 </a></li>
                                </ul>
                            </li>
                            <li><a class="dropdown-item" href="?jur=TE&kelas=XII&tanggal=<?= $tanggal_pilih; ?>">Kelas XII TE&nbsp;&raquo; </a>
                                <ul class="submenu dropdown-menu">
                                    <li><a class="dropdown-item" href="?kelasjur=XIITE1&tanggal=<?= $tanggal_pilih; ?>"> XII TE 1 </a></li>
                                    <li><a class="dropdown-item" href="?kelasjur=XIITE2&tanggal=<?= $tanggal_pilih; ?>"> XII TE 2 </a></li>
                                    <li><a class="dropdown-item" href="?kelasjur=XIITE3&tanggal=<?= $tanggal_pilih; ?>"> XII TE 3 </a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="card elevation-3 bg-primary bg-gradient-primary border-0" style="z-index: 1;">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i>&nbsp;
                    Rekap Presensi Semua Siswa
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
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr style="text-align: center; position: sticky;">
                                <th>No.</th>
                                <th>Nama</th>
                                <th>Foto</th>
                                <th>Kelas</th>
                                <th>N/1</th>
                                <th>N/2</th>
                                <th>N/3</th>
                                <th>N/4</th>
                                <th>N/5</th>
                                <th>N/6</th>
                                <th>Keterangan</th>
                                <th>Kontak</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            while ($no < $hit_datasiswa) {

                                // $tanggal_pilih = '2022-02-10';
                                // $tanggal_pilih_dmY = '10-02-2022';

                                $nis_siswa = $hasil_datasiswa[$no]['nis'];
                                $hasil_cari_presensi = cari_data_presensi($nis_siswa, $hasil_datapresensi);
                                $hasil_cari_tanggal = cari_tanggal($tanggal_pilih, $hasil_cari_presensi);

                                $hct_waktumasuk = @$hasil_cari_tanggal[0]['waktumasuk'] ? $hasil_cari_tanggal[0]['waktumasuk'] : '-';
                                $hct_ketmasuk = @$hasil_cari_tanggal[0]['ketmasuk'] ? $hasil_cari_tanggal[0]['ketmasuk'] : '-';
                                $hct_a_time = @$hasil_cari_tanggal[0]['a_time'] ? $hasil_cari_tanggal[0]['a_time'] : '-';
                                $hct_waktupulang = @$hasil_cari_tanggal[0]['waktupulang'] ? $hasil_cari_tanggal[0]['waktupulang'] : '-';
                                $hct_ketpulang = @$hasil_cari_tanggal[0]['ketpulang'] ? $hasil_cari_tanggal[0]['ketpulang'] : '-';
                                $hct_b_time = @$hasil_cari_tanggal[0]['b_time'] ? $hasil_cari_tanggal[0]['b_time'] : '-';
                                $hct_keterangan = @$hasil_cari_tanggal[0]['keterangan'] ? $hasil_cari_tanggal[0]['keterangan'] : '-';

                                if ($hct_ketmasuk == 'MSK') {
                                    $hct_ketmasuk = '<span class="badge badge-success">On Time</span>';
                                } elseif ($hct_ketmasuk == 'TLT') {
                                    $hct_ketmasuk = '<span class="badge badge-warning">Terlambat</span>';
                                }

                                if ($hct_ketpulang == 'PLG') {
                                    $hct_ketpulang = '<span class="badge badge-success">Pulang</span>';
                                } elseif ($hct_ketpulang == "PA") {
                                    $hct_ketpulang = '<span class="badge badge-warning">Pulang Awal</span>';
                                }

                                if ($hct_keterangan != '-' && $hct_keterangan != '') {
                                    $hct_bg_keterangan = 'class = "bg-info"';
                                } else {
                                    $hct_bg_keterangan = '';
                                }
                            ?>
                                <tr <?= $hct_bg_keterangan; ?> style="text-align: center;">
                                    <td style="width: 5%;"><?= $no + 1; ?></td>

                                    <td style="text-align: left;"><?= $hasil_datasiswa[$no]['nama']; ?></td>
                                    <td><img src="../img/user/<?= $hasil_datasiswa[$no]['foto'] ? $hasil_datasiswa[$no]['foto'] : 'default.jpg'; ?>" style="height: 50px; width: 50px; image-rendering: auto; border-image: auto; border-radius: 50%;"></td>
                                    <td><?= $hasil_datasiswa[$no]['kelas']; ?></td>
                                    <td><?= $hct_waktumasuk; ?></td>
                                    <td><?= $hct_ketmasuk; ?></td>
                                    <td><?= $hct_a_time; ?></td>
                                    <td><?= $hct_waktupulang; ?></td>
                                    <td><?= $hct_ketpulang; ?></td>
                                    <td><?= $hct_b_time; ?></td>
                                    <td><?= $hct_keterangan; ?></td>
                                    <td>
                                        <!-- <img id="sosmed_WA" src="../img/app/message-circle_w.svg" style="background-color: lime; border-radius: 100%; padding: 5px;"> -->
                                        <!-- <span class="iconify" data-icon="ion:logo-whatsapp" style="background-color: lime; border-radius: 100%; padding: 5px;"></span> -->
                                        <a id="waicon_01" href="#" target="_blank">
                                            <i class="fab fa-whatsapp"></i>
                                            <!-- <img src="../img/app/message-circle_w.svg"> -->
                                        </a>
                                    </td>
                                </tr>
                                <?php $no++; ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- konten end -->

<?php
include 'views/footer.php';
?>

<script>
    $(function() {
        $("#example1").DataTable({
            dom: 'flBtip',
            "autoWidth": false,
            "responsive": true,
            "lengthChange": true,
            "lengthMenu": [
                [5, 10, 15, 20, 25, 50, 100, -1],
                [5, 10, 15, 20, 25, 50, 100, "Semua"]
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
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>

<?php
function cari_data_presensi($nis_siswa, $hasil_datapresensi)
{
    $hasil_cari_presensi = array();
    foreach ($hasil_datapresensi as $dtp) {
        if ($dtp['nokartu'] == $nis_siswa) {
            $hasil_cari_presensi[] = $dtp;
        }
    }

    return $hasil_cari_presensi;
}


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

?>
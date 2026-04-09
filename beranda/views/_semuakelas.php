<?php
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
$nokartu_siswa = $hasil_datasiswa[5]['nokartu'];
$hasil_cari_presensi = cari_data_presensi($nokartu_siswa, $hasil_datapresensi);

// print_r('sql1 : ');
// print_r($sql1);
// print_r('<br>');
// print_r('query1 : ');

// if ($konek) {
//     print_r('koneksi berhasil');
// } else {
//     print_r('koneksi gagal');
// }

// print_r('<br>');

// if ($query1) {
//     echo ('true');
// } else {
//     echo ('false <br>');
//     echo ('Gagal query: ' . mysqli_error($konek));
// }

// die;

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

// print_r('hasil dari cari : ');
// print_r('<br>');
// print_r('hitung hasil cari presensi: ' . $hitung_hasil_cari_tanggal);
// print_r('<br> tanggal presensi siswa : ');
// print_r($tanggal_presensi_siswa);
// print_r('<br> nama presensi siswa : ');
// print_r($nama_presensi_siswa);
// print_r('<br> waktumasuk presensi siswa : ');
// print_r($waktumasuk_presensi_siswa);
// print_r('<br> ketmasuk presensi siswa : ');
// print_r($ketmasuk_presensi_siswa);

// print_r('<br>');
// print_r('<br>');
// print_r('hasil_cari_tanggal: ');
// printf("<pre>%s</pre>", print_r($hasil_cari_tanggal, true));
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
?>

<?php include('views/header.php'); ?>

<link href="plugins/jquery-ui-1.11.4/smoothness/jquery-ui.css" rel="stylesheet" />
<!-- <script src="plugins/jquery-ui-1.11.4/external/jquery/jquery.js"></script> -->
<!-- <script src="plugins/jquery-ui-1.11.4/jquery-ui.js"></script> -->
<script src="plugins/jquery-ui-1.11.4/jquery-ui.min.js"></script>
<link rel="stylesheet" href="plugins/jquery-ui-1.11.4/jquery-ui.theme.css">
<script>
    $(document).ready(function() {
        $("#tanggal").datepicker({})
    })
</script>

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
                        <a class="nav-link bg-light elevation-3" style="border-radius: 5px;" href="semuakelas.php?tanggal=<?= $tanggal_pilih_min; ?><?= $link_pilih; ?>">
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
                        <div>
                            <input type="button" class="btn btn-light btn-sm" id="tanggal" onchange="ketanggal(this.value, '<?= $link_pilih; ?>');" value='📅&nbsp;ke Tanggal'>
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
                    <?php
                    $bbtn_kls_TE = "";
                    $bbtn_kls_AT = "";
                    $bbtn_kls_DKV = "";
                    $bbtn_kls_smw = "";
                    $elevation3_TE = " elevation-3";
                    $elevation3_AT = " elevation-3";
                    $elevation3_DKV = " elevation-3";
                    $elevation3_smw = " elevation-3";

                    $get_jur = @$_GET['jur'];
                    if ($get_jur == "TE") {
                        $bbtn_kls_TE = " disabled";
                        $elevation3_TE = "";
                    } elseif ($get_jur == "AT") {
                        $bbtn_kls_AT = " disabled";
                        $elevation3_AT = "";
                    } elseif ($get_jur == "DKV") {
                        $bbtn_kls_DKV = " disabled";
                        $elevation3_DKV = "";
                    } else {
                        $bbtn_kls_smw = " disabled";
                        $elevation3_smw = "";
                    }
                    ?>
                    <div>
                        <a href="?tanggal=<?= $tanggal_pilih; ?>" class="btn btn-dark bg-gradient-dark <?= $elevation3_smw; ?> no-border<?= $bbtn_kls_smw; ?>">
                            <i class="fas fa-database"></i>
                            <span>
                                &nbsp;Semua
                            </span>
                        </a>
                    </div>
                    <div class="btn-group">
                        <a href="?jur=AT&tanggal=<?= $tanggal_pilih; ?>&kelas=" type="button" class="btn btn-success bg-gradient-success<?= $elevation3_AT; ?><?= $bbtn_kls_AT; ?>">AT</a>
                        <button class="elevation-3 btn btn-success bg-gradient-success dropdown-toggle dropdown-toggle-split" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"></button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item" href="?jur=AT&kelas=X&tanggal=<?= $tanggal_pilih; ?>">Kelas X AT &nbsp;&nbsp;&raquo;</a>
                                <ul class="submenu dropdown-menu">
                                    <li><a class="dropdown-item" href="?kelasjur=XAT1&tanggal=<?= $tanggal_pilih; ?>"> X AT 1 </a></li>
                                    <li><a class="dropdown-item" href="?kelasjur=XAT2&tanggal=<?= $tanggal_pilih; ?>"> X AT 2 </a></li>
                                    <li><a class="dropdown-item" href="?kelasjur=XAT3&tanggal=<?= $tanggal_pilih; ?>"> X AT 3 </a></li>
                                    <!-- <li><a class="dropdown-item" href="?kelasjur=XAT4&tanggal=<?= $tanggal_pilih; ?>"> X AT 4 </a></li> -->
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
                        <a href="?jur=DKV&tanggal=<?= $tanggal_pilih; ?>&kelas=" type="button" class="btn btn-primary bg-gradient-primary<?= $elevation3_DKV; ?><?= $bbtn_kls_DKV; ?>">DKV</a>
                        <button class="elevation-3 btn btn-primary bg-gradient-primary dropdown-toggle dropdown-toggle-split" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"></button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item" href="?jur=DKV&kelas=X&tanggal=<?= $tanggal_pilih; ?>">Kelas X DKV&nbsp;&nbsp;&nbsp;&raquo;</a>
                                <ul class="submenu dropdown-menu">
                                    <li><a class="dropdown-item" href="?kelasjur=XDKV1&tanggal=<?= $tanggal_pilih; ?>"> X DKV 1 </a></li>
                                    <li><a class="dropdown-item" href="?kelasjur=XDKV2&tanggal=<?= $tanggal_pilih; ?>"> X DKV 2 </a></li>
                                    <li><a class="dropdown-item" href="?kelasjur=XDKV3&tanggal=<?= $tanggal_pilih; ?>"> X DKV 3 </a></li>
                                    <!-- <li><a class="dropdown-item" href="?kelasjur=XDKV4&tanggal=<?= $tanggal_pilih; ?>"> X DKV 4 </a></li> -->
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
                        <a href="?jur=TE&tanggal=<?= $tanggal_pilih; ?>&kelas=" type="button" class="btn btn-warning bg-gradient-warning<?= $elevation3_TE; ?><?= $bbtn_kls_TE; ?>">TE</a>
                        <button class="elevation-3 btn btn-warning bg-gradient-warning dropdown-toggle dropdown-toggle-split" type="button" data-bs-toggle="dropdown"></button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item" href="?jur=TE&kelas=X&tanggal=<?= $tanggal_pilih; ?>">Kelas X TE&nbsp;&nbsp;&nbsp;&raquo;</a>
                                <ul class="submenu dropdown-menu">
                                    <li><a class="dropdown-item" href="?kelasjur=XTE1&tanggal=<?= $tanggal_pilih; ?>"> X TE 1 </a></li>
                                    <li><a class="dropdown-item" href="?kelasjur=XTE2&tanggal=<?= $tanggal_pilih; ?>"> X TE 2 </a></li>
                                    <li><a class="dropdown-item" href="?kelasjur=XTE3&tanggal=<?= $tanggal_pilih; ?>"> X TE 3 </a></li>
                                    <li><a class="dropdown-item" href="?kelasjur=XTE4&tanggal=<?= $tanggal_pilih; ?>"> X TE 4 </a></li>
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
                    <!-- <div class="btn-group">
            <a class="btn btn-dark" href="#" data-bs-toggle="dropdown"> More items </a>
            <button class="btn btn-dark dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"></button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#"> Dropdown item 1 </a></li>
                <li><a class="dropdown-item" href="#"> Dropdown item 2 &raquo; </a>
                    <ul class="submenu dropdown-menu">
                        <li><a class="dropdown-item" href="#">Submenu item 1</a></li>
                        <li><a class="dropdown-item" href="#">Submenu item 2</a></li>
                        <li><a class="dropdown-item" href="#">Submenu item 3</a></li>
                    </ul>
                </li>
                <li><a class="dropdown-item" href="#"> Dropdown item 3 &raquo; </a>
                    <ul class="submenu dropdown-menu">
                        <li><a class="dropdown-item" href="#">Another submenu 1</a></li>
                        <li><a class="dropdown-item" href="#">Another submenu 2</a></li>
                        <li><a class="dropdown-item" href="#">Another submenu 3</a></li>
                        <li><a class="dropdown-item" href="#">Another submenu 4</a></li>
                    </ul>
                </li>
                <li><a class="dropdown-item" href="#"> Dropdown item 4 &raquo;</a>
                    <ul class="submenu dropdown-menu">
                        <li><a class="dropdown-item" href="#">Another submenu 1</a></li>
                        <li><a class="dropdown-item" href="#">Another submenu 2</a></li>
                        <li><a class="dropdown-item" href="#">Another submenu 3</a></li>
                        <li><a class="dropdown-item" href="#">Another submenu 4</a></li>
                    </ul>
                </li>
                <li><a class="dropdown-item" href="#"> Dropdown item 5 </a></li>
                <li><a class="dropdown-item" href="#"> Dropdown item 6 </a></li>
            </ul>
        </div> -->
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
                                <th>Tanggal</th>
                                <th>NIS</th>
                                <th>Nama</th>
                                <!-- <th>Foto</th> -->
                                <th>Kelas</th>
                                <th>Masuk</th>
                                <th>Pulang</th>
                                <!-- <th>Status</th> -->
                                <!-- <th>Info [Jam] [Catatan]</th> -->
                                <!-- <th>Ket</th> -->
                                <!-- <th>Kontak</th> -->
                                <!-- <th>Detail</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            while ($no < $hit_datasiswa) {

                                // $tanggal_pilih = '2022-02-10';
                                // $tanggal_pilih_dmY = '10-02-2022';

                                $nokartu_siswa = $hasil_datasiswa[$no]['nokartu'];
                                $hasil_cari_presensi = cari_data_presensi($nokartu_siswa, $hasil_datapresensi);
                                $hasil_cari_tanggal = cari_tanggal($tanggal_pilih, $hasil_cari_presensi);

                                $hct_waktumasuk = @$hasil_cari_tanggal[0]['waktumasuk'] ? $hasil_cari_tanggal[0]['waktumasuk'] : '-';
                                $hct_ketmasuk = @$hasil_cari_tanggal[0]['ketmasuk'] ? $hasil_cari_tanggal[0]['ketmasuk'] : '-';
                                $hct_a_time = @$hasil_cari_tanggal[0]['a_time'] ? $hasil_cari_tanggal[0]['a_time'] : '-';
                                $hct_waktupulang = @$hasil_cari_tanggal[0]['waktupulang'] ? $hasil_cari_tanggal[0]['waktupulang'] : '-';
                                $hct_ketpulang = @$hasil_cari_tanggal[0]['ketpulang'] ? $hasil_cari_tanggal[0]['ketpulang'] : '-';
                                $hct_b_time = @$hasil_cari_tanggal[0]['b_time'] ? $hasil_cari_tanggal[0]['b_time'] : '-';
                                $hct_keterangan = @$hasil_cari_tanggal[0]['keterangan'] ? $hasil_cari_tanggal[0]['keterangan'] : '-';

                                if ($hct_ketmasuk == 'MSK') {
                                    $hct_ketmasuk = '<span class="badge badge-success">Masuk</span>';
                                    $hct_a_time = '<span class="badge badge-success">' . $hct_a_time . '</span>';
                                } elseif ($hct_ketmasuk == 'TLT') {
                                    $hct_ketmasuk = '<span class="badge badge-warning">Terlambat</span>';
                                    $hct_a_time = '<span class="badge badge-warning">' . $hct_a_time . '</span>';
                                }

                                if ($hct_ketpulang == 'PLG') {
                                    $hct_ketpulang = '<span class="badge badge-success">Pulang</span>';
                                    $hct_b_time = '<span class="badge badge-success">' . $hct_b_time . '</span>';
                                } elseif ($hct_ketpulang == "PA") {
                                    $hct_ketpulang = '<span class="badge badge-warning">P. Awal</span>';
                                    $hct_b_time = '<span class="badge badge-warning">' . $hct_b_time . '</span>';
                                }

                                if ($hct_keterangan != '-' && $hct_keterangan != '') {
                                    $hct_bg_keterangan = 'class = "bg-info"';
                                } else {
                                    $infodevice_masuk = @$hasil_cari_tanggal[0]['infodevice'] ? $hasil_cari_tanggal[0]['infodevice'] : '-';
                                    $infodevice_pulang = @$hasil_cari_tanggal[0]['infodevice2'] ? $hasil_cari_tanggal[0]['infodevice2'] : '-';
                                    
                                    
                                    $hct_bg_keterangan = '';
                                    $hct_keterangan = "$infodevice_masuk / $infodevice_pulang";
                                }
                            ?>
                                <tr <?= $hct_bg_keterangan; ?> style="text-align: center;">
                                    <?php 
                                        if((!$hct_waktumasuk || $hct_waktumasuk == "-") && (!$hct_waktupulang || $hct_waktupulang == "-")){
                                            $bg_sel_ket = "bg-danger";
                                            $hct_waktumasuk = "";
                                            $hct_waktupulang = "";
                                        } else {
                                            $bg_sel_ket = "";
                                        }
                                    ?>
                                    <td style="width: 5%;"><?= $no + 1; ?></td>
                                    <td><?= $nama_hari_singkat_pilih; ?>,&nbsp;<?= $tanggal_pilih_dmY; ?></td>
                                    <td style="text-align: left;"><?= $hasil_datasiswa[$no]['nis']; ?></td>
                                    <td style="text-align: left; cursor: pointer;" onclick="window.open('rekappersiswa.php?n=<?= $hasil_datasiswa[$no]['nick']; ?>', '_blank')">
                                       <?= str_replace(' ', '&nbsp;', $hasil_datasiswa[$no]['nama']); ?>
                                    </td>
                                    <!-- <td><img src="../img/user/<?= $hasil_datasiswa[$no]['foto'] ? $hasil_datasiswa[$no]['foto'] : 'default.jpg'; ?>" style="height: 50px; width: 50px; image-rendering: auto; border-image: auto; border-radius: 50%;"></td> -->
                                    <td><?= str_replace(' ', '&nbsp;', $hasil_datasiswa[$no]['kelas']); ?></td>
                                    <td class="<?= $bg_sel_ket; ?>">
                                        <?php if ($hct_waktumasuk != "00:00:00") { ?>
                                            <?= $hct_waktumasuk; ?>&nbsp;<?= $hct_ketmasuk; ?>
                                            <!-- <?= $hct_a_time; ?> -->
                                        <?php } else {
                                            echo "<span class='badge badge-secondary'>TPM</span>";
                                        } ?>
                                    </td>
                                    <td class="<?= $bg_sel_ket; ?>">
                                        <?php if ($hct_waktupulang != "00:00:00") { ?>
                                            <?= $hct_waktupulang; ?>&nbsp;<?= $hct_ketpulang; ?>
                                            <!-- <?= $hct_b_time; ?> -->
                                        <?php } else {
                                            echo "<span class='badge badge-secondary'>BPP</span>";
                                        } ?>
                                    </td>

                                    <?php
                                    // $sql_presensikelas = mysqli_query($konek, "SELECT * FROM presensikelas WHERE nis = '" . $hasil_datasiswa[$no]['nis'] . "' AND " . "tanggal = '" . $tanggal_pilih . "'");

                                    // $data_hadir_kelas = array();
                                    // $info_hadir_kelas = array();
                                    // while ($data_presensikelas = mysqli_fetch_array($sql_presensikelas)) {
                                    //     $data_hadir_kelas[] = $data_presensikelas['status'];
                                    //     $info_hadir_kelas[] = '<span class="badge badge-secondary">[' . $data_presensikelas['mulai_jamke'] . '-' . $data_presensikelas['sampai_jamke'] . ']</span><br>' . $data_presensikelas['catatan'];
                                    // }
                                    ?>

                                    <!-- <td> -->
                                    <?php
                                    // if ($data_hadir_kelas) {
                                    //     $i = 0;
                                    //     foreach ($data_hadir_kelas as $value_data) {
                                    //         if ($value_data[$i] == 'H') {
                                    //             $bg_ = 'success';
                                    //         } elseif ($value_data[$i] == 'I') {
                                    //             $bg_ = 'primary';
                                    //         } elseif ($value_data[$i] == 'S') {
                                    //             $bg_ = 'info';
                                    //         } elseif ($value_data[$i] == 'T') {
                                    //             $bg_ = 'warning';
                                    //         } elseif ($value_data[$i] == 'A') {
                                    //             $bg_ = 'danger';
                                    //         } else {
                                    //             $bg_ = 'dark';
                                    //         }


                                    //         echo "<pre class='badge badge-" . $bg_ . "'>";
                                    //         echo ($value_data);
                                    //         echo "</pre>";
                                    //         $i++;
                                    //     }
                                    // }
                                    ?>
                                    <!-- </td> -->
                                    <!-- <td> -->
                                    <?php
                                    // if ($info_hadir_kelas) {
                                    //     $i = 0;
                                    //     foreach ($info_hadir_kelas as $value_info) {
                                    //         echo "<pre>";
                                    //         echo ($value_info);
                                    //         echo "</pre>";
                                    //         $i++;
                                    //     }
                                    // }
                                    ?>
                                    <!-- </td> -->

                                    <!-- <td class="<?= $bg_sel_ket; ?>" style="text-align: center; font-size: 8px;"><?= $hct_keterangan; ?></td> -->
                                    <!-- <td> -->
                                    <!-- <img id="sosmed_WA" src="../img/app/message-circle_w.svg" style="background-color: lime; border-radius: 100%; padding: 5px;"> -->
                                    <!-- <span class="iconify" data-icon="ion:logo-whatsapp" style="background-color: lime; border-radius: 100%; padding: 5px;"></span> -->
                                    <!-- <a id="waicon_01" href="#" target="_blank"> -->
                                    <!-- <i class="fab fa-whatsapp"></i> -->
                                    <!-- <img src="../img/app/message-circle_w.svg"> -->
                                    <!-- </a> -->
                                    <!-- </td> -->
                                    <!-- <td class="<?= $bg_sel_ket; ?>">
                                        <a target="_blank" href="rekappersiswa.php?n=<?= $hasil_datasiswa[$no]['nick']; ?>" class="btn btn-sm btn-light shadow">
                                            <i class="fas fa-info"></i>
                                        </a>
                                    </td> -->
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

<?php include('views/footer.php'); ?>

<script>
    $(function() {
    $("#example1").DataTable({
        dom: 'flBtip',
        "autoWidth": false,
        "responsive": true,
        "lengthChange": true,
        "lengthMenu": [
            [50, 100, 500, -1],
            [50, 100, 500, "Semua"]
        ],
        "pagingType": "full",
        "language": {
            "emptyTable": "Tidak ada data untuk tanggal yang dipilih.",
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
        "buttons": [
            "print",
            {
                extend: "pdfHtml5",
                customize: function(doc) {
                    for (let i = 1; i < doc.content[1].table.body.length; i++) {
                        for (let j = 0; j < doc.content[1].table.body[i].length; j++) {
                            if (doc.content[1].table.body[i][j].text === '-') {
                                doc.content[1].table.body[i][j].fillColor = 'red';
                            }
                        }
                    }
                }
            },
            "excel",
            "colvis"
        ],
        "rowCallback": function(row, data, index) {
            $('td', row).each(function() {
                if ($(this).text().trim() === '-') {
                    $(this).css('background-color', 'red');
                }
            });
        }
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
});


    function ketanggal(tanggal, link) {
        // alert('oke berhasil: ' + tanggal);
        window.location.href = "semuakelas.php?tanggal=" + tanggal + link;
    }
</script>

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
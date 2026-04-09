<?php
// 

// $sql2 = "SELECT * FROM presensikelas WHERE timestamp LIKE '%$tanggal_pilih%'";
// $query2 = mysqli_query($konek, $sql2);

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
                        <a class="nav-link bg-light elevation-3" style="border-radius: 5px;" href="kelas.php?&jur=<?= @$jur_pilih; ?>&tanggal=<?= @$tanggal_pilih_min; ?><?= @$link_pilih; ?>">
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
                            <input type="button" class="btn btn-light btn-sm" id="tanggal" onchange="ketanggal(this.value, '<?= $jur_pilih; ?>');" value='📅&nbsp;ke Tanggal'>
                        </div>
                    </div>
                    <div>
                        <a class="border-0 nav-link elevation-3 bg-light<?= $disabled; ?>" style="border-radius: 5px;" href="<?= $tanggal_pilih_plus; ?>">
                            <div style="display: flex; gap: 10px;">
                                <span>Berikutnya</span>
                                <i class="fas fa-angle-double-right"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div id="pilihkelasrekapsemua" class="col-12" style="display: block; margin-left: auto; margin-right: auto; margin-top: -20px;">
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
                        <a href="?p=y&jur=AT&tanggal=<?= $tanggal_pilih; ?>&kelas=AT" type="button" class="btn btn-success bg-gradient-success elevation-3">AT</a>
                        <button class="elevation-3 btn btn-success bg-gradient-success dropdown-toggle dropdown-toggle-split" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"></button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item" href="?p=y&jur=AT&tingkat=X&tanggal=<?= $tanggal_pilih; ?>">Kelas X AT &nbsp;&nbsp;&raquo;</a>
                                <ul class="submenu dropdown-menu">
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XAT1&tanggal=<?= $tanggal_pilih; ?>"> X AT 1 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XAT2&tanggal=<?= $tanggal_pilih; ?>"> X AT 2 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XAT3&tanggal=<?= $tanggal_pilih; ?>"> X AT 3 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=AT&tingkat=X&kelompokjur=1&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 1 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=AT&tingkat=X&kelompokjur=2&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 2 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=AT&tingkat=X&kelompokjur=3&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 3 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=AT&tingkat=X&kelompokjur=4&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 4 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=AT&tingkat=X&kelompokjur=5&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 5 </a></li>
                                </ul>
                            </li>
                            <li><a class="dropdown-item" href="?p=y&jur=AT&tingkat=XI&tanggal=<?= $tanggal_pilih; ?>">Kelas XI AT &nbsp;&raquo;</a>
                                <ul class="submenu dropdown-menu">
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XIAT1&tanggal=<?= $tanggal_pilih; ?>"> XI AT 1 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XIAT2&tanggal=<?= $tanggal_pilih; ?>"> XI AT 2 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XIAT3&tanggal=<?= $tanggal_pilih; ?>"> XI AT 3 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=AT&tingkat=XI&kelompokjur=1&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 1 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=AT&tingkat=XI&kelompokjur=2&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 2 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=AT&tingkat=XI&kelompokjur=3&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 3 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=AT&tingkat=XI&kelompokjur=4&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 4 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=AT&tingkat=XI&kelompokjur=5&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 5 </a></li>
                                </ul>
                            </li>
                            <li><a class="dropdown-item" href="?p=y&jur=AT&tingkat=XII&tanggal=<?= $tanggal_pilih; ?>">Kelas XII AT &raquo;</a>
                                <ul class="submenu dropdown-menu">
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XIIAT1&tanggal=<?= $tanggal_pilih; ?>"> XII AT 1 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XIIAT2&tanggal=<?= $tanggal_pilih; ?>"> XII AT 2 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XIIAT3&tanggal=<?= $tanggal_pilih; ?>"> XII AT 3 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=AT&tingkat=XII&kelompokjur=1&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 1 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=AT&tingkat=XII&kelompokjur=2&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 2 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=AT&tingkat=XII&kelompokjur=3&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 3 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=AT&tingkat=XII&kelompokjur=4&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 4 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=AT&tingkat=XII&kelompokjur=5&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 5 </a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="btn-group">
                        <a href="?p=y&jur=DKV&tanggal=<?= $tanggal_pilih; ?>&kelas=DKV" type="button" class="btn btn-primary bg-gradient-primary elevation-3">DKV</a>
                        <button class="elevation-3 btn btn-primary bg-gradient-primary dropdown-toggle dropdown-toggle-split" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"></button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item" href="?p=y&jur=DKV&tingkat=X&tanggal=<?= $tanggal_pilih; ?>">Kelas X DKV&nbsp;&nbsp;&nbsp;&raquo;</a>
                                <ul class="submenu dropdown-menu">
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XDKV1&tanggal=<?= $tanggal_pilih; ?>"> X DKV 1 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XDKV2&tanggal=<?= $tanggal_pilih; ?>"> X DKV 2 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XDKV3&tanggal=<?= $tanggal_pilih; ?>"> X DKV 3 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=DKV&tingkat=X&kelompokjur=1&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 1 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=DKV&tingkat=X&kelompokjur=2&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 2 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=DKV&tingkat=X&kelompokjur=3&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 3 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=DKV&tingkat=X&kelompokjur=4&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 4 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=DKV&tingkat=X&kelompokjur=5&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 5 </a></li>
                                </ul>
                            </li>
                            <li><a class="dropdown-item" href="?p=y&jur=DKV&tingkat=XI&tanggal=<?= $tanggal_pilih; ?>">Kelas XI DKV&nbsp;&nbsp;&raquo;</a>
                                <ul class="submenu dropdown-menu">
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XIDKV1&tanggal=<?= $tanggal_pilih; ?>"> XI DKV 1 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XIDKV2&tanggal=<?= $tanggal_pilih; ?>"> XI DKV 2 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XIDKV3&tanggal=<?= $tanggal_pilih; ?>"> XI DKV 3 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=DKV&tingkat=XI&kelompokjur=1&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 1 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=DKV&tingkat=XI&kelompokjur=2&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 2 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=DKV&tingkat=XI&kelompokjur=3&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 3 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=DKV&tingkat=XI&kelompokjur=4&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 4 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=DKV&tingkat=XI&kelompokjur=5&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 5 </a></li>
                                </ul>
                            </li>
                            <li><a class="dropdown-item" href="?p=y&jur=DKV&tingkat=XII&tanggal=<?= $tanggal_pilih; ?>">Kelas XII DKV&nbsp;&raquo;</a>
                                <ul class="submenu dropdown-menu">
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XIIDKV1&tanggal=<?= $tanggal_pilih; ?>"> XII DKV 1 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XIIDKV2&tanggal=<?= $tanggal_pilih; ?>"> XII DKV 2 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XIIDKV3&tanggal=<?= $tanggal_pilih; ?>"> XII DKV 3 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=DKV&tingkat=XII&kelompokjur=1&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 1 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=DKV&tingkat=XII&kelompokjur=2&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 2 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=DKV&tingkat=XII&kelompokjur=3&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 3 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=DKV&tingkat=XII&kelompokjur=4&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 4 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=DKV&tingkat=XII&kelompokjur=5&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 5 </a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="btn-group">
                        <a href="?p=y&jur=TE&tanggal=<?= $tanggal_pilih; ?>&kelas=TE" type="button" class="btn btn-warning bg-gradient-warning elevation-3">TE</a>
                        <button class="elevation-3 btn btn-warning bg-gradient-warning dropdown-toggle dropdown-toggle-split" type="button" data-bs-toggle="dropdown"></button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item" href="?p=y&jur=TE&tingkat=X&tanggal=<?= $tanggal_pilih; ?>">Kelas X TE&nbsp;&nbsp;&nbsp;&raquo;</a>
                                <ul class="submenu dropdown-menu">
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XTE1&tanggal=<?= $tanggal_pilih; ?>"> X TE 1 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XTE2&tanggal=<?= $tanggal_pilih; ?>"> X TE 2 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XTE3&tanggal=<?= $tanggal_pilih; ?>"> X TE 3 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=TE&tingkat=X&kelompokjur=1&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 1 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=TE&tingkat=X&kelompokjur=2&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 2 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=TE&tingkat=X&kelompokjur=3&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 3 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=TE&tingkat=X&kelompokjur=4&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 4 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=TE&tingkat=X&kelompokjur=5&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 5 </a></li>
                                </ul>
                            </li>
                            <li><a class="dropdown-item" href="?p=y&jur=TE&tingkat=XI&tanggal=<?= $tanggal_pilih; ?>">Kelas XI TE&nbsp;&nbsp;&raquo;</a>
                                <ul class="submenu dropdown-menu">
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XITE1&tanggal=<?= $tanggal_pilih; ?>"> XI TE 1 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XITE2&tanggal=<?= $tanggal_pilih; ?>"> XI TE 2 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XITE3&tanggal=<?= $tanggal_pilih; ?>"> XI TE 3 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=TE&tingkat=XI&kelompokjur=1&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 1 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=TE&tingkat=XI&kelompokjur=2&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 2 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=TE&tingkat=XI&kelompokjur=3&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 3 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=TE&tingkat=XI&kelompokjur=4&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 4 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=TE&tingkat=XI&kelompokjur=5&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 5 </a></li>
                                </ul>
                            </li>
                            <li><a class="dropdown-item" href="?p=y&jur=TE&tingkat=XII&tanggal=<?= $tanggal_pilih; ?>">Kelas XII TE&nbsp;&raquo; </a>
                                <ul class="submenu dropdown-menu">
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XIITE1&tanggal=<?= $tanggal_pilih; ?>"> XII TE 1 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XIITE2&tanggal=<?= $tanggal_pilih; ?>"> XII TE 2 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&kelasjur=XIITE3&tanggal=<?= $tanggal_pilih; ?>"> XII TE 3 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=TE&tingkat=XII&kelompokjur=1&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 1 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=TE&tingkat=XII&kelompokjur=2&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 2 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=TE&tingkat=XII&kelompokjur=3&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 3 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=TE&tingkat=XII&kelompokjur=4&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 4 </a></li>
                                    <li><a class="dropdown-item" href="?p=y&jur=TE&tingkat=XII&kelompokjur=5&tanggal=<?= $tanggal_pilih; ?>"> Kelompok 5 </a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div> -->

        <div class="card elevation-3 bg-primary bg-gradient-primary border-0" style="z-index: 1;">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i>&nbsp;
                    Presensi Siswa Kelas Saya
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
                    <?php if ($hit_datasiswa) { ?>
                        <h5>Kelas
                            <?= (@$tingkat_kbm || @$kelasjur) ? (@$tingkat_kbm ? $tingkat_kbm : $kelasjur) : " Semua Kelas "; ?>&nbsp;-&nbsp;<?= @$kelompok_kbm ? "Kelompok : " . $kelompok_kbm : (@$kelas_kbm ? $kelas_kbm : " Semua Kelompok "); ?>&nbsp;<?= @$keterangan_ruang; ?>
                            
                            <span class="badge bg-gradient-danger"><?= @$info_ruangan . " [" . @$ruangan . "]"; ?></span>
                        </h5>
                    <?php } ?>
                    <?php
                    $query_instruktur = mysqli_query($konek, "SELECT * FROM jadwalkbm WHERE ruangan = '$ruangan' AND tanggal = '$tanggal_pilih'");
                    $cek_nama_instruktur = mysqli_num_rows($query_instruktur);

                    if ($cek_nama_instruktur > 0) {
                        echo "<h6>Instruktur :</h6>";
                        $no = "";
                        while ($data_instruktur = mysqli_fetch_array($query_instruktur)) {
                            $no++;
                            $nick = @$data_instruktur['nick'];

                            $sql_instruktur = "SELECT * FROM dataguru WHERE nick = '$nick'";
                            $que_instruktur = mysqli_query($konek, $sql_instruktur);
                            $hasil_inst = mysqli_fetch_array($que_instruktur);

                            $nama_instruktur = @$hasil_inst['nama'];
                            $nip_instruktur = @$hasil_inst['nip'];
                    ?>
                            <label for=""><?= $no; ?>. <?= $nama_instruktur; ?>

                                <?php if ($nick == $nick_login) { ?>&nbsp;<i class="fas fa-check-circle" style="color: green;"></i>
                            <?php } ?>
                            </label><br>
                        <?php }
                    } else { ?>
                        <h6>Guru :</h6>
                        <label for=""><?= $nama_login; ?>&nbsp;<i class="fas fa-check-circle" style="color: green;"></i></label>
                    <?php } ?>

                    <?php if (@$hit_datasiswa) { ?>
                        <div class="alert alert-info">
                            <h5 for="">Jadwal Hari <?= $hari_indo; ?>, <?= $tanggal_indo_pilih; ?></h5>

                            <?php
                            if ($hit_jadwal_guru > 1) {
                                echo '<div class="mb-1"><i class="fas fa-info-circle text-info"></i>&nbsp;';
                                echo "Pilih List untuk ditampilkan</div>";
                            }
                            for ($c_jadwal = 0; $c_jadwal < $hit_jadwal_guru; $c_jadwal++) {
                                if ($ruangan == @$hasil_cek_ruanganguru_jadwalkbm[$c_jadwal]['ruangan']) {
                                    $_radio_jadwal = 'checked';
                                } else {
                                    $_radio_jadwal = '';
                                }
                            ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault<?= $c_jadwal; ?>" <?= $_radio_jadwal; ?> onclick="self.location = 'kelas.php?jur=<?= $jur_pilih; ?>&tanggal=<?= $tanggal_pilih; ?>&idjdwl=<?= $c_jadwal; ?>'">
                                    <label class="form-check-label" for="flexRadioDefault<?= $c_jadwal; ?>">
                                        <p><?= $hasil_cek_ruanganguru_jadwalkbm[$c_jadwal]['kelompok'] ? 'Kelas: ' . $hasil_cek_ruanganguru_jadwalkbm[$c_jadwal]['tingkat'] . ', Jur: ' . $hasil_cek_ruanganguru_jadwalkbm[$c_jadwal]['jur'] . ', Kelompok: ' . $hasil_cek_ruanganguru_jadwalkbm[$c_jadwal]['kelompok'] : $hasil_cek_ruanganguru_jadwalkbm[$c_jadwal]['kelas']; ?>, Jam ke : <?= $hasil_cek_ruanganguru_jadwalkbm[$c_jadwal]['mulai_jamke'] . ' - ' . $hasil_cek_ruanganguru_jadwalkbm[$c_jadwal]['sampai_jamke']; ?>, Ruangan : <?= @$hasil_cek_ruanganguru_jadwalkbm[$c_jadwal]['info'] . " [" . @$hasil_cek_ruanganguru_jadwalkbm[$c_jadwal]['ruangan'] . "]"; ?></p>
                                    </label>
                                </div>
                            <?php } ?>

                            <p>
                                <i class="fas fa-info-circle text-warning"></i>&nbsp;Tips : Klik Nama Siswa untuk mengetahui detail / Riwayat Catatan SIswa tersebut.
                            </p>
                            <!-- <i class="fas fa-file text-fuchsia"></i> -->
                            <!-- <i class="fas fa-circle"></i> -->
                            <!-- <i class="far fa-circle"></i> -->
                            <!-- <i class="fas fa-heart"></i> -->
                        </div>

                        <form method="post">
                            <input type="hidden" name="nick" value="<?= @$nick_login; ?>">
                            <input type="hidden" name="nama_guru" value="<?= @$nama_login; ?>">
                            <input type="hidden" name="ruangan" value="<?= @$ruangan; ?>">
                            <input type="hidden" name="info_ruangan" value="<?= @$info_ruangan; ?>">
                            <input type="hidden" name="kelas" value="<?= @$kelas_kbm; ?>">
                            <input type="hidden" name="kelompok" value="<?= @$kelompok_kbm; ?>">
                            <input type="hidden" name="jur" value="<?= @$jur_kbm; ?>">
                            <input type="hidden" name="tingkat" value="<?= @$tingkat_kbm; ?>">
                            <input type="hidden" name="kelas" value="<?= @$kelas_kbm; ?>">
                            <input type="hidden" name="jamke" value="<?= @$mulai_kbm; ?>">
                            <input type="hidden" name="sampai_jamke" value="<?= @$sampai_kbm; ?>">
                            <input type="hidden" name="tanggal" value="<?= @$tanggal_pilih; ?>">

                            <?php
                            $qu_data_jurnal = mysqli_query($konek, "SELECT jurnal FROM jurnalguru WHERE tanggal = '$tanggal_pilih' AND jamke = '" . @$mulai_kbm . "' AND kelas = '$kelas_kbm' AND ruangan = '$ruangan' AND nick = '$nick_login'");
                            $data_jurnal = mysqli_fetch_array($qu_data_jurnal);
                            ?>

                            <style>
                                @media only screen and (max-width: 768px) {
                                    .kolomjurnal {
                                        display: flex;
                                        flex-direction: column;
                                    }

                                    .kolomjurnal textarea {
                                        width: 150%;
                                    }

                                    .tombol_kolomjurnal {
                                        display: flex;
                                        justify-content: center;
                                        /* margin-left: 50px; */
                                        width: 100%;
                                    }
                                }
                            </style>

                            <div class="row m-3 kolomjurnal">
                                <div class="col-8">
                                    <div class="form-floating">
                                        <textarea class="form-control" name="jurnal" placeholder="Isikan ringkasan kegiatan/materi di kelas." id="area_jurnal" style="height: 100px;"><?= @$data_jurnal['jurnal']; ?></textarea>
                                        <label for="floatingTextarea">Jurnal Mengajar
                                            <i class="fas fa-check-circle"></i>
                                        </label>
                                    </div>

                                </div>
                                <div class="col-4 tombol_kolomjurnal">
                                    <div class="row m-2">
                                        <button type="submit" class="btn btn-info"><i class="fas fa-edit"></i>&nbsp;Update Jurnal</button>
                                    </div>
                                    <div class="row m-2">
                                        <a target="_blank" class="btn btn-secondary" href="jurnalsaya.php"><i class="fas fa-list"></i>&nbsp;Semua Jurnal</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    <?php } ?>
                    <?php if ($hit_datasiswa) { ?>
                        <div class="row">
                            <table id="example1" class="table table-hover table-bordered table-striped">
                                <thead>
                                    <tr style="text-align: center; position: sticky;">
                                        <!-- <th>No.</th> -->
                                        <!-- <th>NIS</th> -->
                                        <th>Status</th>
                                        <th>Nama</th>
                                        <!-- <th>Detail</th> -->
                                        <th>Kelas</th>
                                        <th>Validasi</th>
                                        <th style="width: 25%;">Catatan</th>
                                        <th>Nilai: Aff | Psi | Kog | <i class="fas fa-plus-circle text-indigo"></i></th>
                                        <th>Kel.</th>
                                        <!-- <th>Kontak Siswa</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 0;


                                    while ($no < $hit_datasiswa) {

                                        // $tanggal_pilih = '2022-02-10';
                                        // $tanggal_pilih_dmY = '10-02-2022';

                                        $nis_siswa = $hasil_datasiswa[$no]['nis'];
                                        $nokartu_siswa = $hasil_datasiswa[$no]['nokartu'];
                                        $nick_siswa = $hasil_datasiswa[$no]['nick'];

                                        $sql_cek_presensikelas = "SELECT * FROM presensikelas WHERE nis = '$nis_siswa' AND tanggal LIKE '%$tanggal_pilih%'";
                                        $que_cek_presensikelas = mysqli_query($konek, $sql_cek_presensikelas);
                                        $array_hasil = mysqli_fetch_array(@$que_cek_presensikelas);

                                        $hasil_cek_presensikelas = mysqli_num_rows(@$que_cek_presensikelas);

                                        $id_presensi_kelas = @$array_hasil['id'] ? $array_hasil['id'] : '0';
                                    ?>
                                        <tr <?= @$hct_bg_keterangan; ?> style="text-align: center;">
                                            <!-- <td style="width: 5%;"><?= $no + 1; ?></td> -->
                                            <!-- <td><?= $nama_hari_singkat_pilih; ?>, <?= $tanggal_pilih_dmY; ?></td> -->
                                            <td>
                                                <div class="d-flex flex-wrap justify-content-center">
                                                    <div id="iconstatus<?= @$nis_siswa; ?>">
                                                        <?php
                                                        if ($hasil_cek_presensikelas) {
                                                            if (@$array_hasil['status'] == 'H') {
                                                        ?>
                                                                <i class="fas fa-check-circle text-success"></i>
                                                            <?php } else if (@$array_hasil['status'] == 'T') {
                                                            ?>
                                                                <i class="fas fa-check-circle text-primary"></i>
                                                            <?php
                                                            } elseif (@$array_hasil['status'] == 'I') {
                                                            ?>
                                                                <i class="fas fa-info-circle text-info"></i>
                                                            <?php
                                                            } elseif (@$array_hasil['status'] == 'S') {
                                                            ?>
                                                                <i class="fas fa-info-circle text-primary"></i>
                                                            <?php
                                                            } elseif (@$array_hasil['status'] == 'A') {
                                                            ?>
                                                                <i class="fas fa-times-circle text-danger"></i>
                                                            <?php
                                                            } else {
                                                            ?>
                                                                <i class="fas fa-question-circle text-warning"></i>
                                                            <?php
                                                            }
                                                        } else {
                                                            ?>
                                                            <i class="fas fa-question-circle text-warning"></i>
                                                        <?php } ?>
                                                    </div>
                                                    <div id="iconcatatan<?= @$nis_siswa; ?>" class="mx-1">
                                                        <?php
                                                        if (@$array_hasil['catatan']) {
                                                        ?>
                                                            <i class="fas fa-edit text-fuchsia"></i>
                                                        <?php
                                                        }
                                                        ?>
                                                    </div>

                                                    <div id="iconaff<?= @$nis_siswa; ?>" class="mx-1">
                                                        <?php
                                                        if (@$array_hasil['aff']) {
                                                        ?>
                                                            <i class="fas fa-heart text-danger"></i>
                                                        <?php
                                                        }
                                                        ?>
                                                    </div>
                                                    <div id="iconpsi<?= @$nis_siswa; ?>" class="mx-1">
                                                        <?php
                                                        if (@$array_hasil['psi']) {
                                                        ?>
                                                            <i class="far fa-circle text-primary"></i>
                                                        <?php
                                                        }
                                                        ?>
                                                    </div>
                                                    <div id="iconkog<?= @$nis_siswa; ?>" class="mx-1">
                                                        <?php
                                                        if (@$array_hasil['kog']) {
                                                        ?>
                                                            <i class="fas fa-circle text-success"></i>
                                                        <?php
                                                        }
                                                        ?>
                                                    </div>

                                                    <div id="iconplus<?= @$nis_siswa; ?>" class="mx-1">
                                                        <?php
                                                        if (@$array_hasil['plus']) {
                                                        ?>
                                                            <i class="fas fa-plus-circle text-danger"></i>
                                                        <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="text-align: left;">
                                                <a target="_blank" href="catatan_siswa.php?nis=<?= $nis_siswa; ?>&info=notes&db=datasiswa" class="text-decoration-none text-dark">
                                                    <?= $hasil_datasiswa[$no]['nama']; ?>&nbsp;<i class="fas fa-info-circle text-info"></i>
                                                </a>
                                            </td>
                                            <!-- <td>
                                            <span class="badge badge-sm bg-info"><i class="fas fa-info"></i></span>
                                        </td> -->
                                            <td>
                                                <?= @$hasil_datasiswa[$no]['kelas']; ?></td>
                                            <td>
                                                <?php
                                                if ($hasil_cek_presensikelas) {
                                                    if (@$array_hasil['status'] == 'H') {
                                                        $masuk_0 = '';
                                                        $masuk_1 = "selected";
                                                        $masuk_2 = '';
                                                        $masuk_3 = '';
                                                        $masuk_4 = '';
                                                        $masuk_5 = '';
                                                    } elseif (@$array_hasil['status'] == 'T') {
                                                        $masuk_0 = '';
                                                        $masuk_1 = '';
                                                        $masuk_2 = "selected";
                                                        $masuk_3 = '';
                                                        $masuk_4 = '';
                                                        $masuk_5 = '';
                                                    } elseif (@$array_hasil['status'] == 'I') {
                                                        $masuk_0 = '';
                                                        $masuk_1 = '';
                                                        $masuk_2 = '';
                                                        $masuk_3 = "selected";
                                                        $masuk_4 = '';
                                                        $masuk_5 = '';
                                                    } elseif (@$array_hasil['status'] == 'S') {
                                                        $masuk_0 = '';
                                                        $masuk_1 = '';
                                                        $masuk_2 = '';
                                                        $masuk_3 = '';
                                                        $masuk_4 = "selected";
                                                        $masuk_5 = '';
                                                    } elseif (@$array_hasil['status'] == "A") {
                                                        $masuk_0 = '';
                                                        $masuk_1 = '';
                                                        $masuk_2 = '';
                                                        $masuk_3 = '';
                                                        $masuk_4 = '';
                                                        $masuk_5 = "selected";
                                                    }
                                                } else {
                                                    $masuk_0 = '';
                                                    $masuk_1 = '';
                                                    $masuk_2 = '';
                                                    $masuk_3 = '';
                                                    $masuk_4 = '';
                                                    $masuk_5 = '';
                                                }
                                                ?>
                                                <div class="d-flex">
                                                    <!-- select -->
                                                    <select class="form-control" name="masuk" id="masuk" onchange="update_masuk('<?= @$tanggal_pilih_Ymd; ?>', '<?= $nokartu_siswa; ?>', '<?= @$nis_siswa; ?>', this.value);">
                                                        <option value="" <?= @$masuk_0; ?>>-</option>
                                                        <option value="H#<?= $mulai_kbm; ?>#<?= $sampai_kbm; ?>#<?= $ruangan; ?>#<?= $nick_login; ?>" <?= @$masuk_1; ?>>H</option>
                                                        <option value="T#<?= $mulai_kbm; ?>#<?= $sampai_kbm; ?>#<?= $ruangan; ?>#<?= $nick_login; ?>" <?= @$masuk_2; ?>>T</option>
                                                        <option value="I#<?= $mulai_kbm; ?>#<?= $sampai_kbm; ?>#<?= $ruangan; ?>#<?= $nick_login; ?>" <?= @$masuk_3; ?>>I</option>
                                                        <option value="S#<?= $mulai_kbm; ?>#<?= $sampai_kbm; ?>#<?= $ruangan; ?>#<?= $nick_login; ?>" <?= @$masuk_4; ?>>S</option>
                                                        <option value="A#<?= $mulai_kbm; ?>#<?= $sampai_kbm; ?>#<?= $ruangan; ?>#<?= $nick_login; ?>" <?= @$masuk_5; ?>>A</option>
                                                    </select>
                                                    <!-- <div class="form-check form-switch mx-2 my-2">
                                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault">
                                            </div> -->
                                                </div>
                                            </td>
                                            <td>
                                                <!-- text box -->
                                                <div class="">
                                                    <textarea id="catatan" class="form-control" placeholder="Catatan siswa hari ini" id="floatingTextarea" onchange="update_catatan(<?= @$tanggal_pilih_Ymd; ?> + '#' + <?= @$hasil_cek_ruanganguru_jadwalkbm[$idjdwl]['mulai_jamke']; ?>, <?= @$nis_siswa; ?>, this.value);" style="height: 60px;"><?= @$array_hasil['catatan']; ?></textarea>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <input type="number" class="form-control" style="font-size: 12px;" name="nilai_aff" id="nilai_aff" value="<?= @$array_hasil['aff']; ?>" placeholder="Affective" onchange="update_nilai_aff(<?= @$tanggal_pilih_Ymd; ?> + '#' + <?= @$hasil_cek_ruanganguru_jadwalkbm[$idjdwl]['mulai_jamke']; ?>, <?= @$nis_siswa; ?>, this.value);">

                                                    <input type="number" class="form-control" style="font-size: 12px;" name="nilai_psi" id="nilai_psi" value="<?= @$array_hasil['psi']; ?>" placeholder="Psikomotor" onchange="update_nilai_psi(<?= @$tanggal_pilih_Ymd; ?> + '#' + <?= @$hasil_cek_ruanganguru_jadwalkbm[$idjdwl]['mulai_jamke']; ?>, <?= @$nis_siswa; ?>, this.value);">

                                                    <input type="number" class="form-control" style="font-size: 12px;" name="nilai_kog" id="nilai_kog" value="<?= @$array_hasil['kog']; ?>" placeholder="Kognitif" onchange="update_nilai_kog(<?= @$tanggal_pilih_Ymd; ?> + '#' + <?= @$hasil_cek_ruanganguru_jadwalkbm[$idjdwl]['mulai_jamke']; ?>, <?= @$nis_siswa; ?>, this.value);">

                                                    <input type="number" class="form-control" style="font-size: 12px;" name="nilai_plus" id="nilai_plus" value="<?= @$array_hasil['plus']; ?>" placeholder="Lainnya" onchange="update_nilai_plus(<?= @$tanggal_pilih_Ymd; ?> + '#' + <?= @$hasil_cek_ruanganguru_jadwalkbm[$idjdwl]['mulai_jamke']; ?>, <?= @$nis_siswa; ?>, this.value);">
                                                </div>
                                            </td>

                                            <!-- <input type="checkbox" class="btn-check btn-sm border-0" id="btn-check-2" checked autocomplete="off"> -->
                                            <!-- <label class="btn btn-primary" for="btn-check-2"><i class="fas fa-check-circle text-light"></i></label> -->
                                            <td><?= @$hasil_datasiswa[$no]['kelompok']; ?></td>
                                            <td>

                                                <?php
                                                $data_tentang = @$hasil_datasiswa[$no]['tentang'];

                                                if ($data_tentang) {
                                                    $parsing = explode('#', $data_tentang);

                                                    // print_r($parsing);

                                                    $nomor_wa = $parsing[10];

                                                    if (@$nomor_wa != '') {
                                                ?>
                                                        <a id="waicon_01" href="https://api.whatsapp.com/send?phone=<?= @$nomor_wa; ?>" target="_blank">
                                                            <i class="fab fa-whatsapp"></i>
                                                        </a>
                                                    <?php
                                                    } else { ?>
                                                        <button class="border-0" id="waicon_01" onclick="alert('Belum ada nomor telepon')">
                                                            <i class="fab fa-whatsapp"></i>
                                                        </button>
                                                <?php }
                                                } ?>

                                            </td>
                                        </tr>
                                        <?php $no++; ?>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="btn-group">
                            <a target="_blank" href="print_kelas.php?jur=<?= $jur_pilih; ?>&tanggal=<?= $tanggal_pilih; ?>&idjdwl=<?= @$_GET['idjdwl'] ? $_GET['idjdwl'] : '0'; ?>" class="btn btn-dark">
                                <i class="fas fa-print text-info"></i>&nbsp;Cetak&nbsp;/&nbsp;Download&nbsp;Dokumen&nbsp;<i class="fas fa-download text-info"></i>&nbsp;<i class="fas fa-file-excel text-success"></i>&nbsp;<i class="fas fa-file-pdf text-danger"></i>
                            </a>
                        </div>
                        <div class="btn-group">
                            <a target="_blank" href="nilaikelassaya.php" class="btn btn-success">
                                <i class="fas fa-list text-light"></i>&nbsp;Nilai Kelas Saya
                            </a>
                        </div>
                    <?php
                    } else {
                    ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <?php
                            if ($tanggal_pilih == $tanggal) {
                                $text_1 = "hari ini";
                            } else {
                                $text_1 = "hari yang dipilih";
                            }
                            ?>
                            <h4 style="text-align: center;">- Tidak ada kelas untuk <?= $text_1; ?> -</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php
                    } ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('views/footer.php'); ?>

<script>
    $(function() {
        $("#example1").DataTable({
            dom: 'fltip',
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

    // kalender

    function ketanggal(tanggal, jur) {
        // alert('oke berhasil: ' + tanggal);
        window.location.href = "kelas.php?tanggal=" + tanggal + "&jur=" + jur;
    }

    // <!-- script js select update_masuk ke database -->

    function update_masuk(tanggal, nokartu, nis, masuk) {
        $.ajax({
            url: "app/update_masuk.php",
            type: "POST",
            data: {
                tanggal: tanggal,
                nokartu: nokartu,
                nis: nis,
                masuk: masuk
            },
            success: function(data) {
                var _masuk = masuk.substring(0, 1);

                if (_masuk == 'H') {
                    document.getElementById("iconstatus" + nis).innerHTML = '<i class="fas fa-check-circle text-success"></i>';
                } else if (_masuk == 'T') {
                    document.getElementById("iconstatus" + nis).innerHTML = '<i class="fas fa-check-circle text-primary"></i>';
                } else if (_masuk == 'I') {
                    document.getElementById("iconstatus" + nis).innerHTML = '<i class="fas fa-info-circle text-info"></i>';
                } else if (_masuk == 'S') {
                    document.getElementById("iconstatus" + nis).innerHTML = '<i class="fas fa-info-circle text-primary"></i>';
                } else if (_masuk == 'A') {
                    document.getElementById("iconstatus" + nis).innerHTML = '<i class="fas fa-times-circle text-danger"></i>';
                } else {
                    document.getElementById("iconstatus" + nis).innerHTML = '<i class="fas fa-question-circle text-warning"></i>';
                }

                // alert(_masuk);
                // alert(data);
            },
            error: function() {
                alert('error!');
            }
        });
    }

    // <!-- script js textbox update_catatan ke database -->

    function update_catatan(tanggal, nis, catatan) {
        $.ajax({
            url: "app/update_catatan.php",
            type: "POST",
            data: {
                tanggal: tanggal,
                nis: nis,
                catatan: catatan
            },
            success: function(data) {
                document.getElementById("iconcatatan" + nis).innerHTML = '<i class="fas fa-edit text-indigo"></i>';

                // alert(data);
            },
            error: function() {
                alert('error!');
            }
        });
    }

    // <!-- script js textbox update_nilai_aff ke database -->
    function update_nilai_aff(tanggal, nis, aff) {
        $.ajax({
            url: "app/update_nilai_aff.php",
            type: "POST",
            data: {
                tanggal: tanggal,
                nis: nis,
                aff: aff
            },
            success: function(data) {
                document.getElementById("iconaff" + nis).innerHTML = '<i class="fas fa-heart text-indigo"></i>';

                // alert(data);
            },
            error: function() {
                alert('error!');
            }
        });
    }

    // <!-- script js textbox update_nilai_psi ke database -->
    function update_nilai_psi(tanggal, nis, psi) {
        $.ajax({
            url: "app/update_nilai_psi.php",
            type: "POST",
            data: {
                tanggal: tanggal,
                nis: nis,
                psi: psi
            },
            success: function(data) {
                document.getElementById("iconpsi" + nis).innerHTML = '<i class="far fa-circle text-indigo"></i>';

                // alert(data);
            },
            error: function() {
                alert('error!');
            }
        });
    }

    // <!-- script js textbox update_nilai_kog ke database -->
    function update_nilai_kog(tanggal, nis, kog) {
        $.ajax({
            url: "app/update_nilai_kog.php",
            type: "POST",
            data: {
                tanggal: tanggal,
                nis: nis,
                kog: kog
            },
            success: function(data) {
                document.getElementById("iconkog" + nis).innerHTML = '<i class="fas fa-circle text-indigo"></i>';

                // alert(data);
            },
            error: function() {
                alert('error!');
            }
        });
    }

    // <!-- script js textbox update_nilai_plus ke database -->
    function update_nilai_plus(tanggal, nis, plus) {
        $.ajax({
            url: "app/update_nilai_plus.php",
            type: "POST",
            data: {
                tanggal: tanggal,
                nis: nis,
                plus: plus
            },
            success: function(data) {
                document.getElementById("iconplus" + nis).innerHTML = '<i class="fas fa-plus-circle text-indigo"></i>';

                // alert(data);
            },
            error: function() {
                alert('error!');
            }
        });
    }
</script>

<?php
function cari_data_presensi($nis_siswa, $hasil_datapresensi)
{
    $hasil_cari_presensi = array();
    foreach ($hasil_datapresensi as $dtp) {
        if ($dtp['nis'] == $nis_siswa) {
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

<style>
    @media only screen and (max-width: 768px) {

        #example1,
        #example1 #masuk {
            font-size: 12px;
        }
    }
</style>
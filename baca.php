<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

//tanggal dan jam hari ini
date_default_timezone_set('Asia/Jakarta');
$timestamp = date('Y-m-d H:i:s');
$tanggal = date('Y-m-d');
$jam     = date('H:i:s');

// Konek database
include('config/konesi.php');
// echo "test";

// Ambil data dari tabel statusnya
$query_select_statusnya = "SELECT * FROM statusnya";
$stmt_select_statusnya = mysqli_stmt_init($konek);
mysqli_stmt_prepare($stmt_select_statusnya, $query_select_statusnya);
mysqli_stmt_execute($stmt_select_statusnya);
$result_select_statusnya = mysqli_stmt_get_result($stmt_select_statusnya);
$data = mysqli_fetch_array($result_select_statusnya);

// Ambil data "mode"
$mode = $data['mode'];

// Ambil waktu dari statusnya
$waktumasuk = $data['waktumasuk'];
$waktupulang = $data['waktupulang'];
$harikerja = $data['info'];

// Ambil data setting waktu
$query_select_setting_waktu = "SELECT wa, wta, wtp, wp FROM statusnya";
$stmt_select_setting_waktu = mysqli_stmt_init($konek);
mysqli_stmt_prepare($stmt_select_setting_waktu, $query_select_setting_waktu);
mysqli_stmt_execute($stmt_select_setting_waktu);
$result_select_setting_waktu = mysqli_stmt_get_result($stmt_select_setting_waktu);
$data_waktu = mysqli_fetch_array($result_select_setting_waktu);
$wa = date('H:i', strtotime($data_waktu['wa']));  // Waktu awal absen masuk
$wta = date('H:i', strtotime($data_waktu['wta'])); // Batas waktu absen masuk
$wtp = date('H:i', strtotime($data_waktu['wtp'])); // Waktu absen pulang
$wp = date('H:i', strtotime($data_waktu['wp']));  // Batas waktu absen pulang

if ($harikerja) {
    if ($harikerja == "5") {
        $harilibur = limaharikerja($tanggal);
    } elseif ($harikerja == "6") {
        $harilibur = enamharikerja($tanggal);
    } else {
        $harilibur = false;
    }
}

// Baca nokartu dari tmprfid
$query_select_nokartu_tmprfid = "SELECT nokartu FROM tmprfid";
$stmt_select_nokartu_tmprfid = mysqli_stmt_init($konek);
mysqli_stmt_prepare($stmt_select_nokartu_tmprfid, $query_select_nokartu_tmprfid);
mysqli_stmt_execute($stmt_select_nokartu_tmprfid);
$result_select_nokartu_tmprfid = mysqli_stmt_get_result($stmt_select_nokartu_tmprfid);
$data_nokartu = mysqli_fetch_array($result_select_nokartu_tmprfid);
$nokartu = isset($data_nokartu['nokartu']) ? $data_nokartu['nokartu'] : "";

// echo 'mode: ';
// echo $mode;
// echo '<br>';
// echo 'waktumasuk: ';
// echo $waktumasuk;
// echo '<br>';
// echo 'waktupulang: ';
// echo $waktupulang;
// echo '<br>';
// echo 'harikerja: ';
// echo $harikerja;
// echo '<br>';
// echo 'harilibur: ';
// echo $harilibur;
// echo '<br>';
// echo 'nokartu: ';
// echo $nokartu;
// die;

if ($harilibur == false) {
    $modeAbsen = "<br><i>Akses masuk dibuka pukul : </i>" . $wa . " - " . $wta . "<br><i>Akses pulang dibuka pukul : </i>" . $wtp . " - " . $wp;

    //autoset mode absen
    if ($jam < $wta && $jam >= $wa) {
        $mode = 1;
    } elseif ($jam >= $wtp && $jam < $wp) {
        $mode = 2;
    } else {
        $mode = 0;
    }

    // Update auto set mode
    $query_update_statusnya = "UPDATE statusnya SET mode = ?";
    $stmt_update_statusnya = mysqli_stmt_init($konek);
    mysqli_stmt_prepare($stmt_update_statusnya, $query_update_statusnya);
    mysqli_stmt_bind_param($stmt_update_statusnya, "s", $mode);
    mysqli_stmt_execute($stmt_update_statusnya);

    // set pesan untuk mode absen
    if ($mode == 1) {
        $modeAbsen = "MASUK";
    } elseif ($mode == 2) {
        $modeAbsen = "PULANG";
    } else {
    }

    // if($sql){
    //     echo "kebaca nokartu " . $nokartu;
    // } else {
    //     echo "nokartu nggak kebaca";
    // }

    // echo("nokartu: " . $nokartu . ", mode: " . $mode); die;
    // $nokartu = "456768990987656000";
    // $nokartu = "6309694912097227";

    if ($nokartu) {
        // echo ("ada nokartu" . $nokartu);
        $t_waktu_telat_siswa = "";
        $poin_siswa = "";
        $t_waktu_telat_guru = "";
        $poin_guru = "";

        // Cek nokartu di database dataGTK
        $query_check_guru = "SELECT nama, nip, foto, info, kode FROM dataguru WHERE nokartu = ?";
        $stmt_check_guru = mysqli_stmt_init($konek);
        mysqli_stmt_prepare($stmt_check_guru, $query_check_guru);
        mysqli_stmt_bind_param($stmt_check_guru, "s", $nokartu);
        mysqli_stmt_execute($stmt_check_guru);
        mysqli_stmt_store_result($stmt_check_guru);

        $nama_gtk = "";
        $nip_gtk = "";
        $info_gtk = "";
        $foto_gtk = "";
        $kode_gtk = "";

        if (mysqli_stmt_num_rows($stmt_check_guru) > 0) {
            mysqli_stmt_bind_result($stmt_check_guru, $nama_gtk, $nip_gtk, $foto_gtk, $info_gtk, $kode_gtk);
            mysqli_stmt_fetch($stmt_check_guru);
        }

        // Cek nokartu di database datasiswa
        $query_check_siswa = "SELECT nama, nis, foto, kelas, kode FROM datasiswa WHERE nokartu = ?";
        $stmt_check_siswa = mysqli_stmt_init($konek);
        mysqli_stmt_prepare($stmt_check_siswa, $query_check_siswa);
        mysqli_stmt_bind_param($stmt_check_siswa, "s", $nokartu);
        mysqli_stmt_execute($stmt_check_siswa);
        mysqli_stmt_store_result($stmt_check_siswa);

        $nama_siswa = "";
        $nis_siswa = "";
        $foto_siswa = "";
        $info_siswa = "";
        $kode_siswa = "";

        if (mysqli_stmt_num_rows($stmt_check_siswa) > 0) {
            mysqli_stmt_bind_result($stmt_check_siswa, $nama_siswa, $nis_siswa, $foto_siswa, $info_siswa, $kode_siswa);
            mysqli_stmt_fetch($stmt_check_siswa);
        }

        $nomorInduk = $nip_gtk ? $nip_gtk : $nis_siswa;
        $nama = @$nama_gtk ? $nama_gtk : $nama_siswa;
        $noReg = @$nip_gtk ? $nip_gtk : $nis_siswa;
        $foto = @$foto_gtk ? $foto_gtk : (@$foto_siswa ? $foto_siswa : '-');
        $info = @$info_gtk ? $info_gtk : @$info_siswa;
        $keterangan = @$keterangan_gtk ? $keterangan_gtk : @$keterangan_siswa;
        $tglakhir = @$tglakhir_gtk ? $tglakhir_gtk : @$tglakhir_siswa;
        $kode = @$kode_gtk ? $kode_gtk : @$kode_siswa;

        // jika ada yang ditemukan
        if ($nama_gtk != "" || $nama_siswa != "") {

            // jika data guru yang ditemukan
            if ($nama_gtk) {
                // echo "ditemukan: " . $nama_gtk;
                // ambil datanya
                $nama_gtk = $nama_gtk;
                $nip_gtk = @$datagtk['nip'];
                $foto_gtk = @$datagtk['foto'];
                $info_gtk = @$datagtk['jabatan'];
                $keterangan_gtk = @$datagtk['keterangan'];
                $tglakhir_gtk = @$datagtk['tglakhirdispo'];
                $kode_gtk = @$datagtk['kode'];
                // echo($nama . $foto . $info . $keterangan . $tglakhir);


                // ambil total waktu telat (t_waktu_telat) dari dataguru
                $t_waktu_telat_guru = isset($datagtk['t_waktu_telat']) ? $datagtk['t_waktu_telat'] : "";
                $poin_guru = isset($datagtk['poin']) ? $datagtk['poin'] : "";

                // echo("telat: " . $t_waktu_telat_guru . ", poin:" .$poin_guru . ", no:" . $nokartu); die;

                // jika tanggal ijin/disposisi telah berlalu/berakhir
                if ($tglakhir < $tanggal) {
                    // keterangan dan tanggal dikosongkan
                    $keterangan = "";
                    $query_update_dispo = "UPDATE dataguru SET keterangan = NULL, tglawaldispo = NULL, tglakhirdispo = NULL, docdis = NULL WHERE nokartu = ?";
                    $stmt_update_dispo = mysqli_stmt_init($konek);
                    mysqli_stmt_prepare($stmt_update_dispo, $query_update_dispo);
                    mysqli_stmt_bind_param($stmt_update_dispo, "s", $nokartu);
                    mysqli_stmt_execute($stmt_update_dispo);

                    // echo ("masuk update keterangan" . $nokartu . "," . $waktukosong . "," . $nama); die;
                }
                // jika yang ditemukan adalah data siswa
            } elseif ($nama_siswa) {
                // echo "detemukan: " . $nama_siswa;
                // ambil datanya
                // $nama_siswa = $nama_siswa;
                $nis_siswa = @$data_siswa['nis'];
                $foto_siswa = @$data_siswa['foto'];
                $info_siswa = @$data_siswa['kelas'];
                $keterangan_siswa = @$data_siswa['keterangan'];
                $tglakhir_siswa = @$data_siswa['tglakhir'];
                $kode_siswa = @$data_siswa['kode'];
                // echo($nama . $foto . $info . $keterangan . $tglakhir);

                // jika tanggal ijin/disposisi telah berlalu/berakhir
                if ($tglakhir < $tanggal) {
                    // keterangan dan tanggal dikosongkan
                    $keterangan = "";
                    $query_update_dispo_siswa = "UPDATE datasiswa SET keterangan = NULL, tglawal = NULL, tglakhir = NULL, fotodoc = NULL WHERE nokartu = ?";
                    $stmt_update_dispo_siswa = mysqli_stmt_init($konek);
                    mysqli_stmt_prepare($stmt_update_dispo_siswa, $query_update_dispo_siswa);
                    mysqli_stmt_bind_param($stmt_update_dispo_siswa, "s", $nokartu);
                    mysqli_stmt_execute($stmt_update_dispo_siswa);
                }

                // ambil total waktu telat (t_waktu_telat) dari datasiswa
                $t_waktu_telat_siswa = isset($data_siswa['t_waktu_telat']) ? $data_siswa['t_waktu_telat'] : "";
                $poin_siswa = isset($data_siswa['poin']) ? $data_siswa['poin'] : "";
            }

            if ($mode == 1) {
                // cek nokartu di data presensi
                $query_cek_nokartu = "SELECT nokartu FROM datapresensi WHERE nokartu = ? AND tanggal = ?";
                $stmt_cek_nokartu = mysqli_stmt_init($konek);
                mysqli_stmt_prepare($stmt_cek_nokartu, $query_cek_nokartu);
                mysqli_stmt_bind_param($stmt_cek_nokartu, "ss", $nokartu, $tanggal);
                mysqli_stmt_execute($stmt_cek_nokartu);
                mysqli_stmt_bind_result($stmt_cek_nokartu, $hasilCek);
                mysqli_stmt_fetch($stmt_cek_nokartu);

                if (!$hasilCek) {

                    // echo ("masuk set insert datapresen   si"); die;

                    if ($jam <= $waktumasuk) {
                        $ket = "MSK";

                        $selisihWaktu = selisih($jam, $waktumasuk);
                    } else {
                        $ket = "TLT";
                        // menghitung selisih jam
                        $selisihWaktu = selisih($waktumasuk, $jam);


                        if ($t_waktu_telat_siswa) {
                            // hitung total waktu telat dan poin
                            $hasil = jumlahkanwaktu($t_waktu_telat_siswa, $selisihWaktu);
                            $t_waktu_telat_siswa = $hasil;
                            $poin_siswa = (int)$poin_siswa + 1;

                            // echo ("telat: " . $t_waktu_telat_siswa . ", poin:" .$poin_siswa . ", no:" . $nokartu); die;

                            // update total waktu telat dan poin
                            $query_update_waktu_telat_poin = "UPDATE datasiswa SET t_waktu_telat=?, poin=? WHERE nokartu=?";
                            $stmt_update_waktu_telat_poin = mysqli_stmt_init($konek);
                            mysqli_stmt_prepare($stmt_update_waktu_telat_poin, $query_update_waktu_telat_poin);
                            mysqli_stmt_bind_param($stmt_update_waktu_telat_poin, "dds", $t_waktu_telat_siswa, $poin_siswa, $nokartu);
                            mysqli_stmt_execute($stmt_update_waktu_telat_poin);

                            // if($sql){
                            //     echo "berhasil";
                            // }else{
                            //     echo "gagal";
                            // }

                        } elseif ($t_waktu_telat_guru) {
                            // hitung total waktu telat dan poin
                            $hasil = jumlahkanwaktu($t_waktu_telat_guru, $selisihWaktu);
                            $t_waktu_telat_guru = $hasil;
                            $poin_guru = (int)$poin_guru + 1;

                            // echo ("telat: " . $t_waktu_telat_guru . ", poin:" .$poin_guru . ", no:" . $nokartu); die;

                            // update total waktu telat dan poin
                            $query_update_waktu_telat_poin = "UPDATE dataguru SET t_waktu_telat=?, poin=? WHERE nokartu=?";
                            $stmt_update_waktu_telat_poin = mysqli_stmt_init($konek);
                            mysqli_stmt_prepare($stmt_update_waktu_telat_poin, $query_update_waktu_telat_poin);
                            mysqli_stmt_bind_param($stmt_update_waktu_telat_poin, "dds", $t_waktu_telat_guru, $poin_guru, $nokartu);
                            mysqli_stmt_execute($stmt_update_waktu_telat_poin);

                            // if($sql){
                            //     echo "berhasil";
                            // }else{
                            //     echo "gagal";
                            // } die;

                        }
                    }

                    // echo 'selisih : ' . $selisihWaktu . '<br>';
                    // echo ("masuk buat insert datapresensi"); die;

                    // buat data di database datapresensi
                    $query_insert_datapresensi = "INSERT INTO datapresensi (nokartu, nomorinduk, nama, info, foto, waktumasuk, ketmasuk, a_time, tanggal, keterangan, updated_at, kode) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt_insert_datapresensi = mysqli_stmt_init($konek);
                    mysqli_stmt_prepare($stmt_insert_datapresensi, $query_insert_datapresensi);
                    mysqli_stmt_bind_param(
                        $stmt_insert_datapresensi,
                        "ssssssssssss",
                        $nokartu,
                        $nomorInduk,
                        $nama,
                        $info,
                        $foto,
                        $jam,
                        $ket,
                        $selisihWaktu,
                        $tanggal,
                        $keterangan,
                        $timestamp,
                        $kode
                    );
                    mysqli_stmt_execute($stmt_insert_datapresensi);


                    // if($sql){
                    //     echo "berhasil update ke datapresensi";
                    // }else{
                    //     echo "gagal update ke datapresensi";
                    // } die;

                    $pesan = "<b class='fs-2'>" . $nama . "</b>, Anda Berhasil melakukan Presensi.<br>Terima Kasih :)";
                } else {
                    // jika sudah absen, nokartu ada di datapresensi
                    if ($keterangan) {
                        $pesan = "<b class='fs-2'>" . $nama . "</b>, " . $keterangan;
                    } else {

                        $pesan = "<b class='fs-2'>" . $nama . "</b>, Anda Sudah melakukan Presensi.<br>Terima Kasih :)";
                    }
                }
            } elseif ($mode == 2) {
                if (!$keterangan) {
                    if ($jam <= $waktupulang) {
                        $ket = "PA";
                        $pesan = "<b class='fs-2'>" . $nama . "</b>,<br>Anda pulang lebih awal.<br>Tunggu sampai jam " . $waktupulang . ".";

                        // menghitung selisih jam
                        $selisihWaktu = selisih($jam, $waktupulang);
                    } else {
                        $ket = "PLG";
                        $pesan = "Presensi Pulang berhasil.<br>Hati-hati di jalan " . "<b class='fs-2'>" . $nama . "</b>";

                        // menghitung selisih jam
                        $selisihWaktu = selisih($waktupulang, $jam);
                    }
                } else {
                    $pesan = "<b class='fs-2'>" . $nama . "</b>, " . $keterangan;
                }
                // baca data nokartu di database datapresensi

                $query_select_datapresensi = "SELECT nokartu FROM datapresensi WHERE nokartu = ? and tanggal = ?";
                $stmt_select_datapresensi = mysqli_stmt_init($konek);
                mysqli_stmt_prepare($stmt_select_datapresensi, $query_select_datapresensi);
                mysqli_stmt_bind_param(
                    $stmt_select_datapresensi,
                    "ss",
                    $nokartu,
                    $tanggal
                );
                mysqli_stmt_execute($stmt_select_datapresensi);
                $result = mysqli_stmt_get_result($stmt_select_datapresensi);
                $data = mysqli_fetch_array($result);
                $hasilCek = isset($data['nokartu']) ? $data['nokartu'] : "";

                //Jika tidak ada data nokartu di datapresensi, Update waktu pulang
                if (!$hasilCek) {

                    // buat data di database datapresensi
                    $query_insert_datapresensi = "INSERT INTO datapresensi (nokartu, nomorinduk, nama, info, foto, waktupulang, ketpulang, b_time, tanggal, keterangan, updated_at, kode) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt_insert_datapresensi = mysqli_stmt_init($konek);
                    mysqli_stmt_prepare($stmt_insert_datapresensi, $query_insert_datapresensi);
                    mysqli_stmt_bind_param($stmt_insert_datapresensi, "ssssssssssss", $nokartu, $nomorInduk, $nama, $info, $foto, $jam, $ket, $selisihWaktu, $tanggal, $keterangan, $timestamp, $kode);
                    $insert = mysqli_stmt_execute($stmt_insert_datapresensi);

                    if (!$insert) {
                        echo "Error: " . $sql . "<br>" . mysqli_error($konek);
                    }
                    // echo ("masuk nggak ketemu hasil cek:" . $hasilCek . ", nokartu:" . $nokartu . ", ket:" . $ket) . ", pesan:" . $pesan; die;

                } else {
                    //Jika sudah ada, update data pulang
                    $pesan = "<b class='fs-2'>" . $nama . "</b>,<br>Anda sudah melakukan presensi pulang.";
                    $query_update_datapresensi = "UPDATE datapresensi SET waktupulang=?, ketpulang=?, b_time=?, keterangan=?, updated_at=? WHERE nokartu=? AND tanggal=?";
                    $stmt_update_datapresensi = mysqli_stmt_init($konek);
                    mysqli_stmt_prepare($stmt_update_datapresensi, $query_update_datapresensi);
                    mysqli_stmt_bind_param(
                        $stmt_update_datapresensi,
                        "sssssss",
                        $jam,
                        $ket,
                        $selisihWaktu,
                        $keterangan,
                        $timestamp,
                        $nokartu,
                        $tanggal
                    );
                    $update = mysqli_stmt_execute($stmt_update_datapresensi);

                    // if ($update) {
                    //     echo "New record created successfully, ";
                    // } else {
                    //     echo "Error: " . $sql . "<br>" . mysqli_error($konek) . ", ";
                    // }
                    // echo ("masuk kalo ketemu hasil cek:" . $hasilCek . ", nokartu:" . $nokartu . ", ket:" . $ket . ", pesan:" . $pesan); die;
                }
            } elseif ($mode == 0) {
                $pesan = "<b class='fs-2'>" . $nama . "</b>,<br>Tidak bisa melakukan presensi sekarang.<br>Masuk : " . $wa . " - " . $wta . "<br>Pulang : " . $wtp . " - " . $wp;
            }
        } else {
            // echo "data tidak ditemukan";
            // jika nokartu belum terdaftar
            $nama = "";
            $pesan = "Maaf,<br>Kartu ID ini belum terdaftar";
        }
        // echo ("hasil cek:" . $hasilCek . ", nokartu:" . $nokartu . ", ket:" . $ket . ", pesan:" . $pesan);

    } else {
    }
} else {
    $nokartu = isset($nokartu) ? $nokartu : "";
    $pesan = isset($pesan) ? $pesan : "";
    $nama = isset($nama) ? $nama : "";
    $pesan = "Hari ini Libur";
    $modeAbsen = "Hari ini Libur. Akses presensi ditutup!";
    // echo "tidak ada nokartu";
}


// die;
// menghapus nomorkartu di tmprfid
include("config/konesi.php");
$query_delete_tmprfid = "DELETE FROM tmprfid";
$delete = mysqli_query($konek, $query_delete_tmprfid);

// $stmt_delete_tmprfid = mysqli_stmt_init($konek);
// mysqli_stmt_prepare($stmt_delete_tmprfid, $query_delete_tmprfid);
// $delete = mysqli_stmt_execute($stmt_delete_tmprfid);

if ($delete) {
    // echo "berhasil dihapus nokartu di tmprfid";
} else {
    echo "Error tmprfid 0x00EE";
}

// echo '<br>';
// echo 'nokartu: ';
// echo $nokartu;
// echo '<br>';
// echo 'pesan: ';
// echo $pesan;
// die;

// Menutup statement jika valid
if (isset($stmt_select_statusnya) && $stmt_select_statusnya) {
    mysqli_stmt_close($stmt_select_statusnya);
}

if (isset($stmt_select_setting_waktu) && $stmt_select_setting_waktu) {
    mysqli_stmt_close($stmt_select_setting_waktu);
}

if (isset($stmt_select_nokartu_tmprfid) && $stmt_select_nokartu_tmprfid) {
    mysqli_stmt_close($stmt_select_nokartu_tmprfid);
}

if (isset($stmt_update_statusnya) && $stmt_update_statusnya) {
    mysqli_stmt_close($stmt_update_statusnya);
}

// mysqli_stmt_close(@$stmt_delete_tmprfid);

if (@$stmt_check_guru) {
    mysqli_stmt_close(@$stmt_check_guru);
}
if (@$stmt_check_siswa) {
    mysqli_stmt_close(@$stmt_check_siswa);
}
if (@$stmt_update_dispo) {
    mysqli_stmt_close(@$stmt_update_dispo);
}
if (@$stmt_update_dispo_siswa) {
    mysqli_stmt_close(@$stmt_update_dispo_siswa);
}
if (@$stmt_cek_nokartu) {
    mysqli_stmt_close(@$stmt_cek_nokartu);
}
if (@$stmt_update_waktu_telat_poin) {
    mysqli_stmt_close(@$stmt_update_waktu_telat_poin);
}
if (@$stmt_update_waktu_telat_poin) {
    mysqli_stmt_close(@$stmt_update_waktu_telat_poin);
}
if (@$stmt_insert_datapresensi) {
    mysqli_stmt_close(@$stmt_insert_datapresensi);
}
if (@$stmt_select_datapresensi) {
    mysqli_stmt_close(@$stmt_select_datapresensi);
}
if (@$stmt_update_datapresensi) {
    mysqli_stmt_close(@$stmt_update_datapresensi);
}

mysqli_close($konek);

if (!$nokartu) {
    // echo " Scan: tidak ada nokartu, pesan: " . $pesan;
?>

    <div class="baca">
        <?php
        if ($harilibur == true || $mode == 0) {
            echo '<div class="alert" role="alert" style="text-align: center;">';
            echo '<h2 class="shadow_1">&otimes;&nbsp;Akses Ditutup&nbsp;&otimes;</h2>';
            echo '</div>';
        } else {
        ?>
            <div class="tag-img">
                <div class="tag1">
                    <!-- <img src="img/app/tag.png"> -->
                </div>
                <div class="tag2">
                    <!-- <h1>Scan kartu</h1> -->
                </div>
            </div>
            <div class="tag-detail">
                <h2 class="shadow_1">&nbsp;Tempelkan kartu ID Anda</h2>
            </div>
        <?php } ?>
        <div class="tag-mode shadow_1">
            <h4><?= $mode == 0 ? '' : 'Presensi : '; ?><b><?= $modeAbsen; ?></b></h4>
        </div>
    </div>

    <div class="scan-img">

        <?php
        if ($mode == 0 || $harilibur == true) {
            echo '<img src="img/app/nfc.png" style="width: 200px;">';
        } else {
            echo '<img src="img/app/rfid-unscreen.gif" style="width: 250px;">';
        }
        ?>

    </div>
    <?php } else {

    // echo (" Scan: ada nokartu" . $nokartu . ", pesan : " . $pesan);

    if (!$nama) { ?>

        <!--  -->

        <div>
            <img src="img/app/lock.gif" style="width: 150px" class="mx-auto my-5 d-block">
        </div>
        <div class="mt-1" style="text-align: center;">
            <h1><?php echo $pesan; ?></h1><br>
        </div>
    <?php

    } else {

    ?>
        <div>
            <img src="<?php if ($mode == 0) {
                        ?>img/app/lock.gif<?php
                                        } else { ?>img/app/centang2.gif<?php } ?>" alt="" style="height: 20ex;" class="mx-auto my-5 d-block">
        </div>
        <div style="text-align: center;">
            <h1><?php echo $pesan; ?></h1>
        </div>
    <?php } ?>
<?php } ?>

<!-- function -->
<?php
// fungsi menghitung selisih waktu terlambat, waktu sekarang dengan waktu masuk dan pulang
function selisih($waktu1, $waktu2)
{
    $w_awal = strtotime($waktu1);
    $w_akhir = strtotime($waktu2);

    // Proses konversi
    $sel_wa = $w_akhir - $w_awal;
    $temp = $sel_wa;
    $selisihjam = sprintf("%02d", (int)($temp / (60 * 60)));
    $selisihmenit = sprintf("%02d", (int)(($temp / 60) - (60 * $selisihjam)));
    $selisihdetik = sprintf("%02d", (int)($temp % 60));
    // penggabungan
    return $selisihjam . ":" . $selisihmenit . ":" . $selisihdetik;
}

// fungsi menjumlahkan akumulasi wakruterlambat dari dataguru dan datasiswa
function jumlahkanwaktu($time1, $time2)
{
    $times = array($time1, $time2);
    $seconds = 0;
    foreach ($times as $time) {
        list($hour, $minute, $second) = explode(':', $time);
        $seconds += $hour * 3600;
        $seconds += $minute * 60;
        $seconds += $second;
    }
    $hours = floor($seconds / 3600);
    $seconds -= $hours * 3600;
    $minutes = floor($seconds / 60);
    $seconds -= $minutes * 60;
    return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
}

// hari libur
function enamharikerja($tanggal)
{
    $tanggal = strtotime($tanggal);
    $tanggal = date('l', $tanggal);
    $tanggal = strtolower($tanggal);
    if ($tanggal == "sunday") {
        return true;
    } else {
        return false;
    }
}

function limaharikerja($tanggal)
{
    $tanggal = strtotime($tanggal);
    $tanggal = date('l', $tanggal);
    $tanggal = strtolower($tanggal);
    if ($tanggal == "sunday" || $tanggal == "saturday") {
        return true;
    } else {
        return false;
    }
}
?>

<style>
    .baca h1 {
        font-size: 25px;
        font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
        color: aliceblue;
        font-style: italic;
        text-shadow: #0035717a 2px 5px 5px;
        /* text-shadow: #838383 2px 5px 5px; */
    }

    .baca h2 {
        /* font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif; */
        font-family: 'Courier New', Courier, monospace;
        background-color: rgb(255, 83, 21);
        color: rgb(255, 255, 255);
        /* font-style: italic; */
        font-size: 40px;
        font-weight: 600;
        text-align: center;
        margin-left: auto;
        margin-right: auto;
        /* text-shadow: #00357175 2px 5px 5px; */
        /* dua warna background bertumpuk layer drop */
        /* background-image: linear-gradient(to right, #ff9500, #ff4107); */
        background-image: linear-gradient(to bottom, rgba(255, 83, 21, 1) 0%, rgba(0, 0, 0, 0.3) 100%);
        /* filter: drop-shadow(5px 5px 5px #222); */
        padding: 5px;
        border-radius: 50px 4px 50px 4px;
        max-width: 700px;
    }

    .baca h4 {
        font-size: 18px;
        font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
        text-align: center;
        margin-left: auto;
        margin-right: auto;
    }

    .tag-img img {
        width: 40px;
    }

    .tag-img div {
        text-align: center;
    }

    .tag2 {
        margin-top: 15px;
    }

    .scan-img img {
        display: block;
        margin-left: auto;
        margin-right: auto;
        /* text-shadow: #00357188 2px 5px 2px; */
        filter: drop-shadow(5px 5px 5px #222);
    }

    .tag-img {
        display: flex;
        justify-content: center;
    }

    .tag-mode {
        color: rgb(66, 66, 66);
        /* background-color: #ffffff83; */
        background: linear-gradient(to bottom, #ffffff96 0%, #b7b7b75b 100%);
        /* min-width: 30%;
    max-width: 60%; */
        width: fit-content;
        margin-left: auto;
        margin-right: auto;
        border-radius: 40px 3px 40px 3px;
        padding: 10px 30px 4px 30px;
    }
</style>
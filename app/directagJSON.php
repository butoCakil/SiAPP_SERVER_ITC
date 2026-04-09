<?php
ini_set('display_errors', 1);
// Atau
// error_reporting(E_ALL && ~E_NOTICE);

//tanggal dan jam hari ini
date_default_timezone_set('Asia/Jakarta');
$timestamp = date('Y-m-d H:i:s');
$tanggal = date('Y-m-d');
$jam     = date('H:i:s');

// Mendapatkan hari dalam bahasa Inggris
// $hari_eng = date('D', $timestamp);

header('Content-Type: application/json');
//baca nomor kartu dari NodeMCU
$nokartu = @$_GET['nokartu'];
// baca id chip nodeMCU
$idchip = @$_GET['idchip'];
// nomor device
$nodevice = @$_GET['nodevice'];
$key = @$_GET['key'];


$sub_pesan = "";
$hasil_nodevice = "";
$hasil_kode_device = "";
$jadwal_ruangan = "";
$nama_guru = "";
$selisih_waktu = 0;

if (!isSafeInput($nokartu) || !isSafeInput($idchip) || !isSafeInput($nodevice)) {
    die("[0x20] Program dihentikan karena karakter mencurigakan ditemukan.");
}

// Jika ada nomor kartu
if ($nokartu) {
    // konek Database
    include("../config/konesi.php");

    $nokartu_clean = mysqli_real_escape_string($konek, $nokartu);
    $idchip_clean = mysqli_real_escape_string($konek, $idchip);

    // Jika ada ID chip
    if ($idchip_clean) {
        // Prepare the SELECT statement to check if the chip ID is registered
        $query_select_reg_device = "SELECT no_device, kode, info_device FROM reg_device WHERE chip_id = ?";
        $stmt_select_reg_device = mysqli_stmt_init($konek);
        mysqli_stmt_prepare($stmt_select_reg_device, $query_select_reg_device);
        mysqli_stmt_bind_param(
            $stmt_select_reg_device,
            "s",
            $idchip_clean
        );

        mysqli_stmt_execute($stmt_select_reg_device);
        $result_select_reg_device = mysqli_stmt_get_result($stmt_select_reg_device);

        if ($row = mysqli_fetch_assoc($result_select_reg_device)) {
            $hasil_nodevice = $row['no_device'];
            $hasil_kode_device = $row['kode'];
            $hasil_info_device = $row['info_device'];

            // Jika ID chip terdaftar
            if ($nodevice == $hasil_nodevice) {
                if ($key == "1234567890987654321") {
                    // Jika nomor device dari ID chip sama dengan nomor device nya
                    // AKSES DIBERIKAN
                    // Mulai olah data dari nomor kartu

                    // Prepare the SELECT statement to retrieve data from the 'statusnya' table
                    $query_select_statusnya = "SELECT mode, waktumasuk, waktupulang, info, DATE_FORMAT(wa, '%H:%i:%s') as wa, DATE_FORMAT(wta, '%H:%i:%s') as wta, DATE_FORMAT(wtp, '%H:%i:%s') as wtp, DATE_FORMAT(wp, '%H:%i:%s') as wp FROM statusnya";
                    $stmt_select_statusnya = mysqli_stmt_init($konek);
                    mysqli_stmt_prepare($stmt_select_statusnya, $query_select_statusnya);
                    mysqli_stmt_execute($stmt_select_statusnya);
                    $result_select_statusnya = mysqli_stmt_get_result($stmt_select_statusnya);

                    if ($row = mysqli_fetch_assoc($result_select_statusnya)) {
                        $mode = $row['mode'];
                        $waktumasuk = $row['waktumasuk'];
                        $waktupulang = $row['waktupulang'];
                        $harikerja = $row['info'];
                        $wa = $row['wa'];
                        $wta = $row['wta'];
                        $wtp = $row['wtp'];
                        $wp = $row['wp'];
                    }

                    // Tutup prepared statement
                    mysqli_stmt_close($stmt_select_statusnya);

                    if ($harikerja) {
                        if ($harikerja == "5") {
                            $harilibur = limaharikerja($tanggal);
                        } elseif ($harikerja == "6") {
                            $harilibur = enamharikerja($tanggal);
                        } else {
                            $harilibur = false;
                        }
                    }

                    $t_waktu_telat_siswa = "";
                    $poin_siswa = "";
                    $t_waktu_telat_guru = "";
                    $poin_guru = "";

                    // Cek nokartu di database dataGTK
                    $query_select_dataguru = "SELECT * FROM dataguru WHERE nokartu = ?";
                    $stmt_select_dataguru = mysqli_stmt_init($konek);
                    mysqli_stmt_prepare($stmt_select_dataguru, $query_select_dataguru);
                    mysqli_stmt_bind_param($stmt_select_dataguru, "s", $nokartu_clean);
                    mysqli_stmt_execute($stmt_select_dataguru);
                    $result_select_dataguru = mysqli_stmt_get_result($stmt_select_dataguru);

                    if ($row = mysqli_fetch_assoc($result_select_dataguru)) {
                        $nama_gtk = $row['nama'];
                        $nip_gtk = $row['nip'];
                        $noReg_gtk = @$row['nik'];
                        $foto_gtk = @$row['foto'] ? @$row['foto'] : "default.jpg";
                        $info_gtk = @$row['jabatan'];
                        $keterangan_gtk = @$row['keterangan'];
                        $tglakhir_gtk = @$row['tglakhirdispo'];
                        $kode_gtk = @$row['kode'];
                        // ambil total waktu telat (t_waktu_telat) dari dataguru
                        $t_waktu_telat_guru = isset($row['t_waktu_telat']) ? $row['t_waktu_telat'] : "";
                        $poin_guru = isset($row['poin']) ? $row['poin'] : "";
                    } else {
                        $nama_gtk = "";
                        $nip_gtk = "";
                    }

                    // Tutup prepared statement
                    mysqli_stmt_close($stmt_select_dataguru);

                    // Prepare the SELECT statement to check nokartu in the 'datasiswa' table
                    $query_select_datasiswa = "SELECT * FROM datasiswa WHERE nokartu = ?";
                    $stmt_select_datasiswa = mysqli_stmt_init($konek);
                    mysqli_stmt_prepare($stmt_select_datasiswa, $query_select_datasiswa);
                    mysqli_stmt_bind_param($stmt_select_datasiswa, "s", $nokartu_clean);
                    mysqli_stmt_execute($stmt_select_datasiswa);
                    $result_select_datasiswa = mysqli_stmt_get_result($stmt_select_datasiswa);

                    if ($row = mysqli_fetch_assoc($result_select_datasiswa)) {
                        $nama_siswa = $row['nama'];
                        $nis_siswa = $row['nis'];
                        $noReg_siswa = $row['nis'];
                        $foto_siswa = @$row['foto'] ? $row['foto'] : "default.jpg";
                        $info_siswa = @$row['kelas'];
                        $keterangan_siswa = @$row['keterangan'];
                        $tglakhir_siswa = @$row['tglakhir'];
                        $kode_siswa = @$row['kode'];
                    } else {
                        $nama_siswa = "";
                        $nis_siswa = "";
                    }

                    // Tutup prepared statement
                    mysqli_stmt_close($stmt_select_datasiswa);

                    $nama = @$nama_gtk ? $nama_gtk : $nama_siswa;
                    $ni = @$nip_gtk ? $nip_gtk : $nis_siswa;
                    $noReg = @$nip_gtk ? $nip_gtk : $nis_siswa;
                    $foto = @$foto_gtk ? $foto_gtk : (@$foto_siswa ? $foto_siswa : 'default.jpg');
                    $info = @$info_gtk ? $info_gtk : @$info_siswa;
                    $keterangan = @$keterangan_gtk ? $keterangan_gtk : @$keterangan_siswa;
                    $tglakhir = @$tglakhir_gtk ? $tglakhir_gtk : @$tglakhir_siswa;
                    $kode = @$kode_gtk ? $kode_gtk : @$kode_siswa;

                    // jika ada yang ditemukan
                    // Lihat kode device

                    if ($nama_gtk != "" || $nama_siswa != "") {
                        // jika device untuk GERBANG (GATE)
                        if ($hasil_kode_device == "GATE" || $hasil_kode_device == "GATETL") {

                            // jika bukan hari libur, Lanjut
                            if ($harilibur == false) {
                                //autoset mode absen
                                if ($jam < $wta && $jam >= $wa) {
                                    $mode = 1;
                                } elseif ($jam >= $wtp && $jam < $wp) {
                                    $mode = 2;
                                } else {
                                    $mode = 0;
                                }

                                // update auto set mode
                                $query_update_statusnya = "UPDATE statusnya SET mode = ?";
                                $stmt_update_statusnya = mysqli_stmt_init($konek);
                                mysqli_stmt_prepare($stmt_update_statusnya, $query_update_statusnya);
                                mysqli_stmt_bind_param($stmt_update_statusnya, "s", $mode);
                                mysqli_stmt_execute($stmt_update_statusnya);

                                // Tutup prepared statement
                                mysqli_stmt_close($stmt_update_statusnya);

                                // jika data guru yang ditemukan
                                if ($nama_gtk) {
                                    // echo "ditemukan: " . $nama_gtk;
                                    // ambil datanya
                                    $nama = $nama_gtk;

                                    // echo("telat: " . $t_waktu_telat_guru . ", poin:" .$poin_guru . ", no:" . $nokartu_clean); die;

                                    // jika tanggal ijin/disposisi telah berlalu/berakhir
                                    if ($tglakhir < $tanggal) {
                                        // Keterangan dan tanggal dikosongkan
                                        $keterangan = "";
                                        $query_update_dataguru = "UPDATE dataguru SET keterangan = NULL, tglawaldispo = NULL, tglakhirdispo = NULL, docdis = NULL WHERE nokartu = ?";
                                        $stmt_update_dataguru = mysqli_stmt_init($konek);
                                        mysqli_stmt_prepare(
                                            $stmt_update_dataguru,
                                            $query_update_dataguru
                                        );
                                        mysqli_stmt_bind_param(
                                            $stmt_update_dataguru,
                                            "s",
                                            $nokartu_clean
                                        );
                                        mysqli_stmt_execute($stmt_update_dataguru);

                                        // Tutup prepared statement
                                        mysqli_stmt_close($stmt_update_dataguru);

                                        // echo ("masuk update keterangan" . $nokartu_clean . "," . $waktukosong . "," . $nama); die;
                                    }

                                    // jika yang ditemukan adalah data siswa
                                } elseif ($nama_siswa) {
                                    // echo "detemukan: " . $nama_siswa;
                                    // ambil datanya
                                    $nama = $nama_siswa;
                                    // $noReg = $data_siswa['nis'];
                                    // $foto = $data_siswa['foto'];
                                    // $info = $data_siswa['kelas'];
                                    // $keterangan = $data_siswa['keterangan'];
                                    // $tglakhir = $data_siswa['tglakhir'];
                                    // $kode = $data_siswa['kode'];
                                    // echo($nama . $foto . $info . $keterangan . $tglakhir);

                                    // Jika tanggal ijin/disposisi telah berlalu/berakhir
                                    if ($tglakhir < $tanggal) {
                                        // Keterangan dan tanggal dikosongkan
                                        $keterangan = "";
                                        $query_update_datasiswa = "UPDATE datasiswa SET keterangan = NULL, tglawal = NULL, tglakhir = NULL, fotodoc = NULL WHERE nokartu = ?";
                                        $stmt_update_datasiswa = mysqli_stmt_init($konek);
                                        mysqli_stmt_prepare($stmt_update_datasiswa, $query_update_datasiswa);
                                        mysqli_stmt_bind_param(
                                            $stmt_update_datasiswa,
                                            "s",
                                            $nokartu_clean
                                        );
                                        mysqli_stmt_execute($stmt_update_datasiswa);

                                        // Tutup prepared statement
                                        mysqli_stmt_close($stmt_update_datasiswa);
                                    }

                                    // Ambil total waktu telat (t_waktu_telat) dan poin dari datasiswa
                                    $query_select_datasiswa = "SELECT t_waktu_telat, poin FROM datasiswa WHERE nokartu = ?";
                                    $stmt_select_datasiswa = mysqli_stmt_init($konek);
                                    mysqli_stmt_prepare($stmt_select_datasiswa, $query_select_datasiswa);
                                    mysqli_stmt_bind_param(
                                        $stmt_select_datasiswa,
                                        "s",
                                        $nokartu_clean
                                    );
                                    mysqli_stmt_execute($stmt_select_datasiswa);
                                    $result_select_datasiswa = mysqli_stmt_get_result($stmt_select_datasiswa);

                                    if ($row = mysqli_fetch_assoc($result_select_datasiswa)) {
                                        $t_waktu_telat_siswa = $row['t_waktu_telat'];
                                        $poin_siswa = $row['poin'];
                                    } else {
                                        $t_waktu_telat_siswa = "";
                                        $poin_siswa = "";
                                    }

                                    // Tutup prepared statement
                                    mysqli_stmt_close($stmt_select_datasiswa);
                                }

                                // Jika yang masuk adalah GATETL (TERLAMBAT)
                                $datetime_masuk = DateTime::createFromFormat('H:i:s', @$waktumasuk);
                                $datetime_sekarang = DateTime::createFromFormat('H:i:s', @$jam);

                                if ($hasil_kode_device == "GATETL" && $datetime_masuk < $datetime_sekarang) {
                                    // cek nokartu di data presensi terlambat?
                                    $query_select_dataketerlambatan = "SELECT nokartu FROM daftarketerlambatan WHERE nokartu = ? AND tanggal = ?";
                                    $stmt_select_dataketerlambatan = mysqli_stmt_init($konek);
                                    mysqli_stmt_prepare($stmt_select_dataketerlambatan, $query_select_dataketerlambatan);
                                    mysqli_stmt_bind_param(
                                        $stmt_select_dataketerlambatan,
                                        "ss",
                                        $nokartu_clean,
                                        $tanggal
                                    );
                                    mysqli_stmt_execute($stmt_select_dataketerlambatan);
                                    $result_select_dataketerlambatan = mysqli_stmt_get_result($stmt_select_dataketerlambatan);

                                    $data = "";
                                    while ($row = mysqli_fetch_assoc($result_select_dataketerlambatan)) {
                                        $data = $row['nokartu'];
                                    }

                                    // Tutup prepared statement
                                    mysqli_stmt_close($stmt_select_dataketerlambatan);

                                    $hasilCekKeterlambatan = @$data;

                                    if (!$hasilCekKeterlambatan) {
                                        // buat data di database datapresensi
                                        /* 
                                        INSERT INTO `daftarketerlambatan`(`id`, `nokartu`, `tanggal`, `nis`, `nama`, `kelas`, `tingkat`, `jurusan`, `waktuterlambat`, `jumlahwaktut`, `timestamp`) VALUES ('[value-1]','[value-2]','[value-3]','[value-4]','[value-5]','[value-6]','[value-7]','[value-8]','[value-9]','[value-10]','[value-11]')
                                        */

                                        // Pisahkan string berdasarkan spasi
                                        $parts = explode(' ', $info);

                                        // Ambil bagian-bagian yang diperlukan
                                        $kodetingkat = $parts[0];
                                        $kodejurusan = $parts[1];

                                        $selisihWaktu = selisih($jam, $waktumasuk);

                                        $query_insert_datapresensi = "INSERT INTO daftarketerlambatan (`nokartu`, `tanggal`, `nis`, `nama`, `kelas`, `tingkat`, `jurusan`, `waktu`, `waktuterlambat`, `timestamp`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                                        $stmt_insert_datapresensi = mysqli_stmt_init($konek);
                                        mysqli_stmt_prepare($stmt_insert_datapresensi, $query_insert_datapresensi);
                                        mysqli_stmt_bind_param(
                                            $stmt_insert_datapresensi,
                                            "ssssssssss",
                                            $nokartu_clean, // ok
                                            $tanggal, // ok
                                            $noReg, // ok
                                            $nama, // ok
                                            $info, // ok
                                            $kodetingkat, // ok
                                            $kodejurusan, // ok
                                            $jam,   // ok
                                            $selisihWaktu, // ok
                                            $timestamp // ok
                                        );
                                        mysqli_stmt_execute($stmt_insert_datapresensi);

                                        // Tutup prepared statement
                                        mysqli_stmt_close($stmt_insert_datapresensi);

                                        // Berhasil Memasukkan Data Presensi MAsuk
                                        $pesan = "BMPM";
                                    }
                                }

                                // END GATETL (TERLAMBAT)

                                if ($mode == 1) {
                                    // cek nokartu di data presensi
                                    $query_select_datapresensi = "SELECT nokartu FROM datapresensi WHERE nokartu = ? AND tanggal = ?";
                                    $stmt_select_datapresensi = mysqli_stmt_init($konek);
                                    mysqli_stmt_prepare($stmt_select_datapresensi, $query_select_datapresensi);
                                    mysqli_stmt_bind_param(
                                        $stmt_select_datapresensi,
                                        "ss",
                                        $nokartu_clean,
                                        $tanggal
                                    );
                                    mysqli_stmt_execute($stmt_select_datapresensi);
                                    $result_select_datapresensi = mysqli_stmt_get_result($stmt_select_datapresensi);

                                    $data = "";
                                    while ($row = mysqli_fetch_assoc($result_select_datapresensi)) {
                                        $data = $row['nokartu'];
                                    }

                                    // Tutup prepared statement
                                    mysqli_stmt_close($stmt_select_datapresensi);

                                    $hasilCek = @$data;

                                    if (!$hasilCek) {
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

                                                $query_update_datasiswa = "UPDATE datasiswa SET t_waktu_telat = ?, poin = ? WHERE nokartu = ?";
                                                $stmt_update_datasiswa = mysqli_stmt_init($konek);
                                                mysqli_stmt_prepare($stmt_update_datasiswa, $query_update_datasiswa);
                                                mysqli_stmt_bind_param(
                                                    $stmt_update_datasiswa,
                                                    "sss",
                                                    $t_waktu_telat_siswa,
                                                    $poin_siswa,
                                                    $nokartu_clean
                                                );
                                                mysqli_stmt_execute($stmt_update_datasiswa);

                                                // Tutup prepared statement
                                                mysqli_stmt_close($stmt_update_datasiswa);
                                            } elseif ($t_waktu_telat_guru) {
                                                // hitung total waktu telat dan poin
                                                $hasil = jumlahkanwaktu($t_waktu_telat_guru, $selisihWaktu);
                                                $t_waktu_telat_guru = $hasil;
                                                $poin_guru = (int)$poin_guru + 1;

                                                // update total waktu telat dan poin
                                                $query_update_dataguru = "UPDATE dataguru SET t_waktu_telat = ?, poin = ? WHERE nokartu = ?";
                                                $stmt_update_dataguru = mysqli_stmt_init($konek);
                                                mysqli_stmt_prepare($stmt_update_dataguru, $query_update_dataguru);
                                                mysqli_stmt_bind_param(
                                                    $stmt_update_dataguru,
                                                    "sss",
                                                    $t_waktu_telat_guru,
                                                    $poin_guru,
                                                    $nokartu_clean
                                                );
                                                mysqli_stmt_execute($stmt_update_dataguru);

                                                // Tutup prepared statement
                                                mysqli_stmt_close($stmt_update_dataguru);
                                            }
                                        }

                                        $data_waktupulang = "00:00:00";
                                        $data_ketpulang = "-";
                                        $data_btime = "00:00:00";

                                        // buat data di database datapresensi
                                        $query_insert_datapresensi = "INSERT INTO datapresensi (nokartu, nama, nomorinduk, info, foto, waktumasuk, ketmasuk, a_time, waktupulang, ketpulang, b_time, tanggal, keterangan, updated_at, kode, infodevice) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                                        $stmt_insert_datapresensi = mysqli_stmt_init($konek);
                                        mysqli_stmt_prepare($stmt_insert_datapresensi, $query_insert_datapresensi);
                                        mysqli_stmt_bind_param(
                                            $stmt_insert_datapresensi,
                                            "ssssssssssssssss",
                                            $nokartu_clean,
                                            $nama,
                                            $noReg,
                                            $info,
                                            $foto,
                                            $jam,
                                            $ket,
                                            $selisihWaktu,
                                            $data_waktupulang,
                                            $data_ketpulang,
                                            $data_btime,
                                            $tanggal,
                                            $keterangan,
                                            $timestamp,
                                            $kode,
                                            $hasil_info_device
                                        );
                                        mysqli_stmt_execute($stmt_insert_datapresensi);

                                        // Tutup prepared statement
                                        mysqli_stmt_close($stmt_insert_datapresensi);

                                        // Berhasil Memasukkan Data Presensi MAsuk
                                        $pesan = "BMPM";
                                    } else {
                                        // jika sudah absen, nokartu ada di datapresensi
                                        if ($keterangan) {
                                            $pesan = "MMMM";
                                        } else {
                                            $pesan = "SMPM";
                                        }
                                    }
                                } elseif ($mode == 2) {
                                    if (!$keterangan) {
                                        if ($jam <= $waktupulang) {
                                            $ket = "PA";
                                            $pesan = "PLAW";
                                            // menghitung selisih jam
                                            $selisihWaktu = selisih($jam, $waktupulang);
                                        } else {
                                            $ket = "PLG";
                                            $pesan = "PPBH";

                                            // menghitung selisih jam
                                            $selisihWaktu = selisih($waktupulang, $jam);
                                        }
                                    } else {
                                        $pesan = "PPPP";
                                    }

                                    // baca data nokartu di database datapresensi
                                    $query_select_datapresensi = "SELECT nokartu FROM datapresensi WHERE nokartu = ? AND tanggal = ?";
                                    $stmt_select_datapresensi = mysqli_stmt_init($konek);
                                    mysqli_stmt_prepare($stmt_select_datapresensi, $query_select_datapresensi);
                                    mysqli_stmt_bind_param(
                                        $stmt_select_datapresensi,
                                        "ss",
                                        $nokartu_clean,
                                        $tanggal
                                    );
                                    mysqli_stmt_execute($stmt_select_datapresensi);
                                    $result_select_datapresensi = mysqli_stmt_get_result($stmt_select_datapresensi);

                                    $data = "";
                                    if ($row = mysqli_fetch_assoc($result_select_datapresensi)) {
                                        $data = $row['nokartu'];
                                    }

                                    // Tutup prepared statement
                                    mysqli_stmt_close($stmt_select_datapresensi);

                                    if (!$data) {
                                        // Jika tidak ada data 'nokartu' di 'datapresensi', buat data baru
                                        // $query_insert_datapresensi = "INSERT INTO datapresensi (nokartu, nama, nomorinduk, info, foto, waktupulang, ketpulang, b_time, tanggal, keterangan, updated_at, kode) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                                        // $stmt_insert_datapresensi = mysqli_stmt_init($konek);
                                        // mysqli_stmt_prepare($stmt_insert_datapresensi, $query_insert_datapresensi);
                                        // mysqli_stmt_bind_param(
                                        //     $stmt_insert_datapresensi,
                                        //     "ssssssssssss",
                                        //     $nokartu_clean,
                                        //     $nama,
                                        //     $noReg,
                                        //     $info,
                                        //     $foto,
                                        //     $jam,
                                        //     $ket,
                                        //     $selisihWaktu,
                                        //     $tanggal,
                                        //     $keterangan,
                                        //     $timestamp,
                                        //     $kode
                                        // );
                                        // $insert = mysqli_stmt_execute($stmt_insert_datapresensi);

                                        // // Tutup prepared statement
                                        // mysqli_stmt_close($stmt_insert_datapresensi);


                                        $data_waktumasuk = "00:00:00";
                                        $data_ketmasuk = "-";
                                        $data_atime = "00:00:00";

                                        // buat data di database datapresensi
                                        $query_insert_datapresensi = "INSERT INTO datapresensi (nokartu, nama, nomorinduk, info, foto, waktumasuk, ketmasuk, a_time, waktupulang, ketpulang, b_time, tanggal, keterangan, updated_at, kode, infodevice2) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                                        $stmt_insert_datapresensi = mysqli_stmt_init($konek);
                                        mysqli_stmt_prepare($stmt_insert_datapresensi, $query_insert_datapresensi);
                                        mysqli_stmt_bind_param(
                                            $stmt_insert_datapresensi,
                                            "ssssssssssssssss",
                                            $nokartu_clean,
                                            $nama,
                                            $noReg,
                                            $info,
                                            $foto,
                                            $data_waktumasuk,
                                            $data_ketmasuk,
                                            $data_atime,
                                            $jam,
                                            $ket,
                                            $selisihWaktu,
                                            $tanggal,
                                            $keterangan,
                                            $timestamp,
                                            $kode,
                                            $hasil_info_device
                                        );
                                        mysqli_stmt_execute($stmt_insert_datapresensi);

                                        // Tutup prepared statement
                                        mysqli_stmt_close($stmt_insert_datapresensi);
                                    } else {
                                        // Jika sudah ada data 'nokartu', update data pulang
                                        $query_update_datapresensi = "UPDATE datapresensi SET waktupulang = ?, ketpulang = ?, b_time = ?, keterangan = ?, updated_at = ?, infodevice2 = ? WHERE nokartu = ? AND tanggal = ?";
                                        $stmt_update_datapresensi = mysqli_stmt_init($konek);
                                        mysqli_stmt_prepare($stmt_update_datapresensi, $query_update_datapresensi);
                                        mysqli_stmt_bind_param(
                                            $stmt_update_datapresensi,
                                            "ssssssss",
                                            $jam,
                                            $ket,
                                            $selisihWaktu,
                                            $keterangan,
                                            $timestamp,
                                            $hasil_info_device,
                                            $nokartu_clean,
                                            $tanggal
                                        );
                                        $update = mysqli_stmt_execute($stmt_update_datapresensi);

                                        // Tutup prepared statement
                                        mysqli_stmt_close($stmt_update_datapresensi);

                                        // Jika pembaruan berhasil
                                        if ($update) {
                                            $pesan = "SAPP";
                                        } else {
                                            // Jika pembaruan gagal
                                            $pesan = "Error: " . mysqli_error($konek);
                                            $pesan = "505";
                                        }
                                    }
                                } elseif ($mode == 0) {
                                    $pesan = "TBPS";
                                }
                            } else {
                                $nokartu_clean = isset($nokartu_clean) ? $nokartu_clean : "";
                                $pesan = isset($pesan) ? $pesan : "";
                                $nama = isset($nama) ? $nama : "";
                                $pesan = "HLTM";
                            }
                        } elseif ($hasil_kode_device === "MASJID") {

                            // ==============================
                            // DEFINISI BATAS WAKTU
                            // ==============================
                            $batasMulai_Dzuhur    = "11:45:00";
                            $batasSelesai_Dzuhur = "14:30:00";
                            $batasMulai_Ashar    = "14:30:01";
                            $batasSelesai_Ashar  = "17:00:00";

                            $fase = null;
                            $now  = strtotime($jam);

                            if ($now >= strtotime($batasMulai_Dzuhur) && $now <= strtotime($batasSelesai_Dzuhur)) {
                                $fase = "DZUHUR";
                            } elseif ($now >= strtotime($batasMulai_Ashar) && $now <= strtotime($batasSelesai_Ashar)) {
                                $fase = "ASHAR";
                            }

                            if ($fase === null) {
                                $pesan = "TBPS"; // di luar waktu masjid
                            } else {

                                // ==============================
                                // CEK APAKAH FASE SUDAH ADA
                                // ==============================
                                $stmt = mysqli_prepare(
                                    $konek,
                                    "SELECT 1 FROM presensiEvent
             WHERE nokartu = ?
               AND tanggal = ?
               AND keterangan = ?
             LIMIT 1"
                                );
                                mysqli_stmt_bind_param(
                                    $stmt,
                                    "sss",
                                    $nokartu_clean,
                                    $tanggal,
                                    $fase
                                );
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);
                                $sudahAda = mysqli_fetch_assoc($result);
                                mysqli_stmt_close($stmt);

                                if ($sudahAda) {

                                    // Sudah presensi DZUHUR / ASHAR
                                    $pesan = "SMPM";
                                } else {

                                    // ==============================
                                    // INSERT ROW BARU
                                    // ==============================
                                    $stmt = mysqli_prepare(
                                        $konek,
                                        "INSERT INTO presensiEvent
                 (nokartu, nis, ruang, mulai, jam, tanggal, keterangan)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
                                    );
                                    mysqli_stmt_bind_param(
                                        $stmt,
                                        "sssssss",
                                        $nokartu_clean,
                                        $ni,
                                        $hasil_info_device,
                                        $jam,
                                        $jam,
                                        $tanggal,
                                        $fase
                                    );
                                    mysqli_stmt_execute($stmt);
                                    mysqli_stmt_close($stmt);

                                    $pesan = "BMPE"; // Berhasil Presensi
                                }
                            }

                            // ==============================
                            // KODE LANJUTAN ANDA DI SINI
                            // ==============================
                        } elseif ($hasil_kode_device == "EVENT") {
                            // Ambil data
                            // Cek apakah sebelumnya telah melakukan Presensi?

                            // Membuat prepared statement
                            $stmt = mysqli_stmt_init($konek);
                            if (mysqli_stmt_prepare($stmt, "SELECT * FROM `presensiEvent` WHERE `nokartu` = ? AND `tanggal` = ? ORDER BY `timestamp` DESC LIMIT 1")) {
                                mysqli_stmt_bind_param(
                                    $stmt,
                                    "ss",
                                    $nokartu_clean,
                                    $tanggal
                                );
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);

                                // Memeriksa apakah ada data yang cocok
                                if ($row = mysqli_fetch_assoc($result)) {
                                    $waktu_sekarang = strtotime($jam);
                                    $timestamp_event = $row['timestamp'];

                                    // Cek APakah `mulai` dan `selesai` sudah terisi?
                                    if ($row['mulai'] && $row['selesai']) {
                                        // jika semua sudah terisi semua (`mulai` dan `selesai`)
                                        // cek waktu
                                        $waktu_selesai = strtotime($row['selesai']);

                                        // Hitung selisih waktu dalam detik
                                        $selisih_waktu = $waktu_sekarang - $waktu_selesai;

                                        if ($selisih_waktu > 300) {
                                            // Jika sudah 5 menit maka insert data (row) baru
                                            $stmt = mysqli_stmt_init($konek);
                                            if (mysqli_stmt_prepare($stmt, "INSERT INTO presensiEvent (nokartu, nis, ruang, mulai, jam, tanggal, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?)")) {
                                                mysqli_stmt_bind_param(
                                                    $stmt,
                                                    "sssssss",
                                                    $nokartu_clean,
                                                    $ni,
                                                    $hasil_info_device,
                                                    $jam,
                                                    $jam,
                                                    $tanggal,
                                                    $hasil_kode_device
                                                );
                                                mysqli_stmt_execute($stmt);

                                                $pesan = "BPEB";
                                            } else {
                                                $pesan = "545";
                                            }
                                        } else {
                                            $pesan = "510";
                                        }
                                    } else {
                                        // Data sudah ada, tapi belum `selesai`, maka lakukan UPDATE
                                        $waktu_mulai = strtotime($row['mulai']);
                                        $waktu_tanggal = strtotime($row['tanggal']);

                                        // Hitung selisih waktu dalam detik
                                        $selisih_waktu = $waktu_sekarang - $waktu_mulai;

                                        if ($selisih_waktu > 120) {
                                            if (strtotime($tanggal) == ($waktu_tanggal)) { // 300 detik = 5 menit
                                                $stmt = mysqli_stmt_init($konek);
                                                if (mysqli_stmt_prepare($stmt, "UPDATE presensiEvent SET selesai = ? WHERE nokartu = ? AND `timestamp` = ?")) {
                                                    mysqli_stmt_bind_param(
                                                        $stmt,
                                                        "sss",
                                                        $jam,
                                                        $nokartu_clean,
                                                        $timestamp_event
                                                    );
                                                    mysqli_stmt_execute($stmt);

                                                    $pesan = "BPSE";
                                                } else {
                                                    $pesan = "555";
                                                }
                                            } else {
                                                $stmt = mysqli_stmt_init($konek);
                                                if (mysqli_stmt_prepare($stmt, "INSERT INTO presensiEvent (nokartu, nis, ruang, selesai, jam, tanggal, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?)")) {
                                                    mysqli_stmt_bind_param(
                                                        $stmt,
                                                        "sssssss",
                                                        $nokartu_clean,
                                                        $ni,
                                                        $hasil_info_device,
                                                        $jam,
                                                        $jam,
                                                        $tanggal,
                                                        $hasil_kode_device
                                                    );
                                                    mysqli_stmt_execute($stmt);

                                                    $pesan = "BPEB";
                                                } else {
                                                    $pesan = "545";
                                                }
                                            }
                                        } else {
                                            $pesan = "510";
                                        }
                                    }
                                } else {
                                    // Data belum ada, maka lakukan INSERT
                                    $stmt = mysqli_stmt_init($konek);
                                    if (mysqli_stmt_prepare($stmt, "INSERT INTO presensiEvent (nokartu, nis, ruang, mulai, jam, tanggal, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?)")) {
                                        mysqli_stmt_bind_param(
                                            $stmt,
                                            "sssssss",
                                            $nokartu_clean,
                                            $ni,
                                            $hasil_info_device,
                                            $jam,
                                            $jam,
                                            $tanggal,
                                            $hasil_kode_device
                                        );
                                        mysqli_stmt_execute($stmt);

                                        $pesan = "BMPE";
                                    } else {
                                        $pesan = "545";
                                    }
                                }

                                // Tutup prepared statement
                                mysqli_stmt_close($stmt);
                            } else {
                                $pesan = "505";
                            }
                        } elseif ($hasil_kode_device == "IJIN") {
                            // Ambil data
                            // Cek apakah sebelumnya telah melakukan Presensi?

                            // Membuat prepared statement
                            $stmt = mysqli_stmt_init($konek);
                            if (mysqli_stmt_prepare($stmt, "SELECT * FROM `daftarijin` WHERE `nokartu` = ? AND `tanggalijin` = ? ORDER BY `timestamp` DESC LIMIT 1")) {
                                mysqli_stmt_bind_param(
                                    $stmt,
                                    "ss",
                                    $nokartu_clean,
                                    $tanggal
                                );
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);

                                // Memeriksa apakah ada data yang cocok
                                if ($row = mysqli_fetch_assoc($result)) {
                                    $waktu_sekarang = strtotime($jam);
                                    $timestamp_event = $row['timestamp'];

                                    // Cek APakah `mulai` dan `selesai` sudah terisi?
                                    if ($row['jam_keluar'] && $row['jam_kembali']) {
                                        // jika semua sudah terisi semua (`mulai` dan `selesai`)
                                        // cek waktu
                                        $waktu_selesai = strtotime($row['jam_kembali']);

                                        // Hitung selisih waktu dalam detik
                                        $selisih_waktu = $waktu_sekarang - $waktu_selesai;

                                        if ($selisih_waktu > 300) {
                                            // Jika sudah 5 menit maka insert data (row) baru
                                            $stmt = mysqli_stmt_init($konek);
                                            if (mysqli_stmt_prepare($stmt, "INSERT INTO `daftarijin`(`nokartu`, `nis`, `nama`, `info`, `jam_keluar`, `tanggalijin`, `kode`) VALUES (?, ?, ?, ?, ?, ?, ?)")) {
                                                mysqli_stmt_bind_param(
                                                    $stmt,
                                                    "sssssss",
                                                    $nokartu_clean,
                                                    $ni,
                                                    $nama,
                                                    $hasil_info_device,
                                                    $jam,
                                                    $tanggal,
                                                    $hasil_kode_device
                                                );
                                                mysqli_stmt_execute($stmt);

                                                $pesan = "BMIJ";
                                            } else {
                                                $pesan = "545";
                                            }
                                        } else {
                                            $pesan = "510";
                                        }
                                    } else {
                                        // Data sudah ada, tapi belum `selesai`, maka lakukan UPDATE
                                        $waktu_mulai = strtotime($row['jam_keluar']);
                                        $waktu_tanggal = strtotime($row['tanggalijin']);

                                        // Hitung selisih waktu dalam detik
                                        $selisih_waktu = $waktu_sekarang - $waktu_mulai;

                                        if ($selisih_waktu > 300) {
                                            if (strtotime($tanggal) == ($waktu_tanggal)) {
                                                $stmt = mysqli_stmt_init($konek);
                                                if (mysqli_stmt_prepare($stmt, "UPDATE daftarijin SET jam_kembali = ? WHERE nokartu = ? AND `timestamp` = ?")) {
                                                    mysqli_stmt_bind_param(
                                                        $stmt,
                                                        "sss",
                                                        $jam,
                                                        $nokartu_clean,
                                                        $timestamp_event
                                                    );
                                                    mysqli_stmt_execute($stmt);

                                                    $pesan = "BMIJ";
                                                } else {
                                                    $pesan = "555";
                                                }
                                            } else {
                                                $stmt = mysqli_stmt_init($konek);
                                                if (mysqli_stmt_prepare($stmt, "INSERT INTO `daftarijin`(`nokartu`, `nis`, `nama`, `info`, `jam_keluar`, `tanggalijin`, `kode`) VALUES (?, ?, ?, ?, ?, ?, ?)")) {
                                                    mysqli_stmt_bind_param(
                                                        $stmt,
                                                        "sssssss",
                                                        $nokartu_clean,
                                                        $ni,
                                                        $nama,
                                                        $hasil_info_device,
                                                        $jam,
                                                        $tanggal,
                                                        $hasil_kode_device
                                                    );
                                                    mysqli_stmt_execute($stmt);

                                                    $pesan = "BMIJ";
                                                } else {
                                                    $pesan = "545";
                                                }
                                            }
                                        } else {
                                            $pesan = "510";
                                        }
                                    }
                                } else {
                                    // Data belum ada, maka lakukan INSERT
                                    $stmt = mysqli_stmt_init($konek);
                                    if (mysqli_stmt_prepare($stmt, "INSERT INTO `daftarijin`(`nokartu`, `nis`, `nama`, `info`, `jam_keluar`, `tanggalijin`, `kode`) VALUES (?, ?, ?, ?, ?, ?, ?)")) {
                                        mysqli_stmt_bind_param(
                                            $stmt,
                                            "sssssss",
                                            $nokartu_clean,
                                            $ni,
                                            $nama,
                                            $hasil_info_device,
                                            $jam,
                                            $tanggal,
                                            $hasil_kode_device
                                        );
                                        mysqli_stmt_execute($stmt);

                                        $pesan = "BMIJ";
                                    } else {
                                        $pesan = "545";
                                    }
                                }

                                // Tutup prepared statement
                                mysqli_stmt_close($stmt);
                            } else {
                                $pesan = "505";
                            }
                        } else {
                            // tambah logic preensi kelas
                            // 0. Cek ID (terdftar)
                            // 1. Dapatkan Ruangan
                            // 2. Dapatkan Jam ke
                            // 3. Ambil Jadwal
                            // 4. Ambil info jamke
                            // 5. Ambil nick guru
                            // 6. catat timestamp sekarang

                            // Prepare the SELECT statement for jadwalkbm
                            $statement_select_jadwalkbm = mysqli_stmt_init($konek);
                            $query_select_jadwalkbm = "SELECT info, kelas, kelompok, tingkat, jur, nick, tanggal, mulai_jamke, sampai_jamke FROM jadwalkbm WHERE ruangan = ? AND tanggal = ?";
                            mysqli_stmt_prepare($statement_select_jadwalkbm, $query_select_jadwalkbm);
                            mysqli_stmt_bind_param(
                                $statement_select_jadwalkbm,
                                "ss",
                                $hasil_kode_device,
                                $tanggal
                            );

                            mysqli_stmt_execute($statement_select_jadwalkbm);
                            $result_select_jadwalkbm = mysqli_stmt_get_result($statement_select_jadwalkbm);

                            if ($row = mysqli_fetch_assoc($result_select_jadwalkbm)) {
                                $jadwal_ruangan = $row['info'];
                                $jadwal_kelas = $row['kelas'];
                                $jadwal_kelompok = $row['kelompok'];
                                $jadwal_tingkat = $row['tingkat'];
                                $jadwal_jur = $row['jur'];
                                $jadwal_nick = $row['nick'];
                                $jadwal_taggal = $row['tanggal'];
                                $jadwal_mulai_jamke = $row['mulai_jamke'];
                                $jadwal_sampai_jamke = $row['sampai_jamke'];

                                // Prepare the SELECT statement for dataguru
                                $statement_select_dataguru = mysqli_stmt_init($konek);
                                $query_select_dataguru = "SELECT nama FROM dataguru WHERE nick = ?";
                                mysqli_stmt_prepare($statement_select_dataguru, $query_select_dataguru);
                                mysqli_stmt_bind_param(
                                    $statement_select_dataguru,
                                    "s",
                                    $jadwal_nick
                                );

                                mysqli_stmt_execute($statement_select_dataguru);
                                $result_select_dataguru = mysqli_stmt_get_result($statement_select_dataguru);
                                $nama_guru = mysqli_fetch_assoc($result_select_dataguru)['nama'];
                                // Close the SELECT statement_select_dataguru statement
                                mysqli_stmt_close($statement_select_dataguru);
                                echo "nama guru: $nama_guru\n";

                                // Prepare the INSERT statement for presensikelas
                                $statement_insert_presensikelas = mysqli_stmt_init($konek);
                                $query_insert_presensikelas = "INSERT INTO presensikelas (nokartu, nis, nama, ruangan, kelas, mulai_jamke, sampai_jamke, nick_guru, nama_guru, tanggal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                                mysqli_stmt_prepare($statement_insert_presensikelas, $query_insert_presensikelas);
                                mysqli_stmt_bind_param(
                                    $statement_insert_presensikelas,
                                    "ssssssssss",
                                    $nokartu_clean,
                                    $ni,
                                    $nama,
                                    $hasil_kode_device,
                                    $jadwal_kelas,
                                    $jadwal_mulai_jamke,
                                    $jadwal_sampai_jamke,
                                    $jadwal_nick,
                                    $nama_guru,
                                    $tanggal
                                );

                                mysqli_stmt_execute($statement_insert_presensikelas);
                                mysqli_stmt_close($statement_insert_presensikelas);

                                // Presensi Kelas Berhasil Dilakukan
                                $pesan = "PKBD";
                            } else {
                                $query_select_jadwalkbm = "SELECT info FROM jadwalkbm WHERE ruangan = ? LIMIT 1";
                                $statement_select_jadwalkbm = mysqli_stmt_init($konek);
                                mysqli_stmt_prepare($statement_select_jadwalkbm, $query_select_jadwalkbm);
                                mysqli_stmt_bind_param(
                                    $statement_select_jadwalkbm,
                                    "s",
                                    $hasil_kode_device
                                );

                                mysqli_stmt_execute($statement_select_jadwalkbm);
                                $row = mysqli_fetch_assoc(mysqli_stmt_get_result($statement_select_jadwalkbm));
                                $jadwal_ruangan = $row['info'];

                                // Close the SELECT jadwalkbm statement
                                mysqli_stmt_close($statement_select_jadwalkbm);

                                $pesan = "TAKS";
                            }
                        }
                    } else {
                        // echo "data tidak ditemukan";
                        // jika nokartu belum terdaftar
                        $nama = "";
                        $pesan = "IDTT";
                        $sub_pesan = $nokartu_clean;
                    }
                } else {
                    $pesan = "404";
                }
            } else {
                // AKSES DITOLAK DEVICE TIDAK SESUAI
                $pesan = "407";
            }

            // Tutup prepared statement
            mysqli_stmt_close($stmt_select_reg_device);
        } else {
            // ID CHIP TIDAK TERDAFTAR
            $pesan = "406";
        }
    } else {
        // TIDAK DIBERI AKSES
        $pesan = "405";
    }
} else {
    $pesan = "404";
}

// beri feedback
$info_kode_array = array(
    "404" => "ERROR!--Method Not Allowed.",
    "405" => "ERROR!--REQ TIDAK DI IJINKAN",
    "406" => "ERROR!--CHIP TIDAK TERDAFTAR",
    "407" => "ERROR!--DEVICE TIDAK SESUAI",
    "505" => "ERROR!--DATABASE SERVER: ",
    "510" =>  @$nama . "--TERCATAT! " . @$selisih_waktu . " dtk lalu",
    "545" => "ERROR!--DATABASE SERVER INSERT: ",
    "555" => "ERROR!--DATABASE SERVER UPDTAE: ",
    // "IDTT" => "$sub_pesan--Kartu ID ini belum terdaftar",
    "IDTT" => "Kartu ID ini--belum terdaftar",
    "HLTM" => @$nama . "--" . "Hari ini Libur",
    "TBPS" => @$nama . "--" . "Tidak bisa melakukan presensi sekarang.",
    "SAPP" => @$nama . "--" . "Sudah melakukan presensi pulang",
    "PLAW" => @$nama . "--" . "Pulang lebih awal",
    "PPBH" => @$nama . "--" . "Berhasil Presensi Pulang",
    "PPPP" => @$nama . "--" . @$keterangan,
    "SMPM" => @$nama . "--" . "Anda Sudah melakukan Presensi",
    "MMMM" => @$nama . "--" . @$keterangan,
    "BMPM" => @$nama . "--" . "Berhasil Presensi",
    "PKBD" => @$nama . "--" . "Berhasil Presensi Kelas: " . @$jadwal_ruangan,
    "TAKS" => @$nama . "--" . "Tidak Ada KBM di Kelas: " . @$hasil_info_device,
    "BMPE" => @$nama . "--" . "Berhasil Presensi -Mulai-",
    "BPSE" => @$nama . "--" . "Berhasil Presensi -Selesai-",
    "BPEB" => @$nama . "--" . "Berhasil Presensi -Mulai Baru-",
    "BMIJ" => @$nama . "--" . "Ijin Berhasil",
);

$array = array(
    "0" => array(
        "id" => @$idchip_clean,
        "nodevice" => @$hasil_nodevice,
        "message" => "$pesan",
        "info" => "$info_kode_array[$pesan]",
        "nokartu" => @$nokartu_clean
    )
);

// encode array to json
$json = json_encode(array('respon' => $array));
print_r($json);

$nokartu_clean = "";
$nodevice = "";
$idchip_clean = "";
$array = array();

// =================================================================
$ip_a = isset($_GET['ipa']) ? $_GET['ipa'] : null;

// if (!isSafeInput2($ip_a)) {
//     die("[0x01] Program dihentikan karena karakter mencurigakan ditemukan.");
// }

if (!$ip_a) {
    $clientIP = $_SERVER['REMOTE_ADDR'];
} else {
    $clientIP = $ip_a;
}

$requestUrl = $_SERVER['REQUEST_URI'];

// Inisialisasi data
$data = [];

// Loop melalui semua parameter GET dan masukkan ke dalam "data"
foreach ($_GET as $key => $value) {
    $data[$key] = $value;
}

// Buat JSON
$response = [
    'data' => $data,
    'url' => $requestUrl,
];

$jsonResponse = json_encode($response);

// Prepare the INSERT statement to insert data into the 'tempreq' table
$timestamp = date('Y-m-d H:i:s');
// cek tabel data yang sama
$stmt = mysqli_stmt_init($konek);
if (mysqli_stmt_prepare($stmt, "SELECT * FROM tempreq WHERE timestamp = ? AND ip = ? AND req = ?")) {
    mysqli_stmt_bind_param(
        $stmt,
        "sss",
        $timestamp,
        $clientIP,
        $jsonResponse
    );
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Memeriksa apakah ada data yang cocok
    if ($row = mysqli_fetch_assoc($result)) {
    } else {
        $query_insert_tempreq = "INSERT INTO tempreq (ip, req, info, detail) VALUES (?, ?, ?, ?)";
        $stmt_insert_tempreq = mysqli_stmt_init($konek);
        mysqli_stmt_prepare($stmt_insert_tempreq, $query_insert_tempreq);
        mysqli_stmt_bind_param(
            $stmt_insert_tempreq,
            "ssss",
            $clientIP,
            $jsonResponse,
            $pesan,
            $detail
        );
        $detail = "[$hasil_kode_device] - " . $info_kode_array[$pesan];
        $insert = mysqli_stmt_execute($stmt_insert_tempreq);

        // Tutup prepared statement
        mysqli_stmt_close($stmt_insert_tempreq);
    }

    // Tutup prepared statement
    mysqli_stmt_close($stmt);
}

mysqli_close($konek);

$pesan = "";

// <!-- function -->
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

function isSafeInput($input)
{
    // Buat pola regex untuk karakter yang dianggap aman
    $safePattern = "/^[a-zA-Z0-9\s]+$/";

    // Periksa apakah input mengandung karakter mencurigakan
    if (preg_match($safePattern, $input)) {
        return true; // Input aman
    } else {
        return false; // Input mencurigakan
    }
}

function isSafeInput2($input)
{
    // Buat pola regex untuk karakter yang dianggap aman
    $safePattern = "/^[0-9\s.]+$/";

    // Periksa apakah input mengandung karakter mencurigakan
    if (preg_match($safePattern, $input)) {
        return true; // Input aman
    } else {
        return false; // Input mencurigakan
    }
}

<?php
//tanggal dan jam hari ini
date_default_timezone_set('Asia/Jakarta');
$tahun = date('Y');

function hariIndo($hariInggris)
{
    switch ($hariInggris) {
        case 'Sunday':
            return 'Minggu';
        case 'Monday':
            return 'Senin';
        case 'Tuesday':
            return 'Selasa';
        case 'Wednesday':
            return 'Rabu';
        case 'Thursday':
            return 'Kamis';
        case 'Friday':
            return 'Jumat';
        case 'Saturday':
            return 'Sabtu';
        default:
            return 'hari tidak valid';
    }
}

function hariSingkatIndo($hariInggris)
{
    switch ($hariInggris) {
        case 'Sunday':
            return 'Min';
        case 'Monday':
            return 'Sen';
        case 'Tuesday':
            return 'Sel';
        case 'Wednesday':
            return 'Rab';
        case 'Thursday':
            return 'Kam';
        case 'Friday':
            return 'Jum';
        case 'Saturday':
            return 'Sab';
        default:
            return 'invalid';
    }
}

$hariBahasaInggris = date('l');
$hariBahasaIndonesia = hariIndo($hariBahasaInggris);
$namaHari = $hariBahasaIndonesia;

function bulanIndo($bulanInggris)
{
    switch ($bulanInggris) {
        case 'January':
            return 'Januari';
        case 'February':
            return 'Februari';
        case 'March':
            return 'Maret';
        case 'April':
            return 'April';
        case 'May':
            return 'Mei';
        case 'June':
            return 'Juni';
        case 'July':
            return 'Juli';
        case 'August':
            return 'Agustus';
        case 'September':
            return 'September';
        case 'October':
            return 'Oktober';
        case 'November':
            return 'November';
        case 'December':
            return 'Desember';
        default:
            return 'bulan tidak valid';
    }
}

$bulanBahasaInggris = date('F');
$bulanBahasaIndonesia = bulanIndo($bulanBahasaInggris);

function bulansingkatindo($bulan)
{
    switch ($bulan) {
        case 'January':
            return 'Jan';
        case 'February':
            return 'Feb';
        case 'March':
            return 'Mar';
        case 'April':
            return 'Apr';
        case 'May':
            return 'Mei';
        case 'June':
            return 'Jun';
        case 'July':
            return 'Jul';
        case 'August':
            return 'Agt';
        case 'September':
            return 'Sep';
        case 'October':
            return 'Okt';
        case 'November':
            return 'Nov';
        case 'December':
            return 'Des';
        default:
            return 'bulan tidak valid';
    }
}

function nama_bulan_indo_dariangka($angka)
{
    switch ($angka) {
        case 1:
            return 'Januari';
        case 2:
            return 'Februari';
        case 3:
            return 'Maret';
        case 4:
            return 'April';
        case 5:
            return 'Mei';
        case 6:
            return 'Juni';
        case 7:
            return 'Juli';
        case 8:
            return 'Agustus';
        case 9:
            return 'September';
        case 10:
            return 'Oktober';
        case 11:
            return 'November';
        case 12:
            return 'Desember';
        default:
            return 'bulan tidak valid';
    }
}

function namasingkat_bulan_indo_dariangka($angka)
{
    switch ($angka) {
        case 1:
            return 'Jan';
        case 2:
            return 'Feb';
        case 3:
            return 'Mar';
        case 4:
            return 'Apr';
        case 5:
            return 'Mei';
        case 6:
            return 'Jun';
        case 7:
            return 'Jul';
        case 8:
            return 'Agt';
        case 9:
            return 'Sep';
        case 10:
            return 'Okt';
        case 11:
            return 'Nov';
        case 12:
            return 'Des';
        default:
            return '#';
    }
}

function jmlhari($tgl)
{
    $bulan = bulanIndo(date('F', strtotime($tgl)));
    $tahun = date('Y');

    if ($bulan == 'Januari') {
        $jumlahHari = 31;
        return $jumlahHari;
    } elseif ($bulan == 'Februari') {
        if ($tahun % 4 == 0) {
            $jumlahHari = 29;
            return $jumlahHari;
        } else {
            $jumlahHari = 28;
            return $jumlahHari;
        }
    } elseif ($bulan == 'Maret') {
        $jumlahHari = 31;
        return $jumlahHari;
    } elseif ($bulan == 'April') {
        $jumlahHari = 30;
        return $jumlahHari;
    } elseif ($bulan == 'Mei') {
        $jumlahHari = 31;
        return $jumlahHari;
    } elseif ($bulan == 'Juni') {
        $jumlahHari = 30;
        return $jumlahHari;
    } elseif ($bulan == 'Juli') {
        $jumlahHari = 31;
        return $jumlahHari;
    } elseif ($bulan == 'Agustus') {
        $jumlahHari = 31;
        return $jumlahHari;
    } elseif ($bulan == 'September') {
        $jumlahHari = 30;
        return $jumlahHari;
    } elseif ($bulan == 'Oktober') {
        $jumlahHari = 31;
        return $jumlahHari;
    } elseif ($bulan == 'November') {
        $jumlahHari = 30;
        return $jumlahHari;
    } elseif ($bulan == 'Desember') {
        $jumlahHari = 31;
        return $jumlahHari;
    } else {
        $jumlahHari = 30;
        return $jumlahHari;
    }
}

function jmlhari_($jml_angka, $tahun)
{
    // $tahun = date('Y', strtotime($tahun));

    if ($jml_angka == 1) {
        $jmlhari_ = 31;
        return $jmlhari_;
    } elseif ($jml_angka == 2) {
        if ($tahun % 4 == 0) {
            $jmlhari_ = 29;
            return $jmlhari_;
        } else {
            $jmlhari_ = 28;
            return $jmlhari_;
        }
    } elseif ($jml_angka == 3) {
        $jmlhari_ = 31;
        return $jmlhari_;
    } elseif ($jml_angka == 4) {
        $jmlhari_ = 30;
        return $jmlhari_;
    } elseif ($jml_angka == 5) {
        $jmlhari_ = 31;
        return $jmlhari_;
    } elseif ($jml_angka == 6) {
        $jmlhari_ = 30;
        return $jmlhari_;
    } elseif ($jml_angka == 7) {
        $jmlhari_ = 31;
        return $jmlhari_;
    } elseif ($jml_angka == 8) {
        $jmlhari_ = 31;
        return $jmlhari_;
    } elseif ($jml_angka == 9) {
        $jmlhari_ = 30;
        return $jmlhari_;
    } elseif ($jml_angka == 10) {
        $jmlhari_ = 31;
        return $jmlhari_;
    } elseif ($jml_angka == 11) {
        $jmlhari_ = 30;
        return $jmlhari_;
    } elseif ($jml_angka == 12) {
        $jmlhari_ = 31;
        return $jmlhari_;
    } else {
        $jmlhari_ = 30;
        return $jmlhari_;
    }
}

function lima_harikerja($namaHari)
{

    if ($namaHari == 'Sabtu' || $namaHari == 'Minggu') {
        $hariKerja = false;
        return $hariKerja;
    } else {
        $hariKerja = true;
        return $hariKerja;
    }
}

function lima_harikerja_ing($namaHari)
{

    if ($namaHari == 'Saturday' || $namaHari == 'Sunday') {
        $hariKerja = false;
        return $hariKerja;
    } else {
        $hariKerja = true;
        return $hariKerja;
    }
}

function enam_harikerja($namaHari)
{

    if ($namaHari == 'Minggu') {
        $hariKerja = false;
        return $hariKerja;
    } else {
        $hariKerja = true;
        return $hariKerja;
    }
}

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

// kalender indonesia
function kalender_indo($tanggal)
{
    $bulan = array(
        1 =>   'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );
    $pecahkan = explode('-', $tanggal);

    // variabel pecahkan 0 = tanggal
    // variabel pecahkan 1 = bulan
    // variabel pecahkan 2 = tahun

    return $pecahkan[2] . ' ' . $bulan[(int) $pecahkan[1]] . ' ' . $pecahkan[0];
}



//fungsi check tanggal merah
function tanggalMerah($value)
{
    // $array = json_decode(file_get_contents("https://raw.githubusercontent.com/guangrei/Json-Indonesia-holidays/master/calendar.json"), true);
    $array = json_decode(file_get_contents("../beranda/app/liburnas.json"), true);

    //check tanggal merah berdasarkan libur nasional
    if (isset($array[$value])) :
        // echo "tanggal merah " . $array[$value]["deskripsi"];
        $deskripsi = 'Libur Nasional : ' . $array[$value]["deskripsi"];
        return $deskripsi;

    //check tanggal merah berdasarkan hari minggu
    // elseif (date("D", strtotime($value)) === "Sun") :        
    //     // echo "tanggal merah hari minggu";
    //     $deskripsi = "Hari Minggu";
    //     return true;

    //bukan tanggal merah
    else :
        // echo "bukan tanggal merah";
        $deskripsi = "";
        return $deskripsi;
    endif;
}

//testing
// $hari_ini = date("Y-m-d");

// echo"<b>Check untuk hari ini (".date("d-m-Y",strtotime($hari_ini)).")</b><br>";
// $dis = tanggalMerah($hari_ini);
// echo 'tetetete: ' . $dis;

function jumlah_hari_bulan($bulan, $tahun)
{
    $bulan = (int) $bulan;
    $tahun = (int) $tahun;
    $jumlah_hari = 0;
    if ($bulan == 1) {
        $jumlah_hari = 31;
    } elseif ($bulan == 2) {
        if ($tahun % 4 == 0) {
            $jumlah_hari = 29;
        } else {
            $jumlah_hari = 28;
        }
    } elseif ($bulan == 3) {
        $jumlah_hari = 31;
    } elseif ($bulan == 4) {
        $jumlah_hari = 30;
    } elseif ($bulan == 5) {
        $jumlah_hari = 31;
    } elseif ($bulan == 6) {
        $jumlah_hari = 30;
    } elseif ($bulan == 7) {
        $jumlah_hari = 31;
    } elseif ($bulan == 8) {
        $jumlah_hari = 31;
    } elseif ($bulan == 9) {
        $jumlah_hari = 30;
    } elseif ($bulan == 10) {
        $jumlah_hari = 31;
    } elseif ($bulan == 11) {
        $jumlah_hari = 30;
    } elseif ($bulan == 12) {
        $jumlah_hari = 31;
    } else {
        $jumlah_hari = 30;
    }
    return $jumlah_hari;
}

// mencari tanggal dihari yang dicari
function tanggal_hariminggu_terakhir($tahun, $bulan)
{
    $tgl = 1;
    $tgl_max = jumlah_hari_bulan($bulan, $tahun);
    $tgl_minggu[] = array();
    while ($tgl <= $tgl_max) {
        $tanggal = $tahun . '-' . $bulan . '-' . $tgl;
        $tanggal = strtotime($tanggal);
        $tanggal = date('l', $tanggal);
        $tanggal = strtolower($tanggal);
        if ($tanggal == "sunday") {
            $tgl_minggu[] += $tgl;
        }
        $tgl++;
    }
    return $tgl_minggu;
}

function tgl_minggu_terakhir($tanggal)
{
    $tanggal = strtotime($tanggal);
    $tanggal = date('l', $tanggal);
    $tanggal = strtolower($tanggal);
    if ($tanggal == "sunday") {
        $tanggal = date('Y-m-d', strtotime('last sunday', strtotime($tanggal)));
        return $tanggal;
    } else {
        $tanggal = date('Y-m-d', strtotime('last sunday', strtotime($tanggal)));
        return $tanggal;
    }
}

// bbaru maret 2024

function hitung_hari($_tanggal)
{
    // Ambil tahun dan bulan dari tanggal yang diberikan
    $year = date('Y', strtotime($_tanggal));
    $month = date('n', strtotime($_tanggal));

    // Hitung jumlah hari dalam bulan yang ditentukan
    $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    return $jumlah_hari;
}

function hitung_hari_kerja($_tanggal)
{
    // Ambil tahun dan bulan dari tanggal yang diberikan
    $year = date('Y', strtotime($_tanggal));
    $month = date('n', strtotime($_tanggal));

    // Hitung jumlah hari dalam bulan yang ditentukan
    $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    // Inisialisasi jumlah hari kerja
    $jumlah_hari_kerja = 0;

    // Loop untuk setiap tanggal dalam bulan yang ditentukan
    for ($i = 1; $i <= $jumlah_hari; $i++) {
        // Tentukan hari dalam seminggu (0 = Minggu, 6 = Sabtu)
        $hari = date('w', mktime(0, 0, 0, $month, $i, $year));

        // Jika hari bukan Sabtu (6) atau Minggu (0), tambahkan ke jumlah hari kerja
        if ($hari != 0 && $hari != 6) {
            $jumlah_hari_kerja++;
        }
    }

    return $jumlah_hari_kerja;
    // return $jumlah_hari;
}

function tanggal_ke_hari($tanggal)
{
    // Daftar nama hari dalam Bahasa Inggris
    $nama_hari_inggris = array(
        'Sunday',
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday'
    );

    // Daftar nama hari dalam Bahasa Indonesia
    $nama_hari_indonesia = array(
        'Minggu',
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu'
    );

    // Ambil nama hari dalam Bahasa Inggris dari tanggal yang diberikan
    $nama_hari_inggris_get = date('l', strtotime($tanggal));

    // Temukan indeks nama hari dalam Bahasa Inggris
    $index_hari = array_search($nama_hari_inggris_get, $nama_hari_inggris);

    // Ambil nama hari dalam Bahasa Indonesia berdasarkan indeks yang ditemukan
    $nama_hari_indonesia = $nama_hari_indonesia[$index_hari];

    return $nama_hari_indonesia;
}

function duadigit($angka)
{
    return sprintf("%02d", $angka);
}

function cari_data($data_array, $isi_data_kolom_dicari, $index_kolom_yang_dicari)
{
    // Inisialisasi array untuk menyimpan data yang sesuai
    $data_hasil = array();

    // Loop untuk setiap elemen dalam data array
    foreach ($data_array as $data) {
        // Periksa jika tanggal dalam data array sama dengan tanggal yang dicari
        if ($data[$index_kolom_yang_dicari] === $isi_data_kolom_dicari) {
            // Jika sama, tambahkan data ke dalam array hasil
            $data_hasil[] = $data;
        }
    }

    if (!empty($data_hasil)) {
        return $data_hasil;
    } else {
        return 0;
    }
}

function cari_data_ganda($data_array, $isi_data_kolom_dicari_1, $index_kolom_yang_dicari_1, $isi_data_kolom_dicari_2, $index_kolom_yang_dicari_2)
{
    // Inisialisasi array untuk menyimpan data yang sesuai
    $data_hasil = array();

    // Loop untuk setiap elemen dalam data array
    foreach ($data_array as $data) {
        // Periksa jika tanggal dalam data array sama dengan tanggal yang dicari
        if ($data[$index_kolom_yang_dicari_1] === $isi_data_kolom_dicari_1) {
            // Jika sama, tambahkan data ke dalam array hasil
            if ($data[$index_kolom_yang_dicari_2] === $isi_data_kolom_dicari_2) {
                $data_hasil[] = $data;
            }
        }
    }

    if (!empty($data_hasil)) {
        return $data_hasil;
    } else {
        return 0;
    }
}

function cari_presensi_gabungan(
    $data_array,
    $isi_data_kolom_dicari_1,
    $index_kolom_yang_dicari_1,
    $isi_data_kolom_dicari_2,
    $index_kolom_yang_dicari_2
) {
    $dzuhur = null;
    $ashar  = null;
    $izin_mens = false;

    // LOOP 1: deteksi Izin Mens + DZUHUR
    foreach ($data_array as $data) {

        if (
            $data[$index_kolom_yang_dicari_1] == $isi_data_kolom_dicari_1 &&
            $data[$index_kolom_yang_dicari_2] == $isi_data_kolom_dicari_2
        ) {

            // Deteksi Izin Mens
            if (!empty($data['ruang']) && strpos($data['ruang'], 'Izin Mens') !== false) {
                $izin_mens = true;
            }

            if (!empty($data['dzuhur'])) {
                $dzuhur = $data['dzuhur'];
            }
        }
    }

    // LOOP 2: ASHAR
    foreach ($data_array as $data) {

        if (
            $data[$index_kolom_yang_dicari_1] == $isi_data_kolom_dicari_1 &&
            $data[$index_kolom_yang_dicari_2] == $isi_data_kolom_dicari_2
        ) {

            if (!empty($data['ashar'])) {
                $ashar = $data['ashar'];
            }
        }
    }

    // Jika Izin Mens → override hasil
    if ($izin_mens) {
        $dzuhur = 'IM';
        $ashar  = 'IM';
    }

    // Tidak ada data sama sekali
    if ($dzuhur === null && $ashar === null) {
        return 0;
    }

    return [[
        'dzuhur' => $dzuhur,
        'ashar'  => $ashar
    ]];
}


function hari_indo_singkat($tanggal)
{
    // Daftar nama hari dalam Bahasa Inggris
    $nama_hari_inggris_full = array(
        'Sunday',
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday'
    );

    // Daftar nama hari dalam Bahasa Indonesia
    // $nama_hari_indonesia = array(
    //     'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
    // );

    $nama_hari_indonesia = array(
        'Min',
        'Sen',
        'Sel',
        'Rab',
        'Kam',
        'Jum',
        'Sab'
    );

    // Ambil nama hari dalam Bahasa Inggris dari tanggal yang diberikan
    $nama_hari_inggris = date('l', strtotime($tanggal));

    // Temukan indeks nama hari dalam Bahasa Inggris
    $index_hari = array_search($nama_hari_inggris, $nama_hari_inggris_full);

    // Ambil nama hari dalam Bahasa Indonesia berdasarkan indeks yang ditemukan
    $nama_hari_indonesia = $nama_hari_indonesia[$index_hari];

    // Ambil tiga karakter pertama dari nama hari dalam Bahasa Indonesia
    $hari_tiga_karakter = substr($nama_hari_indonesia, 0, 3);

    return $hari_tiga_karakter;
}

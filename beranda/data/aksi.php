<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "vendor/autoload.php";

include "../../config/konesi.php";

if (@$konek) {
    echo "<div class='badge bg-dark mb-2 mt-1'><span class='text-success'>&#9673;&nbsp;</span>DB Ready</div>";
    echo '<br>';
} else {
    echo "<div class='badge bg-danger mb-2 mt-1'><span class='text-light'>&#9673;&nbsp;</span>DB Gak Konek Boss</div>";
    echo '<br>';
}

if (@$_POST['upload']) {
    if (@$_POST['log'] == 'log') {
        echo "<div id='box_log_query'>";
        echo "<h1>log_input_db</h2>";
        echo "<div id='log_query'>";
    }

    $error          = "";
    $ekstensi       = "";
    $success        = "";
    $spreadsheet    = "";
    $sheetData      = "";

    $array_database = array(
        // daftarruang
        "kode, inforuang, keterangan",
        // dataguru
        "nokartu, nip, nama, nick, status, info, foto, created_at, updated_at, keterangan, tglawaldispo, tglakhirdispo, docdis, t_waktu_telat, poin, kode, jabatan, akses, ket_akses, saldo, total_transaksi, total_belanja, email, password, login, tentang, template_pesan, level_login",
        // datasiswa
        "nokartu, nis, nama, nick, kelas, foto, kelompok, t_waktu_telat, poin, created_at, updated_at, keterangan, tglawal, tglakhir, fotodoc, kode, tingkat, jur, saldo, total_transaksi, total_belanja, tentang, email, password, login",
        // jadwalgurujur
        "ruangan, keterangan_ruang, nick, nama, jur",
        // jadwalkbm
        "ruangan, info, kelas, kelompok, tingkat, jur, nick, tanggal, mulai_jamke, sampai_jamke",
        // kodeinfo
        "kode, info, tingkat, jur"
    );

    $jumlah_kolom = 0;
    $tbl_db = "";
    $text_query = "";

    $database_pilih = @$_POST['upload'];
    $jumlah_kolom = @$_POST['jumlah_kolom'];
    $key_db = @$_POST['key_db'];

    $tbl_db = $database_pilih;
    $text_query = $array_database[$key_db];

    $file_name  = $_FILES['filexls']['name'];
    $file_data  = $_FILES['filexls']['tmp_name'];

    if (!$file_name) {
        $error .= "<li>Maukkan file xls/xlsx</li>";
    } else {
        $ekstensi = pathinfo($file_name)['extension'];
    }

    $ekstensi_diijinkan = array("xls", "xlsx");

    if (!in_array($ekstensi, $ekstensi_diijinkan)) {
        $error .= "<li>Masukkan file hanya yang ber-ekstensi XLS atau XLSX</li>";
    }

    if (!$error) {
        $reader         = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file_data);
        $spreadsheet    = $reader->load($file_data);
        $sheetData      = $spreadsheet->getActiveSheet()->toArray();

        if (@$_POST['hapus'] == 'hapus') {

            $truncate_table = mysqli_query($konek, "TRUNCATE TABLE $tbl_db");

            if ($truncate_table) {
                $success .= "<li>Data lama dari Tabel <b>'$tbl_db'</b>, berhasil dibersihkan.</li>";
            } else {
                $success .= "<li>Data lama dari Tabel <b>'$tbl_db'</b>, GAGAL dibersihkan.</li>";
            }
        }

        $jumlahData = 0;
        for ($i = 1; $i < count($sheetData); $i++) {
            $string_kolom   = "";
            $sql_query      = "";

            $array_kolom = array();

            for ($kol = 1; $kol <= $jumlah_kolom; $kol++) {
                if ($sheetData[$i][$kol]) {
                    $array_kolom[$i][] = str_replace("\"", " ", str_replace("'", " ", $sheetData[$i][$kol]));
                } else {
                    $array_kolom[$i][] = $sheetData[$i][$kol];
                }
            }

            $kol = 0;

            for ($kol = 0; $kol < $jumlah_kolom; $kol++) {
                $isi = $array_kolom[$i][$kol];
                if (($isi) == "NULL") {
                    $string_kolom .= "NULL" . (($kol == ($jumlah_kolom - 1)) ? "" : ", ");
                } else {
                    $string_kolom .= "'" . $isi . "'" . (($kol == ($jumlah_kolom - 1)) ? "" : ", ");
                }
            }


            $sql_query = "INSERT INTO " . $tbl_db . " (" . $text_query . ") VALUES (" . $string_kolom . ")";

            $query = mysqli_query($konek, $sql_query);

            // echo $query;

            if (@$query) {
                if (@$_POST['log'] == 'log') {
                    echo "<span>$i --></span> $string_kolom - <label>OK</label><br>";
                }
            } else {
                echo "<li>$i --> $string_kolom - GAGAL: " . mysqli_error($konek) . "</li>";
            }

            $jumlahData++;
        }

        if ($jumlahData) {
            $success .= "<li>$jumlahData data berhasil dimasukkan ke database <b>'$tbl_db'</b></li>";
        }
    } else {
        // echo "ERROR<br>";
    }

    if (@$_POST['log'] == 'log') {
        echo "</div>";
        echo "</div>";
    }

    if ($error) {
?>
        <div class="alert alert-danger p-1"><?= $error; ?></div>
    <?php
    }

    if ($success) {
    ?>
        <div class="alert alert-success p-1"><?= $success; ?></div>
<?php

    }
}


// echo "POST: <br>";

// echo '<pre>';
// print_r(@$_POST);
// echo '</pre>';

// echo "FILES:<br>";
// echo '<pre>';
// print_r(@$_FILES);
// echo '</pre>';

// echo "file_name:<br>";
// echo '<pre>';
// print_r(@$file_name);
// echo '</pre>';

// echo "file_data:<br>";
// echo '<pre>';
// print_r(@@$file_data);
// echo '</pre>';

// echo "ekstensi: <br>";
// echo '<pre>';
// print_r(@$ekstensi);
// echo '</pre>';
?>
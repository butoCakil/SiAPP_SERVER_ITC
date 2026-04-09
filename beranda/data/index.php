<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$subDir = 1;
include('../app/get_user.php');
if (@$_SESSION["level_login"] == "superadmin" || @$_SESSION["level_login"] == "admin") {
    $title = 'Upload Excel (.xls, .xlsx) ke Database';
    $navlink = 'Form Ijin';

?>
    <?php
    // include('../views/header.php'); 
    ?>

    <!doctype html>
    <html lang="en">

    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Bootstrap CSS -->
        <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> -->
        <link rel="stylesheet" href="../../dist/bootstrap-5.2.3-dist/css/bootstrap.min.css">
        <link rel="shortcut icon" href="../dist/img/logoInstitusi.png" type="image/x-icon">

        <!-- <title>Upload Database (Excel)</title> -->
    </head>

    <body>
        <?php include "../views/header.php"; ?>
        <?php include "../views/navbar.php"; ?>
        <style>
            .container {
                text-align: center;
            }

            .container #formFile {
                width: 50%;
                margin: auto;
            }
        </style>

        <div class="container p-2">
            <button class="btn btn-sm btn-secondary border-0" onclick="window.location='../';">
                << Beranda</button><br>
                    <?php
                    include "aksi.php";
                    $label = "";
                    $file_download = "";

                    $array_database = array(
                        "daftarruang",
                        "dataguru",
                        "datasiswa",
                        "jadwalgurujur",
                        "jadwalkbm",
                        "kodeinfo"
                    );
                    $array_database_info = array(
                        "Daftar Ruang",
                        "Data Guru",
                        "Data Siswa",
                        "Jadwal Guru",
                        "Jadwal KBM",
                        "Kode Info"
                    );
                    $array_database_jml = array(
                        3,
                        28,
                        25,
                        5,
                        10,
                        4
                    );

                    $database_pilih = @$_GET['db'];
                    $key_db = array_search($database_pilih, $array_database);
                    $label = $array_database_info[$key_db];

                    $file_download = $database_pilih . ".xlsx";

                    ?>

                    <select class="form-select w-50 mx-auto" aria-label="Default select example" onchange="pilih_database(this.value);">
                        <option value="" selected>Pilih Database</option>
                        <?php for ($qq = 0; $qq < count($array_database); $qq++) {
                            if ($database_pilih == $array_database[$qq]) {
                                $selected = " selected";
                            } else {
                                $selected = "";
                            }
                        ?>
                            <option value="<?= $array_database[$qq]; ?>" <?= $selected; ?>><?= $array_database_info[$qq]; ?></option>
                        <?php } ?>
                    </select>

                    <hr>

                    <?php if (@$database_pilih) { ?>
                        <form action="" class="m-2" method="POST" enctype="multipart/form-data">
                            <div class="my-1">
                                <label for="formFile" class="form-label">Upload <b><?= $label; ?></b> file Excel (XLS, XLSX) ke database</label>
                                <div>
                                    <label for="contoh1"><i>Download Template Format Excel untuk <?= $label; ?> >> </i></label>
                                    <a href="download/<?= $file_download; ?>" class="text-decoration-none" download>&nbsp;
                                        <svg style="color: rgb(23, 135, 8);" width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3.79293 7.49998L2.14648 5.85353L2.85359 5.14642L4.50004 6.79287L6.14648 5.14642L6.85359 5.85353L5.20714 7.49998L6.85359 9.14642L6.14648 9.85353L4.50004 8.20708L2.85359 9.85353L2.14648 9.14642L3.79293 7.49998Z" fill="#178708"></path>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.5 0C2.67157 0 2 0.671573 2 1.5V3H1.5C0.671573 3 0 3.67157 0 4.5V10.5C0 11.3284 0.671573 12 1.5 12H2V13.5C2 14.3284 2.67157 15 3.5 15H13.5C14.3284 15 15 14.3284 15 13.5V1.5C15 0.671573 14.3284 0 13.5 0H3.5ZM1.5 4C1.22386 4 1 4.22386 1 4.5V10.5C1 10.7761 1.22386 11 1.5 11H7.5C7.77614 11 8 10.7761 8 10.5V4.5C8 4.22386 7.77614 4 7.5 4H1.5Z" fill="#178708"></path>
                                        </svg>
                                        <?= $file_download; ?>
                                    </a>
                                </div>
                                <div class="alert alert-warning p-1 mb-2 w-75 mx-auto">
                                    <li><b>Jangan mengubah format dan rumus pada tabel</b> pada template, cukup ubah data sesuai format.</li>
                                    <li><b>Isi data kolom yang kosong dengan "0" (Nol)</b> (untuk bilangan/jumlah) <b>dan "-" atau "NULL" </b> (untuk keterangan/nama).</li>
                                </div>
                                <input class="form-control" type="file" name="filexls" id="formFile" accept=".xls,.xlsx">
                            </div>

                            <div class="m-2">
                                <input type="checkbox" name="hapus" value="hapus" class="form-check-input" id="exampleCheck1" onchange="return confirm('Mencentang chek box ini, akan menghapus database sebelumnya, yakin?\nJangan di centang jika hanya ingin menambah database, bukan menggantinya');">
                                <label class="form-check-label" for="exampleCheck1">Hapus Data Sebelumnya?</label>
                                
                                <input type="checkbox" name="log" value="log" class="form-check-input mx-1" id="exampleCheck2">
                                <label class="form-check-label mx-4" for="exampleCheck2">Tampilkan Log</label>
                            </div>

                            <input type="hidden" name="jumlah_kolom" value="<?= $array_database_jml[$key_db]; ?>">

                            <input type="hidden" name="key_db" value="<?= $key_db; ?>">

                            <button type="submit" name="upload" value="<?= $database_pilih; ?>" class="btn btn-primary btn-sm">UPLOAD</button>
                        </form>
                    <?php } else { ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <h5>Dataftar tabel database yang bisa diinputkan.</h5>
                            <?php for ($dddbb = 0; $dddbb < count($array_database_info); $dddbb++) { ?>
                                <li>
                                    <?= $array_database_info[$dddbb]; ?>
                                </li>
                            <?php } ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php } ?>
        </div>

        <!-- Optional JavaScript; choose one of the two! -->

        <!-- Option 1: Bootstrap Bundle with Popper -->
        <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> -->
        <script src="../../dist/bootstrap-5.2.3-dist/js/bootstrap.bundle.min.js"></script>

        <!-- Option 2: Separate Popper and Bootstrap JS -->
        <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->

        <script>
            function pilih_database(_database) {
                // alert(_database + " telah dipilih");

                window.location.href = "?db=" + _database;
            }
        </script>

        <style>
            #box_log_query {
                background-color: cadetblue;
                height: 320px;
                padding: 1px;
                padding-top: 12px;
                margin: 5px;
                margin-bottom: 20px;
                border-radius: 10px;
            }

            #box_log_query h1 {
                font-size: 16px;
                margin: 0;
            }

            #log_query {
                height: 270px;
                background-color: rgba(50, 50, 50, 0.9);
                border-radius: 10px;
                color: aliceblue;
                margin: 10px;
                overflow: auto;
            }

            #log_query label {
                font-weight: 900;
                color: chartreuse;
            }

            #log_query li {
                color: crimson;
            }

            #log_query span {
                color: burlywood;
            }

            #log_query p {
                color: dodgerblue;
            }

            /* Menyembunyikan tampilan scrollbar di browser berbasis Webkit */
            #log_query::-webkit-scrollbar {
                width: 0.5em;
            }

            #log_query::-webkit-scrollbar-thumb {
                background-color: darkgrey;
                outline: 1px solid slategrey;
            }
        </style>

        <script>
            // Fungsi untuk menggulir ke bawah elemen log_query
            function scrollToBottom() {
                var logQuery = document.getElementById('log_query');
                logQuery.scrollTop = logQuery.scrollHeight;
            }

            // Contoh pembaruan log_query (Anda dapat memanggil ini saat ada pembaruan data)
            function updateLog() {
                var logQuery = document.getElementById('log_query');
                // Contoh menambahkan teks baru
                logQuery.innerHTML += '<p>>> SELESAI <<</p>';
                scrollToBottom(); // Setelah menambahkan teks baru, gulir ke bawah
            }

            // Panggil fungsi updateLog() ketika ada pembaruan data
            // Sebagai contoh, Anda dapat memanggilnya saat data IoT diterima atau peristiwa lainnya.
            updateLog();
        </script>

        <?php
        // echo "<pre>";
        // print_r($_GET);
        // echo "</pre>";
        ?>
    </body>

    </html>

    <?php
    include('../views/footer.php');
    ?>

<?php
} else {
    // alert 
    echo "<script>
            alert('Maaf, Anda tidak memiliki hak akses ke halaman ini! Level user anda: " . $_SESSION["level_login"] . "');
            window.location.href='../';
            </script>";
} ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6 connectedSortable">
                <div class="card elevation-5">
                    <div class="card-header bg-gradient-navy">
                        <h3 class="card-title">
                            <i class="fas fa-cog fa-spin"></i></i>&nbsp;
                            Setting Waktu
                        </h3>
                        <div class="card-tools">
                            <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Mengubah Setting Aplikasi, dan mengatur Admin"></i>
                            <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <!-- <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button> -->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="nama-setting alert alert-default-info">
                            <button type="button" class="close mr-3" data-dismiss="alert">&times;</button>
                            Pengaturan waktu masuk dan waktu pulang
                        </div>
                        <div id="setting" class="container">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="setting-1">
                                    <div class="pilihhari">
                                        <label class="lb1" for="pilihhari">Hari Buka Akses</label>
                                        <select id="pilihhari" class="form-select" aria-label="Default select example" name="pilihhari">
                                            <?php

                                            if (isset($harikerja) == "Senin - Jum'at") {
                                                $pilihan_1 = 'selected';
                                                $pilihan_2 = '';
                                                $pilihan_3 = '';
                                                $pilihan_0 = '';
                                            } elseif (isset($harikerja) == "Senin - Sabtu") {
                                                $pilihan_1 = '';
                                                $pilihan_2 = 'selected';
                                                $pilihan_3 = '';
                                                $pilihan_0 = '';
                                            } elseif (isset($harikerja) == "Semua Hari") {
                                                $pilihan_1 = '';
                                                $pilihan_2 = '';
                                                $pilihan_3 = 'selected';
                                                $pilihan_0 = '';
                                            } else {
                                                $pilihan_1 = '';
                                                $pilihan_2 = '';
                                                $pilihan_3 = '';
                                                $pilihan_0 = 'selected';
                                            }
                                            ?>
                                            <option <?= $pilihan_1; ?> value="5">Senin - Jum'at</option>
                                            <option <?= $pilihan_2; ?> value="6">Senin - Sabtu</option>
                                            <option <?= $pilihan_3; ?> value="7">Semua Hari</option>
                                            <option <?= $pilihan_0; ?> value="0">Tidak di set</option>
                                        </select>
                                    </div>

                                    <div class="waktumasuk mt-2">
                                        <label class="lb2" for="waktumasuk">Waktu Masuk</label>
                                        <input id="waktumasuk" type="time" name="waktumasuk" class="form-control" value="<?= $waktumasuk; ?>">
                                    </div>

                                    <div class="waktupulang mt-2">
                                        <label class="lb3" for="waktupulang">Waktu Pulang</label>
                                        <input id="waktupulang" type="time" name="waktupulang" class="form-control" value="<?= $waktupulang; ?>">
                                    </div>
                                    <div id="tombolsetting mt-2">
                                        <button type="submit" value="Terapkan" class="btn btn-success mt-3 elevation-2" name="setting1">
                                            <i class="fas fa-check"></i>&nbsp;
                                            Terapkan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 connectedSortable">
                <div class="card elevation-5">
                    <div class="card-header bg-gradient-navy">
                        <h3 class="card-title">
                            <i class="fas fa-cog fa-spin"></i>&nbsp;
                            Setting Batas Akses
                        </h3>
                        <div class="card-tools">
                            <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Mengubah Setting Aplikasi, dan mengatur Admin"></i>
                            <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <!-- <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button> -->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="nama-setting alert alert-default-info">
                            <button type="button" class="close mr-3" data-dismiss="alert">&times;</button>
                            Pengaturan Batas Akses
                        </div>

                        <div id="setting" class="container">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="setting-2 mb-3">
                                    <div class="waktu-1">
                                        <label class="lb2-1 mt-2" for="bukamasuk">Buka Akses Masuk</label>
                                        <input id="bukamasuk" type="time" name="bukamasuk" class="form-control" value="<?= $wa; ?>">
                                    </div>
                                    <div class="waktu-2">
                                        <label class="lb2-2 mt-2" for="tutupmasuk">Tutup Akses Masuk</label>
                                        <input id="tutupmasuk" type="time" name="tutupmasuk" class="form-control" value="<?= $wta; ?>">
                                    </div>
                                    <div class="waktu-3">
                                        <label class="lb2-3 mt-2" for="bukapulang">Buka Akses Pulang</label>
                                        <input id="bukapulang" type="time" name="bukapulang" class="form-control" value="<?= $wtp; ?>">
                                    </div>
                                    <div class="waktu-4">
                                        <label class="lb2-4 mt-2" for="tutuppulang">Tutup Akses Pulang</label>
                                        <input id="tutuppulang" type="time" name="tutuppulang" class="form-control" value="<?= $wp; ?>">
                                    </div>
                                    <div id="tombolsetting2">
                                        <button type="submit" value="set" class="btn btn-success mt-3 elevation-2" name="setting2">
                                            <i class="fas fa-check"></i>&nbsp;
                                            Terapkan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            #setting_admin .card-body {
                /* max-height: 200px; */
                margin-bottom: -50px;
                /* overflow: auto; */
            }
        </style>
        <div class="row">
            <div class="col-lg-12 connectedSortable">
                <?php if (@$_SESSION['level_login'] == 'superadmin') { ?>
                    <div id="setting_admin" class="card elevation-5">
                        <div class="card-header bg-gradient-navy">
                            <h3 class="card-title">
                                <i class="fas fa-user-cog"></i></i>&nbsp;
                                Setting Admin
                            </h3>
                            <div class="card-tools">
                                <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Mengubah Setting Aplikasi, dan mengatur Admin"></i>
                                <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <!-- <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button> -->
                            </div>
                        </div>
                        <div class="card-body">
                            <div>
                                <button class="btn btn-danger mt-2 elevation-2" data-bs-toggle="modal" data-bs-target="#editadmin">
                                    <i class="fas fa-user-edit"></i>&nbsp;
                                    Ubah User dan Password</button>
                                <button class="btn btn-info mt-2 ml-2 elevation-2" data-bs-toggle="modal" data-bs-target="#tambahadmin">
                                    <i class="fas fa-user-plus"></i>&nbsp;
                                    Tambah Super Admin</button>

                                <div style="margin-top: 25px; margin-bottom: 50px;">
                                    <!-- <h6><a href="admin.php">user admin login</a></h6> -->
                                    <table class="table table-responsive-xl table-bordered elevation-2">
                                        <thead style="text-align: center;">
                                            <tr>
                                                <th><i class="fas fa-user-tie text-blue"></i>&nbsp;&nbsp;User</th>
                                                <th><i class="fas fa-level-up-alt text-warning"></i>&nbsp;&nbsp;&nbsp;Level</th>
                                                <th><i class="fas fa-sign-in-alt text-success"></i>&nbsp;&nbsp;Login</th>
                                                <th><i class="fas fa-cog fa-spin"></i></th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                            <?php
                                            include("../config/konesi.php");
                                            $sql = "SELECT * FROM admin";
                                            $result = mysqli_query($konek, $sql);
                                            $nnn = 0;
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                if ($nama_login == 'Pengembang' || $row['username'] != 'Pengembang') {
                                                    $nnn++;
                                            ?>
                                                    <tr>
                                                        <td class="text-left">
                                                            <span class="badge badge-secondary">
                                                                <?= $nnn; ?>
                                                            </span>
                                                            &nbsp;&nbsp;
                                                            <?= $row['username']; ?>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-warning">
                                                                superadmin
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php if ($row['status'] == 'login') { ?>
                                                                <span class="badge badge-success">
                                                                    <?= date('d-m-Y', strtotime($row['timestamp'])); ?>
                                                                </span>
                                                                <span class="badge badge-primary">
                                                                    <?= date('H:i:s', strtotime($row['timestamp'])); ?>
                                                                </span>
                                                            <?php } else { ?>
                                                                <span class="badge badge-secondary">
                                                                    <i class="fas fa-times"></i>&nbsp;
                                                                    offline
                                                                </span>
                                                            <?php } ?>
                                                            <?= $row['username']; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($nama_login == 'Pengembang' || $nama_login == "kepsek") { ?>
                                                                <?php if ($row['username'] != 'Pengembang' && $row['username'] != 'kepsek') { ?>
                                                                    <div class="btn-group">
                                                                        <!-- <button class="btn btn-sm bg-info" onclick="return confirm('Anda akan mengubah akun ini.. Lanjut?')">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button> -->

                                                                        <form method="post">
                                                                            <input type="hidden" name="id_hapus" value="<?= $row['id']; ?>">
                                                                            <button type="submit" name="hapusadmin" value="hapusadmin" class="btn btn-sm bg-danger" onclick="return confirm('Yakin akan menghapus akun Admin ini??')">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                <?php } ?>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <style>
                        #tabel_dataguru_admin {
                            max-height: 600px;
                            overflow: auto;
                        }
                    </style>
                    <div class="card elevation-5">
                        <div class="card-header bg-gradient-navy">
                            <h3 class="card-title">
                                <i class="fas fa-user-cog"></i></i>&nbsp;Set Admin
                            </h3>
                            <div class="card-tools">
                                <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Mengubah Setting Aplikasi, dan mengatur Admin"></i>
                                <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <!-- <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button> -->
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="tabel_dataguru_admin">
                                <?php include('app/dataguru.php'); ?>
                            </div>
                        </div>
                    </div>


                    <div class="card elevation-5">
                        <div class="card-header bg-gradient-navy">
                            <h3 class="card-title">
                                <i class="fas fa-user-cog"></i></i>&nbsp;Export Database
                            </h3>
                            <div class="card-tools">
                                <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Mengubah Setting Aplikasi, dan mengatur Admin"></i>
                                <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <!-- <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button> -->
                            </div>
                        </div>
                        <div id="bodiexdb" class="card-body">
                            <div class="row mb-3">
                                <button id="tambahbarisexdb" class="btn btn-light shadow bg-gradient-success border-0"><i class="fa fa-plus-circle"></i>&nbsp;Tambah Entry
                                </button>
                            </div>

                            <?php
                            // Daftar tabel yang ingin Anda tampilkan
                            $tablesToDisplay = array(
                                "daftarruang",
                                "daftarijin",
                                "dataguru",
                                "datapresensi",
                                "datasiswa",
                                "jadwalgurujur",
                                "jadwalkbm",
                                "jampelajaran",
                                "jurnalguru",
                                "kalender",
                                "kelompokkelas",
                                "kodeinfo",
                                "pengumuman",
                                "presensikelas",
                                "presensiEvent",
                            );

                            // ambil data dari tabel `exportdb`
                            $query = "SELECT * FROM exportdb WHERE `status` = 'diijinkan'";
                            $result = mysqli_query($konek, $query);

                            if ($result) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $dblink_id = $row['id'];
                                    $dblink_db = $row['db'];
                                    $dblink_link = $row['link'];
                                    $dblink_keyapi = @$row['keyapi'];
                            ?>
                                    <div class="row" id="exdb<?= $dblink_id ? "_$dblink_id" : ""; ?>">
                                        <div class="d-flex justify-content-between">
                                            <div class="col-3">
                                                <?php
                                                // Query untuk mendapatkan daftar tabel yang sesuai dengan kriteria
                                                $queryTables = "SHOW TABLES LIKE '%'";
                                                $resultTables = mysqli_query($konek, $queryTables);

                                                if ($resultTables) {
                                                    echo '<select id="pilihexdb_' . $dblink_id . '" class="form-select" aria-label="Default select example">';
                                                    echo "<option value=\"\">-- Pilih Tabel --</option>";
                                                    while ($rowTables = mysqli_fetch_row($resultTables)) {
                                                        $tableName = $rowTables[0];
                                                        $selected = (strtolower($dblink_db) == strtolower($tableName)) ? "selected" : "";
                                                        echo "<option value=\"$tableName\" $selected>$tableName</option>";
                                                    }
                                                    echo '</select>';
                                                } else {
                                                    echo "Tidak dapat mengeksekusi query: " . mysqli_error($konek);
                                                }

                                                ?>
                                            </div>
                                            <div class="col-8 mb-3">
                                                <textarea id="exdbarea_<?= $dblink_id; ?>" class="form-control" name="linkexdb" rows="1" placeholder="link"><?= $dblink_link ?></textarea>
                                                <div class="password-input d-flex">
                                                    <input type="password" id="apitexdbarea_<?= $dblink_id; ?>" class="form-control" name="keyexdb" value="<?= $dblink_keyapi ?>" data-id="<?= $dblink_id; ?>" placeholder="Key">
                                                    <button class="btn btn-secondary show-hide-button" data-id="<?= $dblink_id; ?>"><i class="fa fa-eye"></i></button>
                                                </div>
                                            </div>
                                            <div class="mb-3 col-1 d-flex flex-column gap-1">
                                                <div id="btnokexdb<?= $dblink_id ? "_$dblink_id" : ""; ?>">
                                                    <button class="btn btn-primary border-0" onclick="inputlink('<?= $dblink_id ? $dblink_id : 0; ?>');"><i class="fa fa-check"></i></button>
                                                </div>
                                                <div id="btncancelexdb<?= $dblink_id ? "_$dblink_id" : ""; ?>">
                                                    <button class="btn btn-danger border-0" onclick="deletelink('<?= $dblink_id; ?>');"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            <?php
                                }
                            }
                            ?>

                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>

<!-- tambah admin -->
<div class="modal fade" id="tambahadmin" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Super Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="hidden" name="foto" value="default.jpg">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="username" class="form-control" id="username" name="usernameadmin" placeholder="Username Admin" required>
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">Email address</label>
                        <input type="text" class="form-control" id="" aria-describedby="" name="emailadmin" placeholder="Email Admin" required>
                    </div>
                    <div class="mb-3">
                        <label for="pass" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" id="pass" class="form-control" name="passwordadmin" placeholder="Password" required>
                            <div class="input-group-append">

                                <!-- kita pasang onclick untuk merubah icon buka/tutup mata setiap diklik  -->
                                <span id="mybutton" onclick="change()" class="input-group-text">

                                    <!-- icon mata bawaan bootstrap  -->
                                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z" />
                                        <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="kontak" class="form-label">CP</label>
                        <input type="tel" class="form-control" id="kontak" placeholder="No. tlp/ WA: 628xxxxxxxxxx" name="kontakadmin">
                    </div>

            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-primary" name="tambahadmin" value="Tambahkan">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ubah profil admin -->
<div class="modal fade" id="editadmin" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editadminLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editadminLabel">Ubah User Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="hidden" name="foto" value="default.jpg">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="hidden" name="usernameadminlama" value="<?= $nama_login; ?>">
                        <input type="username" class="form-control" id="username" name="usernameadminbaru" value="<?= $nama_login; ?>" placeholder="Username Admin" required>
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">Email address</label>
                        <input type="hidden" name="emailadminlama" value="<?= $email_login; ?>">
                        <input type="text" class="form-control" id="" aria-describedby="" name="emailadminbaru" value="<?= $email_login; ?>" placeholder="Email Admin" required>
                    </div>
                    <div class="mb-3">
                        <label for="pass" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="hidden" name="passwordadminlama" value="<?= $password_login; ?>">
                            <input type="password" id="passEdit" class="form-control" name="passwordadminbaru" value="<?= $password_login; ?>" placeholder="Password" required>
                            <div class="input-group-append">

                                <!-- kita pasang onclick untuk merubah icon buka/tutup mata setiap diklik  -->
                                <span id="tmblubahdataadmin" onclick="change2()" class="input-group-text">

                                    <!-- icon mata bawaan bootstrap  -->
                                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z" />
                                        <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="pass" class="form-label">Ulangi Password</label>
                        <div class="input-group">
                            <input type="password" id="passEdit2" class="form-control" name="passwordadminbaru_ulang" value="<?= $password_login; ?>" placeholder="Password" required>
                            <div class="input-group-append">

                                <!-- kita pasang onclick untuk merubah icon buka/tutup mata setiap diklik  -->
                                <span id="tmblubahdataadmin2" onclick="change3()" class="input-group-text">

                                    <!-- icon mata bawaan bootstrap  -->
                                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z" />
                                        <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-primary" name="ubahdataadmin" value="Ubah">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function change() {

        // membuat variabel berisi tipe input dari id='pass', id='pass' adalah form input password 
        var x = document.getElementById('pass').type;

        //membuat if kondisi, jika tipe x adalah password maka jalankan perintah di bawahnya
        if (x == 'password') {

            //ubah form input password menjadi text
            document.getElementById('pass').type = 'text';

            //ubah icon mata terbuka menjadi tertutup
            document.getElementById('mybutton').innerHTML = `<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-slash-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10.79 12.912l-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
                                                                    <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708l-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829z"/>
                                                                    <path fill-rule="evenodd" d="M13.646 14.354l-12-12 .708-.708 12 12-.708.708z"/>
                                                                    </svg>`;
        } else {

            //ubah form input password menjadi text
            document.getElementById('pass').type = 'password';

            //ubah icon mata terbuka menjadi tertutup
            document.getElementById('mybutton').innerHTML = `<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                                    <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                                    </svg>`;
        }
    }

    function change2() {

        // membuat variabel berisi tipe input dari id='pass', id='pass' adalah form input password 
        var x = document.getElementById('passEdit').type;

        //membuat if kondisi, jika tipe x adalah password maka jalankan perintah di bawahnya
        if (x == 'password') {

            //ubah form input password menjadi text
            document.getElementById('passEdit').type = 'text';

            //ubah icon mata terbuka menjadi tertutup
            document.getElementById('tmblubahdataadmin').innerHTML = `<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-slash-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10.79 12.912l-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
                                                                    <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708l-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829z"/>
                                                                    <path fill-rule="evenodd" d="M13.646 14.354l-12-12 .708-.708 12 12-.708.708z"/>
                                                                    </svg>`;
        } else {

            //ubah form input password menjadi text
            document.getElementById('passEdit').type = 'password';

            //ubah icon mata terbuka menjadi tertutup
            document.getElementById('tmblubahdataadmin').innerHTML = `<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                                    <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                                    </svg>`;
        }
    }

    function change3() {

        // membuat variabel berisi tipe input dari id='pass', id='pass' adalah form input password 
        var x = document.getElementById('passEdit2').type;

        //membuat if kondisi, jika tipe x adalah password maka jalankan perintah di bawahnya
        if (x == 'password') {

            //ubah form input password menjadi text
            document.getElementById('passEdit2').type = 'text';

            //ubah icon mata terbuka menjadi tertutup
            document.getElementById('tmblubahdataadmin2').innerHTML = `<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-slash-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10.79 12.912l-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
                                                                    <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708l-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829z"/>
                                                                    <path fill-rule="evenodd" d="M13.646 14.354l-12-12 .708-.708 12 12-.708.708z"/>
                                                                    </svg>`;
        } else {

            //ubah form input password menjadi text
            document.getElementById('passEdit2').type = 'password';

            //ubah icon mata terbuka menjadi tertutup
            document.getElementById('tmblubahdataadmin2').innerHTML = `<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                                    <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                                    </svg>`;
        }
    }

    // Export db
    function tambahBaris() {
        // Mendapatkan elemen dengan ID yang sesuai, misal "exdb_1"
        var lastRow = document.querySelector('[id^="exdb_"]:last-child');
        var newRow = lastRow.cloneNode(true);

        document.getElementById("bodiexdb").appendChild(newRow);

        // Mengganti ID dan value
        var newRowId = newRow.id;
        var newRowNumber = parseInt(newRowId.split("_")[1]) + 1;
        newRow.id = "exdb_" + newRowNumber;

        // Mengganti ID dan mengosongkan nilai untuk select
        var select = newRow.querySelector("#pilihexdb_" + (newRowNumber - 1));
        select.id = "pilihexdb_" + newRowNumber;
        select.value = "";

        // Mengosongkan nilai pada textarea
        var textarea = newRow.querySelector("#exdbarea_" + (newRowNumber - 1));
        textarea.id = "exdbarea_" + newRowNumber;
        textarea.value = "";

        // Mengosongkan nilai pada textarea
        var textarea = newRow.querySelector("#apitexdbarea_" + (newRowNumber - 1));
        textarea.id = "apitexdbarea_" + newRowNumber;
        textarea.value = "";

        // Mengganti ID tombol ok
        var okButton = newRow.querySelector("#btnokexdb_" + (newRowNumber - 1));
        okButton.id = "btnokexdb_" + newRowNumber;
        okButton.innerHTML = '<button class="btn btn-primary border-0" onclick="inputlink(' + newRowNumber + ');"><i class="fa fa-check "></i></button>';

        // Tambahkan event listener untuk tombol "OK"
        // okButton.querySelector('button').addEventListener('click', function() {
        //     inputlink(newRowNumber);
        // });

        // Mengganti ID tombol times
        deleteBtn = newRow.querySelector("#btncancelexdb_" + (newRowNumber - 1));
        deleteBtn.id = "btncancelexdb_" + newRowNumber;
        deleteBtn.innerHTML = '<button class="btn btn-danger border-0" onclick="deletelink(' + newRowNumber + ');"><i class="fa fa-times "></i></button>';

        // Sembunyikan tombol "times" di baris baru
        var tombolTimes = newRow.querySelector('#btncancelexdb_' + newRowNumber);
        tombolTimes.style.display = "block"; // Tampilkan tombol "times" di baris tambahan

        // Tambahkan event listener untuk tombol "times"
        tombolTimes.addEventListener("click", function() {
            // Cari parent terdekat dengan class "row" dan hapusnya
            var parentRow = this.closest('.row');
            if (parentRow) {
                parentRow.remove();
            }
        });
    }

    // Menambahkan event listener untuk tombol "plus"
    document.getElementById("tambahbarisexdb").addEventListener("click", tambahBaris);

    // Sembunyikan tombol "times" di baris pertama
    var tombolTimesPertama = document.querySelector('[id^="btncancelexdb_"]');
    if (tombolTimesPertama) {
        tombolTimesPertama.style.display = "none";
    }


    // var lastRow = document.querySelector('[id^="exdb_"]:last-child');

    // // Tambahkan event listener untuk tombol "times" di baris pertama
    // lastRow.addEventListener("click", function() {
    //     // Cari parent terdekat dengan class "row" dan hapusnya
    //     var parentRow = this.closest('.row');
    //     if (parentRow) {
    //         parentRow.remove();
    //     }
    // });

    // operasi ajax ke DB

    function inputlink(db_id) {
        // Dapatkan nilai select dan textarea
        var selectValue = $("#exdb_" + db_id + " #pilihexdb_" + db_id).val();
        var textareaValue = $("#exdb_" + db_id + " #exdbarea_" + db_id).val();
        var keyAreaValue = $("#exdb_" + db_id + " #apitexdbarea_" + db_id).val();

        $.ajax({
            url: "app/linkexdb.php",
            type: "POST",
            data: {
                set: "inputlink",
                id_db: db_id,
                select_value: selectValue,
                textarea_value: textareaValue,
                apikey: keyAreaValue,
                key: '$1-9(SiApp)'
            },
            success: function(data) {
                var okButton = document.getElementById("btnokexdb_" + db_id);
                okButton.innerHTML = '';
                okButton.innerHTML = '<button id="btnokexdb_' + db_id + '" class="btn btn-success border-0" onclick="inputlink("' + (db_id) + '");"><i class="fa fa-check"></i></button>';

                alert(data);
                okButton.querySelector('button').addEventListener('click', function() {
                    inputlink(db_id);
                });
            },
            error: function() {
                alert('ERROR! ' + data);
            }
        });
    }

    function deletelink(db_id) {
        if (!confirm("link export DB akan dihapus")) {
            return;
        }

        $.ajax({
            url: "app/linkexdb.php",
            type: "POST",
            data: {
                set: "deletelink",
                id_db: db_id,
                key: '$1-9(SiApp)'
            },
            success: function(data) {
                alert(data);
            },
            error: function() {
                alert('ERROR! ' + data);
            }
        });

        var pilihRow = document.getElementById("exdb_" + db_id);

        // Tambahkan event listener untuk tombol "times" di baris pertama
        pilihRow.addEventListener("click", function() {
            // Cari parent terdekat dengan class "row" dan hapusnya
            var parentRow = this.closest('.row');
            if (parentRow) {
                parentRow.remove();
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Temukan semua elemen dengan class "password-input"
        var passwordInputs = document.querySelectorAll(".password-input");

        // Iterasi melalui setiap elemen
        passwordInputs.forEach(function(passwordInput) {
            // Temukan input dan tombol dalam setiap elemen
            var input = passwordInput.querySelector("input");
            var showHideButton = passwordInput.querySelector(".show-hide-button");

            // Setel awalnya input adalah type="password"
            input.type = "password";

            // Tambahkan event listener ke tombol "Tampilkan"
            showHideButton.addEventListener("click", function() {
                if (input.type === "password") {
                    input.type = "text"; // Jika saat ini tipe adalah "password", ubah menjadi "text" untuk menampilkan karakter
                    showHideButton.innerHTML = '<i class="fa fa-eye-slash"></i>';
                } else {
                    input.type = "password"; // Jika saat ini tipe adalah "text", ubah kembali menjadi "password" untuk menyembunyikan karakter
                    showHideButton.innerHTML = '<i class="fa fa-eye"></i>';
                }
            });
        });
    });
</script>
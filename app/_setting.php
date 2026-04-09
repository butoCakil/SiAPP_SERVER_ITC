<?php include('views/header.php'); ?>

<?php
if (@$_POST['tambahadmin'] == 'Submit') {
    // echo 'ada POST';
    // echo '<br>';
    if (!@$_POST['usernamebaru']) {
        $pesan = "Username tidak boleh kosong";
        $_SESSION['pesan'] = $pesan;
        $_SESSION['alert1'] = "danger";
        // echo 'error: ' . $pesan;
    } else if (!@$_POST['passbaru']) {
        $pesan = "Password tidak boleh kosong";
        $_SESSION['pesan'] = $pesan;
        $_SESSION['alert1'] = "danger";
        // echo 'error: ' . $pesan;
    } else if (!@$_POST['emailbaru']) {
        $pesan = "Email tidak boleh kosong";
        $_SESSION['pesan'] = $pesan;
        $_SESSION['alert1'] = "danger";
        // echo 'error: ' . $pesan;
    } else if (!@$_POST['usernamebaru'] && !@$_POST['passbaru']) {
        $pesan = "Username dan Password tidak boleh kosong";
        $_SESSION['pesan'] = $pesan;
        $_SESSION['alert1'] = "danger";
        // echo 'error: ' . $pesan;
    } else if (!@$_POST['usernamebaru'] && !@$_POST['emailbaru']) {
        $pesan = "Username dan Email tidak boleh kosong";
        $_SESSION['pesan'] = $pesan;
        $_SESSION['alert1'] = "danger";
        // echo 'error: ' . $pesan;
    } else if (!@$_POST['passbaru'] && !@$_POST['emailbaru']) {
        $pesan = "Password dan Email tidak boleh kosong";
        $_SESSION['pesan'] = $pesan;
        $_SESSION['alert1'] = "danger";
        // echo 'error: ' . $pesan;
    } else if (!@$_POST['usernamebaru'] && !@$_POST['passbaru'] && !@$_POST['emailbaru']) {
        $pesan = "Username, Password dan Email tidak boleh kosong";
        $_SESSION['pesan'] = $pesan;
        $_SESSION['alert1'] = "danger";
        // echo 'error: ' . $pesan;
    } else if (@$_POST['usernamebaru'] && @$_POST['passbaru'] && @$_POST['emailbaru']) {
        // echo 'masuk olah post';
        $pesan = "Username, Password dan Email tidak boleh kosong<br>";
        $usernamebaru = $_POST['usernamebaru'];
        $emailbaru = $_POST['emailbaru'];
        $passwordbaru = md5($_POST['passbaru']);
        $nobaru = @$_POST['nobaru'];



        // echo '<pre>';
        // echo 'usernamebaru: ' . $usernamebaru . '<br>';
        // echo 'emailbaru: ' . $emailbaru . '<br>';
        // echo 'passwordbaru: ' . $_POST['passbaru'] . '<br>';
        // echo 'passwordbaru: ' . $passwordbaru . '<br>';
        // echo 'nobaru: ' . $nobaru . '<br>';
        // echo '</pre>';
        // echo '<br>';
        // echo 'Pesan : ' . @$pesan;
        // die;

        include('config/konesi.php');

        // if($konek){
        //     echo 'berhasil konek';
        // } else {
        //     echo 'gagal konek';
        // }

        // die;

        $cek_stmt = mysqli_prepare($konek, "SELECT * FROM admin WHERE username = ? OR email = ?");
        mysqli_stmt_bind_param($cek_stmt, "ss", $usernamebaru, $emailbaru);
        mysqli_stmt_execute($cek_stmt);
        mysqli_stmt_store_result($cek_stmt);
        $cek2 = mysqli_stmt_num_rows($cek_stmt);

        if ($cek2 > 0) {
            $pesan = 'Username "' . $usernamebaru . '", sudah ada!';
            $_SESSION['pesan'] = $pesan;
            $_SESSION['alert1'] = "danger";
        } else {
            $cek_stmt = mysqli_prepare($konek, "SELECT * FROM admin WHERE email = ?");
            mysqli_stmt_bind_param($cek_stmt, "s", $emailbaru);
            mysqli_stmt_execute($cek_stmt);
            mysqli_stmt_store_result($cek_stmt);
            $cek2 = mysqli_stmt_num_rows($cek_stmt);

            if ($cek2 > 0) {
                $pesan = 'Email "' . $emailbaru . '", sudah ada!';
                $_SESSION['pesan'] = $pesan;
                $_SESSION['alert1'] = "danger";
            } else {
                $insert_stmt = mysqli_prepare($konek, "INSERT INTO admin (username, email, password, wa, foto) VALUES (?, ?, ?, ?, 'default.jpg')");
                mysqli_stmt_bind_param($insert_stmt, "ssss", $usernamebaru, $emailbaru, $passwordbaru, $nobaru);
                $sql = mysqli_stmt_execute($insert_stmt);

                if ($sql) {
                    $pesan = "Berhasil menambahkan admin! " . $usernamebaru;
                    $_SESSION['pesan'] = $pesan;
                    $_SESSION['alert1'] = "success";
                } else {
                    $pesan = "Gagal menambahkan admin! " . $usernamebaru;
                    $_SESSION['pesan'] = $pesan;
                    $_SESSION['alert1'] = "danger";
                }
            }
        }

        //     echo '<br>';
        //     echo 'Pesan : ' . @$pesan;
        //     echo '<br>';
        //     if($cek){
        //         echo 'berhasil cek';
        //     } else {
        //         echo 'gagal cek';
        //     }

        //     die;
        // } else {
        //     echo "tidak ada POST";
        //     die;
    }
}
?>

<?php

$url = isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : '';

// ambil data dari tabel statusnya
$sql = mysqli_query($konek, "SELECT * FROM statusnya");
$data = mysqli_fetch_array($sql);

$info = $data['info'];
$waktumasuk = $data['waktumasuk'];
$waktupulang = $data['waktupulang'];
$wa = $data['wa'];
$wta = $data['wta'];
$wtp = $data['wtp'];
$wp = $data['wp'];

/*
echo ("info : ");
echo ($info);
echo (", waktumasuk : ");
echo ($waktumasuk);
echo (", waktupulang : ");
echo ($waktupulang);
echo (", wa : ");
echo ($wa);
echo (", wta : ");
echo ($wta);
echo (", wtp : ");
echo ($wtp);
echo (", wp : ");
echo ($wp);
die;
*/

if ($info == 5) {
    $harikerja = "Senin - Jum'at";
} elseif ($info == 6) {
    $harikerja = "Senin - Sabtu";
} elseif ($info == 7) {
    $harikerja = "Semua Hari";
} else {
    $harikerja = "";
}



?>

<div class="judul">
    <h1>Pengaturan</h1>
</div>
<div id="setting" class="container">
    <?php include('_alert_label.php'); ?>
    <div class="nama-setting">
        Pengaturan waktu masuk dan waktu pulang
    </div>
    <form action="app/setting1.php" method="POST" enctype="multipart/form-data">
        <div class="setting-1">
            <div class="pilihhari">
                <label class="lb1" for="pilihhari">Hari Buka Akses</label>
                <select id="pilihhari" class="form-select" aria-label="Default select example" name="pilihhari">
                    <option value="<?= $info; ?>" selected><?= isset($harikerja) ? $harikerja : "Tidak di set"; ?></option>
                    <option value="5">Senin - Jum'at</option>
                    <option value="6">Senin - Sabtu</option>
                    <option value="7">Semua Hari</option>
                    <option value="0">Tidak di set</option>
                </select>
            </div>

            <div class="waktumasuk">
                <label class="lb2" for="waktumasuk">Waktu Masuk</label>
                <input id="waktumasuk" type="time" name="waktumasuk" class="form-control" value="<?= $waktumasuk; ?>">
            </div>

            <div class="waktupulang">
                <label class="lb3" for="waktupulang">Waktu Pulang</label>
                <input id="waktupulang" type="time" name="waktupulang" class="form-control" value="<?= $waktupulang; ?>">
            </div>
            <div id="tombolsetting">
                <label for="">Terapkan Set.</label>
                <input type="submit" value="Terapkan" class="btn btn-success" name="btn_terapkan">
            </div>
        </div>
        <div class="border border-dark" style="width: 50%;"></div>
    </form>

    <div class="nama-setting">Pengaturan Batas Akses</div>
    <form action="app/setting2.php" method="POST" enctype="multipart/form-data">
        <div class="setting-2 mb-3">
            <div class="waktu-1">
                <label class="lb2-1" for="bukamasuk">Buka Akses Masuk</label>
                <input id="bukamasuk" type="time" name="bukamasuk" class="form-control" value="<?= $wa; ?>">
            </div>
            <div class="waktu-2">
                <label class="lb2-2" for="tutupmasuk">Tutup Akses Masuk</label>
                <input id="tutupmasuk" type="time" name="tutupmasuk" class="form-control" value="<?= $wta; ?>">
            </div>
            <div class="waktu-3">
                <label class="lb2-3" for="bukapulang">Buka Akses Pulang</label>
                <input id="bukapulang" type="time" name="bukapulang" class="form-control" value="<?= $wtp; ?>">
            </div>
            <div class="waktu-4">
                <label class="lb2-4" for="tutuppulang">Tutup Akses Pulang</label>
                <input id="tutuppulang" type="time" name="tutuppulang" class="form-control" value="<?= $wp; ?>">
            </div>
            <div id="tombolsetting2">
                <input type="submit" value="set" class="btn btn-success" name="btn_set">
            </div>
        </div>
    </form>
    <a href="<?= $url; ?>" class="btn btn-dark mr-2 mt-2">Kembali</a>

    <button class="btn btn-danger mt-2" data-bs-toggle="modal" data-bs-target="#ubahsettingadmin">Ubah User dan Password</button>
    <button class="btn btn-warning mt-2" data-bs-toggle="modal" data-bs-target="#tambahadmin">Tambah Admin</button>

    <div style="color: red; margin-top: 5px; text-align: right; margin-bottom: 50px;">
        <h6><a href="admin.php">user admin login</a></h6>
        <table style="float:right">
            <?php
            // include("config/konesi.php");
            $sql = "SELECT * FROM admin WHERE status = 'login'";
            $result = mysqli_query($konek, $sql);

            while ($row = mysqli_fetch_assoc($result)) {
            ?>
                <tbody>
                    <tr>
                        <td><?= $row['username']; ?></td>
                        <td> | </td>
                        <td><?= $row['timestamp']; ?></td>
                    </tr>
                </tbody>
            <?php }
            mysqli_close($konek); ?>
        </table>
    </div>
</div>
<!-- tambah admin -->
<div class="modal fade" id="tambahadmin" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="username" class="form-control" id="username" name="usernamebaru" placeholder="Username Admin">
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">Email address</label>
                        <input type="text" class="form-control" id="" aria-describedby="" name="emailbaru" placeholder="Email Admin">
                    </div>
                    <div class="mb-3">
                        <label for="pass" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" id="pass" class="form-control" name="passbaru" placeholder="Password">
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
                        <input type="tel" class="form-control" id="kontak" placeholder="No. tlp/ WA: 628XXXXXXXXXX" name="nobaru">
                    </div>

            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-primary" name="tambahadmin" value="Submit">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                </form>
            </div>
        </div>
    </div>
</div>
<!--  -->
<div class="modal fade" id="ubahsettingadmin" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="username" class="form-control" id="username">
                    </div>

                    <div class="mb-3">
                        <label for="" class="form-label">Password Lama</label>
                        <div class="input-group">
                            <input type="password" id="pswd2" class="form-control">
                            <div class="input-group-append">

                                <!-- kita pasang onclick untuk merubah icon buka/tutup mata setiap diklik  -->
                                <span id="mata2" onclick="ganti2()" class="input-group-text">

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
                        <label for="" class="form-label">Password Baru</label>
                        <div class="input-group">
                            <input type="password" id="pswd" class="form-control">
                            <div class="input-group-append">

                                <!-- kita pasang onclick untuk merubah icon buka/tutup mata setiap diklik  -->
                                <span id="mata" onclick="ganti()" class="input-group-text">

                                    <!-- icon mata bawaan bootstrap  -->
                                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z" />
                                        <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>

                    <script>
                        // membuat fungsi change
                        function ganti2() {

                            // membuat variabel berisi tipe input dari id='pass', id='pass' adalah form input password 
                            var y = document.getElementById('pswd2').type;

                            //membuat if kondisi, jika tipe x adalah password maka jalankan perintah di bawahnya
                            if (y == 'password') {

                                //ubah form input password menjadi text
                                document.getElementById('pswd2').type = 'text';

                                //ubah icon mata terbuka menjadi tertutup
                                document.getElementById('mata2').innerHTML = `<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-slash-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10.79 12.912l-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
                                                                    <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708l-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829z"/>
                                                                    <path fill-rule="evenodd" d="M13.646 14.354l-12-12 .708-.708 12 12-.708.708z"/>
                                                                    </svg>`;
                            } else {

                                //ubah form input password menjadi text
                                document.getElementById('pswd2').type = 'password';

                                //ubah icon mata terbuka menjadi tertutup
                                document.getElementById('mata2').innerHTML = `<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                                    <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                                    </svg>`;
                            }
                        }
                        // membuat fungsi change
                        function ganti() {

                            // membuat variabel berisi tipe input dari id='pass', id='pass' adalah form input password 
                            var x = document.getElementById('pswd').type;

                            //membuat if kondisi, jika tipe x adalah password maka jalankan perintah di bawahnya
                            if (x == 'password') {

                                //ubah form input password menjadi text
                                document.getElementById('pswd').type = 'text';

                                //ubah icon mata terbuka menjadi tertutup
                                document.getElementById('mata').innerHTML = `<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-slash-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10.79 12.912l-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
                                                                    <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708l-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829z"/>
                                                                    <path fill-rule="evenodd" d="M13.646 14.354l-12-12 .708-.708 12 12-.708.708z"/>
                                                                    </svg>`;
                            } else {

                                //ubah form input password menjadi text
                                document.getElementById('pswd').type = 'password';

                                //ubah icon mata terbuka menjadi tertutup
                                document.getElementById('mata').innerHTML = `<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                                    <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                                    </svg>`;
                            }
                        }
                    </script>
            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-primary" value="Submit">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // membuat fungsi change
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
</script>
<?php include('views/footer.php'); ?>
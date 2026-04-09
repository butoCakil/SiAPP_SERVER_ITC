<?php
function tabel_user()
{
    printf("<pre>%s</pre>", print_r("
====================================================================================================================================================================
================================================================   TABEL ADMIN, USER dan AKSES  ====================================================================
╒═══════╤══════════════════╤═══════════════════════╤═══════════════════════╤═════════════════════════╤═══════════════════╤═════════════════════════════════════════╕
|░░░░░░░|░░░░░░░░░░░░░░░░░░|░░░░░░░░░░░░░░░░░░░░░░░|░░░░░░░░░░░░░░░░░░░░░░░|░░░░░░░░░░░░░░░░░░░░░░░░░|░░░░░░░░░░░░░░░░░░░|░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░|
|░ No. ░|░░░ info_login ░░░|░░░ ★ level_login  ░░░░|░░░░░░ ★ akses  ░░░░░░░|░░░░░░ ket_akses ░░░░░░░░|░░░░ datab_login ░░|░░░ ★ hak akses => Keterangan  akses ░░░░|
|░░░░░░░|░░░░░░░░░░░░░░░░░░|░░░░░░░░░░░░░░░░░░░░░░░|░░░░░░░░░░░░░░░░░░░░░░░|░░░░░░░░░░░░░░░░░░░░░░░░░|░░░░░░░░░░░░░░░░░░░|░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░|
╞═══════╪══════════════════╪═══════════════════════╪═══════════════════════╪═════════════════════════╪═══════════════════╡═════════════════════════════════════════╡
|   1.  |    Super Admin   |    ★ superadmin       |   ◯───◯ superadmin    |  ──◯  superadmin        |     admin         | #       => Full akses, admin manager    |
├───────┼──────────────────┼───────────────────────┼───────────────────────┼─────────────────────────┼───────────────────┼─────────────────────────────────────────┤
|   2.  |    Admin         |    ★ admin            |   ◯─┬─◯ admin         |  ──◯  admin             |     dataguru      | 5★★★★★  => Rekap Guru, Siswa, setting   |
|       |                  |                       |     ├─◯ bk            |  ──◯  *kelas (X/XI/XII) |     dataguru      | 4★★★★   => Rekap Siswa (Tingkat)        |
|       |                  |                       |     └─◯ walikelas     |  ──◯  *kelas wali       |     dataguru      | 3★★★    => Rekap Siswa (kelas wali)     |
├───────┼──────────────────┼───────────────────────┼───────────────────────┼─────────────────────────┼───────────────────┼─────────────────────────────────────────┤
|   3.  |    GTK           |    ★ user_gtk         |   ◯─┬─◯ bk            |  ──◯  *kelas (X/XI/XII) |     dataguru      | 4★★★★   => Rekap Siswa (Tingkat)        |
|       |                  |                       |     ├─◯ walikelas     |  ──◯  *kelas wali       |     dataguru      | 3★★★    => Rekap Siswa (kelas wali)     |
|       |                  |                       |     └─◯ user (NULL)   |  ──◯  user_gtk (NULL)   |     dataguru      | 2★★     => Rekap Siswa (by Search)      |
├───────┼──────────────────┼───────────────────────┼───────────────────────┼─────────────────────────┼───────────────────┼─────────────────────────────────────────┤
|   4.  |    Siswa         |    ★ user_siswa       |   ◯───◯ user (NULL)   |  ──◯  user_siswa (NULL) |     datasiswa     | 1★      => Rekap Pribadi, Search        |
└───────┴──────────────────┴───────────────────────┴───────────────────────┴─────────────────────────┴───────────────────┴─────────────────────────────────────────┘
Keterangan [ hak akses ] :

NavBar 1 ═══╦═════  Home
            ╠═════  Beranda [ 1★ ]
            ╠═════  Data Presensi ══╗
            ║                       ╠═════  Rekap Harian
            ║                       ╠═════  Rekap Bulanan
            ║                       ╠═════  Rekap Individu  
            ║                       ╚═════  Semua Rekap Data  [ 2★★ ]
            ╠═════  Admin ══╗                                 
            ║ [ 5★★★★★ ]    ╠═════  Data Guru            
            ║               ╠═════  Data Siswa                
            ║               ╠═════  Tambah Data  [ # ]                 
            ║               ╚═════  Setting ══╗
            ╠═════  About                     ╚═════ Atur Admin
            ╚═════  Log Out / Log In                 [ # ]

NavBar 2 ═══╦═════  Statistik 
            ║       [ 5★★★★★ ]
            ║
            ╠═════  Data Guru dan Tendik ══╗
            ║       [ 5★★★★★ ]             ╠═════  Data Guru dan Tendik
            ║                              ╚═════  Rekap  ══╗
            ║                                               ╠═════  Harian
            ║                                               ╠═════  Bulanan
            ║                                               ╚═════  Tahunan
            ╠═════  Data Kelas (Siswa)  ══╗    
            ║                             ╟────────────────────────┬─── Data Siswa + Rekap Harian (Search Table) ─┐
            ║                             ║    [ 2★★ ]             ├─── Rekap Kelas, Rekap Per Tingkat (Select)  ─┤
            ║                             ║                        └─── Rekap perorangan (Klik Nama/IconPhoto)   ─┘
            ║                             ║    
            ║                             ╠══  Rekap Kelas ═════╗
            ║                             ║    [ 3★★★ ]         ╠════   Rekap Harian
            ║                             ║                     ╠════   Rekap Bulanan
            ║                             ║                     ╚════   Rekap Tahunan
            ║                             ║
            ║                             ╚══  Rekap Tingkat kelas  ═════╗
            ║                                  [ 4★★★★ ]                 ╠════   Rekap Harian
            ║                                                            ╠════   Rekap Bulanan
            ║                                                            ╚════   Rekap Tahunan
            ╠═════  Form ══════╗
            ║                  ╠════  Form Ijin (Search) ─┐ ┌── [ 5★★★★★ ] ⟹  [ input Ijin / Presensi WFH sendiri dan GTK lain sesuai hak akses ]
            ║                  ╚════  Presensi PJJ       ─┴─┼── [ 4★★★★ ]  ⟹  [ input ijin / presensi Siswa sesuai hak akses ]
            ║                                               ├── [ 3★★★ ]   ⟹  [ input ijin / presensi Siswa Kelas walinya ]   
            ║                                               ├── [ 2★★ ]    ⟹  [ input Ijin / Presensi WFH sendiri ]
            ╠═════  Profil ══╗                              └── [ 1★ ]     ⟹  [ input Ijin / Presensi PJJ sendiri ]
            ║                ╠═════  Profil Saya
            ║                ╠═════  Edit Profil
            ║                ╚═════  Kalender / Jadwal
            ╠═════  Log Out
            ╠═════  kembali ke Home
            ╚═════  Kembali

            sumber karakter: https://dev.w3.org/html5/html-author/charref
            &Sopf;&iopf;&sopf;&Aopf;&bopf;&aopf;&Ropf;
            𝕊𝕚𝕤𝔸𝕓𝕒ℝ
", true));
}

// tabel_user();

date_default_timezone_set('Asia/Jakarta');
$timestamp = date('Y-m-d H:i:s');

include('config/konesi.php');

session_start();

if (isset($_POST['login'])) {
    $email = $_POST['username'];
    $oripass = $_POST['password'];
    $password = md5($_POST['password']);

    if (empty($email)) {
        $_SESSION['pesan_error'] = 'Email tidak boleh kosong!';
    } elseif (empty($oripass)) {
        $_SESSION['pesan_error'] = 'Password tidak boleh kosong!';
    } elseif (strlen($oripass) < 4) {
        $_SESSION['pesan_error'] = 'Password minimal 4 karakter!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['pesan_error'] = 'Email tidak valid!';
    } else {
        $stmt = mysqli_prepare($konek, "SELECT * FROM admin WHERE username = ? OR email = ?");
        mysqli_stmt_bind_param($stmt, "ss", $email, $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            if ($password == $row['password']) {
                // Simpan session dan lakukan tindakan lainnya
                $_SESSION['username_login'] = $row['username'];
                $_SESSION['email_login'] = $row['email'];
                $_SESSION['password_login'] = $_POST['password'];
                $_SESSION['foto_login'] = $row['foto'];
                $_SESSION['info_login'] = 'Super Admin';
                $_SESSION['level_login'] = 'superadmin';
                $_SESSION['akses'] = 'admin';
                $_SESSION['ket_akses'] = 'superadmin';
                $_SESSION['datab_login'] = '';

                // Update status login di database
                $stmt = mysqli_prepare($konek, 'UPDATE admin SET status = "login" WHERE email = ?');
                mysqli_stmt_bind_param($stmt, "s", $row['email']);
                mysqli_stmt_execute($stmt);

                $_SESSION['pesan_login'] = 'Anda login sebagai super admin : ' . $row['username'];
            } else {
                $_SESSION['pesan_error'] = 'Password salah!';
            }
        } else {
            // Jika tidak ada data admin, cek guru
            $stmt = mysqli_prepare($konek, "SELECT * FROM dataguru WHERE email = ?");
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                if ($password == $row['password']) {
                    // Simpan session dan lakukan tindakan lainnya
                    $_SESSION['nokartu_login'] = $row['nokartu'];
                    $_SESSION['username_login'] = $row['nama'];
                    $_SESSION['nick_login'] = $row['nick'];
                    $_SESSION['email_login'] = $row['email'];
                    $_SESSION['password_login'] = $_POST['password'];
                    $_SESSION['foto_login'] = $row['foto'];
                    $_SESSION['info_login'] = $row['jabatan'];
                    $_SESSION['level_login'] = @$row['level_login'] ? $row['level_login'] : 'user_gtk';
                    $_SESSION['akses'] = @$row['akses'] ? $row['akses'] : '';
                    $_SESSION['ket_akses'] = @$row['ket_akses'] ? $row['ket_akses'] : '';
                    $_SESSION['datab_login'] = 'dataguru';

                    // Update status login di database
                    $stmt = mysqli_prepare($konek, 'UPDATE dataguru SET login = "login" WHERE email = ?');
                    mysqli_stmt_bind_param($stmt, "s", $row['email']);
                    mysqli_stmt_execute($stmt);

                    $_SESSION['pesan_login'] = 'Anda login sebagai GTK : ' . $row['nama'];
                } else {
                    $_SESSION['pesan_error'] = $row['nama'] . ', Password Anda salah!';
                }
            } else {
                // Jika tidak ada data guru, cek siswa
                $stmt = mysqli_prepare($konek, "SELECT * FROM datasiswa WHERE email = ?");
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) == 1) {
                    $row = mysqli_fetch_assoc($result);
                    if ($password == $row['password']) {
                        // Simpan session dan lakukan tindakan lainnya
                        $_SESSION['nokartu_login'] = $row['nokartu'];
                        $_SESSION['username_login'] = $row['nama'];
                        $_SESSION['nick_login'] = $row['nick'];
                        $_SESSION['email_login'] = $row['email'];
                        $_SESSION['password_login'] = $_POST['password'];
                        $_SESSION['foto_login'] = $row['foto'];
                        $_SESSION['info_login'] = $row['kelas'];
                        $_SESSION['level_login'] = 'user_siswa';
                        $_SESSION['akses'] = '';
                        $_SESSION['ket_akses'] = '';
                        $_SESSION['datab_login'] = 'datasiswa';

                        // Update status login di database
                        $stmt = mysqli_prepare($konek, 'UPDATE datasiswa SET login = "login" WHERE email = ?');
                        mysqli_stmt_bind_param($stmt, "s", $row['email']);
                        mysqli_stmt_execute($stmt);

                        $_SESSION['pesan_login'] = 'Anda login sebagai siswa : ' . $row['nama'];
                    } else {
                        $_SESSION['pesan_error'] = $row['nama'] . ', Passwordmu salah!';
                    }
                } else {
                    $_SESSION['pesan_error'] = 'Username tidak ditemukan!';
                }
            }
        }

        // Handle cookie dan session lainnya
        if (isset($_POST['ingataku'])) {
            $_SESSION['ingataku'] = $_POST['ingataku'];
            setcookie('username_login', $_POST['username'], time() + (60 * 60 * 24 * 30), "/");
            setcookie('password_login', $_POST['password'], time() + (60 * 60 * 24 * 30), "/");
            ini_set('session.cookie_lifetime', 2000000);
        } else {
            setcookie('username_login', '', time() - (60 * 60), "/");
            setcookie('password_login', '', time() - (60 * 60), "/");
            ini_set('session.cookie_lifetime', 0);
        }
    }
} else {
    $_SESSION['pesan_error'] = 'Silahkan login terlebih dahulu!';
}

mysqli_close($konek);

$_SESSION['login'] = true;
header("Location: ../");

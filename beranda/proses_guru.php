<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../config/konesi.php";

function bersihHP($no)
{
    return preg_replace('/[^0-9]/', '', $no);
}

function updateHPdalamTentang(string $tentangLama, string $hpBaru): string
{
    // Cari HP lama tepat sebelum ##
    if (preg_match('/#(\d+)##$/', $tentangLama, $m)) {
        $hpLama = $m[1];

        // Ganti hanya HP lama → HP baru
        return preg_replace(
            '/#' . preg_quote($hpLama, '/') . '##$/',
            '#' . $hpBaru . '##',
            $tentangLama,
            1
        );
    }

    // Jika tidak ditemukan pola HP → tambahkan di akhir (aman)
    return rtrim($tentangLama, '#') . '#' . $hpBaru . '##';
}

if ($_POST['aksi'] === 'tambah') {

    // ====== INPUT ======
    $nokartu = '0';
    $nip     = $_POST['nip'] ?? null;
    $nama    = $_POST['nama'];
    $status  = $_POST['status'];
    $info    = $_POST['info'] ?? 'NA';
    $jabatan = $_POST['jabatan'];
    $akses   = $_POST['akses'] ?: null;
    $saldo   = $_POST['saldo'] ?: 50000;
    $email   = $_POST['email'] ?: null;

    // ====== NICK ======
    $nick = substr(str_replace(' ', '', strtolower($nama)), 0, 6);
    $nick .= ($nip) ? substr($nip, -4) : rand(1000, 9999);

    // ====== KODE ======
    $kode = in_array($status, ['GR', 'PNS', 'PPPK', 'GTT']) ? 'GR' : 'KR';

    // ====== PASSWORD ======
    $password = !empty($_POST['password'])
        ? md5($_POST['password'])
        : null;

    // ====== HP / TENTANG ======
    $tentang = !empty($_POST['hp'])
        ? '##########' . bersihHP($_POST['hp']) . '##'
        : null;

    // ====== PREPARED STATEMENT ======
    $sql = "INSERT INTO dataguru (
        nokartu, nip, nama, nick, status, info, foto,
        created_at, updated_at,
        t_waktu_telat, poin, kode, jabatan, akses,
        saldo, total_transaksi, total_belanja,
        email, password, login, tentang, level_login
    ) VALUES (
        ?, ?, ?, ?, ?, ?, 'default.jpg',
        NOW(), NOW(),
        0, 0, ?, ?, ?,
        ?, 0, 0,
        ?, ?, 'logout', ?, NULL
    )";

    $stmt = $konek->prepare($sql);

    if (!$stmt) {
        die("Prepare gagal: " . $konek->error);
    }

    $stmt->bind_param(
        "sssssssssisss",
        $nokartu,
        $nip,
        $nama,
        $nick,
        $status,
        $info,
        $kode,
        $jabatan,
        $akses,
        $saldo,
        $email,
        $password,
        $tentang
    );

    if (!$stmt->execute()) {
        die("Execute gagal: " . $stmt->error);
    }

    $stmt->close();

    header("Location: dataguru.php?status=success");
    exit;
}

if ($_POST['aksi'] === 'edit') {
    $id      = $_POST['id'];
    $nokartu = '0';
    $nip     = $_POST['nip'] ?? null;
    $nama    = $_POST['nama'];
    $status  = $_POST['status'];
    $info    = $_POST['info'];
    $jabatan = $_POST['jabatan'];
    $akses   = $_POST['akses'] ?: null;
    $saldo   = $_POST['saldo'] ?: 50000;
    $email   = $_POST['email'] ?: null;

    $q = $konek->prepare("SELECT tentang FROM dataguru WHERE id=?");
    $q->bind_param("i", $id);
    $q->execute();
    $q->bind_result($dataTentangLama);
    $q->fetch();
    $q->close();

    // nick & kode di-rebuild agar konsisten
    $nick = substr(str_replace(' ', '', strtolower($nama)), 0, 6);
    $nick .= ($nip) ? substr($nip, -4) : rand(1000, 9999);

    $kode = in_array($status, ['GR', 'PNS', 'PPPK', 'GTT']) ? 'GR' : 'KR';

    $password = !empty($_POST['password'])
        ? md5($_POST['password'])
        : null;

    $hpBaru = !empty($_POST['hp'])
        ? bersihHP($_POST['hp'])
        : null;

    if ($hpBaru) {
        // $tentang = updateHPdalamTentang($dataTentangLama, $hpBaru);
        $tentang = $hpBaru
            ? updateHPdalamTentang($dataTentangLama, $hpBaru)
            : null;
    } else {
        $tentang = null; // supaya IF(? IS NULL, tentang, ?) bekerja
    }

    $sql = "UPDATE dataguru SET
        nip=?, nama=?, nick=?, status=?, info=?, kode=?,
        jabatan=?, akses=?, saldo=?, email=?,
        password=IF(? IS NULL, password, ?),
        tentang=IF(? IS NULL, tentang, ?),
        updated_at=NOW()
        WHERE id=?";

    $stmt = $konek->prepare($sql);

    $stmt->bind_param(
        "ssssssssisssssi",
        $nip,
        $nama,
        $nick,
        $status,
        $info,
        $kode,
        $jabatan,
        $akses,
        $saldo,
        $email,
        $password,
        $password,
        $tentang,
        $tentang,
        $id
    );

    $stmt->execute();
    $stmt->close();

    header("Location: dataguru.php?update=success");
    exit;
}

if ($_POST['aksi'] === 'hapus') {

    $id = $_POST['id'];

    $stmt = $konek->prepare("DELETE FROM dataguru WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: dataguru.php?hapus=success");
    exit;
}

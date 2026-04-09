<?php
include '../config/konesi.php';

$kelas = $_POST['kelas'] ?? '';
$wa = preg_replace('/\D/', '', $_POST['wa'] ?? '');

if (!$kelas || !$wa) {
    exit;
}

/* =========================
   AMBIL TENTANG LAMA
   ========================= */
$sql = "
SELECT tentang 
FROM dataguru 
WHERE akses='Wali Kelas' 
AND ket_akses='{$kelas}'
LIMIT 1
";
$result = mysqli_query($konek, $sql);
$data = mysqli_fetch_assoc($result);

$tentang_lama = $data['tentang'] ?? '';

/* =========================
   UPDATE NOMOR WA SAJA
   ========================= */
if (preg_match('/######\d+##/', $tentang_lama)) {
    // jika sudah ada nomor → ganti saja
    $tentang_baru = preg_replace(
        '/######\d+##/',
        "######{$wa}##",
        $tentang_lama
    );
} else {
    // jika belum ada → tambahkan di akhir
    $tentang_baru = $tentang_lama . "######{$wa}##";
}

/* =========================
   SIMPAN KEMBALI
   ========================= */
mysqli_query($konek, "
UPDATE dataguru 
SET tentang='" . mysqli_real_escape_string($konek, $tentang_baru) . "'
WHERE akses='Wali Kelas' 
AND ket_akses='{$kelas}'
");

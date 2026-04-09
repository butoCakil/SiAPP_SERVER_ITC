<?php
date_default_timezone_set("Asia/Jakarta");
$date = date("Y-m-d");
$time = date("H:i");

$targetDir = "uploads/";
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

if (isset($_FILES['file'])) {
    $fileName = $date . "_" . basename($_FILES['file']['name']);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
        echo "File berhasil diunggah: " . $fileName;
    } else {
        echo "Gagal mengunggah file.";
    }
} else {
    echo "Tidak ada file yang diterima.";
}

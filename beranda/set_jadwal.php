<?php

// print_r($_POST);
$data = array();

$pilihGuru = $_POST['selectedGuru'];
$pilihRuang = $_POST['selectedRuang'];

$pilihKelas = array();

$pilihKelas = $_POST['kelas'];
$pilihharke = $_POST['harke'];
$pilihke = $_POST['ke'];

// echo "Pilih Guru: $pilihGuru\n";
// echo "Pilih Ruang: $pilihRuang\n";

$hitung = count($pilihKelas);

for ($i = 0; $i < $hitung; $i++) {
    // echo "Pilih Kelas (" . ($i + 1) . ") : ";
    // print_r($pilihKelas[$i]);
    // echo "\n";

    $data['data'][$i]['guru'] = $pilihGuru;
    $data['data'][$i]['ruang'] = $pilihRuang;
    $data['data'][$i]['kelas'] = $pilihKelas[$i];
}

$hitung = count($pilihharke);

for ($i = 0; $i < $hitung; $i++) {
    // echo "Pilih Hari ke (" . ($i + 1) . ") : ";
    // print_r($pilihharke[$i]);
    // echo "\n";

    $data['data'][$i]['guru'] = $pilihGuru;
    $data['data'][$i]['ruang'] = $pilihRuang;
    $data['data'][$i]['harike'] = $pilihharke[$i];
}

$hitung = count($pilihke);

for ($i = 0; $i < $hitung; $i++) {
    // echo "Pilih Jam ke (" . ($i + 1) . ") : ";
    // print_r($pilihke[$i]);
    // echo "\n";

    $data['data'][$i]['guru'] = $pilihGuru;
    $data['data'][$i]['ruang'] = $pilihRuang;
    $data['data'][$i]['jamke'] = $pilihke[$i];
}

print_r($data);

<?php
header('Content-Type: application/json; charset=utf-8');
// header di bawah opsional, kalau mau dipaksa download bisa dipakai
// header('Content-Disposition: inline; filename="siapapi.json"');

$data = [
    "metadata" => [
        "jumlah_data" => 30,
        "timestamp"   => date("c"), // format ISO 8601
        "versi"       => "v0.1"
    ],
    "data" => [
        ["nokartu" => "0A2D8681", "nis" => "2875"],
        ["nokartu" => "0147663B", "nis" => "2878"],
        ["nokartu" => "93284105", "nis" => "2879"],
        ["nokartu" => "13E47B05", "nis" => "2876"],
        ["nokartu" => "536D6705", "nis" => "2877"],
        ["nokartu" => "23BC59E4", "nis" => "3443"],
        ["nokartu" => "73BBEEE4", "nis" => "3444"],
        ["nokartu" => "331EFBE4", "nis" => "3445"],
        ["nokartu" => "036D64E4", "nis" => "3446"],
        ["nokartu" => "6A573469", "nis" => "1234"],
        ["nokartu" => "AB12C345", "nis" => "4001"],
        ["nokartu" => "BC23D456", "nis" => "4002"],
        ["nokartu" => "CD34E567", "nis" => "4003"],
        ["nokartu" => "DE45F678", "nis" => "4004"],
        ["nokartu" => "EF56A789", "nis" => "4005"],
        ["nokartu" => "F067B890", "nis" => "4006"],
        ["nokartu" => "A178C901", "nis" => "4007"],
        ["nokartu" => "B289DA12", "nis" => "4008"],
        ["nokartu" => "C39AE123", "nis" => "4009"],
        ["nokartu" => "D4ABF234", "nis" => "4010"],
        ["nokartu" => "E5BC0123", "nis" => "4011"],
        ["nokartu" => "F6CD1234", "nis" => "4012"],
        ["nokartu" => "A7DE2345", "nis" => "4013"],
        ["nokartu" => "B8EF3456", "nis" => "4014"],
        ["nokartu" => "C9F01234", "nis" => "4015"],
        ["nokartu" => "DA012345", "nis" => "4016"],
        ["nokartu" => "EB123456", "nis" => "4017"],
        ["nokartu" => "FC234567", "nis" => "4018"],
        ["nokartu" => "AD345678", "nis" => "4019"],
        ["nokartu" => "BE456789", "nis" => "4020"]
    ]
];

echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

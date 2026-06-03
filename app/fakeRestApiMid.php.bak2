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
        ["nokartu" => "0A2D8681", "nis" => "2875", "nama" => "Rafael Arya", "kelas" => "X AT 1"],
        ["nokartu" => "0147663B", "nis" => "2878", "nama" => "Nadia Putri", "kelas" => "X DKV 2"],
        ["nokartu" => "93284105", "nis" => "2879", "nama" => "Zara Amalia", "kelas" => "XI TE 1"],
        ["nokartu" => "13E47B05", "nis" => "2876", "nama" => "Kevin Maulana", "kelas" => "XII AT 3"],
        ["nokartu" => "536D6705", "nis" => "2877", "nama" => "Alya Shari", "kelas" => "XI DKV 1"],
        ["nokartu" => "23BC59E4", "nis" => "3443", "nama" => "Farel Dwi Santoso", "kelas" => "X TE 2"],
        ["nokartu" => "73BBEEE4", "nis" => "3444", "nama" => "Maya Intan", "kelas" => "XII DKV 3"],
        ["nokartu" => "331EFBE4", "nis" => "3445", "nama" => "Iqbal Ramadhan", "kelas" => "XI AT 2"],
        ["nokartu" => "036D64E4", "nis" => "3446", "nama" => "Nico Pratama", "kelas" => "X AT 3"],
        ["nokartu" => "6A573469", "nis" => "1234", "nama" => "Sasha Olivia", "kelas" => "X DKV 1"],
        ["nokartu" => "AB12C345", "nis" => "4001", "nama" => "Arga Putra", "kelas" => "XI TE 3"],
        ["nokartu" => "BC23D456", "nis" => "4002", "nama" => "Keisha Putri", "kelas" => "XII AT 2"],
        ["nokartu" => "CD34E567", "nis" => "4003", "nama" => "Dimas Rizky", "kelas" => "X AT 2"],
        ["nokartu" => "DE45F678", "nis" => "4004", "nama" => "Luna Putri Aulia", "kelas" => "XI DKV 3"],
        ["nokartu" => "EF56A789", "nis" => "4005", "nama" => "Rizal Hidayat", "kelas" => "XII TE 1"],
        ["nokartu" => "F067B890", "nis" => "4006", "nama" => "Nadira Fathin", "kelas" => "X AT 1"],
        ["nokartu" => "A178C901", "nis" => "4007", "nama" => "Brian Mahesa", "kelas" => "XI DKV 2"],
        ["nokartu" => "B289DA12", "nis" => "4008", "nama" => "Citra Anindya", "kelas" => "XII AT 3"],
        ["nokartu" => "C39AE123", "nis" => "4009", "nama" => "Gibran Santoso", "kelas" => "X TE 1"],
        ["nokartu" => "D4ABF234", "nis" => "4010", "nama" => "Bella Nur", "kelas" => "XI AT 1"],
        ["nokartu" => "E5BC0123", "nis" => "4011", "nama" => "Keanu Xavier", "kelas" => "XII DKV 2"],
        ["nokartu" => "F6CD1234", "nis" => "4012", "nama" => "Putri Amelina", "kelas" => "X TE 3"],
        ["nokartu" => "A7DE2345", "nis" => "4013", "nama" => "Hafizh Alfarizi", "kelas" => "XI AT 3"],
        ["nokartu" => "B8EF3456", "nis" => "4014", "nama" => "Vina Sekar", "kelas" => "X DKV 2"],
        ["nokartu" => "C9F01234", "nis" => "4015", "nama" => "Raka Surya", "kelas" => "XII TE 2"],
        ["nokartu" => "DA012345", "nis" => "4016", "nama" => "Elisa Maharani", "kelas" => "XI DKV 1"],
        ["nokartu" => "EB123456", "nis" => "4017", "nama" => "Fauzan Ridho", "kelas" => "X AT 2"],
        ["nokartu" => "FC234567", "nis" => "4018", "nama" => "Naila Salsabila", "kelas" => "XII DKV 3"],
        ["nokartu" => "AD345678", "nis" => "4019", "nama" => "Bimo Aditya", "kelas" => "XI TE 1"],
        ["nokartu" => "BE456789", "nis" => "4020", "nama" => "Aulia Mahar", "kelas" => "X AT 1"]
    ]
];

echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

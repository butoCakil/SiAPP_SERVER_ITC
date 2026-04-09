<?php
ini_set('session.cookie_lifetime', 2000000);
session_start();
include "config/konesi.php";
//baca tanggal saat ini
date_default_timezone_set('Asia/Jakarta');
$tanggal = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inputDatakelas = isset($_POST['tampilkelasriwayat']) ? $_POST['tampilkelasriwayat'] : '';

    if (empty($inputDatakelas)) {
        $pilihKelas = "";
    } else {
        $pilihKelas = " AND kode = '$inputDatakelas'";
    }
} else {
}

$data_presensi = array();
$tampilhari = "0";
$tampilket = "semua";
$tampiljlm = "20";
$ket = $tanggal;
$kethari = "Hari ini";
$pilih = "WHERE tanggal = '$tanggal'";
$pilih2 = "WHERE tanggalijin = '$tanggal'";

$temp = [];

$query = "SELECT * FROM presensiEvent " . $pilih . "ORDER BY timestamp DESC";
$sql = mysqli_query($konek, $query);
$jml = mysqli_num_rows($sql);

while ($data = mysqli_fetch_assoc($sql)) {
    $nis = $data['nis'];
    $ket = strtoupper($data['keterangan']); // amankan konsistensi

    if (!isset($temp[$nis])) {
        $temp[$nis] = [
            'nis' => $nis,
            'timestamp' => $data['timestamp'],
            'source' => $data['ruang'],
            'ket' => []
        ];
    }

    // simpan keterangan, cegah duplikat
    if (!in_array($ket, $temp[$nis]['ket'])) {
        $temp[$nis]['ket'][] = $ket;
    }
}

$data_presensi = [];

foreach ($temp as $row) {
    $ket = $row['ket'];

    if (count($ket) == 1) {
        $ket_final = $ket[0]; // DZUHUR atau ASHAR
    } else {
        // mapping singkatan (opsional, tapi rapi)
        $map = [
            'DZUHUR' => 'Dhu',
            'ASHAR'  => 'Ashr'
        ];

        $singkat = [];
        foreach ($ket as $k) {
            $singkat[] = $map[$k] ?? $k;
        }

        $ket_final = implode(' + ', $singkat); // Dhu + Ashr
    }

    $data_presensi[] = [
        'nis' => $row['nis'],
        'timestamp' => $row['timestamp'],
        'source' => $row['source'],
        'ket' => $ket_final
    ];
}

// Query untuk daftarijin
$query2 = "SELECT * FROM daftarijin " . $pilih2 . " ORDER BY timestamp DESC";
$sql2 = mysqli_query($konek, $query2);
$jml2 = mysqli_num_rows($sql);

while ($data = mysqli_fetch_array($sql2)) {
    $data_presensi[] = array(
        'nis' => $data['nis'],
        'timestamp' => $data['timestamp'],
        'source' => $data['kode'] . " - M",
        'ket' => "IZIN"
    );
}

usort($data_presensi, function ($a, $b) {
    // Menggunakan strtotime() untuk memastikan format timestamp dibandingkan dengan benar
    $timestampA = strtotime($a['timestamp']);
    $timestampB = strtotime($b['timestamp']);

    // Mengurutkan berdasarkan timestamp, dari yang paling baru ke yang paling lama
    return $timestampB - $timestampA;
});

// echo "<pre>";
// print_r($data_presensi);
// echo "</pre>";

if ($jml || $jml2) {
?>
    <style>
        /* Menyembunyikan tampilan scrollbar di browser berbasis Webkit */
        .divrecent::-webkit-scrollbar {
            width: 0.5em;
        }

        .divrecent::-webkit-scrollbar-thumb {
            background-color: darkgrey;
            outline: 1px solid slategrey;
        }
    </style>
    <div id="divrecent">
        <table class="table recent-detail mt-1">
            <?php
            $no = 0;
            $nomor = 0;
            foreach ($data_presensi as $data) {
                $rowSiswa = "";

                $nis_recent = $data['nis'];
                $time_recent = $data['timestamp'];
                $formatted_time = date('H:i:s', strtotime($time_recent));
                $tgl_recent = date('d-m-Y', strtotime($time_recent));
                $sumberdata = @$data['source'];
                $keterangan = @$data['ket'];

                $queryDetail = "SELECT * FROM datasiswa WHERE nis = $nis_recent" . $pilihKelas;
                $sqlDetail = mysqli_query($konek, $queryDetail);

                $rowSiswa = mysqli_fetch_assoc($sqlDetail);

                if ($rowSiswa) {
                    $nomor++;
            ?>
                    <tr>
                        <td style="font-size: 12px;"><?= $nomor; ?></td>
                        <td><img id="foto-list" src="img/user/<?php echo $rowSiswa['foto']; ?>"></td>
                        <td><b><?php echo $rowSiswa['nama']; ?></b>

                            <div style="display: flex; flex-direction: column;">
                                <div style="font-size: 12px;">
                                    <label
                                        style="color: aliceblue; font-weight: 600; background-color: rgba(0, 0, 0, 0.5); border-radius: 10px 0px 10px 0px; padding: 1px 7px 0px 7px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 28 28"
                                            fill="none" stroke="white" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" class="feather feather-clock">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        <i class="fa-solid fa-clock-rotate-left"></i>

                                        <?= $formatted_time; ?>
                                    </label>
                                    <label
                                        style="background-color: rgba(255, 255, 255, 0.7); border-radius: 10px 0px 10px 0px; padding: 1px 7px 0px 7px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" width="12" height="12">
                                            <path fill-rule="evenodd"
                                                d="M4.75 0a.75.75 0 01.75.75V2h5V.75a.75.75 0 011.5 0V2h1.25c.966 0 1.75.784 1.75 1.75v10.5A1.75 1.75 0 0113.25 16H2.75A1.75 1.75 0 011 14.25V3.75C1 2.784 1.784 2 2.75 2H4V.75A.75.75 0 014.75 0zm0 3.5h8.5a.25.25 0 01.25.25V6h-11V3.75a.25.25 0 01.25-.25h2zm-2.25 4v6.75c0 .138.112.25.25.25h10.5a.25.25 0 00.25-.25V7.5h-11z">
                                            </path>
                                        </svg>

                                        <?= $tgl_recent; ?>
                                    </label>
                                </div>
                            </div>
                        </td>
                        <td style="color:white; font-size: 14px; vertical-align: middle;"><i><?php echo $rowSiswa['kelas']; ?></i></td>
                        <td style="font-size: 14px; vertical-align: middle; <?= ($sumberdata == 'IJIN - M') ? 'color: blue;' : 'color: black'; ?>">
                            <i><?php echo $sumberdata ?></i>
                            <?php
                            if ($sumberdata == "Izin Mens") {
                                $keterangan = "";
                            ?>
                            <?php } else { ?>
                                <br>
                                <i><?php echo $keterangan ?></i>
                            <?php } ?>
                        </td>
                    </tr>
            <?php
                }
            }
            echo "<label class='badge bg-warning' style='float: right;'>$nomor data</label>";

            mysqli_close($konek); ?>
        </table>
    </div>
<?php } else { ?>
    <h6 style="font-size: 14px; font-weight: 400; font-style: italic; float: right; margin-right: 10px;">Tampilkan:
        <?= $kethari; ?>, kelas <?= isset($inputDatakelas) ? $inputDatakelas : 'Semua'; ?>, <?= isset($nomor) ? $nomor : '0'; ?> Data
    </h6><br>
    <h6 style="margin-left: 10%; margin-right: auto;">- Belum ada data untuk <?= $kethari; ?></h6>
<?php } ?>
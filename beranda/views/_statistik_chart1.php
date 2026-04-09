<script src="node_modules/chart.js/dist/chart.umd.js"></script>
<script src="node_modules/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js"></script>

<style>
    .chart-container {
        position: relative;
        height: 200px; /* Tinggi tetap */
        width: 100%;
        margin-bottom: 60px;
    }

    canvas {
        height: 100% !important; /* Memastikan canvas memenuhi tinggi container */
        width: auto !important; /* Memastikan lebar canvas otomatis */
    }
</style>

<?php
/* 
Array
(
    [1] => Array
        (
            [info] => X AT 1
            [tanggal] => Array
                (
                    [0] => 2024-09-02
                    [1] => 2024-09-03
                    [2] => 2024-09-04
                    [3] => 2024-09-05
                    [4] => 2024-09-06
                    [5] => 2024-09-09
                    [6] => 2024-09-10
                    [7] => 2024-09-11
                    [8] => 2024-09-12
                    [9] => 2024-09-13
                    [10] => 2024-09-16
                    [11] => 2024-09-17
                    [12] => 2024-09-18
                    [13] => 2024-09-19
                    [14] => 2024-09-20
                    [15] => 2024-09-23
                    [16] => 2024-09-24
                    [17] => 2024-09-25
                    [18] => 2024-09-26
                    [19] => 2024-09-27
                    [20] => 2024-09-30
                )

            [data] => Array
                (
                    [0] => 0
                    [1] => 1
                    [2] => 31
                    [3] => 33
                    [4] => 32
                    [5] => 33
                    [6] => 33
                    [7] => 32
                    [8] => 33
                    [9] => 31
                    [10] => 0
                    [11] => 32
                    [12] => 33
                    [13] => 31
                    [14] => 31
                    [15] => 32
                    [16] => 32
                    [17] => 30
                    [18] => 27
                    [19] => 30
                    [20] => 0
                )

            [data_TLT] => Array
                (
                    [0] => 0
                    [1] => 0
                    [2] => 7
                    [3] => 6
                    [4] => 8
                    [5] => 7
                    [6] => 1
                    [7] => 0
                    [8] => 0
                    [9] => 0
                    [10] => 0
                    [11] => 1
                    [12] => 0
                    [13] => 0
                    [14] => 0
                    [15] => 0
                    [16] => 2
                    [17] => 0
                    [18] => 0
                    [19] => 0
                    [20] => 0
                )

        )

    [2] => Array
*/
    $tanggal_set = "$tahun_pilih-$bulan_pilih-1";
    $jumlah_hari_kerja = hitung_hari_kerja($tanggal_set);
    $jumlah_hari = hitung_hari($tanggal_set);

    $datachart = array();

    $j = 0;
    $i = 0;
    foreach ($data_kodeinfo as $value_kodeinfo) {
        if ($j > 2) {
            $i++;
            $datachart[$i]['info'] =  $value_kodeinfo['info'];

            for ($k = 1; $k <= $jumlah_hari; $k++) {
                $ketMSK = 0;
                $ketTLT = 0;
                $ketALP = 0;

                $tanggal_loop = "$tahun_pilih-" . duadigit($bulan_pilih) . "-" . duadigit($k);

                $_data_satukelas = cari_array_($value_kodeinfo['info'], $data_datasiswa, 'kelas');
                $_jml_1_kelas = count($_data_satukelas);

                $tgl_Ymd = date('Ymd', strtotime($tanggal_loop));
                $deskripsi = tanggalMerah($tgl_Ymd);

                if ($harikerja == '5') {
                    $harilibur = limaharikerja($tanggal_loop);
                } elseif ($harikerja == '6') {
                    $harilibur = enamharikerja($tanggal_loop);
                }

                if ($harilibur == false && !$deskripsi) {
                    $hasil_presensi_loop = cari_data_ganda($data_kehadiran_bulan_ini, $tanggal_loop, "tanggal", $value_kodeinfo['info'], "info");
                    $jumlah_elemen = 0;
                    if (!empty($hasil_presensi_loop)) {
                        $jumlah_elemen = count($hasil_presensi_loop);

                        for ($ketM = 0; $ketM < $jumlah_elemen; $ketM++) {
                            $keteranganMasuk = $hasil_presensi_loop[$ketM]['ketmasuk'];

                            if ($keteranganMasuk == "MSK") {
                                $ketMSK++;
                            } elseif ($keteranganMasuk == "TLT") {
                                $ketTLT++;
                            }
                        }
                    }

                    $persent_loop = 0;
                    $ketALP = $_jml_1_kelas - $jumlah_elemen;
                    $persent_loop = number_format(($jumlah_elemen * 100 / $_jml_1_kelas), 0);
                    // $persent_MSK = number_format(($ketMSK * 100 / $_jml_1_kelas), 0);
                    $persent_TLT = number_format(($ketTLT * 100 / $_jml_1_kelas), 0);
                    $persent_ALP = number_format(($ketALP * 100 / $_jml_1_kelas), 0);

                    $datachart[$i]['tanggal'][] = $tanggal_loop;
                    $datachart[$i]['data'][] = $jumlah_elemen;
                    $datachart[$i]['data_TLT'][] = $ketTLT;
                }
            }      
        }                          
    
        $j++;
    }

    // echo "<pre>";
    // print_r($datachart);
    // echo "</pre>";



// Menampilkan grafik untuk setiap kelas
foreach ($datachart as $index => $kelas) {
    $tanggal = $kelas['tanggal'];
    $data_masuk = $kelas['data'];
    $data_tlt = $kelas['data_TLT'];

    $labels = json_encode(array_map(function($date) {
        return date('d M', strtotime($date));
    }, $tanggal));

    $data_masuk_json = json_encode($data_masuk);
    $data_tlt_json = json_encode($data_tlt);
    
    echo "<div class='chart-container'>";
    echo "<h3>Riwayat Kehadiran " . htmlspecialchars($kelas['info']) . "</h3>";
    echo "<canvas id='myChart$index'></canvas>";
    echo "</div>";
}
?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php
        foreach ($datachart as $index => $kelas) {
            $tanggal = $kelas['tanggal'];
            $data_masuk = $kelas['data'];
            $data_tlt = $kelas['data_TLT'];

            $labels = json_encode(array_map(function($date) {
                return date('d M', strtotime($date));
            }, $tanggal));

            $data_masuk_json = json_encode($data_masuk);
            $data_tlt_json = json_encode($data_tlt);
                
            echo "const ctx$index = document.getElementById('myChart$index');";
            echo "new Chart(ctx$index, {
                    type: 'line',
                    data: {
                        labels: $labels,
                        datasets: [
                            {
                                label: 'Kehadiran Masuk (MSK)',
                                data: $data_masuk_json,
                                borderWidth: 2,
                                borderColor: 'green',
                                fill: false,
                            },
                            {
                                label: 'Kehadiran Terlambat (TLT)',
                                data: $data_tlt_json,
                                borderWidth: 2,
                                borderColor: 'orange',
                                fill: false,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Riwayat Kelas " . htmlspecialchars($kelas['info']) . "',
                                color: 'black',
                                font: {
                                weight: 'bold',
                                size: 16
                                }
                            },
                            datalabels: {
                                // Position of the labels 
                                // (start, end, center, etc.)
                                anchor: 'end',
                                // Alignment of the labels 
                                // (start, end, center, etc.)
                                align: 'end',
                                // Color of the labels
                                // color: 'blue',
                                offset: -5, // Menurunkan posisi angka
                                
                                font: {
                                    size: 10, // Mengubah ukuran angka
                                    weight: 'bold',
                                },
                                formatter: function (value, context) {
                                // Display the actual data value
                                return value;
                                }
                            }
                        }
                    }
                });";
        }
        ?>
    });
</script>

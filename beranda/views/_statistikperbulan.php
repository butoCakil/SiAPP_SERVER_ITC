 <style>
     #tbl_rekap_persen_bulan th {
         text-align: center;
         vertical-align: middle;
     }

     #tbl_rekap_persen_bulan td {
         text-align: left;
     }
 </style>

 <?php
    $tanggal_set = "$tahun_pilih-$bulan_pilih-1";
    $jumlah_hari_kerja = hitung_hari_kerja($tanggal_set);
    $jumlah_hari = hitung_hari($tanggal_set);

    // $hasil_presensi_loop = cari_data_ganda($data_kehadiran_bulan_ini, "2024-03-01", "tanggal", "X AT 1", "info");
    // echo "<pre>";
    // print_r($hasil_presensi_loop);
    // echo "</pre>";
    // die;
    ?>

 <div id="" class="">
     <div class="text-center">
         <input type="month" class="form-control-sm" name="" id="inputtgl">
         <div class="form-text"><span class="text-danger m-0">*</span>&nbsp;Klik <i class="far fa-calendar"></i> untuk memilih tampilan sesuai Bulan & Tahun</div>
     </div>
     <div id="rekap_bulan_semua">
        <table id="tbl_rekap_persen_bulan" class="table table-responsive table-striped table-striped-columns">
            <thead>
                <tr>
                    <th rowspan="3">No</th>
                    <th rowspan="3">Kelas</th>
                    <th colspan="<?= $jumlah_hari_kerja; ?>">Tanggal</th>
                </tr>

                <tr>
                    <?php
                        for ($i = 1; $i <= $jumlah_hari; $i++) {
                            $tanggal_loop = "$tahun_pilih-" . duadigit($bulan_pilih) . "-" . duadigit($i);
                            $bulan_ini = "$tahun_pilih-" . duadigit($bulan_pilih);

                            $hari_singkat = hari_indo_singkat($tanggal_loop);

                            if ($harikerja == '5') {
                                $harilibur = limaharikerja($tanggal_loop);
                            } elseif ($harikerja == '6') {
                                $harilibur = enamharikerja($tanggal_loop);
                            }

                            $minggu_t = date('W', strtotime($tanggal_loop));
                            if ($minggu_t % 2) {
                                $bg_col = "secondary";
                            } else {
                                $bg_col = "light";
                            }

                            if($tanggal_loop == $tanggal___){
                                $bg_col = "info";
                            }

                            if (!$harilibur) {
                                // echo "<th class='bg-$bg_col'>$i</th>";
                                echo "<th class='bg-$bg_col'>$hari_singkat</th>";
                                // echo "<th>$i<br>$hari_singkat</th>";
                            }
                        }
                        ?>
                </tr>
                <tr>
                    <?php
                        for ($i = 1; $i <= $jumlah_hari; $i++) {
                            $tanggal_loop = "$tahun_pilih-" . duadigit($bulan_pilih) . "-" . duadigit($i);
                            $bulan_ini = "$tahun_pilih-" . duadigit($bulan_pilih);

                            $hari_singkat = hari_indo_singkat($tanggal_loop);

                            if ($harikerja == '5') {
                                $harilibur = limaharikerja($tanggal_loop);
                            } elseif ($harikerja == '6') {
                                $harilibur = enamharikerja($tanggal_loop);
                            }

                            $minggu_t = date('W', strtotime($tanggal_loop));
                            if ($minggu_t % 2) {
                                $bg_col = "secondary";
                            } else {
                                $bg_col = "light";
                            }

                            if($tanggal_loop == $tanggal___){
                                $bg_col = "info";
                            }

                            if (!$harilibur) {
                                echo "<th class='bg-$bg_col'>$i</th>";
                                // echo "<th class='bg-$bg_col'>$hari_singkat</th>";
                                // echo "<th>$i<br>$hari_singkat</th>";
                            }
                        }
                        ?>
                </tr>
            </thead>

            <tbody>
                <?php
                    $j = 0;
                    $ii = 1;
                    foreach ($data_kodeinfo as $value_kodeinfo) {
                        if ($j > 2) {
                    ?>
                        <tr>
                            <td><?= $ii; ?></td>
                            <td>
                                <a href="rekapbulan.php?kelasjur=<?= preg_replace("/\s+/", "", $value_kodeinfo['info']); ?>&bulan=<?= $tahun_pilih; ?>-<?= duadigit($bulan_pilih); ?>" class="text-decoration-none" style="font-size: 1.4rem;">
                                    <?= preg_replace("/\s+/", "&nbsp;", $value_kodeinfo['info']); ?>
                                </a>
                            </td>
                            <?php
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
                                    if ($_jml_1_kelas != 0) {
                                        $persent_loop = number_format(($jumlah_elemen * 100 / $_jml_1_kelas), 0);
                                    } else {
                                        $persent_loop = 0; // atau bisa dikasih nilai lain / string "N/A"
                                    }

                                    // $persent_MSK = number_format(($ketMSK * 100 / $_jml_1_kelas), 0);
                                    if ($_jml_1_kelas != 0) {
                                        $persent_TLT = number_format(($ketTLT * 100 / $_jml_1_kelas), 0);
                                    } else {
                                        $persent_TLT = 0; // default jika jumlah kelas 0
                                    }

                                    if ($_jml_1_kelas != 0) {
                                        $persent_ALP = number_format(($ketALP * 100 / $_jml_1_kelas), 0);
                                    } else {
                                        $persent_ALP = 0; // default jika jumlah kelas 0
                                    }


                                    if ($persent_loop < 50) {
                                            $bg_persen_bln = "danger";
                                        } else if ($persent_loop < 75) {
                                            $bg_persen_bln = "warning";
                                        } else if ($persent_loop <= 100) {
                                            $bg_persen_bln = "success";
                                        } else {
                                            $bg_persen_bln = "secondary";
                                        }

                                        if($tanggal_loop == $tanggal___){
                                            $bg_td = "bg-info";
                                        } else {
                                            $bg_td = "";
                                        }

                                        if($persent_ALP > 99){
                                            echo "<td class=\"$bg_td\"><span class=\"badge bg-secondary\">-</span></td>";
                                        } else {
                                ?>
                                    <td class="<?= $bg_td; ?>" style="font-size: 1.4rem;">
                                        <span class="badge bg-success"><?= $jumlah_elemen; ?></span>
                                        <span class="badge bg-warning"><?= $ketTLT; ?></span>
                                        <span class="badge bg-danger"><?= $ketALP; ?></span>
                                        <!-- <span class="badge bg-dark"><?= $jumlah_elemen; ?>/<?= $_jml_1_kelas; ?></span> -->
                                    </td>
                            <?php
                                        }
                                    }
                                }
                                ?>
                        </tr>
                <?php
                            $ii++;
                        }
                        $j++;
                    }
                    ?>
            </tbody>
        </table>
    </div>
 </div>

 <script>
     // Fungsi untuk mengalihkan URL
     function redirectURL() {
         // Ambil nilai bulan dan tahun dari input
         var bulanTahun = document.getElementById("inputtgl").value;

         // Ubah URL dengan menambahkan atau mengganti parameter tgl
         var currentURL = window.location.href;
         var newURL;

         // Periksa apakah parameter tgl sudah ada di URL
         if (currentURL.indexOf('tgl=') !== -1) {
             // Jika sudah, ganti nilainya
             newURL = currentURL.replace(/tgl=[^&]*/, "tgl=" + bulanTahun);
         } else {
             // Jika belum, tambahkan sebagai parameter baru
             if (currentURL.indexOf('?') !== -1) {
                 newURL = currentURL + "&tgl=" + bulanTahun;
             } else {
                 newURL = currentURL + "?tgl=" + bulanTahun;
             }
         }

         // Redirect ke URL baru
         window.location.href = newURL;
     }

     // Tambahkan event listener untuk memanggil fungsi ketika nilai input bulan dan tahun berubah
     document.getElementById("inputtgl").addEventListener("change", redirectURL);

     function setInputValueFromURL() {
         var urlParams = new URLSearchParams(window.location.search);
         var tglParam = urlParams.get('tgl');
         if (tglParam) {
             document.getElementById("inputtgl").value = tglParam;
         } else {
             // Jika tidak ada parameter tgl, atur nilai input menjadi bulan ini
             var today = new Date();
             var yearMonth = today.getFullYear() + '-' + ('0' + (today.getMonth() + 1)).slice(-2);
             document.getElementById("inputtgl").value = yearMonth;
         }
     }

     // Panggil fungsi untuk mengisi nilai input saat halaman dimuat
     setInputValueFromURL();
 </script>


 <script>
     $(function() {
         $("#tbl_rekap_persen_bulan").DataTable({
             dom: 'fBt',
             "autoWidth": false,
             "responsive": true,
             "lengthChange": true,
             "lengthMenu": [
                 [-1, 10, 20, 30, -1],
                 ["Semua", 10, 20, 30, "Semua"]
             ],
             "pagingType": "full",
             "language": {
                 "emptyTable": "Tida ada data untuk tanggal yang dipilih.",
                 "info": "Ditampilkan _START_ sampai _END_ dari _TOTAL_ data",
                 "infoEmpty": "Ditampilkan 0 sampai 0 dari 0 data",
                 "infoFiltered": "(Disaring dari _MAX_ total data)",
                 "lengthMenu": "Tampilkan _MENU_ baris data",
                 "loadingRecords": "Memuat...",
                 "processing": "Memproses...",
                 "search": "Cari:",
                 "zeroRecords": "Tidak ditemukan data yang sesuai.",
                 "paginate": {
                     "first": "<<",
                     "last": ">>",
                     "next": "lanjut >",
                     "previous": "< sebelum"
                 },
             },
             "buttons": [{
                     extend: 'print',
                     customize: function(win) {
                         $(win.document.body).find('table')
                             .addClass('compact')
                             .css('font-size', '8px');

                         $(win.document.body).find('h1')
                             .css('text-align', 'center');

                         $(win.document.body).find('table')
                             .css('width', 'auto');

                         $(win.document.body).find('table')
                             .css('margin', 'auto');

                         $(win.document.body).find('table')
                             .css('pageOrientation', 'landscape');
                     }
                 },
                 {
                     extend: 'pdfHtml5',
                     customize: function(doc) {
                         doc.defaultStyle.fontSize = 8;
                         doc.styles.tableHeader.fontSize = 8;
                         doc.styles.title.fontSize = 14;
                         doc.styles.title.alignment = 'center';
                         doc.pageOrientation = 'landscape';
                     }
                 },
                 {
                     extend: 'excel',
                     customize: function(xlsx) {
                         var sheet = xlsx.xl.worksheets['sheet1.xml'];
                         $('row c', sheet).attr('s', '50');
                     }
                 },
                 //  'colvis'
             ]
         }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
     });

     // Tambahkan CSS untuk mengatur orientasi halaman
     var style = document.createElement('style');
     style.type = 'text/css';
     style.innerHTML = '@media print { @page { size: landscape; } table.dataTable { width: 100% !important; } table.dataTable th, table.dataTable td { width: auto !important; } }';
     document.head.appendChild(style);
 </script>
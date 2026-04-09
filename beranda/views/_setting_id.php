<style>
    #container_inputkartu {
        position: sticky;
        top: 0;
        background-color: #fff;
        /* Atur warna latar belakang sesuai kebutuhan */
        z-index: 100;
        /* Atur indeks z sesuai kebutuhan */
    }
</style>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 connectedSortable">
                <div class="card elevation-5">
                    <div class="card-header bg-gradient-navy">
                        <h3 class="card-title">
                            <i class="fas fa-cog fa-spin"></i></i>&nbsp;
                            INPUT ID
                        </h3>
                        <div class="card-tools">
                            <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Mengubah Setting Aplikasi, dan mengatur Admin"></i>
                            <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times text-light"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- input nokartru -->
                        <div id="container_inputkartu">

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1" onclick="loadDB('GTK');" <?= ($getdb == "GTK") ? " checked" : ""; ?>>
                                <label class="form-check-label" for="flexRadioDefault1">
                                    GTK
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2" onclick="loadDB('SISWA');" <?= ($getdb == "SISWA") ? " checked" : ""; ?>>
                                <label class="form-check-label" for="flexRadioDefault2">
                                    SISWA
                                </label>
                            </div>

                            <div id="inputkartu"></div>
                        </div>
                        <?php if ($db) { ?>
                            <div class="table-responsive">
                                <table id="tabeldaftarnomorID" class="table text-center table-striped-columns">
                                    <thead class="bg-dark">
                                        <th>No</th>
                                        <th>SET</th>
                                        <th>ID-Kartu</th>
                                        <th>
                                            <?php
                                            if ($getdb == "GTK") {
                                                echo "NIP";
                                            } elseif ($getdb == "SISWA") {
                                                echo "NIS";
                                            }
                                            ?>
                                        </th>
                                        <th>NAMA</th>
                                        <th>INFO</th>
                                        <!-- <th>OPSI</th> -->
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        $index_warna = true;
                                        $db_kode = '';
                                        foreach ($sql_db as $dt) {
                                            $db_id = $dt['id'];
                                            $db_nokartu = $dt['nokartu'];
                                            $db_noinduk = @$dt["$ni"];
                                            $db_nama = $dt['nama'];
                                            $db_info = $dt["$info"];

                                            $temp_kode = $dt[$urut];
                                            $array_warna = array('light', 'secondary');

                                            if ($temp_kode != $db_kode) {
                                                // Jika $temp_kode berbeda dari $db_kode sebelumnya
                                                $index_warna = !$index_warna;
                                            }

                                            $bg_warna = $array_warna[$index_warna ? 1 : 0];
                                            $db_kode = $dt[$urut];
                                        ?>
                                            <tr class="bg-<?= $bg_warna; ?>">
                                                <td><?= $i; ?></td>
                                                <td id="tblkartubaru_<?= $db_id; ?>">
                                                    <button class="btn btn-sm btn-primary" onclick="setNokartu('<?= $db_id; ?>', '<?= $db; ?>');">SET</button>
                                                </td>
                                                <td id="nokartubaru_<?= $db_id; ?>">
                                                    <label>
                                                        <?= $db_nokartu; ?>
                                                    </label>
                                                </td>
                                                <td><?= $db_noinduk ? $db_noinduk : "-"; ?></td>
                                                <td class="text-left"><?= $db_nama; ?></td>
                                                <td><?= $db_info . " - " . $db_kode; ?></td>
                                                <!-- <td>
                                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Data ID kartu baris ini akan di hapus. Yakin?');">DEL</button>
                                                    <button class="btn btn-sm btn-warning">EDIT</button>
                                                </td> -->
                                            </tr>
                                        <?php $i++;
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(function() {
        $("#tabeldaftarnomorID").DataTable({
            dom: 'flBtip',
            "autoWidth": false,
            "responsive": true,
            "lengthChange": true,
            "lengthMenu": [
                [30, 60, 100, -1],
                [30, 60, 100, "Semua"]
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
            "buttons": ["print", "pdf", "excel", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });

    function loadDB(_DB) {
        window.location.href = ("setting_id.php?db=" + _DB);
    }

    function setNokartu(id, db) {
        var nokartu = document.getElementById('nokartuInput').value;

        if (nokartu) {
            $.ajax({
                type: 'POST',
                url: 'simpan_nokartu.php',
                data: {
                    nokartu: nokartu,
                    id: id,
                    db: db,
                    key: 'updatenokartu$123'
                },
                success: function(data) {
                    if (data === "Berhasil ubah nokartu") {
                        document.getElementById("tblkartubaru_" + id).innerHTML = '<button class="btn btn-sm btn-success" onclick="setNokartu(\'' + id + '\', \'' + db + '\');">SET</button>';
                        document.getElementById("nokartubaru_" + id).innerHTML = '<label>' + nokartu + '</label>';
                    }

                    // alert(data);
                },
                error: function() {
                    alert('Terjadi kesalahan saat menyimpan nomor ke server.');
                }
            });
        } else {
            alert("SCAN kartu untuk mendapatkan nomor ID");
        }
    }
</script>
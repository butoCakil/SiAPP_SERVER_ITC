<?php

?>

<section class="content">
    <div class="container-fluid">
        <div class="card elevation-3 bg-primary bg-gradient-primary border-0" style="z-index: 1;">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i>&nbsp;Catatan Presensi Kelas
                </h3>
                <div class="card-tools">
                    <i class="fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Menampilkan Catatan Presensi kelas per siswa"></i>
                    &nbsp;
                    <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
        </div>
        <div>
            <div class="alert alert-default-info">
                <table>
                    <thead>
                        <tr>
                            <td>Nama &nbsp;</td>
                            <td>:&nbsp;<?= @$data_siswa[0]['nama']; ?></td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Kelas &nbsp;</td>
                            <td>:&nbsp;<?= @$data_siswa[0]['kelas']; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <table id="tabel_catatan" class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Ruangan</th>
                        <th>Status</th>
                        <th>Jam Ke</th>
                        <th>Sampai Jam</th>
                        <th>Catatan</th>
                        <th>APK+</th>
                        <th>Guru</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (@$_hit_data_info) {
                        for ($i = 0; $i < $_hit_data_info; $i++) { ?>
                            <tr>
                                <td><?= $i + 1; ?></td>
                                <td><?= $data_info[$i]['tanggal']; ?></td>
                                <td><?= $data_info[$i]['ruangan']; ?></td>
                                <td><?= $data_info[$i]['status']; ?></td>
                                <td><?= $data_info[$i]['mulai_jamke']; ?></td>
                                <td><?= $data_info[$i]['sampai_jamke']; ?></td>
                                <td><?= $data_info[$i]['catatan']; ?></td>
                                <td>
                                    <span class="badge badge-primary"><?= $data_info[$i]['aff']; ?></span>&nbsp;
                                    <span class="badge badge-primary"><?= $data_info[$i]['psi']; ?></span>&nbsp;
                                    <span class="badge badge-primary"><?= $data_info[$i]['kog']; ?></span>&nbsp;
                                    <span class="badge badge-primary"><?= $data_info[$i]['plus']; ?></span>
                                </td>
                                <td>
                                    <?php
                                    $nick_guru = $data_info[$i]['nick_guru'];

                                    $qu_guru = mysqli_query($konek, "SELECT nama FROM dataguru WHERE nick = '$nick_guru'");
                                    echo (@$data__ = mysqli_fetch_row($qu_guru)[0]);
                                    ?>
                                </td>
                            </tr>
                    <?php }
                    } ?>
                </tbody>
            </table>

            <button class="btn btn-dark btn-sm" onclick="window.close();"><i class="fas fa-arrow-alt-circle-left"></i> Kembali</button>
        </div>
    </div>
</section>
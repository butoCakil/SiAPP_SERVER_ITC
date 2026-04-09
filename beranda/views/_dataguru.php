<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function ambilHPdariTentang(?string $tentang): ?string
{
    if (!$tentang) {
        return null;
    }

    if (preg_match('/#(\d+)##$/', $tentang, $m)) {
        return $m[1];
    }

    return null;
}

$hp = "";
?>
<section class="content">
    <div class="container-fluid">
        <div class="card elevation-3 bg-primary bg-gradient-primary border-0" style="z-index: 1;">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i>&nbsp;
                    Rekap Data Guru
                </h3>
                <div class="card-tools">
                    <i class="fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Menampilkan Catatan Presensi Semua siswa dan dapat memilih perkelas serta perjurusan"></i>
                    &nbsp;
                </div>
            </div>
        </div>

        <div class="col-12" style="margin-top: -20px;">
            <div class="card elevation-3">
                <div class="card-body mb-5">
                    <div class="alert alert-info">
                        <p>
                            <i class="fas fa-info-circle"></i>&nbsp;
                            Tips : <br>
                            &nbsp;&nbsp;&nbsp;<i class="far fa-circle"></i>
                            Klik Judul kolom pada tabel untuk mengurutkan data. <br>
                            &nbsp;&nbsp;&nbsp;<i class="far fa-circle"></i>
                            Ketikkan kata pada kolom "Cari" untuk mencari data tabel.
                        </p>
                        <!-- <i class="fas fa-file text-fuchsia"></i> -->
                        <!-- <i class="far fa-circle"></i> -->
                        <!-- <i class="fas fa-heart"></i> -->
                    </div>
                    <button class="btn btn-sm btn-dark shadow bg-gradient-dark border-0 m-1" onclick="history.go(-1);"><i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Kembali</button>

                    <button class="btn btn-sm btn-primary m-1" data-bs-toggle="modal" data-bs-target="#modalTambahGuru">
                        <i class="fas fa-plus"></i> Tambah Guru
                    </button>

                    <table id="datagururekap" class="table table-bordered table-striped">
                        <thead>
                            <tr style="text-align: center; position: sticky;">
                                <th>No.</th>
                                <th>Foto</th>
                                <th>Nama</th>
                                <th>Status</th>
                                <th>info</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($ii = 0; $ii < $jumlah_data_dataguru; $ii++) { ?>
                                <tr>
                                    <td><?= $ii + 1; ?></td>
                                    <td>
                                        <img src="../img/user/<?= $data_dataguru[$ii]['foto']; ?>" alt="" style="height: 50px; width: 50px; object-fit: cover; object-position: center; border-radius: 50%;">
                                    </td>
                                    <td><?= $data_dataguru[$ii]['nama']; ?></td>
                                    <td><?= $data_dataguru[$ii]['status']; ?></td>
                                    <td><?= $data_dataguru[$ii]['jabatan']; ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="detail_gtk.php?nick=<?= $data_dataguru[$ii]['nick']; ?>&data=detail" class="btn btn-sm btn-success shadow bg-gradient-success border-0">
                                                <i class="fas fa-info-circle"></i>&nbsp;
                                            </a>
                                            <a href="detail_jurnal.php?nick=<?= $data_dataguru[$ii]['nick']; ?>&data=jurnal" class="btn btn-sm btn-warning shadow bg-gradient-warning border-0">
                                                <i class="fas fa-file"></i>
                                            </a>
                                        </div>
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#edit<?= $data_dataguru[$ii]['id']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#hapus<?= $data_dataguru[$ii]['id']; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="edit<?= $data_dataguru[$ii]['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <form method="POST" action="proses_guru.php">
                                            <div class="modal-content">
                                                <div class="modal-header bg-info">
                                                    <h5 class="modal-title">Edit Data Guru</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body row">
                                                    <input type="hidden" name="aksi" value="edit">
                                                    <input type="hidden" name="id" value="<?= $data_dataguru[$ii]['id']; ?>">

                                                    <div class="col-md-6 mb-2">
                                                        <label>NIP</label>
                                                        <input type="text" name="nip" class="form-control"
                                                            value="<?= $data_dataguru[$ii]['nip']; ?>">
                                                    </div>

                                                    <div class="col-md-6 mb-2">
                                                        <label>Nama</label>
                                                        <input type="text" name="nama" class="form-control" required
                                                            value="<?= $data_dataguru[$ii]['nama']; ?>">
                                                    </div>

                                                    <div class="col-md-4 mb-2">
                                                        <label>Status</label>
                                                        <select name="status" class="form-control" required>
                                                            <?php foreach (['GR', 'PNS', 'PPPK', 'GTT', 'PTT', 'KR'] as $s) { ?>
                                                                <option value="<?= $s; ?>" <?= $data_dataguru[$ii]['status'] == $s ? 'selected' : ''; ?>>
                                                                    <?= $s; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4 mb-2">
                                                        <label>Info</label>
                                                        <select name="info" class="form-control">
                                                            <?php foreach (['NA', 'PTT', 'TE', 'AT', 'DKV', 'BK', 'TIK'] as $i) { ?>
                                                                <option value="<?= $i; ?>" <?= $data_dataguru[$ii]['info'] == $i ? 'selected' : ''; ?>>
                                                                    <?= $i; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4 mb-2">
                                                        <label>Jabatan</label>
                                                        <select name="jabatan" class="form-control">
                                                            <?php foreach (['GURU', 'TENDIK', 'KEPALA SEKOLAH'] as $j) { ?>
                                                                <option value="<?= $j; ?>" <?= $data_dataguru[$ii]['jabatan'] == $j ? 'selected' : ''; ?>>
                                                                    <?= $j; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6 mb-2">
                                                        <label>Akses</label>
                                                        <select name="akses" class="form-control">
                                                            <option value="">Tidak diset</option>
                                                            <?php foreach (['Admin', 'Guru BK', 'Wali Kelas'] as $a) { ?>
                                                                <option value="<?= $a; ?>" <?= $data_dataguru[$ii]['akses'] == $a ? 'selected' : ''; ?>>
                                                                    <?= $a; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6 mb-2">
                                                        <label>Saldo</label>
                                                        <input type="number" name="saldo" class="form-control"
                                                            value="<?= $data_dataguru[$ii]['saldo']; ?>">
                                                    </div>

                                                    <div class="col-md-6 mb-2">
                                                        <label>Email</label>
                                                        <input type="email" name="email" class="form-control"
                                                            value="<?= $data_dataguru[$ii]['email']; ?>">
                                                    </div>

                                                    <div class="col-md-6 mb-2">
                                                        <label>Password (kosongkan jika tidak diubah)</label>
                                                        <input type="text" name="password" class="form-control">
                                                    </div>

                                                    <?php
                                                    $hp = ambilHPdariTentang($data_dataguru[$ii]['tentang']);
                                                    ?>
                                                    <div class="col-md-6 mb-2">
                                                        <label>No HP</label>
                                                        <div class="d-flex">
                                                            <span>+62</span>
                                                            <input type="text"
                                                                name="hp"
                                                                class="form-control"
                                                                oninput="filterHP(this)"
                                                                placeholder="Contoh: 85713872418"
                                                                value="<?= htmlspecialchars($hp ?? '') ?>">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button class="btn btn-success">Update</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="modal fade" id="hapus<?= $data_dataguru[$ii]['id']; ?>">
                                    <div class="modal-dialog">
                                        <form method="POST" action="proses_guru.php">
                                            <div class="modal-content">
                                                <div class="modal-body text-center">
                                                    <input type="hidden" name="aksi" value="hapus">
                                                    <input type="hidden" name="id" value="<?= $data_dataguru[$ii]['id']; ?>">
                                                    <p>Hapus data <b><?= $data_dataguru[$ii]['nama']; ?></b>?</p>
                                                    <button class="btn btn-danger">Ya, hapus</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
</section>

<div class="modal fade" id="modalTambahGuru" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="proses_guru.php">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title">Tambah Data Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row">
                    <input type="hidden" name="aksi" value="tambah">

                    <div class="col-md-6 mb-2">
                        <label>NIP</label>
                        <input type="number" name="nip" class="form-control">
                    </div>

                    <div class="col-md-6 mb-2">
                        <label>Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="">- pilih -</option>
                            <option>GR</option>
                            <option>PNS</option>
                            <option>PPPK</option>
                            <option>GTT</option>
                            <option>PTT</option>
                            <option>KR</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label>Info</label>
                        <select name="info" class="form-control">
                            <option>NA</option>
                            <option>PTT</option>
                            <option>TE</option>
                            <option>AT</option>
                            <option>DKV</option>
                            <option>BK</option>
                            <option>TIK</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label>Jabatan</label>
                        <select name="jabatan" class="form-control">
                            <option>GURU</option>
                            <option>TENDIK</option>
                            <option>KEPALA SEKOLAH</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-2">
                        <label>Akses</label>
                        <select name="akses" class="form-control">
                            <option value="">Tidak diset</option>
                            <option>Admin</option>
                            <option>Guru BK</option>
                            <option>Wali Kelas</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-2">
                        <label>Saldo</label>
                        <input type="number" name="saldo" class="form-control" value="50000">
                    </div>

                    <div class="col-md-6 mb-2">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>

                    <div class="col-md-6 mb-2">
                        <label>Password</label>
                        <input type="text" name="password" class="form-control">
                    </div>

                    <div class="col-md-6 mb-2">
                        <label>No HP</label>
                        <div class="d-flex">
                            <span>+62</span>
                            <input type="text"
                                name="hp"
                                class="form-control"
                                oninput="filterHP(this)"
                                placeholder="Contoh: 85713872418">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function filterHP(el) {
        let v = el.value;

        // 1. Buang semua selain angka
        v = v.replace(/\D/g, '');

        // 2. Jika diawali 62 → buang
        if (v.startsWith('62')) {
            v = v.substring(2);
        }

        // 3. Jika diawali 0 → buang
        if (v.startsWith('0')) {
            v = v.substring(1);
        }

        el.value = v;
    }
</script>
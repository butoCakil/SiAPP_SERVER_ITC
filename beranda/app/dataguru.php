<?php
// $sql = "SELECT * FROM dataguru ORDER BY ket_akses, nama ASC";
$sql = "SELECT * FROM dataguru";
$result = mysqli_query($konek, $sql);
$data = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<style>
    #dataguru_admin tbody td img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        object-position: top;
    }

    #dataguru_admin thead {
        background-color: #555;
        background: linear-gradient(to bottom, #555, #333);
        color: #fff;
        text-align: center;
        vertical-align: middle;
        position: sticky;
        top: 0;
        z-index: 2;
    }

    #dataguru_admin tbody td,
    #dataguru_admin thead th {
        text-align: center;
        vertical-align: middle;
    }

    #dataguru_admin #frezz {
        background-color: #fff;
        position: sticky;
        left: 0;
        z-index: 1;
    }

    #dataguru_admin thead #frezzer {
        background-color: #555;
        background: linear-gradient(to bottom, #555, #333);
        position: sticky;
        left: 0;
        top: 0;
        z-index: 3;
    }

    .deladmin .modal-body label {
        font-weight: 400;
    }
</style>

<div id="dataguru_admin">
    <table id="tabel_set_admin" class="table table-bordered">
        <thead>
            <tr>
                <th>No.</th>
                <th id="frezzer">Nama</th>
                <th>
                    <i class="fas fa-user-circle text-info"></i>
                </th>
                <th>
                    <i class="fas fa-info-circle text-light"></i>
                </th>
                <th>
                    Level Login
                </th>
                <th>
                    <i class="fas fa-key text-warning"></i>
                    &nbsp;
                    Akses
                </th>
                <th>
                    Ket. Akses
                </th>
                <th>
                    <i class="fas fa-circle text-danger"></i>
                    &nbsp;
                    Action
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            foreach ($data as $d) {
                if ($d['login'] == 'login') {
                    $bg_titik = 'text-success';
                    $bg_titik2 = 'text-light';
                } else {
                    $bg_titik = 'text-light';
                    $bg_titik2 = 'visually-hidden';
                }
            ?>
                <tr>
                    <td class="d-flex">
                        <i class="fas fa-circle <?= $bg_titik; ?>"></i>
                        &nbsp;
                        <!-- <?= $no++; ?> -->
                    </td>
                    <td id="frezz" class="text-left">
                        <?= $d['nama']; ?>
                    </td>
                    <td><img src="../img/user/<?= $d['foto']; ?>" width="100px" height="100px" class="elevation-2"></td>
                    <td><?= $d['status']; ?></td>
                    <td>
                        <?php if (@$d['level_login']) { ?>
                            <span class="badge badge-danger elevation-2">
                                <i class="fas fa-spinner fa-spin <?= $bg_titik2; ?>"></i>
                                <?= @$d['level_login']; ?>
                            </span>
                        <?php } else { ?>
                            <i class="fas fa-spinner fa-spin <?= $bg_titik; ?>"></i>
                        <?php } ?>
                    </td>
                    <td>
                        <?php if (@$d['akses']) { ?>
                            <span class="badge badge-primary elevation-2">
                                <?= @$d['akses']; ?>
                            </span>
                        <?php } ?>
                    </td>
                    <td>
                        <?php if (@$d['ket_akses']) { ?>
                            <span class="badge badge-info elevation-2">
                                <?= @$d['ket_akses']; ?>
                            </span>
                        <?php } ?>
                    </td>
                    <td>
                        <a href="#" class="badge badge-success text-decoration-none mb-1 elevation-2" data-bs-toggle="modal" data-bs-target="#setadmin_<?= $d['nokartu']; ?>">
                            <i class="fas fa-edit"></i>
                            &nbsp;
                            Set Admin
                        </a>
                        <a href="#" class="badge badge-danger text-decoration-none mt-1 elevation-2" data-bs-toggle="modal" data-bs-target="#deladmin_<?= $d['nokartu']; ?>">
                            <i class="fas fa-trash"></i>
                            &nbsp;
                            Del Admin
                        </a>
                    </td>
                </tr>

                <!-- modal start -->
                <!-- Modal Set Admin -->
                <div class="modal fade" id="setadmin_<?= $d['nokartu']; ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="setadminLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="setadminLabel">Set sebagai Admin "<?= $d['nama']; ?>"</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="container-fluid">
                                    <form method="post">
                                        <input type="hidden" name="nick_user" value="<?= $d['nick']; ?>">
                                        <div class="form-group">
                                            <label for="level_login">Level Login</label>
                                            <select name="level_login" id="level_login" class="form-control">
                                                <?php if ($d['level_login'] == 'admin') { ?>
                                                    <option value="admin" selected>Admin</option>
                                                    <option value="">Tidak di Set</option>
                                                <?php } else { ?>
                                                    <option value="admin">Admin</option>
                                                    <option value="" selected>Tidak di Set</option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="akses">Akses</label>
                                            <select name="akses" id="akses" class="form-control">

                                                <?php
                                                if ($d['akses'] == 'admin') {
                                                    $pilih_akses_1 = 'selected';
                                                    $pilih_akses_2 = '';
                                                    $pilih_akses_3 = '';
                                                    $pilih_akses_4 = '';
                                                } else if ($d['akses'] == 'BK') {
                                                    $pilih_akses_1 = '';
                                                    $pilih_akses_2 = 'selected';
                                                    $pilih_akses_3 = '';
                                                    $pilih_akses_4 = '';
                                                } else if ($d['akses'] == 'Wali Kelas') {
                                                    $pilih_akses_1 = '';
                                                    $pilih_akses_2 = '';
                                                    $pilih_akses_3 = 'selected';
                                                    $pilih_akses_4 = '';
                                                } else if ($d['akses'] == '') {
                                                    $pilih_akses_1 = '';
                                                    $pilih_akses_2 = '';
                                                    $pilih_akses_3 = '';
                                                    $pilih_akses_4 = 'selected';
                                                }
                                                ?>
                                                <option <?= $pilih_akses_1; ?> value="admin">Admin</option>
                                                <option <?= $pilih_akses_2; ?> value="BK">Guru BK</option>
                                                <option <?= $pilih_akses_3; ?> value="Wali Kelas">Wali Kelas</option>
                                                <option <?= $pilih_akses_4; ?> value="">Tidak di Set</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="ket_akses">Keterangan Akses <i class="font-italic font-weight-lighter">(isi dengan '-' jika tidak di set)</i></label>
                                            <input type="text" name="ket_akses" id="ket_akses" class="form-control" placeholder="admin / kelas (misal: X TAV 1) / kosongkan" value="<?= @$d['ket_akses']; ?>" required>
                                        </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i>&nbsp;Batal</button>
                                <button type="submit" name="set_admin" value="set" class="btn btn-success">
                                    <i class="fas fa-check"></i>&nbsp;
                                    Set
                                </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Del Admin -->
                <div class="modal fade deladmin" id="deladmin_<?= $d['nokartu']; ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deladminLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deladminLabel">Hapus Akses - Admin "<?= $d['nama']; ?>"</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="container-fluid">
                                    <form method="post">
                                        <input type="hidden" name="nick_user" value="<?= $d['nick']; ?>">
                                        <div class="form-group">
                                            <?php if ($d['level_login'] && $d['akses'] && $d['ket_akses']) {
                                                $disable_tmb = ''; ?>
                                                <label for="customCheck1">
                                                    <i class="fas fa-question-circle text-danger"></i>&nbsp;
                                                    Apakah anda yakin ingin menghapus admin dan akses dari "<?= $d['nama']; ?>" ?
                                                </label>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" name="cekbox" value="cek" class="custom-control-input" id="customCheck1">
                                                    <label class="custom-control-label" for="customCheck1">
                                                        Yakin
                                                    </label>
                                                </div>
                                            <?php } else {
                                                $disable_tmb = ' visually-hidden'; ?>
                                                <label for="customCheck1">
                                                    <i class="fas fa-exclamation-triangle text-warning"></i>
                                                    "<?= $d['nama']; ?>" tidak memiliki akses admin atau akses tidak di set.
                                                </label>
                                            <?php } ?>
                                        </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times"></i>&nbsp;
                                    Batal
                                </button>
                                <button type="submit" name="set_admin" value="del" class="btn btn-danger<?= $disable_tmb; ?>">
                                    <i class="fas fa-trash"></i>&nbsp;
                                    Hapus Akses
                                </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- modal end -->
            <?php } ?>
        </tbody>
    </table>
</div>


<script>
    $(function() {
        $("#tabel_set_admin").DataTable({
            dom: 'fBt',
            "autoWidth": false,
            "responsive": true,
            "lengthChange": true,
            "lengthMenu": [
                [-1, 7, 14, 21, 31],
                ["Semua", 7, 14, 21, 31]
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
</script>
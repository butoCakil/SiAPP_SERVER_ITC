<style>
    #kartu_kontak {
        display: flex;
        flex-wrap: wrap;
        /* gap: 10px; */
    }

    #profil_kartu {
        padding-left: 10px;
        padding-top: 0px;
        padding-right: 10px;
        padding-bottom: 0px;
    }

    #kartu_kontak #profil_kartu {
        width: 32%;
        margin-left: 10px;
    }

    #kartu_kontak #btn_setting_profil_kontak .badge {
        font-size: 12px;
    }

    #kartu_kontak #btn_setting_profil_kontak .badge a {
        text-decoration: aliceblue;
    }

    #kartu_kontak #profil_kartu #ftpp {
        margin-bottom: 30px;
    }

    #kartu_kontak #profil_kartu #ftpp img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        object-position: center;
        background-color: #fff;
        padding: 5px;
    }

    #kartu_kontak #profil_kartu #ftidpp {
        text-align: center;
    }

    #kartu_kontak #profil_kartu #idpp {
        text-align: center;
    }

    /* modal prev foto */
    .tampilfotoprofil .modal-content {
        background-color: transparent;
        box-shadow: none;
        border: none;
    }

    .tampilfotoprofil .modal-content img {
        border-top: 4px solid blue;
        border-radius: 5px;
        max-height: 70%;
        max-width: 100%;
        margin: auto;
    }

    .modal .tombol_close_gambar {
        /* position: absolute; */
        background-color: #fff;
        border: none;
        border-radius: 50%;
        padding: 20px;
        bottom: 0;
        margin-top: 10px;
        display: block;
        margin-left: auto;
        margin-right: auto;

    }

    @media only screen and (max-width: 749px) {
        #kartu_kontak #profil_kartu {
            width: 45%;
        }
    }

    @media only screen and (max-width: 488px) {
        #kartu_kontak #profil_kartu {
            width: 100%;
            margin-left: 10px;
            margin-right: 10px;
        }
    }

    a {
        cursor: pointer;
    }

    /* social media icon */

    #sosmed_icon {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .tombol_chat {
        border-radius: 30px 20px 20px 3px;
        padding: 5px 15px;
    }
</style>
<div id="kartu_kontak">

    <?php
    @$nomor = 0;
    while ($data_user = mysqli_fetch_assoc($result)) {
        @$nomor++;
        @$tentang = explode('#', @$data_user['tentang']);
        @$tentang_pendidikan = @$tentang[1];
        @$tentang_alamat = @$tentang[2];
        @$tentang_hobi = @$tentang[3];
        @$tentang_notes = @$tentang[4];
        @$tentang_nomorhp = @$tentang[5];
        @$tentang_ig = @$tentang[6];
        @$tentang_fb = @$tentang[7];
        @$tentang_twitter = @$tentang[8];
        @$tentang_line = @$tentang[9];
        @$tentang_wa = @$tentang[10];
        @$tentang_web = @$tentang[11];
        @$tentang_youtube = @$tentang[12];
    ?>

        <!-- Profile Image -->
        <div id="profil_kartu" class="card card-primary card-outline elevation-3" style='background: linear-gradient(to left, rgba(255, 255, 255, 0.86), rgba(255, 255, 255, 0.96)), url("../img/user/<?= @$data_user['foto'] ? @$data_user['foto'] : 'default.jpg'; ?>"); background-repeat: no-repeat; background-size: cover; background-position: center;'>
            <div class="card-body box-profile" id="accordion_1">
                <h6 class="badge badge-secondary elevation-2">
                    <?= @$nomor; ?>
                    <?php if (@$_SESSION['level_login'] == 'admin' || @$_SESSION['level_login'] == 'superadmin') { ?>
                        &nbsp;
                        <a href="editprofil.php?datab=<?= @$datab; ?>&nick=<?= @$data_user['nick']; ?>&linka=cocard" class="text-light">
                            <i class="fas fa-cog fa-spin"></i>
                            &nbsp;Edit
                        </a>
                    <?php } ?>
                </h6>
                <div id="btn_setting_profil_kontak">
                </div>

                <div id="ftidpp">
                    <div id="ftpp">
                        <a data-bs-toggle="modal" data-bs-target="#tampilfotoprofil<?= @$nomor; ?>">
                            <img class="elevation-2" src="../img/user/<?= @$data_user['foto'] ? @$data_user['foto'] : 'default.jpg'; ?>">
                        </a>
                    </div>

                    <div id="idpp" class="">
                        <h3 class="profile-username"><?= @$data_user['nama']; ?></h3>

                        <p class="text-muted"><?= @$data_user[$datab_info]; ?></p>
                    </div>

                </div>

                <!-- tombol follow -->
                <!-- <div class="mb-3">
                    <a class="btn btn-sm btn-primary bg-gradient-primary btn-block elevation-2"><b>Follow</b></a>
                </div> -->

                <div id="sosmed_icon" class="text-center">

                    <?php if ($tentang_web) { ?>
                        <!-- wabsite -->
                        <a class="btn bg-dark bg-gradient-dark btn-sm elevation-2" data-toggle="collapse" href="#collapseWEB<?= @$nomor; ?>">
                            <i class="iconify" data-icon="ion:earth"></i>
                        </a>
                    <?php } ?>

                    <?php if ($tentang_nomorhp) { ?>
                        <!-- telepon -->
                        <a class="btn bg-success bg-gradient-success btn-sm elevation-2" data-toggle="collapse" href="#collapseTelp<?= @$nomor; ?>">
                            <i class="iconify" data-icon="ion:call"></i>
                        </a>
                    <?php } ?>

                    <?php if ($tentang_fb) { ?>
                        <!-- facebook -->
                        <a class="btn bg-primary bg-gradient-primary btn-sm elevation-2" data-toggle="collapse" href="#collapseFB<?= @$nomor; ?>">
                            <i class="fab fa-facebook"></i>
                        </a>
                    <?php } ?>

                    <?php if ($tentang_ig) { ?>
                        <!-- Instagram -->
                        <a class="btn bg-danger bg-gradient-danger btn-sm elevation-2" data-toggle="collapse" href="#collapseIG<?= @$nomor; ?>">
                            <i class="fab fa-instagram"></i>
                        </a>
                    <?php } ?>

                    <?php if ($tentang_twitter) { ?>
                        <!-- twitter -->
                        <a class="btn btn-info btn-sm elevation-2" data-toggle="collapse" href="#collapseTW<?= @$nomor; ?>">
                            <i class="fab fa-twitter"></i>
                        </a>
                    <?php } ?>

                    <?php if ($tentang_youtube) { ?>
                        <!-- youtube -->
                        <a class="btn btn-danger bg-gradient-danger btn-sm elevation-2" data-toggle="collapse" href="#collapseYT<?= @$nomor; ?>">
                            <i class="fab fa-youtube"></i>
                        </a>
                    <?php } ?>

                    <!-- email -->
                    <a class="btn btn-warning bg-gradient-warning btn-sm elevation-2" data-toggle="collapse" href="#collapseEMAIL<?= @$nomor; ?>">
                        <i class="fas fa-envelope"></i>
                    </a>

                    <?php if ($tentang_line) { ?>
                        <!-- line -->
                        <a id="sosmed_line" class="btn btn-sm elevation-2" data-toggle="collapse" href="#collapseLINE<?= @$nomor; ?>">
                            <i class="fab fa-line fa-border-none"></i>
                        </a>
                    <?php } ?>

                    <?php if ($tentang_wa) { ?>
                        <!-- whatsapp -->
                        <a id="sosmed_WA" class="btn btn-sm elevation-2" data-toggle="collapse" href="#collapseWA<?= @$nomor; ?>">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    <?php } ?>

                    <!-- infomase -->
                    <a class="btn btn-primary bg-gradient-navy btn-sm elevation-2" data-toggle="collapse" href="#collapseINFO<?= @$nomor; ?>">

                        <i class="fas fa-info-circle"></i>
                        <!-- <i class="fas fa-chevron-down"></i> -->
                    </a>

                    <!-- infomase -->
                    <a class="btn btn-primary bg-gradient-info btn-sm elevation-2" data-toggle="collapse" href="#collapseTentang<?= @$nomor; ?>">

                        <i class="fas fa-info"></i>&nbsp;&nbsp;
                        <i class="fas fa-chevron-down"></i>
                    </a>
                </div>

                <!-- collapse -->

                <div id="collapseINFO<?= @$nomor; ?>" class="collapse" data-parent="#accordion_1">
                    <div class="card-body">
                        <div class="border mb-2"></div>
                        <a href="#collapseINFO<?= @$nomor; ?>" class="float-right" data-parent="#accordion_1" data-toggle="collapse">
                            <i class="fas fa-times"></i>
                        </a>

                        <table class="table table-borderless table-responsive-lg" style="text-align: left;">
                            <tr>
                                <td class="text-dark">
                                    <i class="fas fa-user-circle"></i>
                                </td>
                                <td>
                                    <?= @$data_user['nama'] ? @$data_user['nama'] : (@$nama_login ? @$nama_login : 'default.jpg'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-info">
                                    <i class="fas fa-info-circle"></i>
                                </td>
                                <td>
                                    <?= @$data_user[$datab_info]; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-danger">
                                    <i class="fas fa-map-marker-alt"></i>
                                </td>
                                <td>
                                    <?= @$tentang_alamat ? @$tentang_alamat : '-'; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-warning">
                                    <i class="fas fa-envelope"></i>
                                </td>
                                <td>
                                    <?= @$data_user['email'] ? @$data_user['email'] : (@$email_login ? @$email_login : 'default.jpg'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-primary">
                                    <i class="fas fa-phone"></i>
                                </td>
                                <td>
                                    <?= @$tentang_nomorhp ? ("0" . @$tentang_nomorhp) : '-'; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-success">
                                    <i class="fab fa-whatsapp"></i>
                                </td>
                                <td>
                                    <?= @$tentang_wa ? ("0" . @$tentang_wa) : '-'; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-secondary">
                                    <i class="fas fa-globe"></i>
                                </td>
                                <td>
                                    <?= @$tentang_web ? 'www.' . @$tentang_web : '-'; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-primary">
                                    <i class="fab fa-facebook"></i>
                                </td>
                                <td>
                                    <?= @$tentang_fb ? 'www.facebook.com/' . @$tentang_fb : '-'; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-danger">
                                    <i class="fab fa-instagram"></i>
                                </td>
                                <td>
                                    <?= @$tentang_ig ? 'www.instagram.com/' . @$tentang_ig : '-'; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-info">
                                    <i class="fab fa-twitter"></i>
                                </td>
                                <td>
                                    <?= @$tentang_twitter ? 'www.twitter.com/' . @$tentang_twitter : '-'; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-success">
                                    <i class="fab fa-line"></i>
                                </td>
                                <td>
                                    <?= @$tentang_line ? 'line.me/ti/p/~' . @$tentang_line : '-'; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-danger">
                                    <i class="fab fa-youtube"></i>
                                </td>
                                <td>
                                    <?= @$tentang_youtube ? @$tentang_youtube : '-'; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- collapse Telp -->
                <div id="collapseTelp<?= @$nomor; ?>" class="collapse" data-parent="#accordion_1">
                    <div class="card-body">
                        <div class="border mb-2"></div>
                        <a href="#collapseTelp<?= @$nomor; ?>" class="float-right" data-parent="#accordion_1" data-toggle="collapse">
                            <i class="fas fa-times"></i>
                        </a>
                        <a target="_blank" href="tel:62<?= @$tentang_nomorhp; ?>" class="btn btn-success bg-gradient-success btn-sm elevation-2" target="_blank" style="border: none;">
                            <i class="fas fa-phone"></i>
                            <span>&nbsp;
                                telepon
                            </span>
                        </a>&nbsp;
                        +62<?= @$tentang_nomorhp; ?>
                    </div>
                </div>

                <!-- collapse WA -->
                <div id="collapseWA<?= @$nomor; ?>" class="collapse" data-parent="#accordion_1">
                    <div class="card-body">
                        <div class="border mb-2"></div>
                        <a href="#collapseWA<?= @$nomor; ?>" class="float-right" data-parent="#accordion_1" data-toggle="collapse">
                            <i class="fas fa-times"></i>
                        </a>
                        <a target="_blank" id="sosmed_WA" href="https://wa.me/62<?= @$tentang_wa; ?>" class="tombol_chat btn btn-sm elevation-2" target="_blank" style="border: none;">
                            <i class="fab fa-whatsapp"></i>
                            <span>&nbsp;
                                Chat
                            </span>
                        </a>&nbsp;
                        +62<?= @$tentang_wa; ?>
                    </div>
                </div>

                <!-- collapse LINE -->
                <div id="collapseLINE<?= @$nomor; ?>" class="collapse" data-parent="#accordion_1">
                    <div class="card-body">
                        <div class="border mb-2"></div>
                        <a href="#collapseLINE<?= @$nomor; ?>" class="float-right" data-parent="#accordion_1" data-toggle="collapse">
                            <i class="fas fa-times"></i>
                        </a>
                        <a target="_blank" id="sosmed_line" href="https://line.me/ti/p/~<?= @$tentang_line; ?>" class="btn elevation-2" target="_blank">
                            <i class="fab fa-line fa-border-none"></i>
                        </a>
                        &nbsp;
                        <?= @$tentang_line; ?>
                    </div>
                </div>

                <!-- collapse Email -->
                <div id="collapseEMAIL<?= @$nomor; ?>" class="collapse" data-parent="#accordion_1">
                    <div class="card-body">
                        <div class="border mb-2"></div>
                        <a href="#collapseEMAIL<?= @$nomor; ?>" class="float-right" data-parent="#accordion_1" data-toggle="collapse">
                            <i class="fas fa-times"></i>
                        </a>
                        <a target="_blank" href="mailto:<?= @$data_user['email'] ? @$data_user['email'] : '-'; ?>" class="btn btn-warning bg-gradient-warning btn-sm elevation-2" target="_blank">
                            <i class="fas fa-envelope"></i>
                            &nbsp;
                            Kirim Email ke : &nbsp;
                        </a>
                        &nbsp;
                        <?= @$data_user['email'] ? @$data_user['email'] : '-'; ?>
                    </div>
                </div>

                <!-- collapse Youtube -->
                <div id="collapseYT<?= @$nomor; ?>" class="collapse" data-parent="#accordion_1">
                    <div class="card-body">
                        <div class="border mb-2"></div>
                        <a href="#collapseYT<?= @$nomor; ?>" class="float-right" data-parent="#accordion_1" data-toggle="collapse">
                            <i class="fas fa-times"></i>
                        </a>
                        <a target="_blank" href="https://www.youtube.com/<?= @$tentang_youtube; ?>" class="btn btn-danger bg-gradient-danger btn-sm elevation-2" target="_blank">
                            <i class="fab fa-youtube"></i>
                            &nbsp;Channel Youtube :&nbsp;
                        </a>
                        &nbsp;
                        <?= @$tentang_youtube; ?>
                    </div>
                </div>

                <!-- collapse Twitter -->
                <div id="collapseTW<?= @$nomor; ?>" class="collapse" data-parent="#accordion_1">
                    <div class="card-body">
                        <div class="border mb-2"></div>
                        <a href="#collapseTW<?= @$nomor; ?>" class="float-right" data-parent="#accordion_1" data-toggle="collapse">
                            <i class="fas fa-times"></i>
                        </a>
                        <a target="_blank" href="https://www.twitter.com/<?= @$tentang_twitter; ?>" class="btn btn-info btn-sm elevation-2" target="_blank">
                            <i class="fab fa-twitter"></i>&nbsp;
                            Twitter :&nbsp;
                        </a>
                        &nbsp;
                        <?= @$tentang_twitter; ?>
                    </div>
                </div>

                <!-- collapse Instagram -->
                <div id="collapseIG<?= @$nomor; ?>" class="collapse" data-parent="#accordion_1">
                    <div class="card-body">
                        <div class="border mb-2"></div>
                        <a href="#collapseIG<?= @$nomor; ?>" class="float-right" data-parent="#accordion_1" data-toggle="collapse">
                            <i class="fas fa-times"></i>
                        </a>
                        <a target="_blank" href="https://www.instagram.com/<?= @$tentang_ig; ?>" class="btn btn-danger bg-gradient-danger btn-sm elevation-2" target="_blank">
                            <i class="fab fa-instagram"></i>&nbsp;
                            Instagram :&nbsp;
                        </a>
                        &nbsp;
                        <?= @$tentang_ig; ?>
                    </div>
                </div>

                <!-- collapse Facebook -->
                <div id="collapseFB<?= @$nomor; ?>" class="collapse" data-parent="#accordion_1">
                    <div class="card-body">
                        <div class="border mb-2"></div>
                        <a href="#collapseFB<?= @$nomor; ?>" class="float-right" data-parent="#accordion_1" data-toggle="collapse">
                            <i class="fas fa-times"></i>
                        </a>
                        <a target="_blank" href="https://www.facebook.com/<?= @$tentang_fb; ?>" class="btn btn-primary bg-gradient-primary btn-sm elevation-2" target="_blank">
                            <i class="fab fa-facebook"></i>&nbsp;
                            Profil Facebook :&nbsp;
                        </a>
                        &nbsp;
                        <?= @$tentang_fb; ?>
                    </div>
                </div>

                <!-- collapse Website -->
                <div id="collapseWEB<?= @$nomor; ?>" class="collapse" data-parent="#accordion_1">
                    <div class="card-body">
                        <div class="border mb-2"></div>
                        <a href="#collapseWEB<?= @$nomor; ?>" class="float-right" data-parent="#accordion_1" data-toggle="collapse">
                            <i class="fas fa-times"></i>
                        </a>
                        <a target="_blank" href="https://www.<?= @$tentang_web; ?>" class="btn bg-dark bg-gradient-dark btn-sm elevation-2" target="_blank" data-toggle="tooltip" data-placement="top" title="www.<?= @$tentang_web; ?>">
                            <span class="iconify" data-icon="ion:earth"></span>&nbsp;
                            Kunjungi Website :&nbsp;
                        </a>
                        &nbsp;
                        www.<?= @$tentang_web; ?>
                    </div>
                </div>

                <!-- collapse Tentang -->
                <div id="collapseTentang<?= @$nomor; ?>" class="collapse" data-parent="#accordion_1">
                    <div class="card-body">
                        <a href="#collapseTentang<?= @$nomor; ?>" class="float-right mt-n3" data-parent="#accordion_1" data-toggle="collapse">
                            <i class="fas fa-times"></i>
                        </a>
                        <hr>
                        <strong><i class="fas fa-book mr-1"></i> Pendidikan</strong>

                        <p class="text-muted">
                            <!-- B.S. in Computer Science from the University of Tennessee at Knoxville -->
                            <?= @$tentang_pendidikan; ?>
                        </p>

                        <hr>

                        <strong><i class="fas fa-map-marker-alt mr-1"></i> Alamat Rumah</strong>

                        <p class="text-muted">
                            <?= @$tentang_alamat; ?>
                        </p>

                        <hr>

                        <strong><i class="fas fa-pencil-alt mr-1"></i> Hobi</strong>

                        <p class="text-muted">

                            <?php
                            @$tentang_hobi_ = explode(',', @$tentang_hobi);

                            @$jumlah_hobi = count(@$tentang_hobi_);
                            for ($key = 1; @$key <= @$jumlah_hobi; @$key++) {
                                if ($key == 1) {
                                    @$badge_hobi = 'badge-primary';
                                } elseif ($key == 2) {
                                    @$badge_hobi = 'badge-success';
                                } elseif ($key == 3) {
                                    @$badge_hobi = 'badge-info';
                                } elseif ($key == 4) {
                                    @$badge_hobi = 'badge-warning';
                                } elseif ($key == 5) {
                                    @$badge_hobi = 'badge-danger';
                                } elseif ($key == 6) {
                                    @$badge_hobi = 'badge-secondary';
                                } elseif ($key == 7) {
                                    @$badge_hobi = 'badge-dark';
                                } elseif ($key == 8) {
                                    @$badge_hobi = 'badge-light';
                                } else {
                                    @$badge_hobi = 'badge-info';
                                }

                                $value = $tentang_hobi_[$key - 1];
                            ?>
                                <span class="badge <?= @$badge_hobi; ?>"><?= @$value; ?></span>
                            <?php } ?>
                        </p>

                        <hr>

                        <strong><i class="far fa-file-alt mr-1"></i>
                            Bio
                        </strong>

                        <p class="text-muted">
                            <?= @$tentang_notes;; ?>
                        </p>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->

        <!-- Modal prev image -->
        <div class="modal fade tampilfotoprofil" id="tampilfotoprofil<?= @$nomor; ?>" tabindex="-1" aria-labelledby="tampilfotoprofilLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <img class="elevation-5" src="../img/user/<?= @$data_user['foto'] ? @$data_user['foto'] : 'default.jpg'; ?>">
                    <!-- <button type="button" class="btn-close tombol_close_gambar elevation-5" data-bs-dismiss="modal" aria-label="Close"></button> -->
                    <button type="button" class="btn-close tombol_close_gambar elevation-5 mb-5" data-bs-toggle="modal" data-bs-target="#modaldetailtentang<?= @$nomor; ?>"></button>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
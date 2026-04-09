<section class="content">
    <!-- <a href="#card9">kartu no 9</a> -->
</section>
<style>
    #kartu_kontak {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .flip-card {
        border-radius: 5px;
        background-color: transparent;
        width: 32%;
        height: 300px;
        perspective: 1000px;
    }

    .flip-card-front img {
        margin-top: 10px;
        margin-bottom: 10px;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        object-position: center;
        background-color: #fff;
        padding: 4px;
    }

    .flip-card-inner {
        border-radius: 5px;
        position: relative;
        width: 100%;
        height: 100%;
        text-align: center;
        transition: transform 0.6s;
        transform-style: preserve-3d;
        box-shadow: 2px 4px 5px 0 rgba(0, 0, 0, 0.5);
    }

    .flip-card:hover .flip-card-inner {
        transform: rotateY(180deg);
    }

    .flip-card-front,
    .flip-card-back {
        position: absolute;
        width: 100%;
        height: 100%;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        /* border line */
        padding: 20px;
        border-radius: 5px;
    }

    .flip-card-front,
    .modal-content {
        border: none;
        border-top: 4px solid blue;
        background-color: #fff;
        color: black;
    }

    .flip-card-back {
        /* border-top: 4px yellow solid; */
        /* background-color: #6495ED; */
        color: white;
        transform: rotateY(180deg);
    }

    .btn {
        border: none;
        margin: 2px;
    }

    .label_flip_back {
        display: flex;
        flex-direction: column;
        font-size: small;
        text-align: left;
    }

    .infomase {
        position: absolute;
        right: 20px;
    }

    .modaldetailtentang .modal-body img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        object-position: center;
        background-color: #fff;
        padding: 4px;
        margin-bottom: 5px;
    }

    .modaldetailtentang .modal-body .info_profil {
        padding: 10px;
        text-align: center;
    }

    .modaldetailtentang .detail_profil {
        padding: 10px;
    }

    .modaldetailtentang .detail_profil .kolom1 {
        width: 100%;
    }

    .modaldetailtentang .detail_profil .kolom2 {
        display: flex;
        flex-direction: column;
    }

    .modaldetailtentang .kolom2 a {
        text-decoration: none;
        color: dimgray;
        /* hover */
        transition: 0.2s;
    }

    a {
        text-decoration: none;
        cursor: pointer;
    }

    .modaldetailtentang .kolom2 div:hover {
        border: none;
        background-color: #6495ED;
        font-weight: 600;
        padding: 3px;
        border-radius: 5px;
        /* shadow */
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        color: #fff;
        cursor: pointer;
    }

    #nomorurutkartu {
        position: absolute;
        right: 20px;
    }

    #tomboleditprofil:hover {
        border: none;
        background-color: orangered;
        font-weight: 600;
        padding: 5px;
        border-radius: 5px;
        /* shadow */
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
        color: #fff;
        cursor: pointer;
    }

    .modal .tml_tutup_detail {
        position: absolute;
        right: 20px;
    }

    /* modal prev image */
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
        margin-top: 5px;
        display: block;
        margin-left: auto;
        margin-right: auto;

    }

    @media only screen and (max-width: 1200px) {
        .flip-card {
            width: 49%;
            height: 350px;
        }
    }

    @media only screen and (max-width: 680px) {
        .flip-card {
            width: 100%;
            height: 300px;
        }
    }

    @media only screen and (max-width: 440px) {
        .flip-card {
            height: 400px;
        }
    }
</style>

<div id="kartu_kontak">

    <?php
    @$nomor = 0;
    while ($data_user = mysqli_fetch_assoc(@$result)) {
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
        <div id="card<?= @$nomor; ?>" class="flip-card">
            <div class="flip-card-inner">
                <div class="flip-card-front" style='background: linear-gradient(to left, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.96)), url("../img/user/<?= @$data_user['foto'] ? @$data_user['foto'] : 'default.jpg'; ?>"); background-repeat: no-repeat; background-size: cover; background-position: center;'>
                    <h6 id="nomorurutkartu" class="badge badge-secondary elevation-2"><?= @$nomor; ?></h6>
                    <img class="elevation-2" src="../img/user/<?= @$data_user['foto'] ? @$data_user['foto'] : 'default.jpg'; ?>">
                    <h3 class="profile-username"><?= @$data_user['nama']; ?></h3>
                    <p class="text-muted"><?= @$data_user[@$datab_info]; ?></p>
                    <div>
                        <?php if (@$tentang_web) { ?>
                            <!-- wabsite -->
                            <span class="btn bg-dark bg-gradient-dark btn-sm elevation-2">
                                <i class="iconify" data-icon="ion:earth"></i>
                            </span>
                        <?php } ?>

                        <?php if (@$tentang_nomorhp) { ?>
                            <!-- telepon -->
                            <span class="btn bg-success bg-gradient-success btn-sm elevation-2">
                                <i class="iconify" data-icon="ion:call"></i>
                            </span>
                        <?php } ?>

                        <?php if (@$tentang_fb) { ?>
                            <!-- facebook -->
                            <span class="btn bg-primary bg-gradient-primary btn-sm elevation-2">
                                <i class="fab fa-facebook"></i>
                            </span>
                        <?php } ?>

                        <?php if (@$tentang_ig) { ?>
                            <!-- Instagram -->
                            <span class="btn bg-danger bg-gradient-danger btn-sm elevation-2">
                                <i class="fab fa-instagram"></i>
                            </span>
                        <?php } ?>

                        <?php if (@$tentang_twitter) { ?>
                            <!-- twitter -->
                            <span class="btn btn-info btn-sm elevation-2">
                                <i class="fab fa-twitter"></i>
                            </span>
                        <?php } ?>

                        <?php if (@$tentang_youtube) { ?>
                            <!-- youtube -->
                            <span class="btn btn-danger bg-gradient-danger btn-sm elevation-2">
                                <i class="fab fa-youtube"></i>
                            </span>
                        <?php } ?>

                        <!-- email -->
                        <span class="btn btn-warning bg-gradient-warning btn-sm elevation-2">
                            <i class="fas fa-envelope"></i>
                        </span>

                        <?php if (@$tentang_line) { ?>
                            <!-- line -->
                            <span id="sosmed_line" class="btn btn-sm elevation-2">
                                <i class="fab fa-line fa-border-none"></i>
                            </span>
                        <?php } ?>

                        <?php if (@$tentang_wa) { ?>
                            <!-- whatsapp -->
                            <span id="sosmed_WA" class="btn btn-sm elevation-2">
                                <i class="fab fa-whatsapp"></i>
                            </span>
                        <?php } ?>
                    </div>
                </div>
                <div class="flip-card-back" style='background: linear-gradient(to left, rgba(30, 67, 86, 0.6), rgba(30, 67, 86, 0.9)), url("../img/user/<?= @$data_user['foto'] ? @$data_user['foto'] : 'default.jpg'; ?>"); background-repeat: no-repeat; background-size: cover; background-position: center;'>
                    <div class="infomase">
                        <!-- infomase -->
                        <a class="btn btn-sm btn-light bg-gradient-light btn-sm elevation-2" data-bs-toggle="modal" data-bs-target="#modaldetailtentang<?= @$nomor; ?>">
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <br>
                        <br>
                        <?php if (@$_SESSION['level_login'] == 'admin' || @$_SESSION['level_login'] == 'superadmin') { ?>
                            <a id="tomboleditprofil" href="editprofil.php?datab=<?= @$datab; ?>&nick=<?= @$data_user['nick']; ?>&linka=cocard&tag=card<?= @$nomor; ?>" class="text-light">
                                <i class="fas fa-cog fa-spin"></i>
                            </a>
                        <?php } ?>
                    </div>
                    <div class="label_flip_back">
                        <!-- <label>
                            <i class="fas fa-user-circle text-dark"></i>
                            &nbsp;
                            <?= @$data_user['nama'] ? @$data_user['nama'] : (@$nama_login ? @$nama_login : 'default.jpg'); ?>
                        </label>
                        <label>
                            <i class="fas fa-info-circle text-info"></i>
                            &nbsp;
                            <?= @$data_user[@$datab_info]; ?>
                        </label> -->
                        <label>
                            <i class="fas fa-map-marker-alt text-danger"></i>
                            &nbsp;
                            <?= @$tentang_alamat ? @$tentang_alamat : '-'; ?>
                        </label>
                        <label>
                            <i class="fas fa-envelope text-warning"></i>
                            &nbsp;
                            <?= @$data_user['email'] ? @$data_user['email'] : (@$email_login ? @$email_login : 'default.jpg'); ?>
                        </label>

                        <?php if (@$tentang_nomorhp) { ?>
                            <label>
                                <i class="fas fa-phone text-primary"></i>
                                &nbsp;
                                <?= @$tentang_nomorhp ? ("0" . @$tentang_nomorhp) : '-'; ?>
                            </label>
                        <?php } ?>

                        <?php if (@$tentang_wa) { ?>
                            <label>
                                <i class="fab fa-whatsapp text-success"></i>
                                &nbsp;
                                <?= @$tentang_wa ? ("0" . @$tentang_wa) : '-'; ?>
                            </label>
                        <?php } ?>

                        <?php if (@$tentang_web) { ?>
                            <label>
                                <i class="fas fa-globe text-secondary"></i>
                                &nbsp;
                                <?= @$tentang_web ? 'www.' . @$tentang_web : '-'; ?>
                            </label>
                        <?php } ?>

                        <?php if (@$tentang_fb) { ?>
                            <label>
                                <i class="fab fa-facebook text-primary"></i>
                                &nbsp;
                                <?= @$tentang_fb ? 'www.facebook.com/' . @$tentang_fb : '-'; ?>
                            </label>
                        <?php } ?>

                        <?php if (@$tentang_ig) { ?>
                            <label>
                                <i class="fab fa-instagram text-danger"></i>
                                &nbsp;
                                <?= @$tentang_ig ? 'www.instagram.com/' . @$tentang_ig : '-'; ?>
                            </label>
                        <?php } ?>

                        <?php if (@$tentang_twitter) { ?>
                            <label>
                                <i class="fab fa-twitter text-info"></i>
                                &nbsp;
                                <?= @$tentang_twitter ? 'www.twitter.com/' . @$tentang_twitter : '-'; ?>
                            </label>
                        <?php } ?>

                        <?php if (@$tentang_line) { ?>
                            <label>
                                <i class="fab fa-line text-success"></i>
                                &nbsp;
                                <?= @$tentang_line ? 'line.me/ti/p/~' . @$tentang_line : '-'; ?>
                            </label>
                        <?php } ?>

                        <?php if (@$tentang_youtube) { ?>
                            <label>
                                <i class="fab fa-youtube text-danger"></i>
                                &nbsp;
                                <?= @$tentang_youtube ? @$tentang_youtube : '-'; ?>
                            </label>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Detail -->
        <div class="modal modaldetailtentang fade" id="modaldetailtentang<?= @$nomor; ?>" tabindex="-1" aria-labelledby="modaldetailtentang<?= @$nomor; ?>Label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content">
                    <div class="modal-body">
                        <button type=" button" class="btn-close tml_tutup_detail" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="info_profil">
                            <a data-bs-toggle="modal" data-bs-target="#tampilfotoprofil<?= @$nomor; ?>">
                                <img class="elevation-2" src="../img/user/<?= @$data_user['foto'] ? @$data_user['foto'] : 'default.jpg'; ?>">
                            </a>
                            <h3 class="profile-username"><?= @$data_user['nama']; ?></h3>
                            <p class="text-muted"><?= @$data_user[@$datab_info]; ?></p>
                        </div>
                        <div class="detail_profil">
                            <div class="kolom1">
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
                                    $tentang_hobi_ = explode(',', @$tentang_hobi);

                                    $jumlah_hobi = count(@$tentang_hobi_);
                                    for ($key = 1; $key <= $jumlah_hobi; $key++) {
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


                            <div class="kolom2">
                                <hr>
                                <strong><i class="fas fa-mobile mb-3"></i>&nbsp;&nbsp;Media Sisoal</strong>

                                <div class="text-muted">
                                    <i class="fas fa-envelope text-warning"></i>
                                    &nbsp;
                                    <a targer=_blank href="<?= @$data_user['email'] ? 'mailto:' . @$data_user['email'] : '#'; ?>">
                                        <?= @$data_user['email'] ? @$data_user['email'] : (@$email_login ? @$email_login : '-'); ?>
                                    </a>
                                </div>

                                <?php if (@$tentang_nomorhp) { ?>
                                    <div class="text-muted">
                                        <i class="fas fa-phone text-primary"></i>
                                        &nbsp;
                                        <a targer=_blank href="tel:62<?= @$tentang_nomorhp; ?>">
                                            <?= @$tentang_nomorhp ? ("0" . @$tentang_nomorhp) : '-'; ?>
                                        </a>
                                    </div>
                                <?php } ?>

                                <?php if (@$tentang_wa) { ?>
                                    <div class="text-muted">
                                        <i class="fab fa-whatsapp text-success"></i>
                                        &nbsp;
                                        <a targer=_blank href="https://wa.me/62<?= @$tentang_wa; ?>">
                                            <?= @$tentang_wa ? ("0" . @$tentang_wa) : '-'; ?>
                                        </a>
                                    </div>
                                <?php } ?>

                                <?php if (@$tentang_web) { ?>
                                    <div class="text-muted">
                                        <i class="fas fa-globe text-secondary"></i>
                                        &nbsp;
                                        <a targer=_blank href="<?= @$tentang_web ? 'https://www.' . @$tentang_web : '#'; ?>" target="_blank">
                                            <?= @$tentang_web ? 'www.' . @$tentang_web : '-'; ?>
                                        </a>
                                    </div>
                                <?php } ?>

                                <?php if (@$tentang_fb) { ?>
                                    <div class="text-muted">
                                        <i class="fab fa-facebook text-primary"></i>
                                        &nbsp;
                                        <a targer=_blank href="<?= @$tentang_fb ? 'https://www.facebook.com/' . @$tentang_fb : '#'; ?>">
                                            <?= @$tentang_fb ? 'www.facebook.com/' . @$tentang_fb : '-'; ?>
                                        </a>
                                    </div>
                                <?php } ?>

                                <?php if (@$tentang_ig) { ?>
                                    <div class="text-muted">
                                        <i class="fab fa-instagram text-danger"></i>
                                        &nbsp;
                                        <a targer=_blank href="<?= @$tentang_ig ? 'https://www.instagram.com/' . @$tentang_ig : '#'; ?>">
                                            <?= @$tentang_ig ? 'www.instagram.com/' . @$tentang_ig : '-'; ?>
                                        </a>
                                    </div>
                                <?php } ?>

                                <?php if (@$tentang_twitter) { ?>
                                    <div class="text-muted">
                                        <i class="fab fa-twitter text-info"></i>
                                        &nbsp;
                                        <a targer=_blank href="<?= @$tentang_twitter ? 'https://www.twitter.com/' . @$tentang_twitter : '#'; ?>">
                                            <?= @$tentang_twitter ? 'www.twitter.com/' . @$tentang_twitter : '-'; ?>
                                        </a>
                                    </div>
                                <?php } ?>

                                <?php if (@$tentang_line) { ?>
                                    <div class="text-muted">
                                        <i class="fab fa-line text-success"></i>
                                        &nbsp;
                                        <a targer=_blank href="<?= @$tentang_line ? 'https://line.me/ti/p/~' . @$tentang_line : '#'; ?>">
                                            <?= @$tentang_line ? 'line.me/ti/p/~' . @$tentang_line : '-'; ?>
                                        </a>
                                    </div>
                                <?php } ?>

                                <?php if (@$tentang_youtube) { ?>
                                    <div class="text-muted">
                                        <i class="fab fa-youtube text-danger"></i>
                                        &nbsp;
                                        <a targer=_blank href="<?= @$tentang_youtube ? 'https://www.youtube.com/' . @$tentang_youtube : '#'; ?>">
                                            <?= @$tentang_youtube ? @$tentang_youtube : '-'; ?>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div> -->
                </div>
            </div>
        </div>

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
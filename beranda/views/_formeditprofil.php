<style>
    #form_edit_profil_01 #form_01 .btn {
        height: 38px;
    }
</style>
<section class="content">

    <?php if (@$pesan_error) { ?>
        <div class="col-12">
            <div class="alert <?= $alert_bg; ?> alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?= $pesan_error; ?>
            </div>
        </div>
        <?php
        ?>
    <?php } ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4 connectedSortable">
                <div class="card elevation-5">
                    <div class="card-header bg-gradient-navy">
                        <h3 class="card-title">
                            <i class="fas fa-user-edit"></i>&nbsp;
                            Edit Profil
                            <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Mengubah email, nama, dan foto profil"></i>
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <!-- <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button> -->
                        </div>
                    </div>
                    <div id="form_edit_profil_01" class="card-body pt-0">
                        <label class="mt-0 mb-1" style="color: brown; font-size: 12px; font-weight: 400;">
                            <i class="fas fa-info-circle"></i>&nbsp;
                            Abaikan form ini jika tidak ingin mengganti Biodata
                        </label>
                        <form action="<?= $link_editprofil; ?>" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="fotolama" value="<?= $data['foto']; ?>">
                            <input type="hidden" name="emaillama" value="<?= $data['email']; ?>">

                            <?php if ($datab_login == 'dataguru') { ?>
                                <input type="hidden" name="niplama" value="<?= $data['nip']; ?>">
                            <?php } elseif ($datab_login == 'datasiswa') { ?>
                                <input type="hidden" name="nislama" value="<?= $data['nis']; ?>">
                            <?php } ?>

                            <div id="form_01" class="input-group form-group">
                                <label id="none03" class="btn btn-secondary" for="nama" data-toggle="tooltip" data-placement="top" title='Nama Tidak bisa diubah begitu saja, kecuali ada kesalahan eja yang tidak sesuai. Jika ada kesalahan, pilih menu "Minta Ubah Nama"'>
                                    <i class="fas fa-user"></i>&nbsp;
                                    <span id="none02">
                                        
                                    </span>
                                </label>
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="<?= $data['nama']; ?>" data-toggle="tooltip" data-placement="top" title='Nama Tidak bisa diubah begitu saja, kecuali ada kesalahan eja yang tidak sesuai. Jika ada kesalahan, pilih menu "Minta Ubah Nama"' disabled>
                                <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#reqeditnama">
                                    <i class="fas fa-edit"></i>&nbsp;
                                    <span id="none01">Minta Ubah
                                        <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Kirim Permintaan untuk mengubah nama, Jika disetujui nama akan berubah seperti yang dituliskan dalam form permintaan ubah nama."></i>
                                    </span>
                                </button>
                            </div>

                            <div class="input-group form-group mt-n3" data-toggle="tooltip" data-placement="top" title="Nomor Induk Tidak bisa diganti, jika ada kesalahan, silakan hubungi Admin/TIM IT">
                                <?php if ($datab_login == 'dataguru') { ?>
                                    <label class="btn btn-secondary">
                                        <i class="fas fa-id-card"></i>&nbsp;
                                        <span id="none02">
                                            NIP
                                        </span>
                                    </label>
                                    <input type="text" class="form-control" id="nip" name="nip" placeholder="<?= $data['nip']; ?>">

                                <?php } elseif ($datab_login == 'datasiswa') { ?>
                                    <label class="btn btn-secondary">
                                        <i class="fas fa-id-card"></i>&nbsp;
                                        <span id="none02">
                                            NIS
                                        </span>
                                    </label>
                                    <input type="text" class="form-control" id="nis" name="nis" placeholder="<?= $data['nis']; ?>">

                                <?php } ?>
                            </div>

                            <div class="input-group form-group" data-toggle="tooltip" data-placement="top" title="Ubah email baru untuk login ke akun ini">
                                <label class="btn btn-secondary" for="email">
                                    <i class="fas fa-envelope"></i>&nbsp;
                                    <span id="none02">
                                        
                                    </span>
                                </label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="<?= $data['email']; ?>">
                            </div>

                            <div class="input-group" data-toggle="tooltip" data-placement="top" title="Upload Foto baru">
                                <!-- preview image after upload -->
                                <div class="form-group mr-3 elevation-2">
                                    <a href="../img/user/<?= @$data['foto'] ? $data['foto'] : 'default.jpg'; ?>">
                                        <img id="photoprofil" src="../img/user/<?= @$data['foto'] ? $data['foto'] : 'default.jpg'; ?>" alt="foto" class="img-fluid img-thumbnail img-preview" style="max-width: 120px; height: 150px; object-fit: cover; object-position: top;">
                                    </a>
                                </div>
                                <div class="form-group">
                                    <label class="btn btn-secondary" for="foto">
                                        <i class="fas fa-image"></i>&nbsp;
                                        Foto Profil
                                    </label>
                                    <input type="file" class="form-control" id="foto" name="fotobaru">
                                </div>
                            </div>
                    </div>
                    <div class="card-footer mt-n3">
                        <a href="<?= $link_back; ?>" class="btn btn-sm ml-n2">
                            <i class="fas fa-arrow-left"></i>&nbsp;
                            <?= $ket_link; ?>
                        </a>
                        <div class="float-right">
                            <button onClick="window.location.reload();" href="#" type="button" class="btn btn-sm btn-outline-secondary mr-2" data-bs-dismiss="modal">
                                <i class="fa fa-times"></i>&nbsp;&nbsp;Batal
                            </button>
                            <button type="submit" name="ubahprofil" value="ubahprofil" class="btn btn-sm btn-success elevation-2">
                                <i class="fas fa-save"></i>&nbsp;
                                Simpan
                            </button>
                        </div>
                        </form>
                    </div>
                </div>

                <!-- ganti password -->
                <div class="card elevation-5">
                    <div class="card-header bg-gradient-navy">
                        <h3 class="card-title">
                            <i class="fas fa-key"></i>&nbsp;
                            Ganti Password
                            <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Mengubah Password Login"></i>
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <!-- <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button> -->
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <label class="mt-1 mb-1" for="submit" style="font-size: 12px; font-weight: 400; color: brown;">
                            <i class="fas fa-info-circle"></i>&nbsp;
                            Abaikan form ini jika tidak ingin mengganti password
                        </label>
                        <form action="<?= $link_editprofil; ?>" method="post">
                            <div class="input-group elevation-2 bg-light" style="padding: 10px; border-radius: 10px;">
                                <input type="password" class="form-control" id="password_lama" name="password_lama" placeholder="Password Lama">
                                <div class="input-group-append">

                                    <!-- kita pasang onclick untuk merubah icon buka/tutup mata setiap diklik  -->
                                    <span id="tombolmata1" onclick="change1()" class="input-group-text">

                                        <!-- icon mata bawaan bootstrap  -->
                                        <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z" />
                                            <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <div class="mt-3 mb-3"></div>
                            <div class="form-group elevation-2 bg-light" style="padding: 10px 10px 1px 10px; border-radius: 10px;">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password_baru" name="password_baru" placeholder="Password Baru">
                                        <div class="input-group-append">

                                            <!-- kita pasang onclick untuk merubah icon buka/tutup mata setiap diklik  -->
                                            <span id="tombolmata2" onclick="change2()" class="input-group-text">

                                                <!-- icon mata bawaan bootstrap  -->
                                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z" />
                                                    <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password_baru_ulang" name="password_baru_ulang" placeholder="Ulangi Password Baru">
                                        <div class="input-group-append">

                                            <!-- kita pasang onclick untuk merubah icon buka/tutup mata setiap diklik  -->
                                            <span id="tombolmata3" onclick="change3()" class="input-group-text">

                                                <!-- icon mata bawaan bootstrap  -->
                                                <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z" />
                                                    <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="card-footer mt-n3">
                        <a href="<?= $link_back; ?>" class="btn btn-sm ml-n2">
                            <i class="fas fa-arrow-left"></i>&nbsp;
                            <?= $ket_link; ?>
                        </a>

                        <div class="float-right">
                            <button onClick="window.location.reload();" href="#" type="button" class="btn btn-sm btn-outline-secondary mr-2" data-bs-dismiss="modal">
                                <i class="fa fa-times"></i>&nbsp;&nbsp;Batal
                            </button>
                            <button type="submit" name="gantipassword" value="gantipassword" class="btn btn-sm btn-success elevation-2">
                                <i class="fas fa-save"></i>&nbsp;
                                Simpan
                            </button>
                        </div>

                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 connectedSortable">
                <div class="card elevation-5">
                    <div class="card-header bg-gradient-navy">
                        <h3 class="card-title">
                            <i class="fas fa-sticky-note"></i>
                            &nbsp;Template Pesan Masuk &nbsp;
                            <a href="https://www.google.com/url?q=https://www.google.com/search?q=template+pesan+masuk&source=lnms&tbm=isch&sa=X&ved=0ahUKEwjNy7KHk5_TAhXCYFQKHWQgC_EQ_AUICigB&biw=1366&bih=662#imgrc=X-_7_0_X-_7_0:" target="_blank" class="badge badge-primary float-right" data-toggle="tooltip" data-placement="top" title="Bantuan">
                                <i class="fas fa-info-circle"></i>
                            </a>
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <!-- <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button> -->
                        </div>
                    </div>
                    <div class="card-body mb-n3 mt-n1">
                        <form action="<?= $link_editprofil; ?>" method="post" id="usrform_temp">
                            <div class="form-group">
                                <!-- form menggunakan textarea -->
                                <!-- set -->
                                <div class="d-flex" style="gap: 10px;">
                                    <button onClick="window.location.reload();" href="#" type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                                        <i class="fa fa-times"></i>Batal
                                    </button>
                                    <textarea name="temp_pesan" class="form-control" form="usrform_temp" id="temp_pesan" rows="2" placeholder="temp_pesan"><?= @$data['template_pesan']; ?></textarea>
                                    <button type="submit" name="templatepesan" value="templatepesan" class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Simpan Template Pesan Masuk">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </div>
                                <span class="float-right mb-0 mt-1" style="color: brown; font-size: 12px; font-weight: 400;">
                                    <i class="fas fa-info-circle"></i>&nbsp;
                                    Tarik ujung kanan bawah untuk memperpanjang area text
                                </span>
                            </div>
                        </form>
                    </div>
                </div>

                <!--  -->
                <div class="card elevation-5">
                    <div class="card-header bg-gradient-navy">
                        <h3 class="card-title">
                            <i class="fas fa-user-edit"></i>&nbsp;
                            Kontak&nbsp;&nbsp;
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <!-- <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button> -->
                        </div>
                    </div>

                    <div class="card-body pt-0">
                        <label class="mt-1 mb-0" style="color: brown; font-size: 12px; font-weight: 400;">
                            <i class="fas fa-info-circle"></i>&nbsp;
                            Abaikan form ini jika tidak ingin mengganti Kontak
                        </label>

                        <label class="mt-1 mb-2" style="color: black; font-size: 12px; font-weight: 400;">
                            <i class="fas fa-info-circle"></i>&nbsp;
                            Pastikan kontak dan ID akun sosmed yang dicantumkan masih <b class="badge badge-danger">aktif</b>.
                        </label>
                        <form action="<?= $link_editprofil; ?>" method="post" enctype="multipart/form-data" id="usrform_kontak">
                            <div class="form-group">
                                <!-- <label for="nomorhp">
                                    <span class="iconify" data-icon="ion:phone-portrait"></span>&nbsp;
                                    Nomor Telp / Hp
                                    <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Nomor Hp yang aktif"></i>
                                </label> -->
                                <div class="input-group">
                                    <button type="button" class="btn btn-secondary" value="" disabled>
                                        <i class="fas fa-phone"></i>
                                    </button>
                                    <input type="text" name="nomorhp" class="form-control" form="usrform_kontak" id="nomorhp" placeholder="Nomor Telp / HP (08xxxxxxxxxx)" value="<?= @$tentang_nomorhp ? ('0' . $tentang_nomorhp) : ''; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <!-- <label for="website">
                                    <span class="iconify" data-icon="ion:earth"></span>&nbsp;
                                    Website / Blog
                                    <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Website/Blog yang aktif"></i>
                                </label> -->
                                <div class="input-group">
                                    <button class="btn btn-secondary" disabled>
                                        <span class="iconify" data-icon="ion:earth"></span>&nbsp;
                                        <span>www.</span>
                                    </button>
                                    <input type="text" name="website" class="form-control" form="usrform_kontak" id="website" placeholder="website.com" value="<?= $tentang_web; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <!-- <label for="instagram">
                                    <span class="iconify" data-icon="ion:social-instagram-outline"></span>&nbsp;
                                    Instagram
                                    <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Instagram yang aktif"></i>
                                </label> -->
                                <div class="input-group">
                                    <button type="button" class="btn btn-danger bg-gradient-danger" disabled>
                                        <i class="fab fa-instagram"></i>
                                    </button>
                                    <input type="text" name="instagram" class="form-control" form="usrform_kontak" id="instagram" placeholder="Instagram" value="<?= $tentang_ig; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <!-- <label for="facebook">
                                    <span class="iconify" data-icon="ion:social-facebook-outline"></span>&nbsp;
                                    Facebook
                                    <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Facebook yang aktif"></i>
                                </label> -->
                                <div class="input-group">
                                    <button type="button" class="btn btn-primary" disabled>
                                        <i class="fab fa-facebook-f"></i>
                                    </button>
                                    <input type="text" name="facebook" class="form-control" form="usrform_kontak" id="facebook" placeholder="Facebook" value="<?= $tentang_fb; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <!-- <label for="twitter">
                                    <span class="iconify" data-icon="ion:social-twitter-outline"></span>&nbsp;
                                    Twitter
                                    <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Twitter yang aktif"></i>
                                </label> -->
                                <div class="input-group">
                                    <button type="button" class="btn btn-info bg-gradient-info" disabled>
                                        <i class="fab fa-twitter"></i>
                                    </button>
                                    <input type="text" name="twitter" class="form-control" form="usrform_kontak" id="twitter" placeholder="Twitter" value="<?= $tentang_twitter; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <!-- <label for="line">
                                    <i class="fab fa-line"></i>&nbsp;
                                    Line
                                    <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Line yang aktif"></i>
                                </label> -->
                                <div class="input-group">
                                    <button class="btn btn-success" disabled>
                                        <i class="fab fa-line"></i>&nbsp;
                                        LINE</button>
                                    <input type="text" name="line" class="form-control" form="usrform_kontak" id="line" placeholder="Line" value="<?= $tentang_line; ?>">
                                </div>
                            </div>
                            <!-- youtube -->
                            <div class="form-group">
                                <!-- <label for="youtube">
                                    <span class="iconify" data-icon="ion:social-youtube-outline"></span>&nbsp;
                                    Youtube
                                    <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Youtube Channel yang aktif"></i>
                                </label> -->
                                <div class="input-group">
                                    <button class="btn btn-danger" disabled>
                                        <i class="fab fa-youtube"></i>
                                    </button>
                                    <input type="text" name="youtube" class="form-control" form="usrform_kontak" id="youtube" placeholder="Youtube" value="<?= $tentang_youtube; ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <!-- <label for="whatsapp">
                                    <span class="iconify" data-icon="ion:social-whatsapp-outline"></span>&nbsp;
                                    Whatsapp
                                    <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Whatsapp yang aktif"></i>
                                </label> -->
                                <div class="input-group">
                                    <button type="button" class="btn btn-outline-success bg-gradient-green" disabled>
                                        <i class="fab fa-whatsapp"></i>&nbsp;
                                    </button>
                                    <input type="text" name="whatsapp" class="form-control" form="usrform_kontak" id="whatsapp" placeholder="Whatsapp (08xxxxxxxxxx)" value="<?= @$tentang_wa ? ('0' . $tentang_wa) : ''; ?>">
                                </div>
                            </div>
                    </div>
                    <div class="card-footer mt-n3">
                        <div class="text-muted d-flex justify-content-between">
                            <div>
                                <a href="<?= $link_back; ?>" class="btn btn-sm ml-n2">
                                    <i class="fas fa-arrow-left"></i>&nbsp;
                                    <?= $ket_link; ?>
                                </a>
                            </div>
                            <div>
                                <button class="mr-2 btn btn-sm btn-outline-secondary">
                                    <i class="fa fa-times"></i>&nbsp;&nbsp;Batal
                                </button>
                                <button type="submit" name="kontak" value="kontak" class="btn btn-sm btn-success elevation-2 rounded" style="float: right;">
                                    <i class="fas fa-save"></i>&nbsp;&nbsp;Simpan
                                </button>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
                <!--  -->
            </div>

            <div class="col-lg-4 connectedSortable">
                <div class="card elevation-5">
                    <div class="card-header bg-gradient-navy">
                        <h3 class="card-title">
                            <i class="fas fa-user-edit"></i>&nbsp;
                            Ubah Biodata
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <!-- <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button> -->
                        </div>
                    </div>

                    <div class="card-body pt-0 mb-n3">
                        <label class="mt-1 mb-0" style="color: brown; font-size: 12px; font-weight: 400;">
                            <i class="fas fa-info-circle"></i>&nbsp;
                            Abaikan form ini jika tidak ingin mengganti Biodata
                        </label>
                        <form action="<?= $link_editprofil; ?>" method="post" id="usrform_bio">
                            <div class="form-group">
                                <!-- form menggunakan textarea -->

                                <label for="pendidikan">Pendidikan</label>
                                <textarea name="pendidikan" class="form-control" form="usrform_bio" id="pendidikan" rows="2" placeholder="Pendidikan"><?= $tentang_pendidikan; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="alamat">Alamat</label>
                                <textarea name="alamat" class="form-control" form="usrform_bio" id="alamat" rows="2" placeholder="Alamat"><?= $tentang_alamat; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="hobi">Hobi
                                    <span class="text-xs font-italic">(Pisahkan dengan koma ",")</span>
                                </label>
                                <textarea name="hobi" class="form-control" form="usrform_bio" id="hobi" rows="2" placeholder="Hobi"><?= $tentang_hobi; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="notes">Notes Bio. </label>
                                <textarea name="notes" class="form-control" form="usrform_bio" id="notes" rows="3" placeholder="Notes / Bio / Quotes"><?= $tentang_notes; ?></textarea>
                            </div>

                            <span class="float-right mb-1 mt-n2" style="color: brown; font-size: 12px; font-weight: 400;">
                                <i class="fas fa-info-circle"></i>&nbsp;
                                Tarik ujung kanan bawah untuk memperpanjang area text
                            </span>
                    </div>
                    <div class="card-footer">
                        <div class="btn-btn-group">
                            <div class="text-muted d-flex justify-content-between">
                                <div>
                                    <a href="<?= $link_back; ?>" class="btn btn-sm mt-1 ml-n2">
                                        <i class="fas fa-arrow-left"></i>&nbsp;
                                        <?= $ket_link; ?>
                                    </a>
                                </div>
                                <div>
                                    <button onClick="window.location.reload();" href="#" type="button" class="btn btn-sm btn-outline-secondary mt-2 mr-2" data-bs-dismiss="modal">
                                        <i class="fa fa-times"></i>&nbsp;&nbsp;Batal
                                    </button>
                                    <button type="submit" name="simpanbio" value="simpanbio" class="btn btn-sm btn-success mt-2 elevation-2" style="float: right;"><i class="fas fa-save"></i>&nbsp;&nbsp;Simpan
                                    </button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!--  -->
            </div>
            <!--  -->
        </div>
        <!--  -->
        <script>
            // membuat fungsi change1
            function change1() {

                // membuat variabel berisi tipe input dari id='pass', id='pass' adalah form input password 
                var x = document.getElementById('password_lama').type;
                // var y = document.getElementById('password_baru').type;

                //membuat if kondisi, jika tipe x adalah password maka jalankan perintah di bawahnya
                if (x == 'password') {

                    //ubah form input password menjadi text
                    document.getElementById('password_lama').type = 'text';

                    //ubah icon mata terbuka menjadi tertutup
                    document.getElementById('tombolmata1').innerHTML = `<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-slash-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10.79 12.912l-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
                                                                    <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708l-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829z"/>
                                                                    <path fill-rule="evenodd" d="M13.646 14.354l-12-12 .708-.708 12 12-.708.708z"/>
                                                                    </svg>`;
                } else {

                    //ubah form input password menjadi text
                    document.getElementById('password_lama').type = 'password';

                    //ubah icon mata terbuka menjadi tertutup
                    document.getElementById('tombolmata1').innerHTML = `<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                                    <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                                    </svg>`;
                }
            }

            // membuat fungsi change1
            function change2() {

                // membuat variabel berisi tipe input dari id='pass', id='pass' adalah form input password 
                // var x = document.getElementById('password_lama').type;
                var y = document.getElementById('password_baru').type;

                //membuat if kondisi, jika tipe x adalah password maka jalankan perintah di bawahnya
                if (y == 'password') {

                    //ubah form input password menjadi text
                    document.getElementById('password_baru').type = 'text';

                    //ubah icon mata terbuka menjadi tertutup
                    document.getElementById('tombolmata2').innerHTML = `<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-slash-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10.79 12.912l-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
                                                                    <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708l-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829z"/>
                                                                    <path fill-rule="evenodd" d="M13.646 14.354l-12-12 .708-.708 12 12-.708.708z"/>
                                                                    </svg>`;
                } else {

                    //ubah form input password menjadi text
                    document.getElementById('password_baru').type = 'password';

                    //ubah icon mata terbuka menjadi tertutup
                    document.getElementById('tombolmata2').innerHTML = `<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                                    <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                                    </svg>`;
                }
            }

            // membuat fungsi change1
            function change3() {

                // membuat variabel berisi tipe input dari id='pass', id='pass' adalah form input password 
                // var x = document.getElementById('password_lama').type;
                var z = document.getElementById('password_baru_ulang').type;

                //membuat if kondisi, jika tipe x adalah password maka jalankan perintah di bawahnya
                if (z == 'password') {

                    //ubah form input password menjadi text
                    document.getElementById('password_baru_ulang').type = 'text';

                    //ubah icon mata terbuka menjadi tertutup
                    document.getElementById('tombolmata3').innerHTML = `<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-slash-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10.79 12.912l-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
                                                                    <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708l-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829z"/>
                                                                    <path fill-rule="evenodd" d="M13.646 14.354l-12-12 .708-.708 12 12-.708.708z"/>
                                                                    </svg>`;
                } else {

                    //ubah form input password menjadi text
                    document.getElementById('password_baru_ulang').type = 'password';

                    //ubah icon mata terbuka menjadi tertutup
                    document.getElementById('tombolmata3').innerHTML = `<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-eye-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                                                    <path fill-rule="evenodd" d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/>
                                                                    </svg>`;
                }
            }
        </script>
        <script type="text/javascript">
            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $('#photoprofil').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(input.files[0]); // convert to base64 string
                }
            }

            $("#foto").change(function() {
                $('#photoprofil').show();
                readURL(this);
            });
        </script>
    </div>
</section>

<!-- modal req edit nama -->
<!-- Modal -->
<div class="modal fade" id="reqeditnama" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="reqeditnamaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reqeditnamaLabel">Minta Ubah Nama</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <div class="input-group">
                        <label class="btn bg-secondary" for="reqnamatext">Nama Baru</label>
                        <input id="reqnamatext" class="form-control" type="text" autofocus value="<?= $data['nama']; ?>">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary bg-gradient-secondary border-0 elevation-2" data-bs-dismiss="modal">
                    <i class="fa fa-times"></i>&nbsp;
                    Batal</button>
                <button type="button" class="btn btn-success bg-gradient-success border-0 elevation-2">
                    <i class="fas fa-paper-plane"></i>&nbsp;
                    Kirim Permintaan</button>
            </div>
        </div>
    </div>
</div>
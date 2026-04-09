<?php include('views/header.php'); ?>

<section class="content">
    <div class="container-fluid">
        <!-- /.content -->
        <div class="row">
            <div class="col-md-6 connectedSortable">
                <!-- Profile Image -->
                <div class="card">
                    <div class="card-header bg-gradient-navy">
                        <h3 class="card-title">
                            <i class="fas fa-user-edit"></i>&nbsp;
                            Form Ijin
                            <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Membuat Ijin"></i>
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
                    <div id="formijin_001" class="card-body box-profile">
                        <div class="card card-primary card-outline">
                            <div class="container-fluid">
                                <form action="" method="post">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <button for="keterangan" class="btn btn-secondary" disabled>
                                                    <i class="fas fa-edit"></i>
                                                    <b>Keterangan</b>&nbsp;&ast;
                                                    <!-- <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Wajib diisi"></i> -->
                                                </button>
                                                <input type="email" class="form-control" id="keterangan" placeholder="Keterangan">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <button class="btn btn-secondary" for="tanggalpengajuan" disabled>
                                                    <i class="fas fa-calendar-alt"></i>
                                                    <b>Tanggal</b>&nbsp;&ast;
                                                    <!-- <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Wajib diisi"></i> -->
                                                </button>
                                                <input type="date" class="form-control" id="tanggalpengajuan" value="<?= date('Y-m-d'); ?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <button class="btn btn-secondary" for="fotodoc" disabled>
                                                    <i class="fas fa-camera"></i>
                                                    <b>Doc</b>&nbsp;&ast;
                                                    <!-- <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Wajib diisi"></i> -->
                                                </button>
                                                <div class="custom-file mr-2">
                                                    <input type="file" class="custom-file-input" id="fotodoc" capture='default'>
                                                    <label class="custom-file-label" for="fotodoc">Pilih file</label>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <img id="fotodocprev" class="img-fluid img-thumbnail img-preview" src="../img/user/docs.png" style="width: 300px; max-height: 300px; object-fit: cover; object-position: top;">
                                                <!-- label untuk gambar -->
                                                <!-- <label for="fotodocprev" class="img-label ml-3">
                                                    <span class="btn btn-secondary btn-sm btn-block">
                                                        <i class="fas fa-upload mr-2"></i>
                                                        Upload
                                                    </span> 
                                                    <span class="file-custom"></span> -->
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- warning -->
                                    <span class="text-danger">
                                        &ast;&nbsp;
                                        Wajib diisi!
                                    </span>
                                    <br>
                                    <i class="fas fa-question-circle text-info"></i>
                                    <span class="text-info">
                                        Pastikan data yang anda masukkan sudah benar!
                                    </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class=" form-button d-flex justify-content-between">
                            <button class="btn btn-secondary bg-gradient-secondary elevation-2">
                                <!-- <i class="fas fa-circle-notch fa-spin"></i>&nbsp; -->
                                <!-- cancel icon -->
                                <i class="fas fa-times"></i>&nbsp;
                                Batal
                            </button>
                            <button type="submit" class="btn btn-primary bg-gradient-primary elevation-2" id="exampleCheck1" name="submit" value="Buat Ijin">
                                <i class="fas fa-edit"></i>&nbsp;
                                Buat Ijin</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6 connectedSortable">
                <div class="card">
                    <div class="card-header bg-gradient-navy">
                        <h3 class="card-title">
                            <i class="fas fa-user-edit"></i>&nbsp;
                            Kalender
                            <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Kalender"></i>
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
                    <?php include '_kalender.php'; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#fotodocprev').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]); // convert to base64 string
        }
    }

    $("#fotodoc").change(function() {
        $('#fotodocprev').show();
        readURL(this);
    });
</script>
<?php include('views/footer.php'); ?>
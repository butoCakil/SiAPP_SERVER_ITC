<section class="content">
    <div class="container-fluid">
        <div class="card bg-dark bg-gradient-dark elevation-3" style="border: none; z-index: 1;">
            <div id="header_rekap" class="card-body">
                <div style="display: flex; justify-content: baseline; justify-content: space-between;">
                    <style>
                        .tombolsettingkbm {
                            display: flex;
                            gap: 10px;
                        }
                    </style>
                    <div class="tombolsettingkbm">
                        <div>
                            <a href="?jadwal_semster=true&jur=AT" class="btn btn-success bg-gradient-success elevation-3 border-0<?= (@$_GET['jur'] == 'AT') ? ' disabled' : ''; ?>">
                                <?= @$_GET['jur'] == 'AT' ? '<i class="fas fa-cog fa-spin"></i>' : ''; ?>
                                &nbsp;AT
                            </a>
                        </div>
                        <div>
                            <a href="?jadwal_semster=true&jur=DKV" class="btn btn-primary bg-gradient-primary elevation-3 border-0<?= (@$_GET['jur'] == 'DKV') ? ' disabled' : ''; ?>">
                                <?= @$_GET['jur'] == 'DKV' ? '<i class="fas fa-cog fa-spin"></i>' : ''; ?>
                                &nbsp;DKV
                            </a>
                        </div>
                        <div>
                            <a href="?jadwal_semster=gasal&jur=TE" class="btn btn-warning bg-gradient-warning elevation-3 border-0<?= (@$_GET['jur'] == 'TE') ? ' disabled' : ''; ?>">
                                <?= @$_GET['jur'] == 'TE' ? '<i class="fas fa-cog fa-spin"></i>' : ''; ?>
                                &nbsp;TE
                            </a>
                        </div>
                        <div>
                            <a href="?jadwal_semster=true" class="btn btn-dark bg-gradient-dark elevation-3 border-0<?= (@$_GET['jur'] == '') ? ' disabled' : ''; ?>">
                                <?= @$_GET['jur'] == '' ? '<i class="fas fa-cog fa-spin"></i>' : ''; ?>
                                &nbsp;SEMUA
                            </a>
                        </div>
                        <div class="float-end">
                            <a href="?" class="btn btn-dark text-light bg-gradient-secondary elevation-3 border-0<?= (@$_GET['set'] == 'jam') ? ' disabled' : ''; ?>">
                                <i class="far fa-calendar"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 connectedSortable">
                <div class="card elevation-5">
                    <div class="card-header bg-gradient-navy">
                        <h3 class="card-title">
                            <i class="fas fa-cog fa-spin"></i>&nbsp;
                            Jadwal&nbsp;<?= mysqli_real_escape_string($konek, $_GET['jur']); ?>&nbsp;<span class="badge bg-light"><?= $kelompokkelas; ?></span>&nbsp;<span class="badge bg-light"><?= $set_bulan; ?> / <?= date('Y'); ?></span>
                        </h3>
                        <div class="card-tools">
                            <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="top" title="Mengubah Setting Aplikasi, dan mengatur Admin"></i>
                            <button type="button" class="btn btn-tool text-light" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <style>
                        .nowrap {
                            white-space: nowrap;
                        }

                        #table {
                            font-size: x-small;
                        }

                        .clickable-cell {
                            cursor: pointer;
                        }
                    </style>

                    <div id="table" class="card-body table-responsive">
                        <div class="col-12">
                            <div class="alert alert-info alert-dismissible" role="alert">
                                <i class="fas fa-info-circle"></i>
                                <b>Setting Jadwal</b>
                                <ul>
                                    <li>Klik Sel yang hendak dipilih, sesuai baris Kelas dan Kolom Hari serta Jam ke berapa.</li>
                                    <li>Klik Tombol "SET", akan muncul Pop-up Window</li>
                                    <li>Pilih Guru dan Ruang sesuai dengan Jadwal yang telah ditentukan</li>
                                    <li>Klik Tombol "Reset" untuk membersihkan pilihan.</li>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                        <div class="m-1 d-flex">
                            <!-- Tambahkan tombol "Setting" -->
                            <button id="setting-button" class="m-1 btn btn-sm border-0 btn-primary" style="display: none;"><i class="fas fa-check"></i>&nbsp;Set</button>
                            <button id="clear-button" class="m-1 btn btn-sm border-0 btn-danger" style="display: none;"><i class="fas fa-times text-light"></i>&nbsp;Reset</button>
                            <button id="clear-button" class="m-1 btn btn-sm shadow btn-light" onclick="location.reload();">⟳&nbsp;Refresh</button>
                        </div>

                        <table class="table table-striped-columns table-bordered">
                            <thead>
                                <tr class="table-dark">
                                    <th rowspan="2" class="text-center align-middle">KELAS</th>
                                    <th colspan="11" class="text-center">Senin</th>
                                    <th colspan="11" class="text-center">Selasa</th>
                                    <th colspan="11" class="text-center">Rabu</th>
                                    <th colspan="11" class="text-center">Kamis</th>
                                    <th colspan="11" class="text-center">Jum'at</th>
                                </tr>
                                <tr class="table-bordered">
                                    <?php
                                    for ($aa = 0; $aa < 5; $aa++) {
                                        for ($bb = 1; $bb <= 11; $bb++) {
                                            echo "<td>$bb</td>";
                                        }
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $baris = 1;
                                foreach ($q_kode as $data_kelas) {
                                    $kelas = $data_kelas['info'];
                                ?>
                                    <tr class="nowrap">
                                        <td><?= $kelas; ?></td>
                                        <?php
                                        for ($i = 0; $i < 55; $i++) {
                                            $jamke = ($i % 11) + 1;
                                            $harike = floor($i / 11) + 1;
                                            echo "<td class='clickable-cell' data-id=\"$kelas-$harike-$jamke\"></td>";
                                        }
                                        ?>
                                    </tr>
                                <?php $baris++;
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Setting -->
<div class="modal fade" id="settingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="settingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="settingModalLabel">Modal title</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div>
                    <label for="">Data yang akan diubah:</label>
                    <div id="selectedDataContainer"></div>
                </div>
                <div class="form-group">
                    <label for="selectGuru">Pilih Guru:</label>
                    <select class="form-control" id="selectGuru">
                        <option value="">-- PILIH GURU --</option>
                        <!-- Isi dengan pilihan guru -->
                        <?php
                        $daftarguru = mysqli_query($konek, "SELECT * FROM dataguru ORDER BY info ASC, nama ASC");

                        foreach ($daftarguru as $dtdfguru) {
                        ?>
                            <option value="<?= $dtdfguru['nick']; ?>">[<?= $dtdfguru['info']; ?>]&nbsp;<?= $dtdfguru['nama']; ?></option>
                            <!-- Tambahkan pilihan guru sesuai kebutuhan -->

                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="selectRuang">Pilih Ruang:</label>
                    <select class="form-control" id="selectRuang">
                        <option value="">-- PILIH RUANG --</option>
                        <!-- Isi dengan pilihan ruang -->
                        <?php
                        $daftarruang = mysqli_query($konek, "SELECT * FROM daftarruang ORDER BY keterangan ASC, kode ASC");
                        foreach ($daftarruang as $dtdfruang) {
                        ?>
                            <option value="<?= $dtdfruang['kode']; ?>"><?= $dtdfruang['inforuang']; ?></option>
                            <!-- Tambahkan pilihan ruang sesuai kebutuhan -->
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button id="saveSettings" type="button" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>


<script>
    // Inisialisasi pesan alert
    let alertMessage = "";

    // Inisialisasi selectedData sebagai array kosong
    let selectedData = [];

    // Inisialisasi flag untuk menandai apakah data telah disimpan
    let dataSaved = false;

    // Fungsi untuk menampilkan dialog pengaturan
    function showSettingDialog(selectedId) {
        if (dataSaved) {
            alertMessage = "";
            selectedData = [];
            dataSaved = false;
        }

        // Di sini, Anda dapat menampilkan dialog pengaturan
        // Menggunakan SweetAlert atau modal Bootstrap
        // alert("Anda memilih sel dengan ID: " + selectedId);

        // Mendapatkan referensi elemen modal
        const settingModal = document.getElementById("settingModal");

        // Mendapatkan elemen header modal
        const modalHeader = settingModal.querySelector(".modal-title");

        // Mendapatkan elemen body modal
        const modalBody = settingModal.querySelector(".modal-body");

        // Mendapatkan elemen select guru
        const guruSelect = settingModal.querySelector("#guruSelect");

        // Mendapatkan elemen select ruang
        const ruangSelect = settingModal.querySelector("#ruangSelect");

        // Mendapatkan seluruh sel yang dipilih
        const clickedCells = document.querySelectorAll(".clicked");

        // Mengumpulkan selectedId dari sel-sel yang dipilih ke dalam array
        const selectedIds = [];
        clickedCells.forEach(cell => {
            const id = cell.getAttribute("data-id");
            selectedIds.push(id);
        });

        // Tambahkan informasi dari selectedId ke header modal
        modalHeader.textContent = "Pengaturan untuk ID yang Dipilih";

        // Mengumpulkan data terpilih dari selectedIds
        selectedData = selectedIds.map(id => {
            // Memecah id menjadi komponen yang sesuai
            const components = id.split('-'); // Misalnya: ["kelas", "1", "2"]

            // Mendapatkan komponen yang Anda butuhkan
            const kelas = components[0];
            const hariIndex = parseInt(components[1]) - 1; // Mengubah angka hari menjadi indeks array
            const jamIndex = parseInt(components[2]) - 1; // Mengubah angka jam ke sekian menjadi indeks array

            // Daftar hari dalam format yang sesuai
            const days = ["Senin", "Selasa", "Rabu", "Kamis", "Jumat"];

            // Daftar jam ke sekian dalam format yang sesuai
            const hours = [
                "07.00 - 07.45",
                "07.45 - 08.30",
                "08.30 - 09.15",
                "09.30 - 10.15",
                "10.15 - 11.00",
                "11.00 - 11.45",
                "12.30 - 13.15",
                "13.15 - 14.00",
                "14.00 - 14.45",
                "14.45 - 15.30",
                "15.30 - 16.15"
            ];

            // Di sini, Anda dapat melakukan apa pun dengan komponen-komponen ini
            // Contoh: return objek dengan komponen-komponen ini
            return {
                kelas,
                hari: days[hariIndex],
                jam: hours[jamIndex],
                ke: jamIndex,
                harke: hariIndex
            };
        });

        // Memasukkan informasi dari selectedData ke dalam elemen body modal
        // atau ke elemen yang sesuai
        // Di sini, saya menampilkan data terpilih dalam format daftar HTML
        let selectedDataHTML = "<ul>";
        selectedData.forEach(data => {
            selectedDataHTML += "<li>";
            selectedDataHTML += "Kelas: " + data.kelas + ", ";
            selectedDataHTML += "Hari: " + data.hari + ", ";
            selectedDataHTML += "Jam ke-" + data.ke + ": (" + data.jam + ")";
            selectedDataHTML += "</li>";
        });
        selectedDataHTML += "</ul>";
        modalBody.querySelector("#selectedDataContainer").innerHTML = selectedDataHTML;

        // Menampilkan modal
        $(settingModal).modal("show");

        // Menambahkan event listener untuk tombol "Simpan"
        const saveSettingsButton = document.getElementById("saveSettings");

        saveSettingsButton.addEventListener("click", function() {
            // Mendapatkan nilai yang dipilih dari dropdown guru dan ruang
            const selectedGuru = document.getElementById("selectGuru").value;
            const selectedRuang = document.getElementById("selectRuang").value;

            // Membuat objek dataToSend
            // Membuat objek FormData
            const formData = new FormData();

            // Menambahkan data ke dalam FormData
            formData.append("selectedGuru", selectedGuru);
            formData.append("selectedRuang", selectedRuang);

            // Loop melalui selectedData untuk mendapatkan informasi yang tepat
            selectedData.forEach(data => {
                formData.append("kelas[]", data.kelas);
                formData.append("harke[]", data.harke + 1);
                formData.append("ke[]", data.ke + 1);
            });

            // Menggunakan metode AJAX tanpa JSON
            $.ajax({
                type: "POST",
                url: "set_jadwal.php", // Ganti dengan URL yang sesuai dengan endpoint server Anda
                data: formData, // Mengirim data dalam bentuk FormData
                processData: false, // Memastikan FormData tidak diubah
                contentType: false, // Menggunakan tipe konten yang sesuai dengan FormData
                success: function(data) {
                    // Tangani respons dari server jika sukses
                    // console.log("Sukses:", response);
                    alert(data);
                    // Tambahkan kode lain yang diperlukan setelah sukses
                },
                error: function(err) {
                    // Tangani kesalahan jika terjadi
                    console.error("Kesalahan:", err);
                    // Tambahkan kode lain yang diperlukan untuk menangani kesalahan
                }
            });

            // Menutup modal
            $(settingModal).modal("hide");

            // Menghapus kelas "clicked" dari sel yang dipilih (jika masih ada)
            const clickedCell = document.querySelector(".clicked");
            if (clickedCell) {
                clickedCell.classList.remove("clicked");
                clickedCell.style.backgroundColor = "";
            }

            // Menyembunyikan tombol "Clear Selection"
            // clearButton.style.display = "none";

            // Mengosongkan selectedData
            selectedData = [];

            // Menandai bahwa data telah disimpan
            dataSaved = true;
        });
    }

    // Menambahkan event listener pada sel yang dapat diklik
    const clickableCells = document.querySelectorAll(".clickable-cell");

    clickableCells.forEach(cell => {
        cell.addEventListener("click", function() {
            // Menyimpan ID sel yang dipilih dalam atribut data
            const selectedId = this.getAttribute("data-id");

            // Toggle kelas "clicked" saat sel diklik
            this.classList.toggle("clicked");

            // Ubah warna latar belakang saat sel terpilih
            if (this.classList.contains("clicked")) {
                this.style.backgroundColor = "lightblue"; // Atur warna sesuai keinginan Anda
            } else {
                this.style.backgroundColor = ""; // Kembalikan ke warna latar belakang aslinya
            }

            // Periksa apakah ada sel yang memiliki kelas "clicked"
            const anyCellClicked = document.querySelector(".clicked");

            // Tampilkan tombol "Clear Selection" jika ada sel yang terpilih, atau sembunyikan jika tidak
            clearButton.style.display = anyCellClicked ? "block" : "none";

            // Menampilkan tombol "Setting" setelah sel dipilih
            const settingButton = document.getElementById("setting-button");
            settingButton.style.display = "block";
        });
    });

    // Menambahkan event listener untuk tombol "Clear Selection"
    const clearButton = document.getElementById("clear-button");

    clearButton.addEventListener("click", function() {
        // Menghapus kelas "clicked" dari semua sel yang memiliki kelas "clicked"
        const clickedCells = document.querySelectorAll(".clicked");

        clickedCells.forEach(cell => {
            cell.classList.remove("clicked");
            cell.style.backgroundColor = ""; // Kembalikan ke warna latar belakang aslinya
        });

        // Menyembunyikan tombol "Clear Selection" setelah menghapus pilihan
        this.style.display = "none";

        // Menyembunyikan tombol "Setting" setelah menghapus pilihan
        const settingButton = document.getElementById("setting-button");
        settingButton.style.display = "none";
    });

    // Menambahkan event listener untuk tombol "Setting"
    const settingButton = document.getElementById("setting-button");

    settingButton.addEventListener("click", function() {
        // Cek apakah ada sel yang memiliki kelas "clicked" saat tombol "Setting" ditekan
        const selectedCell = document.querySelector(".clicked");

        if (selectedCell) {
            // Jika ada sel yang terpilih, tampilkan alert dengan ID sel yang terpilih
            const selectedId = selectedCell.getAttribute("data-id");
            showSettingDialog(selectedId);
        }
    });
</script>


<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> -->
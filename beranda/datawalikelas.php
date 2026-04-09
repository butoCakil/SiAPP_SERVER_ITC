<?php
include '../config/konesi.php';
include('app/get_user.php');

$title = 'Data Walikelas';
$navlink = 'Data Guru';
$navlink_sub = 'datawalikelas';
include('views/header.php');

/* =========================
   AMBIL KELAS + WALI KELAS
   ========================= */
$sql_kelas = "
SELECT 
    k.kelas,
    g.id AS guru_id,
    g.nama AS nama_guru,
    g.tentang
FROM 
    (SELECT DISTINCT kelas FROM datasiswa WHERE kelas<>'' ) k
LEFT JOIN dataguru g 
    ON g.ket_akses = k.kelas 
    AND g.akses = 'Wali Kelas'
ORDER BY k.kelas
";
$result_kelas = mysqli_query($konek, $sql_kelas);

/* =========================
   GURU YANG BELUM JADI WALI
   ========================= */
$sql_guru = "
SELECT id, nama 
FROM dataguru 
WHERE id NOT IN (
    SELECT id FROM dataguru WHERE akses='Wali Kelas'
)
ORDER BY nama
";
$result_guru = mysqli_query($konek, $sql_guru);

$guru_available = [];
while ($g = mysqli_fetch_assoc($result_guru)) {
    $guru_available[] = $g;
}
?>

<section class="content">
    <div class="container-fluid">

        <div class="card elevation-3 bg-primary bg-gradient-primary border-0">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-tie"></i> Data Wali Kelas
                </h3>
            </div>
        </div>

        <div class="card elevation-3 mt-3">
            <div class="card-body">

                <table id="datagururekap" class="table table-bordered table-striped">
                    <thead>
                        <tr style="text-align:center">
                            <th>No</th>
                            <th>Kelas</th>
                            <th>Wali Kelas</th>
                            <th>Nomor WA</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result_kelas)) {

                            // ambil WA dari kolom tentang
                            $wa = '';
                            if (!empty($row['tentang'])) {
                                preg_match('/######(\d+)##/', $row['tentang'], $m);
                                $wa = $m[1] ?? '';
                            }

                            echo "<tr>";
                            echo "<td align='center'>{$no}</td>";
                            echo "<td>{$row['kelas']}</td>";

                            // SELECT WALI
                            echo "<td>";
                            echo "<select class='form-control wali-select' data-kelas='{$row['kelas']}'>";
                            echo "<option value=''>-- Pilih Guru --</option>";

                            // wali aktif
                            if ($row['guru_id']) {
                                echo "<option value='{$row['guru_id']}' selected>{$row['nama_guru']}</option>";
                            }

                            // guru available
                            foreach ($guru_available as $g) {
                                echo "<option value='{$g['id']}'>{$g['nama']}</option>";
                            }

                            echo "</select>";
                            echo "</td>";

                            // INPUT WA
                            echo "<td>";
                            echo "
                            <div class='d-flex'>
                                <span>+62</span><input type='text'
                                class='form-control wa-input'
                                data-kelas='{$row['kelas']}'
                                value='{$wa}'
                                placeholder='822xxxxxxxx'>
                            </div>";
                            echo "</td>";

                            echo "</tr>";
                            $no++;
                        }
                        ?>

                    </tbody>
                </table>

            </div>
        </div>

    </div>
</section>

<?php
mysqli_close($konek);
include('views/footer.php');
?>

<script>
    $(function() {
        $("#datagururekap").DataTable({
            paging: false,
            searching: false,
            info: false
        });
    });

    /* =========================
       UPDATE WALI KELAS
       ========================= */
    $(document).on('change', '.wali-select', function() {
        let kelas = $(this).data('kelas');
        let guru_id = $(this).val();

        $.post('../app/set_walikelas.php', {
            kelas: kelas,
            guru_id: guru_id
        }, function() {
            location.reload();
        });
    });

    /* =========================
       UPDATE NOMOR WA
       ========================= */
    $(document).on('blur', '.wa-input', function() {
        let kelas = $(this).data('kelas');
        let wa = $(this).val();

        $.post('../app/set_wa.php', {
            kelas: kelas,
            wa: wa
        });
    });
</script>

<script>
    /* =========================
   NORMALISASI NOMOR WA
   ========================= */
    function normalizeWA(input) {
        let val = input.value;

        // ambil hanya angka
        val = val.replace(/\D/g, '');

        // hapus prefix 62 jika ada
        if (val.startsWith('62')) {
            val = val.substring(2);
        }
        if (val.startsWith('0')) {
            val = val.substring(1);
        }

        input.value = val;
    }

    /* realtime filter saat ngetik / paste */
    $(document).on('input', '.wa-input', function() {
        normalizeWA(this);
    });

    /* =========================
       UPDATE NOMOR WA (AJAX)
       ========================= */
    $(document).on('blur', '.wa-input', function() {
        let kelas = $(this).data('kelas');
        let wa = $(this).val();

        if (wa === '') return;

        $.post('ajax/set_wa.php', {
            kelas: kelas,
            wa: wa
        });
    });
</script>
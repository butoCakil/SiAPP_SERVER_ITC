<!-- Memeriksa setiap TAG yang masuk dihalaman index -->
<div class="cektag">
    <?php
    include "config/konesi.php";

    // Prepare the SELECT statement to retrieve data from the 'tempreq' table
    $query_select_tempreq = "SELECT * FROM tempreq ORDER BY timestamp DESC LIMIT 100";
    $stmt_select_tempreq = mysqli_stmt_init($konek);
    mysqli_stmt_prepare($stmt_select_tempreq, $query_select_tempreq);
    mysqli_stmt_execute($stmt_select_tempreq);
    $result_select_tempreq = mysqli_stmt_get_result($stmt_select_tempreq);

    foreach ($result_select_tempreq as $dt) {
        $timestamp = $dt['timestamp'];
        $ip = $dt['ip'];
        $info = $dt['info'];
        $req = $dt['req'];
        $detail = $dt['detail'];

        $msg = "$timestamp - $ip - [$info] $detail";

        // Hitung selisih waktu antara timestamp dan waktu saat ini
        $timestampUnix = strtotime($timestamp);
        date_default_timezone_set('Asia/Jakarta');
        $currentTimeUnix = strtotime(date('Y-m-d H:i:s'));
        $timeDifference = $currentTimeUnix - $timestampUnix;

        // Set warna teks berdasarkan kondisi
        $textColor = ($timeDifference < (60 * 10)) ? 'white' : 'grey';

        // Tampilkan pesan dengan warna yang sesuai
        echo "<span style='color: $textColor;'>$msg</span><br>";
    }

    mysqli_stmt_close($stmt_select_tempreq);
    mysqli_close($konek);
    ?>
</div>

<style>
    .cektag {
        margin: 10px;
        max-height: 360px;
        overflow-y: auto;
    }

    /* Menyembunyikan tampilan scrollbar di browser berbasis Webkit */
    .cektag::-webkit-scrollbar {
        width: 0.5em;
    }

    .cektag::-webkit-scrollbar-thumb {
        background-color: darkgrey;
        outline: 1px solid slategrey;
    }
</style>
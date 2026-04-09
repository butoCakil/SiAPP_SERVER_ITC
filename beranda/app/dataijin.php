<?php
// include('../../config/konesi.php');
// $linkfoto = '../../img/user/';

if ($datab == 'dataguru') {
    $sortir = " WHERE kode = 'GR' OR kode = 'KR' ";
} elseif ($datab == 'datasiswa') {
    $sortir = " WHERE kode <> 'GR' AND kode <> 'KR' ";
} else {
    $sortir = "";
}

if ($konek) {
    $sql = "SELECT * FROM daftarijin" . $sortir . "ORDER BY tanggalijin DESC";
    $result = mysqli_query($konek, $sql);
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    // printf('<pre>%s</pre>', var_export($data, true));
} else {
    echo "Koneksi gagal";
}

?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

<style>
    #dataijin_tabel #foto_dataijin,
    #dataijin_tabel #fotodoc_dataijin {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        object-position: top;
        background-color: #ffe;
        padding: 2px;
    }

    #dataijin_tabel thead th {
        background-color: #555;
        background: linear-gradient(to bottom, #555, #333);
        color: #fff;
        text-align: center;
        vertical-align: middle;
        position: sticky;
        top: 0;
        z-index: 2;
    }

    #dataijin_tabel tbody #freez {
        background-color: #fff;
        position: sticky;
        left: 0;
        z-index: 1;
    }

    #dataijin_tabel thead #freezer {
        background-color: #555;
        background: linear-gradient(to bottom, #555, #333);
        position: sticky;
        left: 0;
        top: 0;
        z-index: 3;
    }
</style>

<table id="dataijin_tabel" class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th id="freezer">Nama</th>
            <th>Info</th>
            <th>Foto</th>
            <th>Tanggal</th>
            <th>Ket.</th>
            <th>Doc</th>
            <th>Kode</th>
            <!-- <th>timestamp</th> -->
        </tr>
    </thead>
    <tbody>
        <?php $no = 0; ?>
        <?php foreach ($data as $key => $value) {
            $link_foto = $linkfoto . $value['foto'];
            $link_fotodoc = $linkfoto . $value['fotodoc'];
            $no++; ?>
            <tr>
                <td><?= $no; ?></td>
                <td id="freez"><?= $value['nama']; ?></td>
                <td>
                    <span class="badge bg-info">
                        <?= $value['info']; ?>
                    </span>
                </td>
                <td>
                    <a href="<?= $link_foto; ?>"><img id="foto_dataijin" src="<?= $link_foto; ?>" class="elevation-1"></a>
                </td>
                <td>
                    <span class="badge bg-info">
                        <?= $value['tanggalijin']; ?>
                    </span>
                </td>
                <td>
                    <span class="badge bg-primary">
                        <?= $value['keterangan']; ?>
                    </span>
                </td>
                <td>
                    <a href="<?= $link_fotodoc; ?>"><img id="fotodoc_dataijin" src="<?= $link_fotodoc; ?>" class="elevation-1"></a>
                </td>
                <td>
                    <span class="badge bg-primary">
                        <?= $value['kode']; ?>
                    </span>
                </td>
                <!-- <td>
                    <span class="badge bg-info">
                        <?= date('d-m-Y', strtotime($value['timestamp'])); ?>
                    </span>
                    <span class="badge bg-info">
                        <?= date('H:i:s', strtotime($value['timestamp'])); ?>
                    </span>
                </td> -->
            </tr>
        <?php } ?>
    </tbody>
</table>
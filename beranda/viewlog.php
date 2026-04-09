<?php
$device_id = $_GET['device_id'] ?? '';


if (empty($device_id)) {
    die("Device ID tidak ditemukan.");
}


$dir = '../data/uploads/';
$files = glob($dir . '*_log_' . $device_id . '.txt');


// Urutkan file dari terbaru ke lama (berdasarkan tanggal di nama file)
usort($files, function ($a, $b) {
    return strcmp($b, $a);
});
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Log <?= htmlspecialchars($device_id) ?></title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f5f6f8;
            margin: 30px;
        }

        h2 {
            color: #333;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            background: #fff;
            margin-bottom: 8px;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>


    <h2>📜 Log untuk Device: <?= htmlspecialchars($device_id) ?></h2>


    <?php if (empty($files)) : ?>
        <p>Tidak ada file log untuk device ini.</p>
    <?php else : ?>
        <ul>
            <?php foreach ($files as $file):
                $filename = basename($file);
            ?>
                <li>
                    <a href="<?= htmlspecialchars($dir . $filename) ?>" target="_blank"><?= htmlspecialchars($filename) ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>


    <p><a href="dashdevice.php">⬅️ Kembali ke Dashboard</a></p>


</body>

</html>
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Jakarta');

header('Content-Type: application/json; charset=utf-8');

// ✅ safety check koneksi
if (!file_exists(__DIR__ . "/../config/konesi.php")) {
    echo json_encode(["status" => "error", "message" => "File koneksi tidak ditemukan"]);
    exit;
}
require_once "../config/konesi.php";

// ✅ safety check koneksi object
if (!isset($konek) || !$konek instanceof mysqli) {
    echo json_encode(["status" => "error", "message" => "Koneksi database tidak valid"]);
    exit;
}

$json = file_get_contents("php://input");
$data = json_decode($json, true);

// ✅ safety check JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["status" => "error", "message" => "Format JSON rusak: " . json_last_error_msg()]);
    exit;
}

if (!$data || !isset($data['data']) || !is_array($data['data'])) {
    echo json_encode(["status" => "error", "message" => "Data JSON tidak valid"]);
    exit;
}

// --- Meta Data ---
$meta_chipID = $data['chipID'] ?? '';
$meta_macAddress = $data['macAddress'] ?? ($_SERVER['HTTP_X_MAC'] ?? 'unknown');
$meta_nodevice = $data['nodevice'] ?? '0';
$meta_ipAddress = $data['ipAddress'] ?? ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
$meta_timestamp = $data['timestamp'] ?? date('Y-m-d H:i:s');

$diff_a_time = '0';
$diff_b_time = '0';

$json_content['data'] = [];

// --- Lokasi File JSON ---
$folder = __DIR__ . "/jsonPresensi";
if (!is_dir($folder) && !mkdir($folder, 0777, true)) {
    echo json_encode(["status" => "error", "message" => "Gagal membuat folder penyimpanan"]);
    exit;
}
$today = date("Y-m-d");
$json_file = "$folder/presensi-$today.json";
$error_log = "$folder/errorLog.txt";

// --- Siapkan struktur awal JSON ---
if (file_exists($json_file)) {
    $raw_json = file_get_contents($json_file);
    $json_content = json_decode($raw_json, true);
    if (!is_array($json_content)) $json_content = [];
} else {
    $json_content = [];
    $json_content['recent'][] = [
        "chipID"      => $meta_chipID,
        "macAddress"  => [$meta_macAddress],
        "nodevice"    => [$meta_nodevice],
        "ipAddress"   => [$meta_ipAddress],
        "timestamp"   => [$meta_timestamp],
        "last_update" => date("Y-m-d H:i:s")
    ];
    $json_content['data'] = [];
}

// cek setting waktu masuk dan pulang
$sql = "SELECT waktumasuk, waktupulang FROM statusnya LIMIT 1";
$result = $konek->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $waktumasuk = $row['waktumasuk'] ?? "";
    $waktupulang = $row['waktupulang'] ?? "";
} else {
    $waktumasuk = "";
    $waktupulang = "";
}

// --- Siapkan Statement ---
$stmt_siswa = $konek->prepare("SELECT nama, kelas, kode FROM datasiswa WHERE nokartu = ?");
$stmt_check = $konek->prepare("SELECT waktumasuk, ketmasuk, waktupulang, ketpulang FROM datapresensi WHERE nokartu = ? AND tanggal = ? LIMIT 1");
$stmt_insert = $konek->prepare("
    INSERT INTO datapresensi (
        nokartu, nomorinduk, nama, info, foto,
        waktumasuk, ketmasuk, a_time,
        waktupulang, ketpulang, b_time,
        tanggal, keterangan, updated_at,
        kode, infodevice, infodevice2
    ) VALUES (
        ?, ?, ?, ?, '0',
        ?, ?, ?,
        ?, ?, ?,
        ?, '0', ?,
        ?, ?, ?
    )
");
$stmt_update = $konek->prepare("
    UPDATE datapresensi
    SET waktumasuk = ?, ketmasuk = ?, a_time = ?, waktupulang = ?, ketpulang = ?, b_time = ?, updated_at = ?
    WHERE nokartu = ? AND tanggal = ?
");

if (!$stmt_siswa || !$stmt_check || !$stmt_insert || !$stmt_update) {
    echo json_encode(["status" => "error", "message" => "Gagal prepare statement: " . $konek->error]);
    exit;
}

$konek->begin_transaction();

$inserted = 0;
$updated = 0;
$skipped = 0;
$errors = [];
$errors["nodevice"] = $meta_nodevice;
$errors["detail"] = [];
$detailError = [];

foreach ($data['data'] as $item) {
    // ✅ safety check format input
    if (!is_array($item)) continue;

    $nokartu = trim($item['i'] ?? '');
    $nomorinduk = trim($item['n'] ?? '');
    $t = trim($item['t'] ?? '');
    $tm = trim($item['tm'] ?? '');
    $tp = trim($item['tp'] ?? '');
    $s = strtoupper(trim($item['s'] ?? ''));

    // parse sesi jadi sm (masuk) dan sp (pulang)
    // letakkan setelah: $s = strtoupper(trim($item['s'] ?? ''));
    $sm = ''; // sesi masuk: 'M' atau 'T' (atau '' jika tidak ada)
    $sp = ''; // sesi pulang: 'C' atau 'P' (atau '' jika tidak ada)

    // jika kosong -> tetap kosong
    if ($s !== '') {
        // pastikan hanya ambil karakter yang relevan (ambil tiap char)
        // ini toleran terhadap 'M','T','C','P' atau kombinasi seperti 'MC','MP','TC','TP'
        $chars = str_split($s);
        foreach ($chars as $ch) {
            if ($ch === 'M' || $ch === 'T') {
                // prefer first find for masuk (first-scan wins)
                if ($sm === '') $sm = $ch;
            } elseif ($ch === 'C' || $ch === 'P') {
                if ($sp === '') $sp = $ch;
            }
            // abaikan karakter lain (jika ada noise)
        }
    }

    if (empty($nokartu) || empty($nomorinduk) || empty($t) || empty($s)) {
        $msg = "Data tidak lengkap untuk NIS: $nomorinduk";
        $errors["detail"][] = $meta_timestamp . ": " . $msg;
        file_put_contents($error_log, "[$today] $msg\n", FILE_APPEND);
        continue;
    }

    $stmt_siswa->bind_param("s", $nokartu);
    $stmt_siswa->execute();
    $res_siswa = $stmt_siswa->get_result();

    if (!$res_siswa) {
        $msg = $meta_timestamp . ": " . "Query siswa gagal: " . $stmt_siswa->error;
        $errors["detail"][] = $msg;
        file_put_contents($error_log, "[$today] $msg\n", FILE_APPEND);
        continue;
    }

    if (!($row_siswa = $res_siswa->fetch_assoc())) {
        $msg = $meta_timestamp . ": " . "Kartu tidak terdaftar: $nokartu / $nomorinduk";
        $errors["detail"][] = $msg;
        file_put_contents($error_log, "[$today] $msg\n", FILE_APPEND);
        $skipped++;
        continue;
    }

    $nama = $row_siswa['nama'] ?? '0';
    $info = $row_siswa['kelas'] ?? '0';
    $kode = $row_siswa['kode'] ?? '0';

    $tanggal = substr($t, 0, 10);
    $jam = substr($t, 11, 8);
    $tanggalmasuk = !empty($tm) ? substr($tm, 0, 10) : $tanggal;
    $jammasuk     = !empty($tm) ? substr($tm, 11, 8) : '00:00:00';
    $tanggalpulang = !empty($tp) ? substr($tp, 0, 10) : $tanggal;
    $jampulang     = !empty($tp) ? substr($tp, 11, 8) : '00:00:00';


    $stmt_check->bind_param("ss", $nokartu, $tanggal);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();

    if (!$res_check) {
        $msg = "Query check gagal: " . $stmt_check->error;
        $errors["detail"][] = $meta_timestamp . ": " . $msg;
        file_put_contents($error_log, "[$today] $msg\n", FILE_APPEND);
        continue;
    }

    $statusGabungan = '';
    $json_entry = null;

    if ($row_exist = $res_check->fetch_assoc()) {
        $wmasuk_old = $row_exist['waktumasuk'] ?: '0';
        $kmasuk_old = $row_exist['ketmasuk'] ?: '0';
        $wpulang_old = $row_exist['waktupulang'] ?: '0';
        $kpulang_old = $row_exist['ketpulang'] ?: '0';
        $doUpdate = false;

        if ($sm == 'M' || $sm == 'T') {
            if ($wmasuk_old === '0' || strtotime($jam) < strtotime($wmasuk_old)) {
                $doUpdate = true;
            }
        } elseif ($sp == 'C' || $sp == 'P') {
            if ($wpulang_old === '0' || strtotime($jam) > strtotime($wpulang_old)) {
                $doUpdate = true;
            }
        }

        if ($doUpdate) {
            $wmasuk_new = ($sm == 'M' || $sm == 'T') ? $jammasuk : $wmasuk_old;
            $kmasuk_new = ($sm == 'M' || $sm == 'T') ? $sm : $kmasuk_old;
            $wpulang_new = ($sp == 'C' || $sp == 'P') ? $jampulang : $wpulang_old;
            $kpulang_new = ($sp == 'C' || $sp == 'P') ? $sp : $kpulang_old;
            $diff_a_time = selisihWaktu($waktumasuk, $wmasuk_new);
            $diff_b_time = selisihWaktu($waktupulang, $wpulang_new);

            $stmt_update->bind_param(
                "sssssssss",
                $wmasuk_new,
                $kmasuk_new,
                $diff_a_time,
                $wpulang_new,
                $kpulang_new,
                $diff_b_time,
                $t,
                $nokartu,
                $tanggal
            );
            if ($stmt_update->execute()) {
                $updated++;
                // $statusGabungan = ($kmasuk_new . $kpulang_new);
                $statusGabungan = trim(($kmasuk_new !== '0' ? $kmasuk_new : '') . ($kpulang_new !== '0' ? $kpulang_new : ''));
                $json_entry = [
                    "nokartu" => $nokartu,
                    "nomorinduk" => $nomorinduk,
                    "nama" => $nama,
                    "info" => $info,
                    "kode" => $kode,
                    "tanggal" => $tanggal,
                    "wmasuk" => $wmasuk_new,
                    "wpulang" => $wpulang_new,
                    "status" => $statusGabungan
                ];
            }
        } else {
            $skipped++;
        }
    } else {
        $wmasuk = ($sm == 'M' || $sm == 'T') ? $jammasuk : '0';
        $ketmasuk = ($sm == 'M' || $sm == 'T') ? $sm : '0';
        $wpulang = ($sp == 'C' || $sp == 'P') ? $jampulang : '0';
        $ketpulang = ($sp == 'C' || $sp == 'P') ? $sp : '0';
        $diff_a_time = selisihWaktu($waktumasuk, $wmasuk);
        $diff_b_time = selisihWaktu($waktupulang, $wpulang);

        $nodevice_bind = is_array($meta_nodevice) ? ($meta_nodevice[0] ?? '0') : $meta_nodevice;
        $mac_bind = is_array($meta_macAddress) ? ($meta_macAddress[0] ?? '0') : $meta_macAddress;

        $stmt_insert->bind_param(
            "sssssssssssssss",
            $nokartu,
            $nomorinduk,
            $nama,
            $info,
            $wmasuk,
            $ketmasuk,
            $diff_a_time,
            $wpulang,
            $ketpulang,
            $diff_b_time,
            $tanggal,
            $t,
            $kode,
            $nodevice_bind,
            $mac_bind
        );
        if ($stmt_insert->execute()) {
            $inserted++;
            // $statusGabungan = ($ketmasuk . $ketpulang);
            $statusGabungan = trim(($ketmasuk !== '0' ? $ketmasuk : '') . ($ketpulang !== '0' ? $ketpulang : ''));
            $json_entry = [
                "nokartu" => $nokartu,
                "nomorinduk" => $nomorinduk,
                "nama" => $nama,
                "info" => $info,
                "kode" => $kode,
                "tanggal" => $tanggal,
                "wmasuk" => $wmasuk,
                "wpulang" => $wpulang,
                "status" => $statusGabungan
            ];
        } else {
            $msg = "Gagal insert $nomorinduk: " . $stmt_insert->error;
            $errors["detail"][] = $meta_timestamp . ": " . $msg;
            file_put_contents($error_log, "[$today] $msg\n", FILE_APPEND);
        }
    }

    // --- Update ke JSON jika sukses ---
    if ($json_entry) {
        $found = false;
        if (!isset($json_content['data']) || !is_array($json_content['data'])) {
            $json_content['data'] = [];
        }

        foreach ($json_content['data'] as &$existing) {
            if (($existing['nokartu'] ?? '') === $nokartu && ($existing['tanggal'] ?? '') === $tanggal) {
                $existing = $json_entry;
                $found = true;
                break;
            }
        }
        unset($existing);
        if (!$found) $json_content['data'][] = $json_entry;
    }
}

$konek->commit();

// --- Pastikan recent ---
if (!isset($json_content['recent']) || !is_array($json_content['recent'])) {
    $json_content['recent'] = [];
}

$found = false;
foreach ($json_content['recent'] as &$entry) {
    if (($entry['chipID'] ?? '') === $meta_chipID) {
        $isDifferent = false;

        $last_mac = end($entry['macAddress']);
        $last_nodevice = end($entry['nodevice']);
        $last_ip = end($entry['ipAddress']);

        if ($last_mac !== $meta_macAddress) {
            $entry['macAddress'][] = $meta_macAddress;
            $isDifferent = true;
        }

        if ($last_nodevice !== $meta_nodevice) {
            $entry['nodevice'][] = $meta_nodevice;
            $isDifferent = true;
        }

        if ($last_ip !== $meta_ipAddress) {
            $entry['ipAddress'][] = $meta_ipAddress;
            $isDifferent = true;
        }

        $last_time = end($entry['timestamp']);
        if ($last_time !== $meta_timestamp) {
            $entry['timestamp'][] = $meta_timestamp;
            $isDifferent = true;
        }

        if ($isDifferent) {
            $entry['last_update'] = date("Y-m-d H:i:s");
        }

        $found = true;
        break;
    }
}
unset($entry);

if (!$found) {
    $json_content['recent'][] = [
        "chipID"      => $meta_chipID,
        "macAddress"  => [$meta_macAddress],
        "nodevice"    => [$meta_nodevice],
        "ipAddress"   => [$meta_ipAddress],
        "timestamp"   => [$meta_timestamp],
        "last_update" => date("Y-m-d H:i:s")
    ];
}

if ($errors) {
    // --- Simpan ke JSON bagian errors ---
    if (!isset($json_content['errors']) || !is_array($json_content['errors'])) {
        $json_content['errors'] = [];
    }

    if (!empty($errors["detail"])) {
        $error_found = false;
        foreach ($json_content['errors'] as &$err_entry) {
            if (($err_entry["nodevice"] ?? '') === $meta_nodevice) {
                // Jika nodevice sudah ada, gabungkan detail baru
                foreach ($errors["detail"] as $new_error) {
                    if (!in_array($new_error, $err_entry["detail"] ?? [])) {
                        $err_entry["detail"][] = $new_error;
                    }
                }

                $error_found = true;
                break;
            }
        }
        unset($err_entry);

        // Jika belum ada error untuk nodevice ini, tambahkan baru
        if (!$error_found) {
            $json_content['errors'][] = [
                "nodevice"   => $meta_nodevice,
                "detail"     => array_values(array_unique($errors["detail"]))
            ];
        }
    }
}

// --- Simpan JSON hasil valid ---
if (!file_put_contents(
    $json_file,
    json_encode($json_content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
)) {
    echo json_encode(["status" => "error", "message" => "Gagal menyimpan file JSON"]);
    exit;
}

$stmt_siswa->close();
$stmt_check->close();
$stmt_insert->close();
$stmt_update->close();
$konek->close();

echo json_encode([
    "status" => ($inserted + $updated) > 0 ? "Success" : "noChanges",
    "inserted" => $inserted,
    "updated" => $updated,
    "skipped" => $skipped,
    "errors" => $errors["detail"],
    "json_file" => basename($json_file),
    "timestamp" => date('Y-m-d H:i:s')
], JSON_PRETTY_PRINT);

function selisihWaktu($waktu1, $waktu2)
{
    // Bersihkan input (hapus spasi berlebih)
    $waktu1 = trim(str_replace(' ', '', $waktu1));
    $waktu2 = trim(str_replace(' ', '', $waktu2));

    // Jika ada input kosong atau '0', anggap "00:00:00"
    if ($waktu1 === '' || $waktu1 === '0') $waktu1 = '00:00:00';
    if ($waktu2 === '' || $waktu2 === '0') $waktu2 = '00:00:00';

    try {
        $t1 = new DateTime($waktu1);
        $t2 = new DateTime($waktu2);
    } catch (Exception $e) {
        // Jika parsing gagal, anggap nol
        $t1 = new DateTime('00:00:00');
        $t2 = new DateTime('00:00:00');
    }

    $diff = $t1->diff($t2);
    return $diff->format('%H:%I:%S');
}

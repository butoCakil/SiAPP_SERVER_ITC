<?php

date_default_timezone_set('Asia/Jakarta');

// Tentukan batas waktu (30 Maret 2026)
$batasWaktu = strtotime('2026-03-30');
$tanggalSekarang = time();

// Jika tanggal kurang dari 30 Maret 2026, hentikan program
if ($tanggalSekarang < $batasWaktu) {
    // return; // Gunakan 'exit;' jika ini bukan di dalam fungsi/include
    $kirimPesan = false;
} else {
    $kirimPesan = true;
}

// --- Lanjut program setelahnya di sini ---

include __DIR__ . "/../../config/konesi.php";
include "sendchat.php";

/* ================= CONFIG ================= */

$threshold = 60;
$offline_after = 120;          // 2 missed cron
$escalation_after = 300;       // 5 menit
$quiet_start = 18;
$quiet_end = 6;

$escalation_window_start = 10;
$escalation_window_end   = 16;

$status_file = __DIR__ . '/last_device_status.json';
$escalation_state_file = __DIR__ . '/escalation_state.json';
$log_file = __DIR__ . '/check_offline.log';
$lockFile = __DIR__ . '/check_offline.lock';

$now = date("Y-m-d H:i:s");

/* ================= LOCK ================= */

$fp = fopen($lockFile, 'c');
if (!flock($fp, LOCK_EX | LOCK_NB)) {
    exit;
}

/* ================= HELPERS ================= */

function logMessage($msg)
{
    global $log_file;
    file_put_contents($log_file, "[" . date("Y-m-d H:i:s") . "] $msg\n", FILE_APPEND);
}

function isQuietHours($start, $end)
{
    $hour = (int)date('G');
    if ($start < $end) {
        return ($hour >= $start && $hour < $end);
    }
    return ($hour >= $start || $hour < $end);
}

function isHourInRange($start, $end)
{
    $hour = (int)date('G');
    return ($hour >= $start && $hour < $end);
}

function isWeekday()
{
    $day = (int)date('N');
    return ($day >= 1 && $day <= 5);
}

function formatDuration($seconds)
{
    if ($seconds < 0) $seconds = 0;

    $days = floor($seconds / 86400);
    $hours = floor(($seconds % 86400) / 3600);
    $minutes = floor(($seconds % 3600) / 60);

    // Lebih dari 1 hari
    if ($days > 0) {
        return sprintf("%d h %02d:%02d", $days, $hours, $minutes);
    }

    // Lebih dari 1 jam
    if ($hours > 0) {
        return sprintf("%02d:%02d", $hours, $minutes);
    }

    // Kurang dari 1 jam
    return $minutes . " m";
}

function buildFullSnapshot($currentStatus, $deviceInfo)
{

    $output = "────────────────\n";
    $output .= "📋 STATUS SEMUA DEVICE\n\n";

    foreach ($currentStatus as $id => $online) {

        $info = $deviceInfo[$id]['info'] ? ("_(" . $deviceInfo[$id]['info'] . ")_") : "";

        if ($online == 1) {

            $since = $deviceInfo[$id]['online_since'];
            $duration = formatDuration(time() - strtotime($since));

            $output .= "🟢 *$id* $info ⏳ $duration\n";
        } else {

            $since = $deviceInfo[$id]['offline_since'];
            $duration = formatDuration(time() - strtotime($since));

            $output .= "🔴 *$id* $info ⏳ $duration\n";
        }
    }

    return $output;
}

/* ================= 1️⃣ OFFLINE DETECTION ================= */

$q = "
SELECT id, device_id, online_since, last_status
FROM devices
WHERE online = 1
AND TIMESTAMPDIFF(SECOND, last_seen, NOW()) >= $offline_after
";

$res = mysqli_query($konek, $q);

while ($r = mysqli_fetch_assoc($res)) {

    $status = json_decode($r['last_status'], true);
    if (!is_array($status)) $status = [];

    $status['status'] = 'offline';

    $newStatus = mysqli_real_escape_string(
        $konek,
        json_encode($status, JSON_UNESCAPED_SLASHES)
    );

    mysqli_query($konek, "
        UPDATE devices
        SET online = 0,
            offline_since = '$now',
            last_status = '$newStatus'
        WHERE id = {$r['id']}
    ");
}

/* ================= 2️⃣ SNAPSHOT ================= */

$currentStatus = [];
$deviceInfo = [];

$res_all = mysqli_query($konek, "
SELECT
    d.device_id,
    d.online,
    d.offline_since,
    d.online_since,
    COALESCE(r.info_device, d.info) AS info_device
FROM devices d
LEFT JOIN reg_device r ON r.no_device = d.device_id
ORDER BY d.device_id ASC
");

while ($r = mysqli_fetch_assoc($res_all)) {
    $currentStatus[$r['device_id']] = (int)$r['online'];

    $deviceInfo[$r['device_id']] = [
        'online_since'  => $r['online_since'],
        'offline_since' => $r['offline_since'],
        'info'          => $r['info_device']
    ];
}

/* ================= 3️⃣ LOAD PREVIOUS SNAPSHOT ================= */

$lastStatus = [];
if (file_exists($status_file)) {
    $lastStatus = json_decode(file_get_contents($status_file), true) ?? [];
}

/* ================= 4️⃣ DELTA DETECTION ================= */

$changes = [];
$recoveryList = [];

foreach ($currentStatus as $id => $online) {
    $prev = $lastStatus[$id] ?? null;
    if ($prev !== null && $prev !== $online) {

        $duration = 0;

        if ($online == 0) {
            $duration = time() - strtotime($deviceInfo[$id]['online_since']);
        } else {
            $duration = time() - strtotime($deviceInfo[$id]['offline_since']);
            $recoveryList[] = $id;
        }

        $changes[$id] = [
            'from' => $prev,
            'to'   => $online,
            'duration' => formatDuration($duration)
        ];
    }
}

/* ================= 5️⃣ SEVERITY ================= */

$total_devices = count($currentStatus);
$total_online = array_sum($currentStatus);
$total_offline = $total_devices - $total_online;
$offline_ratio = $total_devices > 0 ? $total_offline / $total_devices : 0;

$severity = 'ℹ️ *INFO*';

if ($total_offline >= 10 || $offline_ratio > 0.2) {
    $severity = '🚨 *CRITICAL*';
} elseif ($offline_ratio > 0) {
    $severity = '⚠️ *WARNING*';
}

$massOutage = ($offline_ratio >= 0.8);
$totalDown  = ($total_online == 0);

/* ================= 6️⃣ DELTA REPORT ================= */

if (count($changes) > 0 && isWeekday()) {

    $msg  = "📢 STATUS UPDATE\n";
    $msg .= "⏱ $now\n";
    $msg .= "Severity: $severity\n";
    $msg .= "────────────────\n\n";

    foreach ($changes as $id => $c) {

        // $info = $deviceInfo[$id]['info'] ?? '-';
        $info = $deviceInfo[$id]['info'] ? ("_(" . $deviceInfo[$id]['info'] . ")_") : "";

        if ($c['to'] == 0) {
            $msg .= "🔴 $id $info\n";
            $msg .= "🟢 Online → 🔴 Offline\n";
            $msg .= "⏳ Online sebelumnya: {$c['duration']}\n\n";
        } else {
            $msg .= "🟢 $id $info\n";
            $msg .= "🔴 Offline → 🟢 Online\n";
            $msg .= "⏳ Offline sebelumnya: {$c['duration']}\n\n";
        }
    }

    $msg .= "\n";
    $msg .= "📊 Online: $total_online | Offline: $total_offline | Total: " . ($total_online + $total_offline);
    $msg .= "\n";
    $msg .= buildFullSnapshot($currentStatus, $deviceInfo);

    if (!isQuietHours($quiet_start, $quiet_end)) {
        if ($kirimPesan)
            sendMessage("082241863393", $msg, null);
    }

    logMessage("Delta report sent.");
}

/* ================= 7️⃣ ESCALATION ================= */

$escalationState = [];
if (file_exists($escalation_state_file)) {
    $escalationState = json_decode(file_get_contents($escalation_state_file), true) ?? [];
}

$today = date('Y-m-d');
$escalateList = [];

foreach ($currentStatus as $id => $online) {

    if ($online == 0) {

        $since = $deviceInfo[$id]['offline_since'];
        if ($since && (time() - strtotime($since)) >= $escalation_after) {

            if (!isset($escalationState[$id]) || $escalationState[$id] !== $today) {
                $escalateList[] = $id;
            }
        }
    }
}

if (
    $severity === 'CRITICAL' &&
    count($escalateList) > 0 &&
    isHourInRange($escalation_window_start, $escalation_window_end) &&
    isWeekday()
) {

    if ($massOutage) {

        $msg  = "🚨 MASS OUTAGE\n";
        $msg .= "$total_offline dari $total_devices device offline\n";
        $msg .= "⏱ $now\n";
        $msg .= "Kemungkinan gangguan jaringan utama.";
    } elseif ($totalDown) {

        $msg  = "🚨 TOTAL SYSTEM DOWN\n";
        $msg .= "Semua device offline\n";
        $msg .= "⏱ $now";
    } else {

        $msg  = "⏰ ESCALATION (CRITICAL)\n";
        $msg .= "⏱ $now\n\n";
        $msg .= "Total Offline: $total_offline dari $total_devices\n\n";

        $countShown = 0;
        foreach ($escalateList as $id) {

            if ($countShown >= 20) {
                $remaining = count($escalateList) - 20;
                $msg .= "(+$remaining device lainnya)\n";
                break;
            }

            $since = $deviceInfo[$id]['offline_since'];
            $duration = formatDuration(time() - strtotime($since));
            $info = $deviceInfo[$id]['info'] ?? '-';

            $msg .= "🔴 $id ($info) – $duration\n";
            $countShown++;
        }
    }

    $msg .= "\n";
    $msg .= buildFullSnapshot($currentStatus, $deviceInfo);

    if (!isQuietHours($quiet_start, $quiet_end)) {

        if ($kirimPesan)
            sendMessage("082241863393", $msg, null);

        foreach ($escalateList as $id) {
            $escalationState[$id] = $today;
        }

        file_put_contents($escalation_state_file, json_encode($escalationState, JSON_PRETTY_PRINT));

        logMessage("Escalation sent.");
    }
}

/* ================= 8️⃣ RECOVERY RESET ================= */

foreach ($recoveryList as $id) {
    if (isset($escalationState[$id])) {
        unset($escalationState[$id]);
    }
}

file_put_contents($escalation_state_file, json_encode($escalationState, JSON_PRETTY_PRINT));

/* ================= 9️⃣ SAVE SNAPSHOT ================= */

file_put_contents($status_file, json_encode($currentStatus, JSON_PRETTY_PRINT));

mysqli_close($konek);

flock($fp, LOCK_UN);
fclose($fp);

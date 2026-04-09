<?php
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

include('../config/konesi.php');
include('app/get_user.php');

// Normalisasi chip_id: terima hex (misal "8A3F0C") maupun desimal, simpan sebagai string VARCHAR
function parse_chip_id(string $raw): string
{
    $raw = trim($raw);
    if ($raw === '') return '';
    if (preg_match('/^(0x)?[0-9A-Fa-f]+$/', $raw)) {
        $clean = preg_replace('/^0x/i', '', $raw);
        return strtoupper($clean);
    }
    return $raw;
}

$expected_key = md5("siap\$bos");
$msg = '';
$msg_type = '';

// ============================================================
// POST HANDLER
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cmd = $_POST['cmd'] ?? '';
    $key  = $_POST['key'] ?? '';

    // --- Hapus dari tabel devices (device online) ---
    if ($cmd === 'del' && isset($_POST['delete_device_id'])) {
        if (hash_equals($expected_key, $key)) {
            $device_id = mysqli_real_escape_string($konek, $_POST['delete_device_id']);
            if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $device_id)) {
                $msg = 'Device ID tidak valid';
                $msg_type = 'error';
            } else {
                $query = "DELETE FROM devices WHERE device_id = '$device_id' LIMIT 1";
                if (mysqli_query($konek, $query)) {
                    header("Location: " . $_SERVER['PHP_SELF'] . "?view=monitor&msg=deleted");
                    exit;
                } else {
                    $msg = 'Gagal hapus: ' . mysqli_error($konek);
                    $msg_type = 'error';
                }
            }
        } else {
            $msg = 'Akses tidak valid!';
            $msg_type = 'error';
        }
    }

    // --- Tambah device baru ke reg_device ---
    if ($cmd === 'add_reg') {
        if (hash_equals($expected_key, $key)) {
            $chip_id     = mysqli_real_escape_string($konek, parse_chip_id($_POST['chip_id'] ?? ''));
            $no_device   = mysqli_real_escape_string($konek, trim($_POST['no_device'] ?? ''));
            $kode        = mysqli_real_escape_string($konek, trim($_POST['kode'] ?? ''));
            $info_device = mysqli_real_escape_string($konek, trim($_POST['info_device'] ?? ''));
            $status      = mysqli_real_escape_string($konek, trim($_POST['status'] ?? 'aktif'));

            if (!$no_device || !$kode) {
                $msg = 'No Device dan Kode wajib diisi!';
                $msg_type = 'error';
            } else {
                $q = "INSERT INTO reg_device (chip_id, no_device, kode, info_device, status)
                      VALUES ('$chip_id','$no_device','$kode','$info_device','$status')";
                if (mysqli_query($konek, $q)) {
                    header("Location: " . $_SERVER['PHP_SELF'] . "?view=regdevice&msg=added");
                    exit;
                } else {
                    $msg = 'Gagal tambah: ' . mysqli_error($konek);
                    $msg_type = 'error';
                }
            }
        } else {
            $msg = 'Akses tidak valid!';
            $msg_type = 'error';
        }
    }

    // --- Edit device di reg_device ---
    if ($cmd === 'edit_reg') {
        if (hash_equals($expected_key, $key)) {
            $id          = (int)($_POST['reg_id'] ?? 0);
            $chip_id     = mysqli_real_escape_string($konek, parse_chip_id($_POST['chip_id'] ?? ''));
            $no_device   = mysqli_real_escape_string($konek, trim($_POST['no_device'] ?? ''));
            $kode        = mysqli_real_escape_string($konek, trim($_POST['kode'] ?? ''));
            $info_device = mysqli_real_escape_string($konek, trim($_POST['info_device'] ?? ''));
            $status      = mysqli_real_escape_string($konek, trim($_POST['status'] ?? 'aktif'));

            $q = "UPDATE reg_device SET chip_id='$chip_id', no_device='$no_device',
                  kode='$kode', info_device='$info_device', status='$status'
                  WHERE id='$id' LIMIT 1";
            if (mysqli_query($konek, $q)) {
                header("Location: " . $_SERVER['PHP_SELF'] . "?view=regdevice&msg=updated");
                exit;
            } else {
                $msg = 'Gagal update: ' . mysqli_error($konek);
                $msg_type = 'error';
            }
        } else {
            $msg = 'Akses tidak valid!';
            $msg_type = 'error';
        }
    }

    // --- Hapus dari reg_device ---
    if ($cmd === 'del_reg') {
        if (hash_equals($expected_key, $key)) {
            $id = (int)($_POST['reg_id'] ?? 0);
            $q  = "DELETE FROM reg_device WHERE id='$id' LIMIT 1";
            if (mysqli_query($konek, $q)) {
                header("Location: " . $_SERVER['PHP_SELF'] . "?view=regdevice&msg=deleted");
                exit;
            } else {
                $msg = 'Gagal hapus: ' . mysqli_error($konek);
                $msg_type = 'error';
            }
        } else {
            $msg = 'Akses tidak valid!';
            $msg_type = 'error';
        }
    }
}

// ============================================================
// Pesan dari redirect
// ============================================================
if (isset($_GET['msg'])) {
    $map = [
        'added'   => ['Berhasil menambahkan device baru!', 'success'],
        'updated' => ['Berhasil memperbarui data device!', 'success'],
        'deleted' => ['Device berhasil dihapus.',          'success'],
    ];
    if (isset($map[$_GET['msg']])) {
        [$msg, $msg_type] = $map[$_GET['msg']];
    }
}

// ============================================================
// Tentukan view aktif
// ============================================================
$view = $_GET['view'] ?? 'monitor'; // monitor | regdevice

// ============================================================
// Ambil data reg_device untuk view regdevice + modal edit
// ============================================================
$reg_devices = [];
$res_reg = mysqli_query($konek, "SELECT * FROM reg_device ORDER BY id DESC");
while ($row = mysqli_fetch_assoc($res_reg)) {
    $reg_devices[] = $row;
}

// Edit mode – ambil 1 row
$edit_row = null;
if (isset($_GET['edit_id'])) {
    $eid = (int)$_GET['edit_id'];
    $re  = mysqli_query($konek, "SELECT * FROM reg_device WHERE id='$eid' LIMIT 1");
    $edit_row = mysqli_fetch_assoc($re);
}

// ============================================================
// HTML Header
// ============================================================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$title = 'Device Monitoring';
$navlink = 'dashdevice';
include('views/header.php');
?>

<link rel="stylesheet" href="css/dashdevice_3.css">
<style>
    /* ============================================================
   SIAP — dashdevice v2 — dual-view + reg_device CRUD
   ============================================================ */

    :root {
        --brand: #0a84ff;
        --brand-dim: #0a84ff22;
        --success: #30d158;
        --danger: #ff453a;
        --warn: #ffd60a;
        --bg-panel: #1c1c1e;
        --bg-card: #2c2c2e;
        --bg-input: #3a3a3c;
        --text: #f2f2f7;
        --text-sub: #8e8e93;
        --border: #3a3a3c;
        --radius: 12px;
        --shadow: 0 4px 24px rgba(0, 0, 0, .45);
    }

    /* ---- View Toggle Bar ---- */
    .view-toggle-bar {
        display: flex;
        align-items: center;
        gap: 6px;
        background: var(--bg-panel);
        border: 1px solid var(--border);
        border-radius: 50px;
        padding: 4px;
        width: fit-content;
        margin: 0 auto 22px;
    }

    .vt-btn {
        display: flex;
        align-items: center;
        gap: 7px;
        padding: 8px 20px;
        border-radius: 40px;
        border: none;
        background: transparent;
        color: var(--text-sub);
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: background .2s, color .2s;
        white-space: nowrap;
    }

    .vt-btn.active {
        background: var(--brand);
        color: #fff;
        box-shadow: 0 2px 12px rgba(10, 132, 255, .4);
    }

    .vt-btn:hover:not(.active) {
        background: var(--bg-input);
        color: var(--text);
    }

    .vt-btn svg {
        width: 15px;
        height: 15px;
        flex-shrink: 0;
    }

    /* ---- Alert / Notif ---- */
    .siap-alert {
        max-width: 900px;
        margin: 0 auto 16px;
        padding: 12px 18px;
        border-radius: var(--radius);
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .siap-alert.success {
        background: #1a3d2b;
        border: 1px solid var(--success);
        color: var(--success);
    }

    .siap-alert.error {
        background: #3d1a1a;
        border: 1px solid var(--danger);
        color: var(--danger);
    }

    /* ---- Batch Toolbar ---- */
    .batch-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        justify-content: center;
        margin-bottom: 18px;
    }

    .btn-batch {
        padding: 9px 18px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--bg-card);
        color: var(--text);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background .18s, transform .12s;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-batch:hover {
        background: var(--bg-input);
        transform: translateY(-1px);
    }

    .btn-batch.sync {
        border-color: var(--brand);
        color: var(--brand);
    }

    .btn-batch.upload {
        border-color: var(--success);
        color: var(--success);
    }

    .btn-batch.reboot {
        border-color: var(--danger);
        color: var(--danger);
    }

    .btn-batch.setting {
        border-color: var(--warn);
        color: var(--warn);
    }

    /* ---- Progress ---- */
    #progressContainer {
        max-width: 700px;
        margin: 0 auto 10px;
        background: var(--bg-panel);
        border-radius: 50px;
        overflow: hidden;
        border: 1px solid var(--border);
        display: none;
    }

    #progressBar {
        height: 8px;
        background: linear-gradient(90deg, var(--brand), var(--success));
        transition: width .3s;
    }

    #report {
        text-align: center;
        font-size: 13px;
        color: var(--text-sub);
        margin-bottom: 12px;
        min-height: 18px;
    }

    /* ---- Device Container ---- */
    /* #view-monitor .device-container {} */
    /* inherits dashdevice_3.css */

    /* ============================================================
   REG DEVICE TABLE VIEW
   ============================================================ */
    #view-regdevice {
        max-width: 1100px;
        margin: 0 auto;
    }

    .reg-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .reg-header h2 {
        font-size: 18px;
        font-weight: 700;
        color: var(--text);
        margin: 0;
    }

    .reg-header .sub {
        font-size: 13px;
        color: var(--text-sub);
        margin-top: 2px;
    }

    .btn-add {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--brand);
        color: #fff;
        padding: 10px 20px;
        border-radius: 10px;
        border: none;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        transition: opacity .18s, transform .12s;
        text-decoration: none;
    }

    .btn-add:hover {
        opacity: .88;
        transform: translateY(-1px);
    }

    /* Table */
    .reg-table-wrap {
        background: var(--bg-panel);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
    }

    .reg-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13.5px;
    }

    .reg-table thead tr {
        background: var(--bg-card);
        border-bottom: 1px solid var(--border);
    }

    .reg-table th {
        padding: 13px 16px;
        text-align: left;
        font-weight: 700;
        color: var(--text-sub);
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: .05em;
        white-space: nowrap;
    }

    .reg-table td {
        padding: 12px 16px;
        color: var(--text);
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }

    .reg-table tbody tr:last-child td {
        border-bottom: none;
    }

    .reg-table tbody tr:hover td {
        background: var(--bg-card);
    }

    .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: .03em;
    }

    .badge-aktif {
        background: #1a3d2b;
        color: var(--success);
        border: 1px solid var(--success)44;
    }

    .badge-nonaktif {
        background: #3d1a1a;
        color: var(--danger);
        border: 1px solid var(--danger)44;
    }

    .badge-lain {
        background: var(--bg-input);
        color: var(--text-sub);
    }

    .no-device-code {
        font-family: 'SF Mono', 'Fira Code', monospace;
        background: var(--brand-dim);
        color: var(--brand);
        padding: 3px 9px;
        border-radius: 6px;
        font-size: 12.5px;
        font-weight: 700;
    }

    .chip-id-code {
        font-family: 'SF Mono', 'Fira Code', monospace;
        color: var(--text-sub);
        font-size: 12px;
    }

    /* Action buttons in table */
    .tbl-actions {
        display: flex;
        gap: 6px;
    }

    .btn-tbl {
        padding: 6px 13px;
        border-radius: 7px;
        border: 1px solid var(--border);
        background: var(--bg-input);
        color: var(--text);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: background .15s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-tbl:hover {
        background: var(--bg-card);
    }

    .btn-tbl.edit {
        border-color: var(--brand)55;
        color: var(--brand);
    }

    .btn-tbl.edit:hover {
        background: var(--brand-dim);
    }

    .btn-tbl.del {
        border-color: var(--danger)55;
        color: var(--danger);
    }

    .btn-tbl.del:hover {
        background: #3d1a1a;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-sub);
    }

    .empty-state svg {
        opacity: .3;
        margin-bottom: 12px;
    }

    .empty-state p {
        font-size: 14px;
    }

    /* ============================================================
   MODAL — Add / Edit
   ============================================================ */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .75);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-overlay.open {
        display: flex;
    }

    .modal-box {
        background: var(--bg-panel);
        border: 1px solid var(--border);
        border-radius: 18px;
        width: 100%;
        max-width: 480px;
        box-shadow: var(--shadow);
        overflow: hidden;
        animation: modalIn .22s ease;
    }

    @keyframes modalIn {
        from {
            opacity: 0;
            transform: scale(.94) translateY(12px);
        }

        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px 16px;
        border-bottom: 1px solid var(--border);
    }

    .modal-head h3 {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
        color: var(--text);
    }

    .modal-close {
        background: var(--bg-input);
        border: none;
        color: var(--text-sub);
        cursor: pointer;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background .15s;
    }

    .modal-close:hover {
        background: var(--bg-card);
        color: var(--text);
    }

    .modal-body {
        padding: 22px 24px;
    }

    .field-group {
        margin-bottom: 16px;
    }

    .field-group label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: var(--text-sub);
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: 7px;
    }

    .field-group label span.req {
        color: var(--danger);
        margin-left: 2px;
    }

    .field-group input,
    .field-group select {
        width: 100%;
        box-sizing: border-box;
        background: var(--bg-input);
        border: 1px solid var(--border);
        color: var(--text);
        padding: 10px 13px;
        border-radius: 9px;
        font-size: 14px;
        transition: border-color .18s, box-shadow .18s;
        outline: none;
    }

    .field-group input:focus,
    .field-group select:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px var(--brand-dim);
    }

    .field-group input::placeholder {
        color: var(--text-sub);
    }

    .field-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .modal-foot {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 16px 24px 22px;
        border-top: 1px solid var(--border);
    }

    .btn-cancel {
        padding: 10px 20px;
        border-radius: 9px;
        border: 1px solid var(--border);
        background: var(--bg-input);
        color: var(--text-sub);
        font-size: 13.5px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-cancel:hover {
        color: var(--text);
    }

    .btn-submit {
        padding: 10px 24px;
        border-radius: 9px;
        background: var(--brand);
        color: #fff;
        border: none;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        transition: opacity .18s;
    }

    .btn-submit:hover {
        opacity: .88;
    }

    /* Konfirmasi hapus mini */
    .modal-overlay.confirm .modal-box {
        max-width: 380px;
    }

    .confirm-icon {
        font-size: 36px;
        text-align: center;
        margin-bottom: 10px;
    }

    .confirm-msg {
        font-size: 15px;
        color: var(--text);
        text-align: center;
        margin-bottom: 6px;
    }

    .confirm-sub {
        font-size: 13px;
        color: var(--text-sub);
        text-align: center;
        margin-bottom: 0;
    }

    .btn-del-confirm {
        padding: 10px 24px;
        border-radius: 9px;
        background: var(--danger);
        color: #fff;
        border: none;
        font-size: 13.5px;
        font-weight: 700;
        cursor: pointer;
        transition: opacity .18s;
    }

    .btn-del-confirm:hover {
        opacity: .85;
    }

    /* ---- Info strip ---- */
    .info-strip {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--bg-panel);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 10px 16px;
        margin-bottom: 16px;
        font-size: 13px;
        color: var(--text-sub);
        flex-wrap: wrap;
        gap: 8px;
    }

    .info-strip strong {
        color: var(--text);
    }

    /* ---- Responsive ---- */
    @media (max-width: 640px) {

        .reg-table th:nth-child(2),
        .reg-table td:nth-child(2) {
            display: none;
        }

        .field-row {
            grid-template-columns: 1fr;
        }

        .batch-toolbar {
            gap: 6px;
        }

        .btn-batch {
            font-size: 12px;
            padding: 8px 13px;
        }
    }
</style>

<body>

    <?php if ($msg): ?>
        <div class="siap-alert <?= $msg_type ?>">
            <?= $msg_type === 'success' ? '✅' : '⚠️' ?> <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <!-- ============================================================
     VIEW TOGGLE BAR
     ============================================================ -->
    <div class="view-toggle-bar">
        <a href="?view=monitor"
            class="vt-btn <?= $view === 'monitor' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <rect x="2" y="3" width="20" height="14" rx="2" />
                <path d="M8 21h8M12 17v4" />
            </svg>
            Monitor Online
        </a>
        <a href="?view=regdevice"
            class="vt-btn <?= $view === 'regdevice' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <rect x="4" y="2" width="16" height="20" rx="2" />
                <path d="M8 7h8M8 11h5M8 15h3" />
            </svg>
            Kelola Device
        </a>
    </div>

    <!-- ============================================================
     VIEW 1 — MONITOR ONLINE (default)
     ============================================================ -->
    <div id="view-monitor" <?= $view !== 'monitor' ? 'style="display:none"' : '' ?>>

        <div id="loading-overlay" class="loading-overlay" style="display:none;">
            <div class="spinner"></div>
        </div>

        <div class="batch-toolbar">
            <button id="btnSetAll" class="btn-batch setting">⚙️ Set All</button>
            <button id="btnSyncAll" class="btn-batch sync">🔄 Sync All</button>
            <button id="btnUploadAll" class="btn-batch upload">⬆️ Upload All</button>
            <button id="btnRebootAll" class="btn-batch reboot">♻️ Reboot All</button>
        </div>

        <div id="progressContainer">
            <div id="progressBar" style="width:0%"></div>
        </div>
        <div id="report"></div>

        <div class="device-container">
            <?php include "device_card.php"; ?>
        </div>

    </div><!-- /view-monitor -->

    <!-- ============================================================
     VIEW 2 — KELOLA REG DEVICE (CRUD)
     ============================================================ -->
    <div id="view-regdevice" <?= $view !== 'regdevice' ? 'style="display:none"' : '' ?>>

        <div class="reg-header">
            <div>
                <h2>Daftar Device Terdaftar</h2>
                <div class="sub">Tabel <code>reg_device</code> — tambah, edit, hapus device</div>
            </div>
            <button class="btn-add" onclick="openAddModal()">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5">
                    <path d="M12 5v14M5 12h14" />
                </svg>
                Tambah Device
            </button>
        </div>

        <!-- Info strip -->
        <div class="info-strip">
            <span>Total terdaftar: <strong><?= count($reg_devices) ?> device</strong></span>
            <span style="color:var(--text-sub); font-size:12px;">
                Klik ✏️ untuk edit, 🗑️ untuk hapus
            </span>
        </div>

        <!-- Tabel -->
        <div class="reg-table-wrap">
            <?php if (empty($reg_devices)): ?>
                <div class="empty-state">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.5">
                        <rect x="4" y="2" width="16" height="20" rx="2" />
                        <path d="M8 7h8M8 11h5M8 15h3" />
                    </svg>
                    <p>Belum ada device yang terdaftar.<br>Klik <strong>Tambah Device</strong> untuk memulai.</p>
                </div>
            <?php else: ?>
                <table class="reg-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Chip ID</th>
                            <th>No Device</th>
                            <th>Kode</th>
                            <th>Info Device</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reg_devices as $i => $rd): ?>
                            <tr>
                                <td style="color:var(--text-sub); font-size:12px;"><?= $i + 1 ?></td>
                                <td><span class="chip-id-code"><?= htmlspecialchars($rd['chip_id']) ?></span></td>
                                <td><span class="no-device-code"><?= htmlspecialchars($rd['no_device']) ?></span></td>
                                <td><?= htmlspecialchars($rd['kode']) ?></td>
                                <td style="color:var(--text-sub); max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"
                                    title="<?= htmlspecialchars($rd['info_device']) ?>">
                                    <?= htmlspecialchars($rd['info_device']) ?: '<em style="color:var(--text-sub)">—</em>' ?>
                                </td>
                                <td>
                                    <?php
                                    $st  = strtolower($rd['status']);
                                    $cls = $st === 'aktif' ? 'badge-aktif' : ($st === 'nonaktif' ? 'badge-nonaktif' : 'badge-lain');
                                    ?>
                                    <span class="badge <?= $cls ?>"><?= htmlspecialchars($rd['status']) ?></span>
                                </td>
                                <td>
                                    <div class="tbl-actions">
                                        <button class="btn-tbl edit"
                                            onclick='openEditModal(<?= htmlspecialchars(json_encode($rd)) ?>)'>
                                            ✏️ Edit
                                        </button>
                                        <button class="btn-tbl del"
                                            onclick='openDelModal(<?= (int)$rd['id'] ?>, "<?= htmlspecialchars($rd['no_device']) ?>")'>
                                            🗑️ Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </div><!-- /view-regdevice -->

    <!-- ============================================================
     MODAL — TAMBAH DEVICE
     ============================================================ -->
    <div class="modal-overlay" id="modalAdd">
        <div class="modal-box">
            <div class="modal-head">
                <h3>➕ Tambah Device Baru</h3>
                <button class="modal-close" onclick="closeModal('modalAdd')">✕</button>
            </div>
            <form method="POST" action="?view=regdevice">
                <input type="hidden" name="cmd" value="add_reg">
                <input type="hidden" name="key" value="<?= md5('siap$bos') ?>">
                <div class="modal-body">
                    <div class="field-row">
                        <div class="field-group">
                            <label>No Device <span class="req">*</span></label>
                            <input type="text" name="no_device" placeholder="cth: DEV-001"
                                required maxlength="50" autocomplete="off">
                        </div>
                        <div class="field-group">
                            <label>Kode <span class="req">*</span></label>
                            <input type="text" name="kode" placeholder="cth: SIAP-A1"
                                required maxlength="50" autocomplete="off">
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field-group">
                            <label>Chip ID</label>
                            <input type="text" name="chip_id" placeholder="cth: 8A3F0C atau kosongkan"
                                maxlength="32" autocomplete="off"
                                oninput="this.value=this.value.toUpperCase()">
                        </div>
                        <div class="field-group">
                            <label>Status</label>
                            <select name="status">
                                <option value="aktif">aktif</option>
                                <option value="nonaktif">nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="field-group">
                        <label>Info Device</label>
                        <input type="text" name="info_device"
                            placeholder="cth: Ruang Lab, Gedung A..."
                            maxlength="255" autocomplete="off">
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn-cancel" onclick="closeModal('modalAdd')">Batal</button>
                    <button type="submit" class="btn-submit">Simpan Device</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================
     MODAL — EDIT DEVICE
     ============================================================ -->
    <div class="modal-overlay" id="modalEdit">
        <div class="modal-box">
            <div class="modal-head">
                <h3>✏️ Edit Device</h3>
                <button class="modal-close" onclick="closeModal('modalEdit')">✕</button>
            </div>
            <form method="POST" action="?view=regdevice">
                <input type="hidden" name="cmd" value="edit_reg">
                <input type="hidden" name="key" value="<?= md5('siap$bos') ?>">
                <input type="hidden" name="reg_id" id="edit_reg_id">
                <div class="modal-body">
                    <div class="field-row">
                        <div class="field-group">
                            <label>No Device <span class="req">*</span></label>
                            <input type="text" name="no_device" id="edit_no_device"
                                required maxlength="50" autocomplete="off">
                        </div>
                        <div class="field-group">
                            <label>Kode <span class="req">*</span></label>
                            <input type="text" name="kode" id="edit_kode"
                                required maxlength="50" autocomplete="off">
                        </div>
                    </div>
                    <div class="field-row">
                        <div class="field-group">
                            <label>Chip ID</label>
                            <input type="text" name="chip_id" id="edit_chip_id"
                                maxlength="32" autocomplete="off"
                                oninput="this.value=this.value.toUpperCase()">
                        </div>
                        <div class="field-group">
                            <label>Status</label>
                            <select name="status" id="edit_status">
                                <option value="aktif">aktif</option>
                                <option value="nonaktif">nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="field-group">
                        <label>Info Device</label>
                        <input type="text" name="info_device" id="edit_info_device"
                            maxlength="255" autocomplete="off">
                    </div>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn-cancel" onclick="closeModal('modalEdit')">Batal</button>
                    <button type="submit" class="btn-submit">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================
     MODAL — KONFIRMASI HAPUS
     ============================================================ -->
    <div class="modal-overlay confirm" id="modalDel">
        <div class="modal-box">
            <div class="modal-head">
                <h3>Konfirmasi Hapus</h3>
                <button class="modal-close" onclick="closeModal('modalDel')">✕</button>
            </div>
            <form method="POST" action="?view=regdevice">
                <input type="hidden" name="cmd" value="del_reg">
                <input type="hidden" name="key" value="<?= md5('siap$bos') ?>">
                <input type="hidden" name="reg_id" id="del_reg_id">
                <div class="modal-body">
                    <div class="confirm-icon">🗑️</div>
                    <p class="confirm-msg">Hapus device <strong id="del_no_device"></strong>?</p>
                    <p class="confirm-sub">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn-cancel" onclick="closeModal('modalDel')">Batal</button>
                    <button type="submit" class="btn-del-confirm">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================
     JS
     ============================================================ -->
    <script src="js/dashdevice_3.js"></script>
    <script>
        // ---- Modal helpers ----
        function openModal(id) {
            document.getElementById(id).classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('open');
            document.body.style.overflow = '';
        }
        // Tutup jika klik di luar modal-box
        document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) closeModal(overlay.id);
            });
        });

        // ---- Add ----
        function openAddModal() {
            openModal('modalAdd');
        }

        // ---- Edit ----
        function openEditModal(rd) {
            document.getElementById('edit_reg_id').value = rd.id;
            document.getElementById('edit_no_device').value = rd.no_device;
            document.getElementById('edit_kode').value = rd.kode;
            document.getElementById('edit_chip_id').value = rd.chip_id;
            document.getElementById('edit_info_device').value = rd.info_device || '';
            var sel = document.getElementById('edit_status');
            sel.value = rd.status;
            openModal('modalEdit');
        }

        // ---- Delete ----
        function openDelModal(id, noDevice) {
            document.getElementById('del_reg_id').value = id;
            document.getElementById('del_no_device').textContent = noDevice;
            openModal('modalDel');
        }

        // ---- Batch commands (view monitor) ----
        async function sendBatchCommand(type) {
            var cards = document.querySelectorAll('.device-card');
            var total = cards.length;
            var pBar = document.getElementById('progressBar');
            var pCont = document.getElementById('progressContainer');
            var report = document.getElementById('report');
            if (!total) {
                report.innerHTML = '⚠️ Tidak ada device online.';
                return;
            }

            pCont.style.display = 'block';
            report.innerHTML = 'Mengirim perintah ke semua perangkat...';
            pBar.style.width = '0%';

            var successCount = 0,
                failCount = 0;
            for (var i = 0; i < total; i++) {
                var card = cards[i];
                var elem = card.querySelector('.device-id-get');
                var deviceId = elem ? elem.textContent.trim() : null;
                if (!deviceId) continue;

                var url = 'http://172.16.80.123/app/mqtt/send_setting.php?device_id=' +
                    encodeURIComponent(deviceId) +
                    '&token=e807f1fcf82d132f9bb018ca6738a19f';

                if (type === 'set') url += '&setSetting=1';
                if (type === 'sync') url += '&sync=1';
                if (type === 'upload') url += '&upload=1';
                if (type === 'reboot') url += '&reboot=1';

                try {
                    var resp = await fetch(url);
                    if (resp.ok) successCount++;
                    else failCount++;
                } catch (e) {
                    failCount++;
                }

                pBar.style.width = (((i + 1) / total) * 100) + '%';
                report.innerHTML = '⏳ Memproses ' + (i + 1) + ' / ' + total + ' ...';
                await new Promise(function(r) {
                    setTimeout(r, 500);
                });
            }

            pBar.style.width = '100%';
            report.innerHTML = '✅ Selesai: ' + successCount + ' berhasil' +
                (failCount ? ', ⚠️ ' + failCount + ' gagal' : '') +
                ' dari ' + total + ' perangkat.';
        }

        document.getElementById('btnSetAll').addEventListener('click', function() {
            sendBatchCommand('set');
        });
        document.getElementById('btnSyncAll').addEventListener('click', function() {
            sendBatchCommand('sync');
        });
        document.getElementById('btnUploadAll').addEventListener('click', function() {
            sendBatchCommand('upload');
        });
        document.getElementById('btnRebootAll').addEventListener('click', function() {
            sendBatchCommand('reboot');
        });
    </script>

</body>
<?php include('views/footer.php'); ?>
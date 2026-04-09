   <?php
    include('../config/konesi.php');

    // Ambil semua device yang memiliki data
    // Hitung jumlah online dan offline
    $result = mysqli_query($konek, "SELECT COUNT(*) AS total, online FROM devices GROUP BY online");
    $onlineCount = 0;
    $offlineCount = 0;
    $totalDevice = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['online'] == 1) {
            $onlineCount = $row['total'];
        } else {
            $offlineCount = $row['total'];
        }
    }

    $totalDevice = $offlineCount + $onlineCount;

    // Ambil semua data device + info_device (kalau ada di reg_device)
    $query = "
        SELECT 
            d.*, 
            r.info_device 
        FROM devices d
        LEFT JOIN reg_device r 
            ON r.no_device = d.device_id
        WHERE d.last_status IS NOT NULL
        ORDER BY d.device_id ASC
    ";
    $result = mysqli_query($konek, $query);
    ?>

   <?php
    while ($row = mysqli_fetch_assoc($result)) {
        $device_id = htmlspecialchars($row['device_id']);

        // Ambil info_device dari reg_device jika ada, kalau tidak pakai info dari devices
        if (!empty($row['info_device'])) {
            $info = htmlspecialchars($row['info_device'], ENT_QUOTES, 'UTF-8');
        } elseif (!empty($row['info'])) {
            $info = htmlspecialchars($row['info'], ENT_QUOTES, 'UTF-8');
        } else {
            $info = "";
        }

        $updated_at = htmlspecialchars($row['updated_at']);
        $statusData = isset($row['last_status']) ? json_decode($row['last_status'], true) : "";
        $settingData = isset($row['last_setting']) ? json_decode($row['last_setting'], true) : "";
        $commandData = isset($row['last_command']) ? json_decode($row['last_command'], true) : "";

        $ssid = isset($statusData['ssid']) ? $statusData['ssid'] : "-";
        $serial = isset($statusData['serial']) ? intval($statusData['serial']) : null;
        $latency = isset($statusData['latency']) ? intval($statusData['latency']) : "-";

        $isOnline = isset($row['online']) && $row['online'] === '1';
        $ramPercent = isset($statusData['ram']) ? intval($statusData['ram']) : 0;
        $ramPercent = min(max($ramPercent, 0), 100);
        $rssi = isset($statusData['rssi']) ? intval($statusData['rssi']) : -100;

        // Konversi RSSI (-100 s.d. -40) ke persen (0–100)
        $rssiPercent = round(((max(-100, min(-40, $rssi)) + 100) / 60) * 100, 1);
    ?>

       <div class="device-card">
           <div class="device-header">
               <div class="tittle-device">
                   <div class="device-id">
                       <div style="display: flex; flex-direction: column;">
                           <div class="device-row">
                               <div class="device-id-get">
                                   <?= $device_id ?>
                               </div>
                               <span class="spanwarning"><?= $row['fw_version'] ?? "" ?></span>
                           </div>
                           <span class="spaninfo"><?= $info ?></span>
                       </div>
                   </div>

                   <div class="timeoffline">
                       <?php
                        // Tentukan apakah online atau offline
                        if (!$isOnline) {
                            $since_label = "Last Offline: ";
                            $timestamp = $row['offline_since'] ?? '';
                            $css_since = '<div class="offline_since">';
                        } else {
                            $since_label = "Last Online: ";
                            $timestamp = $row['online_since'] ?? '';
                            $css_since = '<div class="online_since">';
                        }

                        // Jika ada nilai timestamp
                        if (!empty($timestamp)) {
                            // Ambil tanggal hari ini dan tanggal dari data
                            $today = date('Y-m-d');
                            $date_part = date('Y-m-d', strtotime($timestamp));
                            $time_part = date('H:i:s', strtotime($timestamp));

                            // Kalau tanggalnya hari ini, hanya tampilkan jam
                            if ($date_part === $today) {
                                $display_time = $time_part;
                            } else {
                                // Kalau beda hari, tampilkan tanggal dan jam
                                $display_time = date('d M Y H:i:s', strtotime($timestamp));
                            }

                            echo $css_since . $since_label . $display_time . '</div>';
                        }
                        ?>
                   </div>
               </div>
               <div style="display: flex; flex-direction: column; justify-content: center; align-content: center;">
                   <div class="status-dot <?= $isOnline ? 'online' : 'offline' ?>"></div>
                   <div class="font-6" style="font-family: 'Fira Code', 'JetBrains Mono', 'Source Code Pro', monospace; margin-top: 5px;">
                       <?php if (!$isOnline) {
                            echo "Offline";
                        } else {
                            echo "Online";
                        }
                        ?>
                   </div>
               </div>
           </div>

           <div class="ram-label">💾 RAM: <?= $ramPercent ?>%</div>
           <div class="ram-bar" style="--ram-percent: <?= $ramPercent ?>%;"></div>
           <div class="ram-label">🛜 RSSI: <?= $rssiPercent ?>% (<?= $rssi ?> dB)</div>
           <div class='rssi-bar'>
               <div class='rssi-fill' data-rssi='<?= $rssiPercent; ?>'></div>
           </div>

           <div class="detail">
               <div class="" style="margin-top: 5px;">
                   📡 <?= $ssid ?? "" ?>
               </div>

               <?php
                $latency = $latency ?? 0; // pastikan variabel ada
                $emoji = "⚪"; // default

                if ($latency < 30) {
                    $emoji = "🟢"; // sangat baik
                } elseif ($latency < 60) {
                    $emoji = "🟢"; // baik
                } elseif ($latency < 100) {
                    $emoji = "🟡"; // warning / cukup
                } elseif ($latency < 200) {
                    $emoji = "🟠"; // buruk
                } else {
                    $emoji = "🔴"; // sangat buruk
                }
                ?>

               <div class="" style="margin-top: 5px;">
                   ⏳ Ping: <?= $latency ?> mS <?= $emoji ?>
               </div>

               <div class="">
                   🕒 Last Update
                   <?= htmlspecialchars($updated_at ?? '-') ?>
               </div>

           </div>

           <!-- Sembunyikan - start -->
           <div>
               <div class="section-title">
                   🔍 Detail
                   <button id="arrow-<?= $device_id ?>" class="toggle-btn" onclick="toggleDetails('<?= $device_id ?>')">▼</button>
                   <div id="btn-sm-group-<?= $device_id ?>">
                       <?php if (($row['last_setting'] === null && $row['last_command'] === null) || (!$isOnline)) {
                            $disabledButton = "disabled";
                            $hideButton = "hide";
                        } else {
                            $disabledButton = "";
                            $hideButton = "";
                        } ?>

                       <button onclick="sendCommand('../app/mqtt/send_setting.php?device_id=<?= $device_id ?>&setSetting=1&token=e807f1fcf82d132f9bb018ca6738a19f')" class="btn-sm btn-setting <?= $hideButton; ?>" <?= $disabledButton; ?>>⚙️</button>
                       <button onclick="sendCommand('../app/mqtt/send_setting.php?device_id=<?= $device_id ?>&upload=1&token=e807f1fcf82d132f9bb018ca6738a19f')" class="btn-sm btn-upload <?= $hideButton; ?>" <?= $disabledButton; ?>>📤</button>

                       <button onclick="sendCommand('../app/mqtt/send_setting.php?device_id=<?= $device_id ?>&sync=1&token=e807f1fcf82d132f9bb018ca6738a19f')" class="btn-sm btn-sync <?= $hideButton; ?>" <?= $disabledButton; ?>>🔄</button>

                       <?php
                        // Cek kondisi $serial dan tentukan style + atribut tombol
                        $btnStyle = '';
                        $btnDisabled = '';


                        if ($serial == "1") {
                            // Serial aktif → tombol kuning
                            $btnStyle = 'btn-serial-on';
                            $valueSerial = "2";
                        } elseif ($serial == "0") {
                            // Serial nonaktif → tombol hitam dengan teks putih
                            $btnStyle = 'btn-serial-off';
                            $valueSerial = "1";
                        } elseif ($serial === "") {
                            // Serial kosong → tombol disabled
                            $btnDisabled = "disabled";
                            $btnStyle = "btn-serial";
                        }
                        ?>

                       <?php if ($serial !== null) { ?>
                           <button
                               onclick="sendCommand('../app/mqtt/send_setting.php?device_id=<?= $device_id ?>&toggleSerial=<?= $valueSerial; ?>&token=e807f1fcf82d132f9bb018ca6738a19f')"
                               class="btn-sm <?= $btnStyle ?>"
                               <?= $btnDisabled ?>>
                               🔍
                           </button>
                       <?php } ?>

                       <button onclick="sendCommand('../app/mqtt/send_setting.php?device_id=<?= $device_id ?>&reboot=1&token=e807f1fcf82d132f9bb018ca6738a19f')" class="btn-sm btn-reboot">🔁</button>

                   </div>
                   <!-- <button id="delete-device-<?= $device_id ?>" class="btn btn-reboot" style="display: none;">🗑 Delete Device</button> -->
                   <form method="POST" onsubmit="return confirm('Apakah yakin ingin menghapus device ini <?= $device_id ?>?');" style="display: inline;">
                       <input type="hidden" name="delete_device_id" value="<?= $device_id ?>">
                       <input type="hidden" name="key" value="<?= md5("siap\$bos"); ?>">
                       <input type="hidden" name="cmd" value="del">
                       <button type="submit" class="btn btn-reboot" style="display: none;" id="delete-device-<?= $device_id ?>">🗑 Delete Device</button>
                   </form>
               </div>

               <div id="detail-<?= $device_id ?>" class="detail-wrapper">
                   <?php if (!empty($settingData)) : ?>
                       <div class="section-title">⚙️ Last Setting</div>
                       <div class="detail">
                           <b>Status:</b> <?= htmlspecialchars($settingData['status'] ?? '-') ?><br>
                           <b>Mode:</b> <?= htmlspecialchars($settingData['detail']['mode'] ?? '-') ?><br>
                           <b>Masuk:</b> <?= htmlspecialchars($settingData['detail']['waktumasuk'] ?? '-') ?><br>
                           <b>🕔 </b> <?= htmlspecialchars($settingData['detail']['wa'] ?? '-') ?><br>
                           <b>🔒 </b> <?= htmlspecialchars($settingData['detail']['wta'] ?? '-') ?><br>
                           <b>Pulang:</b> <?= htmlspecialchars($settingData['detail']['waktupulang'] ?? '-') ?><br>
                           <b>🕔 </b> <?= htmlspecialchars($settingData['detail']['wp'] ?? '-') ?><br>
                           <b>🔒 </b> <?= htmlspecialchars($settingData['detail']['wtp'] ?? '-') ?><br>
                           <b>Versi:</b> <?= htmlspecialchars($settingData['version'] ?? '-') ?><br>
                           <b>🗓️ Timestamp:</b> <?= htmlspecialchars($settingData['timestamp'] ?? '-') ?>
                       </div>
                   <?php endif; ?>

                   <?php if (!empty($commandData)) : ?>
                       <div class="section-title">💻 Last Command</div>
                       <div class="detail">
                           <b>Status:</b> <?= htmlspecialchars($commandData['status'] ?? '-') ?><br>
                           <b>💬 Detail:</b> <?= htmlspecialchars($commandData['detail'] ?? '-') ?><br>
                           <b>🗓️ Timestamp:</b> <?= htmlspecialchars($commandData['timestamp'] ?? '-') ?>
                       </div>
                   <?php endif; ?>


                   <div class="viewlog">
                       <a href="viewlog.php?device_id=<?= urlencode($device_id) ?>">📜 View Log</a>
                   </div>

                   <?php if (($row['last_setting'] === null && $row['last_command'] === null) || (!$isOnline)) {
                        $disabledButton = "disabled";
                    } else {
                        $disabledButton = "";
                    } ?>

                   <div class="btn-group">
                       <button onclick="sendCommand('../app/mqtt/send_setting.php?device_id=<?= $device_id ?>&setSetting=1&token=e807f1fcf82d132f9bb018ca6738a19f')" class="btn btn-setting" <?= $disabledButton; ?>>⚙️ Set</button>
                       <button onclick="sendCommand('../app/mqtt/send_setting.php?device_id=<?= $device_id ?>&upload=1&token=e807f1fcf82d132f9bb018ca6738a19f')" class="btn btn-upload" <?= $disabledButton; ?>>📤 Upload</button>
                   </div>
                   <div class="btn-group">
                       <button onclick="sendCommand('../app/mqtt/send_setting.php?device_id=<?= $device_id ?>&sync=1&token=e807f1fcf82d132f9bb018ca6738a19f')" class="btn btn-sync" <?= $disabledButton; ?>>🔄 Sync</button>

                       <?php
                        // Cek kondisi $serial dan tentukan style + atribut tombol
                        $btnStyle = '';
                        $btnDisabled = '';


                        if ($serial == "1") {
                            // Serial aktif → tombol kuning
                            $btnStyle = 'btn-serial-on';
                            $valueSerial = "2";
                        } elseif ($serial == "0") {
                            // Serial nonaktif → tombol hitam dengan teks putih
                            $btnStyle = 'btn-serial-off';
                            $valueSerial = "1";
                        } elseif ($serial === "") {
                            // Serial kosong → tombol disabled
                            $btnDisabled = "disabled";
                            $btnStyle = "btn-serial";
                        }
                        ?>

                       <?php if ($serial !== null) { ?>
                           <button
                               onclick="sendCommand('../app/mqtt/send_setting.php?device_id=<?= $device_id ?>&toggleSerial=<?= $valueSerial; ?>&token=e807f1fcf82d132f9bb018ca6738a19f')"
                               class="btn <?= $btnStyle ?>"
                               <?= $btnDisabled ?>>
                               🔍 Serial&nbsp;<?= $serial ?>
                           </button>
                       <?php } ?>

                       <button onclick="sendCommand('../app/mqtt/send_setting.php?device_id=<?= $device_id ?>&reboot=1&token=e807f1fcf82d132f9bb018ca6738a19f')" class="btn btn-reboot">🔁 Reboot</button>
                   </div>
               </div>
           </div>

           <!-- Sembunyikan - end -->
       </div>
   <?php
    }

    mysqli_close($konek);
    ?>

   <div class="refresh-info">
       🔄 Terakhir refresh: <span id="last-refresh">--:--:--</span> |
       🟢 Online: <b><?= $onlineCount ?></b> |
       🔴 Offline: <b><?= $offlineCount ?></b> |
       📊 Device: <b><?= $totalDevice ?></b>
   </div>
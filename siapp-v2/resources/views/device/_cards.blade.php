@foreach($devices as $device)
@php
    $status   = json_decode($device->last_status,  true) ?? [];
    $setting  = json_decode($device->last_setting, true) ?? [];
    $command  = json_decode($device->last_command, true) ?? [];
    $reg      = $regDevices[$device->device_id] ?? null;
    $isOnline = $device->online == 1;

    $ram      = (int)($status['ram']     ?? 0);
    $ram      = min(max($ram, 0), 100);
    $rssi     = (int)($status['rssi']    ?? -100);
    $rssiPct  = round(((max(-100, min(-40, $rssi)) + 100) / 60) * 100, 1);
    $latency  = (int)($status['latency'] ?? 0);
    $ssid     = $status['ssid']          ?? '-';
    $serial   = isset($status['serial']) ? (int)$status['serial'] : null;

    $infoJson = $device->info ? json_decode($device->info, true) : [];
    $info = $reg->info_device ?? $infoJson['label'] ?? $infoJson['ssid'] ?? '-';

    $today     = date('Y-m-d');
    $sinceRaw  = $isOnline ? ($device->online_since ?? '') : ($device->offline_since ?? '');
    $sinceDate = $sinceRaw ? date('Y-m-d', strtotime($sinceRaw)) : '';
    $sinceTime = $sinceRaw ? ($sinceDate === $today ? date('H:i:s', strtotime($sinceRaw)) : date('d M Y H:i', strtotime($sinceRaw))) : '';

    $latEmoji = $latency < 30 ? '🟢' : ($latency < 60 ? '🟢' : ($latency < 100 ? '🟡' : ($latency < 200 ? '🟠' : '🔴')));

    $bufferNow   = isset($status['count']) ? (int)$status['count'] : null;
    $bufferTotal = (int)($bufferDaily[$device->device_id] ?? 0) + ($bufferNow ?? 0);
    // bufferTotal = sudah diupload hari ini + yang masih di device sekarang

    $hasData    = !empty($setting) || !empty($command);
    $canControl = $hasData && $isOnline;
    $dis        = $canControl ? '' : 'disabled';
@endphp
<div class="device-card {{ $isOnline ? 'is-online' : 'is-offline' }}" data-device-id="{{ $device->device_id }}">

    {{-- Header --}}
    <div class="dc-header">
        <div class="dc-title">
            <div class="dc-id">
                <a href="{{ route('device.detail', $device->device_id) }}" style="color:inherit; text-decoration:none;">
                    {{ $device->device_id }}
                </a>
                @if($device->fw_version)
                    <span class="dc-fw">{{ $device->fw_version }}</span>
                @endif
            </div>
            <span class="dc-info" style="display:inline-flex; align-items:center; gap:4px;">
                {{ $info && $info !== '-' ? $info : '-' }}
                <span onclick="editLabel('{{ $device->device_id }}', '{{ addslashes($info) }}')"
                    title="Edit label" style="cursor:pointer; font-size:10px; opacity:0.6;">✏️</span>
            </span>
            @if($sinceTime)
                <div class="{{ $isOnline ? 'dc-since-online' : 'dc-since-offline' }}">
                    {{ $isOnline ? 'Online' : 'Last Offline' }}: {{ $sinceTime }}
                </div>
            @endif
        </div>
        <div style="text-align:center;">
            <div class="dc-dot {{ $isOnline ? 'online' : 'offline' }}"></div>
            <div class="dc-status-label">{{ $isOnline ? 'Online' : 'Offline' }}</div>
        </div>
        <button title="Hapus"
            style="position:absolute;top:2px;right:4px;width:14px;height:14px;border-radius:0;border:none;background:transparent;color:#bbb;font-size:11px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;z-index:10;transition:color 0.2s;"
            onmouseover="this.style.color='#c00';"
            onmouseout="this.style.color='#bbb';"
            onclick="deleteDevice('{{ $device->device_id }}')">✕</button>
    </div>

    {{-- RAM Bar --}}
    <div class="bar-label">💾 RAM: {{ $ram }}%</div>
    <div class="ram-bar" style="--ram-pct: {{ $ram }}%;"></div>

    {{-- RSSI Bar --}}
    <div class="bar-label">🛜 RSSI: {{ $rssiPct }}% ({{ $rssi }} dB)</div>
    <div class="rssi-bar">
        <div class="rssi-fill" data-pct="{{ $rssiPct }}"></div>
    </div>

    {{-- Buffer RAM --}}
    @php
        $bufferCount = isset($status['count']) ? (int)$status['count'] : null;
        if ($bufferCount === null) {
            $bufferClass = 'dc-buf-unknown';
            $bufferLabel = '🗂️ Buffer: -';
        } elseif ($bufferCount === 0) {
            $bufferClass = 'dc-buf-empty';
            $bufferLabel = '🗂️ Buffer: kosong';
        } elseif ($bufferCount <= 50) {
            $bufferClass = 'dc-buf-warn';
            $bufferLabel = '🗂️ Buffer: ' . $bufferCount . ' data';
        } else {
            $bufferClass = 'dc-buf-danger';
            $bufferLabel = '🗂️ Buffer: ' . $bufferCount . ' data ⚠️';
        }
    @endphp
    <div class="bar-label {{ $bufferClass }}">{{ $bufferLabel }}</div>

    {{-- Sparkline --}}
    <div style="margin: 6px 0;">
        <canvas id="spark-{{ $device->device_id }}" height="70"
            style="width:100%; border-radius:6px; background:transparent;"></canvas>
    </div>

    {{-- Detail Info --}}
    <div class="dc-detail">
        <div>📡 {{ $ssid }}</div>
        <div>⏳ Ping: {{ $latency }} mS {{ $latEmoji }}</div>
        <div>🕒 {{ $device->updated_at }}</div>
    </div>

    {{-- Section: Detail toggle + small buttons --}}
    <div class="dc-section-title">
        🔍 Detail
        <button class="bxr-toggle" id="dc-arrow-{{ $device->device_id }}"
            onclick="toggleDetail('{{ $device->device_id }}')">▼</button>

        {{-- Small round buttons (visible when collapsed) --}}
        <div id="dc-smbtns-{{ $device->device_id }}" style="display:flex;gap:4px;margin-left:auto;">
            <button class="btn-xs-round bxr-set"    title="Set"    {{ $dis }}
                onclick="handleCmd(this,'{{ $device->device_id }}','setSetting')">⚙️</button>
            <button class="btn-xs-round bxr-upload" title="Upload" {{ $dis }}
                onclick="handleCmd(this,'{{ $device->device_id }}','upload')">📤</button>
            <button class="btn-xs-round bxr-sync"   title="Sync"   {{ $dis }}
                onclick="handleCmd(this,'{{ $device->device_id }}','sync')">🔄</button>
            @if($serial !== null)
                @php
                    $serialClass = $serial == 1 ? 'bxr-serial-on' : 'bxr-serial-off';
                    $serialVal   = $serial == 1 ? 2 : 1;
                @endphp
                <button class="btn-xs-round {{ $serialClass }}" title="Serial {{ $serial }}" {{ $dis }}
                    onclick="handleCmd(this,'{{ $device->device_id }}','toggleSerial',{{ $serialVal }})">🔍</button>
            @endif
            <button class="btn-xs-round bxr-reboot" title="Reboot"
                onclick="handleCmd(this,'{{ $device->device_id }}','reboot')">🔁</button>
        </div>
    </div>

    {{-- Collapsible detail --}}
    <div class="dc-collapse" id="dc-collapse-{{ $device->device_id }}">

        @if(!empty($setting))
        <div class="dc-last-setting">
            <strong>⚙️ Last Setting</strong><br>
            Mode: {{ $setting['detail']['mode'] ?? '-' }} |
            Masuk: {{ $setting['detail']['waktumasuk'] ?? '-' }} |
            Pulang: {{ $setting['detail']['waktupulang'] ?? '-' }}<br>
            Versi: {{ $setting['version'] ?? '-' }} |
            {{ $setting['timestamp'] ?? '' }}
        </div>
        @endif

        @if(!empty($command))
        <div class="dc-last-setting">
            <strong>💻 Last Command</strong><br>
            {{ $command['status'] ?? '-' }} — {{ $command['timestamp'] ?? '' }}
        </div>
        @endif

        <div class="dc-viewlog">
            <a href="{{ route('device.log', $device->device_id) }}">
                📜 View Log ({{ $device->device_id }})
            </a>
        </div>

        {{-- Full buttons --}}
        <div class="dc-btn-group">
            <button class="dc-btn btn-set"    {{ $dis }}
                onclick="handleCmd(this,'{{ $device->device_id }}','setSetting')">⚙️ Set</button>
            <button class="dc-btn btn-upload" {{ $dis }}
                onclick="handleCmd(this,'{{ $device->device_id }}','upload')">📤 Upload</button>
        </div>
        <div class="dc-btn-group">
            <button class="dc-btn btn-sync"   {{ $dis }}
                onclick="handleCmd(this,'{{ $device->device_id }}','sync')">🔄 Sync</button>
            @if($serial !== null)
                <button class="dc-btn {{ $serial==1 ? 'btn-serial-on' : 'btn-serial-off' }}" {{ $dis }}
                    onclick="handleCmd(this,'{{ $device->device_id }}','toggleSerial',{{ $serialVal ?? 1 }})">
                    🔍 Serial {{ $serial }}
                </button>
            @endif
            <button class="dc-btn btn-reboot"
                onclick="handleCmd(this,'{{ $device->device_id }}','reboot')">🔁 Reboot</button>
        </div>
</div>
</div>

{{-- Compact Card --}}
@php
    $bufNow   = $bufferNow;
    $bufTotal = $bufferTotal;
    $bufLabel = $bufNow === null ? null : $bufNow . '/' . $bufTotal;
    $bufClass = $bufNow === null ? '' : ($bufNow === 0 ? 'buf-ok' : ($bufNow <= 50 ? 'buf-warn' : 'buf-danger'));
    $updatedAt = $device->updated_at ? date('H:i', strtotime($device->updated_at)) : '--:--';
@endphp
<div class="device-card-compact {{ $isOnline ? 'is-online' : 'is-offline' }}"
    data-device-id="{{ $device->device_id }}"
    onclick="window.location='{{ route('device.detail', $device->device_id) }}'">

    {{-- Baris 1: ID + Dot --}}
    <div class="dcc-row1">
        <span class="dcc-id" title="{{ $device->device_id }}">{{ $device->device_id }}</span>
        <div class="dcc-dot {{ $isOnline ? 'online' : 'offline' }}"></div>
    </div>
    {{-- Baris 2: icon wifi + persen + bar --}}
    <div class="dcc-rssi-row">
        <span class="dcc-badge">🛜 {{ $rssiPct }}%</span>
        <div class="dcc-rssi-wrap" style="flex:1;">
            <div class="dcc-rssi-fill" data-pct="{{ $rssiPct }}"
                style="width:{{ $rssiPct }}%;"></div>
        </div>
    </div>
    {{-- Baris 3: RAM + Buffer + Time --}}
    <div class="dcc-row2">
        <span class="dcc-badge">💾 {{ $ram }}%</span>
        @if($bufLabel !== null)
        <span class="dcc-badge {{ $bufClass }}">🗂️ {{ $bufLabel }}</span>
        @endif
        <span class="dcc-time">{{ $updatedAt }}</span>
    </div>
    {{-- Baris 4: SSID + FW Version --}}
    <div class="dcc-row3">
        <span class="dcc-ssid">{{ $ssid }}</span>
        @if($device->fw_version)
            <span class="badge badge-warning" style="font-size:8px; padding:1px 4px;">{{ $device->fw_version }}</span>
        @endif
    </div>
    {{-- Baris 5: Info Device --}}
    @if($info && $info !== '-')
    <div style="font-size:9px; color:#666; margin-top:2px;">{{ $info }}</div>
    @endif
</div>
@endforeach
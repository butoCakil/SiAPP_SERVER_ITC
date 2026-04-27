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

    $info = $reg->info_device ?? ($device->info ? json_decode($device->info, true)['ssid'] ?? '-' : '-');

    $today     = date('Y-m-d');
    $sinceRaw  = $isOnline ? ($device->online_since ?? '') : ($device->offline_since ?? '');
    $sinceDate = $sinceRaw ? date('Y-m-d', strtotime($sinceRaw)) : '';
    $sinceTime = $sinceRaw ? ($sinceDate === $today ? date('H:i:s', strtotime($sinceRaw)) : date('d M Y H:i', strtotime($sinceRaw))) : '';

    $latEmoji = $latency < 30 ? '🟢' : ($latency < 60 ? '🟢' : ($latency < 100 ? '🟡' : ($latency < 200 ? '🟠' : '🔴')));

    $hasData    = !empty($setting) || !empty($command);
    $canControl = $hasData && $isOnline;
    $dis        = $canControl ? '' : 'disabled';
@endphp
<div class="device-card {{ $isOnline ? 'is-online' : 'is-offline' }}" data-device-id="{{ $device->device_id }}">

    {{-- Header --}}
    <div class="dc-header">
        <div class="dc-title">
            <div class="dc-id">
                {{ $device->device_id }}
                @if($device->fw_version)
                    <span class="dc-fw">{{ $device->fw_version }}</span>
                @endif
            </div>
            @if($info && $info !== '-')
                <span class="dc-info">{{ $info }}</span>
            @endif
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
    </div>

    {{-- RAM Bar --}}
    <div class="bar-label">💾 RAM: {{ $ram }}%</div>
    <div class="ram-bar" style="--ram-pct: {{ $ram }}%;"></div>

    {{-- RSSI Bar --}}
    <div class="bar-label">🛜 RSSI: {{ $rssiPct }}% ({{ $rssi }} dB)</div>
    <div class="rssi-bar">
        <div class="rssi-fill" data-pct="{{ $rssiPct }}"></div>
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

        {{-- Delete button (visible when expanded) --}}
        <button id="dc-del-{{ $device->device_id }}"
            class="btn-xs-round bxr-reboot" title="Hapus"
            style="display:none; margin-left:auto;"
            onclick="if(confirm('Hapus device {{ $device->device_id }}?')) deleteDevice('{{ $device->device_id }}')">🗑</button>
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
            <a href="#">📜 View Log ({{ $device->device_id }})</a>
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
@endforeach
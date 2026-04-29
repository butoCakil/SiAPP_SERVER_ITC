<?php

return [
    'presensi_url' => env('TIMID_PRESENSI_URL', ''),
    'sholat_url'   => env('TIMID_SHOLAT_URL', ''),
    'api_key'      => env('TIMID_API_KEY', ''),
    'interval'     => (int) env('PUSH_INTERVAL', 5),
];
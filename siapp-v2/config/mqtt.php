<?php

return [
    "host"     => env("MQTT_HOST", "localhost"),
    "port"     => (int) env("MQTT_PORT", 1883),
    "username" => env("MQTT_USERNAME", "ben"),
    "password" => env("MQTT_PASSWORD", "1234"),
];

<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MQTT Broker Configuration
    |--------------------------------------------------------------------------
    |
    | File konfigurasi ini memastikan nilai MQTT terbaca dengan aman saat
    | `php artisan config:cache` dijalankan di production / Docker.
    |
    */

    'host' => env('MQTT_HOST', '76.13.21.230'),
    'port' => (int) env('MQTT_PORT', 1883),
    'username' => env('MQTT_USERNAME', 'iot'),
    'password' => env('MQTT_PASSWORD', 'smartgh'),
    'client_id' => env('MQTT_CLIENT_ID', null),

    'topic_pub' => env('MQTT_TOPIC_PUB', '/smartgh01/pub'),
    'topic_sub' => env('MQTT_TOPIC_SUB', '/smartgh01/sub'),
];

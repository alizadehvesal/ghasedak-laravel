<?php

return [
    'api_key' => env('GHASEDAK_API_KEY'),
    'base_url' => env('GHASEDAK_BASE_URL', 'http://api.ghasedaksms.com/v2'),
    'timeout' => (int) env('GHASEDAK_TIMEOUT', 10),
    'connect_timeout' => (int) env('GHASEDAK_CONNECT_TIMEOUT', 5),

    /*
    |--------------------------------------------------------------------------
    | Templates
    |--------------------------------------------------------------------------
    |
    | Keep your Ghasedak panel template names here. A single application can
    | have as many logical template keys as needed.
    |
    | Example:
    | 'verification' => 'login_code',
    | 'invoice'      => 'invoice_code',
    |
    */
    'templates' => [
        // 'verification' => 'your-template-name',
    ],
];

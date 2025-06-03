<?php

return [
    'iyzico' => [
        'api_key' => env('IYZICO_API_KEY', ''),
        'secret_key' => env('IYZICO_SECRET_KEY', ''),
        'base_url' => env('IYZICO_BASE_URL', 'https://sandbox-api.iyzipay.com'),
        'callback_url' => env('IYZICO_CALLBACK_URL', 'https://everesiyedefteri.com.tr/customer/checkout/iyzico-callback'),
        'environment' => env('IYZICO_ENVIRONMENT', 'sandbox'),
        'log_level' => env('IYZICO_LOG_LEVEL', 'debug'),
    ],
];
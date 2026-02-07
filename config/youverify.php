<?php

return [
    /*
    |--------------------------------------------------------------------------
    | YouVerify API Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for YouVerify API. Use sandbox URL for testing
    | and production URL for live verification.
    |
    */
    'base_url' => env('YOUVERIFY_BASE_URL', 'https://api.youverify.co'),

    /*
    |--------------------------------------------------------------------------
    | API Credentials
    |--------------------------------------------------------------------------
    |
    | Your YouVerify API credentials. The secret_key is used as the
    | authentication token in request headers.
    |
    */
    'api_key' => env('YOUVERIFY_API_KEY'),
    'secret_key' => env('YOUVERIFY_SECRET_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout in seconds for API requests to YouVerify.
    |
    */
    'timeout' => env('YOUVERIFY_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | The environment mode for YouVerify integration.
    | Options: 'sandbox', 'production'
    |
    */
    'environment' => env('YOUVERIFY_ENVIRONMENT', 'sandbox'),
];

<?php declare(strict_types=1);

return [
    'api_url' => env('SMS_API_URL'),
    'oauth' => [
        'url' => env('SMS_OAUTH_URL', env('SMS_API_URL').'/oauth/token'),
        'client_id' => env('SMS_OAUTH_CLIENT_ID'),
        'client_secret' => env('SMS_OAUTH_CLIENT_SECRET'),
    ],
];


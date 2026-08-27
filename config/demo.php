<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Public demo mode
    |--------------------------------------------------------------------------
    |
    | Demo mode is opt-in. It is intended for a disposable public showcase,
    | never for a real customer installation. Keep the demo account separate
    | from any operational administrator account.
    |
    */
    'enabled' => (bool) env('DEMO_MODE', false),

    'user' => [
        'email' => env('DEMO_USER_EMAIL', 'demo@orcatech.test'),
        'password' => env('DEMO_USER_PASSWORD', 'OrcaTech-Demo-2026!'),
        'name' => env('DEMO_USER_NAME', 'OrcaTech Demo User'),
    ],
];

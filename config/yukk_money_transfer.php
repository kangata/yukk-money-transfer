<?php

return [
    'environment' => env('YUKK_MONEY_TRANSFER_ENV', 'sandbox'),

    'client_id' => env('YUKK_MONEY_TRANSFER_CLIENT_ID'),

    'client_secret' => env('YUKK_MONEY_TRANSFER_CLIENT_SECRET'),

    'production_base_url' => env('YUKK_MONEY_TRANSFER_PRODUCTION_BASE_URL', 'https://transfer.yukk.co/api'),

    'sandbox_base_url' => env('YUKK_MONEY_TRANSFER_SANDBOX_BASE_URL', ''),

    'endpoints' => [
        'v2' => [
            'bank_account_inquiry' => '/v2/bank-accounts/inquiry',
            'create_disbursement' => '/v2/money-transfers',
            'find_disbursement' => '/v2/money-transfers/:code',
            'get_balance' => '/v2/balance',
            'get_banks' => '/v2/banks',
        ],
    ],
];

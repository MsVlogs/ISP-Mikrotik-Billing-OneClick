<?php

return [
    'payments' => [
        'bkash' => [
            'app_key' => env('BKASH_APP_KEY'),
            'app_secret' => env('BKASH_APP_SECRET'),
            'username' => env('BKASH_USERNAME'),
            'password' => env('BKASH_PASSWORD'),
            'base_url' => env('BKASH_BASE_URL', 'https://checkout.sandbox.bka.sh/v1.2.0'),
        ],
        'nagad' => [
            'merchant_id' => env('NAGAD_MERCHANT_ID'),
            'merchant_key' => env('NAGAD_MERCHANT_KEY'),
            'base_url' => env('NAGAD_BASE_URL', 'https://api.sandbox.nagad.com.bd/api'),
        ],
        'rocket' => [
            'merchant_id' => env('ROCKET_MERCHANT_ID'),
            'merchant_password' => env('ROCKET_MERCHANT_PASSWORD'),
            'base_url' => env('ROCKET_BASE_URL', 'https://sandbox.rocketgateway.com/api'),
        ],
    ],
    'billing' => [
        'invoice_prefix' => env('INVOICE_PREFIX', 'INV'),
        'due_days' => env('INVOICE_DUE_DAYS', 7),
        'grace_days' => env('SUBSCRIPTION_GRACE_DAYS', 3),
    ],
    'reports' => [
        'financial_snapshot_time' => env('FINANCIAL_SNAPSHOT_TIME', '23:59'),
    ],
];

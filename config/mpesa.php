<?php

return [
    'base_url' => env('MPESA_ENV') === 'live' ? 'https://api.safaricom.co.ke' : 'https://sandbox.safaricom.co.ke',
    'consumer_key' => env('MPESA_CONSUMER_KEY'),
    'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
    'shortcode' => env('MPESA_SHORTCODE'),
    'party_b' => env('MPESA_PARTYB'),
    'passkey' => env('MPESA_PASSKEY'),
    'transaction_type' => env('MPESA_TRANSACTIONTYPE'),
    'callback_url' => env('MPESA_CALLBACK_URL'),
    'confirmation_url' => env('MPESA_CONFIRMATION_URL'),
    'validation_url' => env('MPESA_VALIDATION_URL'),
    'env' => env('MPESA_ENV'),
];

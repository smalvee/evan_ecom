<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default courier provider
    |--------------------------------------------------------------------------
    |
    | The provider used when no courier settings row exists yet. The provider
    | adapter itself is resolved by App\Services\Courier\CourierManager.
    |
    */

    'default' => env('COURIER_PROVIDER', 'steadfast'),

    /*
    |--------------------------------------------------------------------------
    | HTTP client tuning
    |--------------------------------------------------------------------------
    |
    | Shipment creation must never hang an admin request indefinitely, and we
    | deliberately do NOT retry creation (it can create duplicate consignments).
    |
    */

    'timeout' => (int) env('COURIER_HTTP_TIMEOUT', 15),

    'connect_timeout' => (int) env('COURIER_HTTP_CONNECT_TIMEOUT', 8),

    /*
    |--------------------------------------------------------------------------
    | Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [
        'steadfast' => [
            'label' => 'Steadfast',
            'base_url' => env('STEADFAST_BASE_URL', 'https://portal.steadfast.com.bd/api/v1'),
        ],
    ],

];

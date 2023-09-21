<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Default Driver
    |--------------------------------------------------------------------------
    */
    'default' => env('SMS_DRIVER', 'aliyun'),
    'fallback' => env('SMS_FALLBACK', 'juhe'),
    'signName' => env('SMS_SIGNNAME', ''),

    /*
    |--------------------------------------------------------------------------
    | List of Drivers
    |--------------------------------------------------------------------------
    */
    'drivers' => [
        // Install: composer require
        'aliyun' => [
            'appKey' => env('ALIYUN_APP_KEY', 'Your Access Key'),
            'appSecret' => env('ALIYUN_APP_SECRET', 'Your Secret Key'),
            'templateId' => env('ALIYUN_TEMPLATE_ID', 0),
            'driverFile' => 'ALiYun',
        ],
        'juhe' => [
            'key' => env('JUHE_KEY', 'Your Access Key'),
            'templateId' => env('JUHE_TEMPLATE_ID', 0),
            'driverFile' => 'JuHe',
        ],
        'yunpian' => [
            'apiKey' => env('YUNPIAN_API_KEY'),
            'templateContent' => env('YUNPIAN_TEMPLATE_CONTENT'),
            'driverFile' => 'YunPian',
        ],
    ],

];

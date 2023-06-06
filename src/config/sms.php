<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Default Driver
    |--------------------------------------------------------------------------
    */
    'default' => env('SMS_DRIVER', 'juhe'),

    /*
    |--------------------------------------------------------------------------
    | List of Drivers
    |--------------------------------------------------------------------------
    */
    'drivers' => [
        // Install: composer require aws/aws-sdk-php
        'aliyun' => [
            'url' => 'dysmsapi.aliyuncs.com',
            'access_secret_id' => 'Your Access Key',
            'access_secret_key' => 'Your Secret Key',
        ],
        'juhe' => [
            'url' => 'http://v.juhe.cn/vercodesms/submitTpl.php?',
            'signature' => 'Your Username',
            'key' => 'Your Key',
            'tplcode' => 'Your template id', // sender
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Maps
    |--------------------------------------------------------------------------
    */
    'map' => [
        'aliyun' => \TimeShow\Sms\Drivers\Aliyun::class,
        'juhe' => \TimeShow\Sms\Drivers\JuHe::class,
    ],


];
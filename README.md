## Laravel SMS

This is a Laravel Package for SMS Gateway Integration. Now Sending SMS is easy.

List of supported gateways:
- [Juhe](https://www.juhe.cn/)
- [Aliyun](https://www.aliyun.com/product/sms)
- [YunPian](https://www.yunpian.com/)

## Install

Via Composer

```bash
$ composer require timeshow/laravel-sms
```

add the `SmsServiceProvider` to your `config/app.php`:

``` bash
//providers
'providers' => [
    // ...
    TimeShow\Sms\SmsServiceProvider::class,
]
    
//aliases
'aliases' => [
    //...
    'Sms' => TimeShow\Sms\Facades\Sms::class,    
]
```

Publish the sms configuration file.

``` bash
php artisan vendor:publish --tag="sms"
```

In the config file you can set the default driver to use for all your SMS. But you can also change the driver at
runtime.

Choose what gateway you would like to use for your application. Then make that as default driver so that you don't have
to specify that everywhere. But, you can also use multiple gateways in a project.

Configure(.env)
```php

//default driver
SMS_DEFAULT=juhe

//fallback driver
SMS_FALLBACK=aliyun

//default signature
SMS_SIGNNAME=sms


ALIYUN_APP_KEY=your-appkey
ALIYUN_APP_SECRET=your-appsecret
ALIYUN_TEMPLATE_ID=your-templates-id

JUHE_KEY=your-key
JUHE_TEMPLATE_ID=your-templates-id

YUNPIAN_API_KEY=your-appkey
YUNPIAN_TEMPLATE_CONTENT=your-template-content
```

Then fill the credentials for that gateway in the drivers array.

```php
/*
|--------------------------------------------------------------------------
| Default Driver
|--------------------------------------------------------------------------
*/
'default' => env('SMS_DRIVER', 'juhe'),
'fallback' => env('SMS_FALLBACK', 'aliyun'),
'signature' => env('SMS_SIGNATURE', ''),

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
```

## Basic Usage
use it like this

```php
//default
$smsDriver = Sms::driver();
//fallback
$smsDriver = Sms::driver('fallback');
//other options
$smsDriver = Sms::driver('juhe');
$smsDriver = Sms::driver('aliyun');
$smsDriver = Sms::driver('yunpian');
```

## Usage

```php
$sms->setSignature('your_signature');
$sms->setContent($content);
$sms->setContentByVerifyCode(20);  //your code {verifyCode} {time} Minutes
$sms->makeStr();
$sms->makeCode(6);
$sms->setTemplateId(1);

$smsDriver->setTemplateVar($templateVar);
$smsDriver->setTemplateVar($templateVar, true);

$smsDriver->send($mobile);
$smsDriver->send($mobile, false);
```

## Example
```php
$sms = Sms::driver();
$content = 'Your verification code is {verifyCode}, Valid for {time} minutes';  //设置短信内容
$sms->setContent($content);
$result = $sms->send($mobile);

//Or
$sms = Sms::driver();
$sms->setTemplateId(123456);
$code = $sms->makeCode(6);
$sms->setContent('#code#='.$code);
$result = $sms->send($mobile, true);

//Or
$sms->setContentByVerifyCode(20);
$result = $sms->send($mobile);
```

## Security
If you discover any security related issues, please email 397975896@qq.com instead of using the issue tracker.

## License
The MIT License (MIT). Please see License File for more information.
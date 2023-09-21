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
SMS_DEFAULT=aliyun

//fallback driver
SMS_FALLBACK=juhe

//default signature  (Important: The signature should be enclosed in {})
SMS_SIGNATURE={sms}


ALIYUN_APP_KEY=your-appkey
ALIYUN_APP_SECRET=your-appsecret
ALIYUN_TEMPLATE_ID=your-templates-id  //Value：SMS_12345678

JUHE_KEY=your-key
JUHE_TEMPLATE_ID=your-templates-id  //Value：123456

YUNPIAN_API_KEY=your-appkey
YUNPIAN_TEMPLATE_CONTENT=your-template-content  //Value：您的验证码是{verifyCode}，有效期为{time}分钟，请尽快验证
```

Then fill the credentials for that gateway in the drivers array.

```php
/*
|--------------------------------------------------------------------------
| Default Driver
|--------------------------------------------------------------------------
*/
'default' => env('SMS_DRIVER', 'aliyun'),
'fallback' => env('SMS_FALLBACK', 'juhe'),
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
$sms = Sms::driver();
//fallback
$sms = Sms::driver('fallback');
//other options
$sms = Sms::driver('juhe');
$sms = Sms::driver('aliyun');
$sms = Sms::driver('yunpian');
```

## Usage

```php
$sms = Sms::driver();
$sms->setTemplateId(123456);
$sms->setSignature('your_signature');
$sms->setContent($content);
$sms->setContentByVerifyCode(20);  //Your verification code is {verifyCode}, valid for 20 minutes
$sms->makeStr();   // Generate a 16 bit default random string
$sms->makeCode(6);   // 100000~999999  1000~9999  10000000~99999999
$sms->makeRandom();  // 100000~999999

$templateVar = ['code' => $verifyCode];   // ['code' => '110101', 'time' =>'10']
$sms->setTemplateVar($templateVar);
$sms->setTemplateVar($templateVar, true);

$sms->send($mobile);
$sms->send($mobile, false);
```

## Example
```php
// Custom
$sms = Sms::driver();
$default = config('sms.default');
$templateId = config('sms.drivers.' . $default . '.templateId');
$sms->setTemplateId($templateId);
$content = 'Your verification code is {verifyCode}, Valid for {time} minutes';
$sms->setContent($content);
$result = $sms->send($mobile);

//Or JuHe
$sms = Sms::driver();
$default = config('sms.default');
$templateId = config('sms.drivers.' . $default . '.templateId');
$sms->setTemplateId($templateId);
$code = $sms->makeCode(6);
$sms->setContent('#code#='.$code);
$result = $sms->send($mobile, true);

//Or ALiYun
$sms = Sms::driver();
$default = config('sms.default');
$templateId = config('sms.drivers.' . $default . '.templateId');
$sms->setTemplateId($templateId);
$code = $sms->makeCode(6);
$templateVar = ['code' => $code];
$sms->setTemplateVar($templateVar, true);
$result = $sms->send($mobile, true);

//Or YunPian
$sms = Sms::driver();
$templateVar = ['yzm' => 'verifyCode'];
$smsDriver->setTemplateVar($templateVar, true);
$sms->setContentByVerifyCode(20);
$result = $sms->send($mobile);

//Or Set Content By Custom
$sms = Sms::driver();
$sms->setSignature('SignName');
$content = '{name},Your account is logged in from another location. If it was not for you, please change the password in a timely manner';  //content
$templateVar = ['name' => 'you name']; 
$smsDriver->setContent($content);
$smsDriver->setContentByCustomVar($templateVar);
//Value：you name,Your account is logged in from another location. If it was not for you, please change the password in a timely manner
```

## Security
If you discover any security related issues, please email 397975896@qq.com instead of using the issue tracker.

## License
The MIT License (MIT). Please see License File for more information.
# Laravel SMS

This is a Laravel Package for SMS Gateway Integration. Now Sending SMS is easy.

List of supported gateways:
- [Juhe](https://www.juhe.cn/)
- [Aliyun](https://www.aliyun.com/product/sms)

## Install

Via Composer

```bash
$ composer require timeshow/laravel-sms
```

add the `SmsServiceProvider` to your `config/app.php`:

``` bash
TimeShow\Sms\SmsServiceProvider::class,
```

Publish the sms configuration file.

``` bash
php artisan vendor:publish --tag="sms"
```

In the config file you can set the default driver to use for all your SMS. But you can also change the driver at
runtime.

Choose what gateway you would like to use for your application. Then make that as default driver so that you don't have
to specify that everywhere. But, you can also use multiple gateways in a project.

```php
// Eg. if you want to use SNS.
'default' => 'aliyun',
```

Then fill the credentials for that gateway in the drivers array.

```php
// Eg. for SNS.
'drivers' => [
    'aliyun' => [
        'url' => 'dysmsapi.aliyuncs.com',
            'access_secret_id' => 'Your Access Key',
            'access_secret_key' => 'Your Secret Key',
    ],
    ...
]
```
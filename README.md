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
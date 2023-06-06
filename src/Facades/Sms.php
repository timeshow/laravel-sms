<?php
namespace TimeShow\Sms\Facades;

use Illuminate\Support\Facades\Facade;

class Sms extends Facades
{
    protected static function getFacadeAccessor()
    {
        return 'sms';
    }
}
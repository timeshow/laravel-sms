<?php
namespace TimeShow\Sms;

class Factory
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function getFactories()
    {
        $drivers = $this->app['config']['sms.drivers'];

        $factories = [];

        foreach ($drivers as $key => $value) {
            $factories[$key] = __NAMESPACE__ . '\Drivers\\' . $value['driverFile'];
        }

        return $factories;
    }
}

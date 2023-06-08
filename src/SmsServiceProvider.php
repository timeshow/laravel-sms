<?php
declare(strict_types=1);
namespace TimeShow\Sms;

use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
    /**
     * The base package path.
     *
     * @var string|null
     * @var string|null
     */
    public static string|null $packagePath = null;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        self::$packagePath = __DIR__;

        $this->publishes(
            [
                self::$packagePath . '/config/sms.php' => config_path('sms.php'),
            ],
            'sms'
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('sms', function () {
            return new Manager($this->app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['sms'];
    }

}

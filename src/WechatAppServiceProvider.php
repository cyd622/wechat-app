<?php
/**
 * WechatAppServiceProvider
 * User:  tony
 * Email: luffywang622@gmail.com
 * Date:  2017/8/24 024
 * Time:  11:48
 *
 */
namespace WechatApp;

use Illuminate\Support\ServiceProvider;

class WechatAppServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $config_file = __DIR__ . '/../config/config.php';

        $this->mergeConfigFrom($config_file, 'wechatApp');

        $this->publishes([
            $config_file => config_path('wechatApp.php')
        ], 'wechatApp');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('WechatAppAuth', function ()
        {
            return new WechatAppAuth();
        });

        $this->app->alias('WechatAppAuth', WechatAppAuth::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['WechatAppAuth', WechatAppAuth::class];
    }
}

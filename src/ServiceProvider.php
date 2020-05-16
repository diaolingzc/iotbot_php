<?php
/*
 * @Author: Yunli
 * @Date: 2020-05-14 16:43:01
 * @LastEditors: Yunli
 * @LastEditTime: 2020-05-14 16:47:18
 * @Description:
 */

namespace Diaolingzc\IotbotPhp;

/**
 * Class ServiceProvider.
 */
class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton(IotBotHttp::class, function () {
            return new IotBotHttp(config('services.iotBot.webApiBaseUrl'), config('services.iotBot.robotQQ'));
        });

        $this->app->alias(IotBotHttp::class, 'iotbot');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [IotBotHttp::class, 'iotbot'];
    }
}

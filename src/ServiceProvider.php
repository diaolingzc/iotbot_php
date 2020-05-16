<?php
/*
 * @Author: Yunli
 * @Date: 2020-05-14 16:43:01
 * @LastEditors: Yunli
 * @LastEditTime: 2020-05-14 16:47:18
 * @Description:
 */

namespace Diaolingzc\IotbotPhp;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(IotBotHttp::class, function () {
            return new IotBotHttp(config('services.iotBot.webApiBaseUrl'), config('services.iotBot.robotQQ'));
        });

        $this->app->alias(IotBotHttp::class, 'iotbot');
    }

    public function provides()
    {
        return [IotBotHttp::class, 'iotbot'];
    }
}

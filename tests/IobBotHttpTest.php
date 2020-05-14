<?php
/*
 * @Author: Yunli
 * @Date: 2020-05-14 12:04:41
 * @LastEditors: Yunli
 * @LastEditTime: 2020-05-14 16:38:49
 * @Description:
 */

namespace Diaolingzc\IotbotPhp\Tests;

use Diaolingzc\IotbotPhp\IotbotHttp;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;

class IobBotHttpTest extends TestCase
{
    public $url;

    public $robotQQ;

    public function testSendMsg()
    {
        $iot = new IotBotHttp($this->url, $this->robotQQ);

        $response = $iot->sendMsg(123456789, 1, 'TextMsg', 'Test');
        $this->assertEquals(0, $response['Ret']);
    }

    public function testGetHttpClient()
    {
        $iot = new IotBotHttp($this->url, $this->robotQQ);

        // 断言返回结果为 GuzzleHttp\ClientInterface 实例
        $this->assertInstanceOf(ClientInterface::class, $iot->getHttpClient());
    }

    public function testSetGuzzleOptions()
    {
        $iot = new IotBotHttp($this->url, $this->robotQQ);

        // 设置参数前，timeout 为 null
        $this->assertNull($iot->getHttpClient()->getConfig('timeout'));

        // 设置参数
        $iot->setGuzzleOptions(['timeout' => 5000]);

        // 设置参数后，timeout 为 5000
        $this->assertSame(5000, $iot->getHttpClient()->getConfig('timeout'));
    }
}

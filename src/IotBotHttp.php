<?php
/*
 * @Author: Yunli
 * @Date: 2020-05-14 12:01:30
 * @LastEditors: Yunli
 * @LastEditTime: 2020-05-14 16:35:15
 * @Description:
 */

namespace Diaolingzc\IotbotPhp;

use Diaolingzc\IotbotPhp\Exceptions\HttpException;
use GuzzleHttp\Client;

class IotBotHttp
{
    protected $webApiBaseUrl;

    protected $guzzleOptions = [];

    protected $robotQQ;

    protected $webApiPaths = [
        'sendMsg' => 'sendMsg',
    ];

    public function __construct(string $webApiBaseUrl, int $roborQQ)
    {
        $this->webApiBaseUrl = $webApiBaseUrl;
        $this->robotQQ = $roborQQ;
    }

    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }

    public function setWebApiPath($key, $value)
    {
        $this->webApiPaths[$key] = $value;
    }

    public function sendMsg(int $toUser, int $sendToType = 1, string $sendMsgType = 'TextMsg', string $content = '', int $groupId = 0, int $atUser = 0)
    {
        $query = array_filter([
            'qq' => $this->robotQQ,
            'funcname' => $this->webApiPaths['sendMsg'],
        ]);

        $data = new \stdClass();
        $data->toUser = (int) $toUser;
        $data->sendToType = (int) $sendToType;
        $data->sendMsgType = (string) $sendMsgType;
        $data->content = (string) $content;
        $data->groupid = (int) $groupId;
        $data->atUser = (int) $atUser;

        try {
            $response = $this->getHttpClient()->request('POST', $this->webApiBaseUrl, [
                'query' => $query,
                'body' => json_encode($data),
                'headers' => [
                    'accept' => '*',
                    'accept-encoding' => 'gzip, deflate, br',
                    'accept-language' => 'zh-CN,zh;q=0.9',
                    'content-type' => 'application/json',
                ],
            ])->getBody()->getContents();

            return json_decode($response, true);
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }
}

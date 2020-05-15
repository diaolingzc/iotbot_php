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
        'send' => 'SendMsg',
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

    public function send(int $toUser, int $sendToType = 1, string $sendMsgType = 'TextMsg', string $content = '', int $groupId = 0, int $atUser = 0, string $voiceUrl = '', string $voiceBase64Buf = '', string $picUrl = '', string $picBase64Buf = '', string $fileMd5 = '', string $replayInfo = '')
    {
        $query = array_filter([
            'qq' => $this->robotQQ,
            'funcname' => $this->webApiPaths['send'],
        ]);

        $data = new \stdClass();
        $data->toUser = (int) $toUser;
        $data->sendToType = (int) $sendToType;
        $data->sendMsgType = (string) $sendMsgType;
        $data->content = (string) $content;
        $data->groupid = (int) $groupId;
        $data->atUser = (int) $atUser;
        if ($voiceUrl) $data->voiceUrl = (string) $voiceUrl;
        if ($voiceBase64Buf) $data->voiceUrl = (string) $voiceBase64Buf;
        if ($picUrl) $data->voiceUrl = (string) $picUrl;
        if ($picBase64Buf) $data->voiceUrl = (string) $picBase64Buf;
        if ($fileMd5) $data->voiceUrl = (string) $fileMd5;
        if ($replayInfo) $data->voiceUrl = (string) $replayInfo;

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

    public function sendTextMsg(int $toUser, int $sendToType = 1, string $content = '', int $groupId = 0, int $atUser = 0)
    {
      return $this->send($toUser, $sendToType, 'TextMsg', $content, $groupId, $atUser);
    }

    public function sendPicMsg(int $toUser, int $sendToType = 1, string $content = '', int $groupId = 0, int $atUser = 0, string $picUrl = '', string $picBase64Buf = '', string $fileMd5 = '')
    {
      return $this->send($toUser, $sendToType, 'PicMsg', $content, $groupId, $atUser, '', '', $picUrl, $picBase64Buf, $fileMd5);
    }

    public function sendVoiceMsg(int $toUser, int $sendToType = 1, string $content = '', int $groupId = 0, int $atUser = 0, string $voiceUrl = '', string $voiceBase64Buf = '')
    {
      return $this->send($toUser, $sendToType, 'VoiceMsg', $content, $groupId, $atUser, $voiceUrl, $voiceBase64Buf);
    }
}

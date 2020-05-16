<?php
/*
 * @Author: Yunli
 * @Date: 2020-05-14 12:01:30
 * @LastEditors: Please set LastEditors
 * @LastEditTime: 2020-05-16 15:15:21
 * @Description:
 */

namespace Diaolingzc\IotbotPhp;

use Diaolingzc\IotbotPhp\Exceptions\HttpException;
use GuzzleHttp\Client;

class IotBotHttp
{
    /**
     * webApi 根地址
     * @var string
     */
    protected $webApiBaseUrl;

    /**
     * GuzzleHttp 配置
     * @var array
     */
    protected $guzzleOptions = [];

    /**
     * Robot QQ 号
     * @var int
     */
    protected $robotQQ;

    /**
     * Api 地址
     * @var array
     */
    protected $webApiPaths = [
        'send' => 'SendMsg',
        'remoke' => 'RevokeMsg',
    ];

    /**
     * IotBotHttp constructor
     * 
     * @param string $webApiBaseUrl
     * @param string $robotQQ
     */
    public function __construct(string $webApiBaseUrl, int $robotQQ)
    {
        $this->webApiBaseUrl = $webApiBaseUrl;
        $this->robotQQ = $robotQQ;
    }

    /**
     * @return \GuzzleHttp\Client
     */
    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    /**
     * @param array @options
     */
    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }

    /**
     * @param array $key
     * @param array $value
     */
    public function setWebApiPath($key, $value)
    {
        $this->webApiPaths[$key] = $value;
    }

    /**
     * 发送信息(包括图片、文本、好友、私人、群消息)
     *
     * @param integer $toUser 欲发给的对象 群或QQ好友或私聊对象
     * @param integer $sendToType 发送消息对象的类型 1好友 2群3私聊
     * @param string $sendMsgType 欲发送消息的类型 TextMsg、JsonMsg、XmlMsg、ReplayMsg、TeXiaoTextMsg、PicMsg、VoiceMsg、PhoneMsg
     * @param string $content 发送的文本内容
     * @param integer $groupId 发送私聊消息是 在此传入群ID 其他情况为0
     * @param integer $atUser At用户 传入用户的QQ号 其他情况为0
     * @param string $voiceUrl 发送语音的网络地址 skilk格式
     * @param string $voiceBase64Buf 发本地送语音的buf 转 bas64 编码
     * @param string $picUrl 	发送图片的网络地址
     * @param string $picBase64Buf 发本地送图片的buf 转 bas64 编码
     * @param string $fileMd5 通过md5 值发送 图片
     * @param string $replayInfo 回复类型消息（回复某人的消息）
     * @return \Psr\Http\Message\ResponseInterface
     * 
     * @throws \Diaolingzc\IotbotPhp\Exceptions\HttpException
     */
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
        if ($voiceUrl || $voiceBase64Buf) {
            $data->voiceUrl = (string) $voiceUrl;
        }
        if ($voiceUrl || $voiceBase64Buf) {
            $data->voiceBase64Buf = (string) $voiceBase64Buf;
        }
        if ($picUrl || $picBase64Buf || $fileMd5) {
            $data->picUrl = (string) $picUrl;
        }
        if ($picUrl || $picBase64Buf || $fileMd5) {
            $data->picBase64Buf = (string) $picBase64Buf;
        }
        if ($picUrl || $picBase64Buf || $fileMd5) {
            $data->fileMd5 = (string) $fileMd5;
        }
        if ($replayInfo) {
            $data->voiceUrl = (string) $replayInfo;
        }

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

    /**
     * 撤回群消息(管理员可撤回其他人消息)
     *
     * @param integer $groupId 	群号
     * @param integer $msgSeq 消息序列号 需要从群消息里获取
     * @param integer $msgRandom 随机值 需要获取并保存
     * @return \Psr\Http\Message\ResponseInterface
     * 
     * @throws \Diaolingzc\IotbotPhp\Exceptions\HttpException
     */
    public function RevokeMsg(int $groupId, int $msgSeq, int $msgRandom)
    {
        $query = array_filter([
            'qq' => $this->robotQQ,
            'funcname' => $this->webApiPaths['remoke'],
        ]);

        $data = new \stdClass();
        $data->GroupID = (int) $groupId;
        $data->MsgSeq = (int) $msgSeq;
        $data->MsgRandom = (int) $msgRandom;

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

    /**
     * 发送文本信息(包括好友、私人、群消息)
     *
     * @param integer $toUser 欲发给的对象 群或QQ好友或私聊对象
     * @param integer $sendToType 发送消息对象的类型 1好友 2群3私聊
     * @param string $content 发送的文本内容
     * @param integer $groupId 发送私聊消息是 在此传入群ID 其他情况为0
     * @param integer $atUser At用户 传入用户的QQ号 其他情况为0
     * @return \Psr\Http\Message\ResponseInterface
     * 
     * @throws \Diaolingzc\IotbotPhp\Exceptions\HttpException
     */
    public function sendTextMsg(int $toUser, int $sendToType = 1, string $content = '', int $groupId = 0, int $atUser = 0)
    {
        return $this->send($toUser, $sendToType, 'TextMsg', $content, $groupId, $atUser);
    }

    /**
     * 发送图片信息(包括好友、私人、群消息)
     *
     * @param integer $toUser 欲发给的对象 群或QQ好友或私聊对象
     * @param integer $sendToType 发送消息对象的类型 1好友 2群3私聊
     * @param string $content 发送的文本内容
     * @param integer $groupId 发送私聊消息是 在此传入群ID 其他情况为0
     * @param integer $atUser At用户 传入用户的QQ号 其他情况为0
     * @param string $picUrl 发送图片的网络地址
     * @param string $picBase64Buf 发本地送图片的buf 转 bas64 编码
     * @param string $fileMd5 通过md5 值发送 图片
     * @return \Psr\Http\Message\ResponseInterface
     * 
     * @throws \Diaolingzc\IotbotPhp\Exceptions\HttpException
     */
    public function sendPicMsg(int $toUser, int $sendToType = 1, string $content = '', int $groupId = 0, int $atUser = 0, string $picUrl = '', string $picBase64Buf = '', string $fileMd5 = '')
    {
        return $this->send($toUser, $sendToType, 'PicMsg', $content, $groupId, $atUser, '', '', $picUrl, $picBase64Buf, $fileMd5);
    }

    /**
     * 发送语音信息(包括好友、私人、群消息)
     *
     * @param integer $toUser 欲发给的对象 群或QQ好友或私聊对象
     * @param integer $sendToType 发送消息对象的类型 1好友 2群3私聊
     * @param string $content 发送的文本内容
     * @param integer $groupId 发送私聊消息是 在此传入群ID 其他情况为0
     * @param integer $atUser At用户 传入用户的QQ号 其他情况为0
     * @param string $voiceUrl 发送语音的网络地址 skilk格式
     * @param string $voiceBase64Buf 发本地送图片的buf 转 bas64 编码
     * @return \Psr\Http\Message\ResponseInterface
     * 
     * @throws \Diaolingzc\IotbotPhp\Exceptions\HttpException
     */
    public function sendVoiceMsg(int $toUser, int $sendToType = 1, string $content = '', int $groupId = 0, int $atUser = 0, string $voiceUrl = '', string $voiceBase64Buf = '')
    {
        return $this->send($toUser, $sendToType, 'VoiceMsg', $content, $groupId, $atUser, $voiceUrl, $voiceBase64Buf);
    }
}

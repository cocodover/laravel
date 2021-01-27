<?php

namespace App\Http\Tools\Warning;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class DingTalk implements WarningInterface
{
    const DING_NOTIFY_URL = 'https://oapi.dingtalk.com/robot/send?access_token=';

    /**
     * 发送钉钉报警
     * @param null $group
     * @param null $content
     * @param array $mobile
     */
    public static function warning($group = null, $content = null, $mobile = array())
    {
        //获取参数配置
        $params = config('params.dingtalk');
        if (!isset($params[$group])) {
            $group = 'default';
        }
        //获取构建参数
        $data = self::getTextWarningData($params[$group]['keywords'], $content, $mobile);
        //获取报警url
        $url = self::DING_NOTIFY_URL . $params[$group]['token'];
        //发送报警信息
        $result = self::sendDingWarning($url, $data);
        if ($result['errcode'] > 0) {
            Log::error("钉钉报警发送失败!\r\n请求地址url:\r\n" . $url . "\r\n请求参数:\r\n" . json_encode($data) . "\r\n返回值:\r\n" . json_encode($result));
        }
    }

    /**
     * 构建text形式的报警参数
     * @param null $keywords
     * @param null $content
     * @param array $mobile
     * @return array
     */
    private static function getTextWarningData($keywords = null, $content = null, $mobile = array())
    {
        //没传手机号时@所有人，传入手机号时只@特定的人
        $flag = empty($mobile);
        return [
            'msgtype' => 'text',
            'text' => [
                //需在自定义机器人中添加自定义关键字
                'content' => $keywords . ":\r" . $content
            ],
            'at' => [
                'atMobiles' => $mobile,
                //@所有人
                'isAtAll' => $flag
            ]
        ];
    }

    /**
     * 发送报警请求
     * @param null $url
     * @param array $data
     * @return mixed
     */
    private static function sendDingWarning($url = null, $data = array())
    {
        $client = new Client();
        $response = $client->post($url, [
            'headers' => [
                'content-type' => 'application/json'
            ],
            'body' => json_encode($data)
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }
}
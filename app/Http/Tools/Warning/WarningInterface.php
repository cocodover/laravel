<?php


namespace App\Http\Tools\Warning;


interface WarningInterface
{
    /**
     * 请求三方服务器发送报警
     * @param string $group 报警收件对象（用于匹配三方对应群组&模板等）
     * @param string $content 报警内容
     * @param array $extra 额外参数
     * @return mixed
     */
    public static function warning($group = null, $content = null, $extra = array());
}
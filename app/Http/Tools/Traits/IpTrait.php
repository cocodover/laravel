<?php

namespace App\Http\Tools\Traits;

trait IpTrait
{
    /**
     * 获取请求IP地址(不支持ipv6)
     * @return int|null
     */
    public function getIp()
    {
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $ipAddress = $_SERVER['REMOTE_ADDR'];
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $ipAddress = getenv('HTTP_X_FORWARDED_FOR');
            } else if (getenv('HTTP_CLIENT_IP')) {
                $ipAddress = getenv('HTTP_CLIENT_IP');
            } else {
                $ipAddress = getenv('REMOTE_ADDR');
            }
        }
        if (strpos($ipAddress, ',') !== false) {
            $ipArr = explode(',', $ipAddress);
            $ipAddress = $ipArr[0];
        }
        $ip = ip2long($ipAddress);
        if ($ip === false) {
            $ip = null;
        }
        return $ip;
    }

}
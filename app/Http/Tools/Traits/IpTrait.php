<?php

namespace App\Http\Tools\Traits;

trait IpTrait
{
    /**
     * 获取请求IP地址(不支持ipv6)
     * @return int|null
     */
    public function getIp(): int
    {
        if (isset($_SERVER)) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['REMOTE_ADDR'];
        } else if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipAddress = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('HTTP_CLIENT_IP')) {
            $ipAddress = getenv('HTTP_CLIENT_IP');
        } else {
            $ipAddress = getenv('REMOTE_ADDR');
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
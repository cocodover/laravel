<?php
/**
 * 本文件master&manager&worker都会加载到
 * 但是平滑重启时不会再次加载该文件
 */

//加载第三方扩展
require_once dirname(__DIR__, 3) . '/vendor/autoload.php';

//自动加载文件
spl_autoload_register(static function ($class) {
    //将\替换为/
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $classPath = sprintf('%s/%s.php', __DIR__, $className);
    if (is_file($classPath)) {
        require_once $classPath;
    }
}, true, true);

//设置默认时区
date_default_timezone_set('PRC');

//启动服务
$server = new MultiServer();
$server->start();
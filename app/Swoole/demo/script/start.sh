#!/usr/bin/env bash
#获取pid
dirPath=$(dirname "$PWD")
path="/server_demo.php"
fileName=${dirPath}${path}
#启动服务
php ${fileName}
echo "server started"
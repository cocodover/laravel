#!/usr/bin/env bash
#获取pid
dirPath=$(dirname $(dirname "$PWD"))
path="/logs/swoole_server.pid"
fileName=${dirPath}${path}
pid=$(cat ${fileName})
#关闭服务
kill -TERM ${pid}
echo "server stopped"
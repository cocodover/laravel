#!/usr/bin/env bash
#获取pid
dirPath=$(dirname $(dirname "$PWD"))
path="/logs/swoole_server.pid"
fileName=${dirPath}${path}
pid=$(cat ${fileName})
#重启worker进程&task进程
kill -USR1 ${pid}
echo "server reloaded"
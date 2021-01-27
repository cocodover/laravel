<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>News Room</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div class="content">
    News Room
</div>
<script src="{{ mix('js/app.js') }}"></script>
<script>
    //参考文档(基于redis-socket.io处理广播)
    //https://learnku.com/laravel/t/13101/using-laravel-echo-server-to-build-real-time-applications
    //https://learnku.com/laravel/t/13521/using-laravel-echo-server-to-build-real-time-applications-two-private-channels

    //选择频道(与events设置的一致)
    Echo.channel('news')
    //选择监听事件(与events名称一致)
        .listen('NewsPushEvent', (e) => {
            //返回结果:{msg: "BIG NEWS: 1598343784", socket: null}
            console.log(e.msg);
        });
</script>
</body>
</html>
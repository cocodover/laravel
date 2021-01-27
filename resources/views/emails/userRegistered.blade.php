<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
{{--    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">--}}
</head>
<body>
<div class="flex-center position-ref full-height">
    尊敬的{{ $name }}用户，您好：
    <br><br/>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;感谢您成为laravel项目第{{ $id }}位用户！
</div>
</body>
</html>
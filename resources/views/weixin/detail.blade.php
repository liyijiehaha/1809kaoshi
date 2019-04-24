<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
@foreach($v as $k=>$a)
        {{$a['goods_id']}}<br>
        {{$a['goods_name']}}<br>
        {{$a['goods_desc']}}<br>
        {{$a['goods_img']}}<br>
@endforeach
</body>
</html>
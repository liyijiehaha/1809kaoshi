{{--商品id:<font style="color:mediumvioletred">{{$res['goods_id']}}</font><br>--}}
{{--商品名称:<font style="color:mediumvioletred">{{$res['goods_name']}}</font><br>--}}
{{--商品描述:<font style="color:mediumvioletred">{{$res['goods_desc']}}</font><br>--}}
{{--浏览次数：<font style="color:mediumvioletred">{{$cache_view}}</font>次<hr>--}}
{{--<h1>排行</h1>--}}
{{--@foreach($res1 as $key=>$val)--}}
{{--    商品id:{{$val->goods_id}}<br>--}}
{{--    商品名称:{{$val->goods_name}}<br>--}}
{{--    商品描述:{{$val->goods_desc}}<hr>--}}
{{--@endforeach--}}

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="http://res2.wx.qq.com/open/js/jweixin-1.4.0.js "></script>
    <script src="/js/jquery-1.12.4.min.js"></script>
    <title>Document</title>
</head>
<body>
        <h1>记录</h1>
        @foreach($res2 as $key=>$val)
            商品id:{{$val->goods_id}}<br>
            商品名称:{{$val->goods_name}}<br>
            商品描述:{{$val->goods_desc}}<hr>
        @endforeach
</body>
</html>
<script>

    wx.config({
        //debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: "{{$jsconfig['appId']}}", // 必填，公众号的唯一标识
        timestamp: "{{$jsconfig['timestamp']}}", // 必填，生成签名的时间戳
        nonceStr: "{{$jsconfig['nonceStr']}}", // 必填，生成签名的随机串
        signature: "{{$jsconfig['signature']}}",// 必填，签名
        jsApiList: ['chooseImage','uploadImage','updateAppMessageShareData'] // 必填，需要使用的JS接口列表
    });
    wx.ready(function () {   //需在用户可能点击分享按钮前就先调用
        wx.updateAppMessageShareData({
            title: '这个是标题', // 分享标题
            desc: '没有什么描述的', // 分享描述
            link: 'http://1809abc.comcto.com', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: 'http://1809abc.comcto.com/images/apple.jpg', // 分享图标
            success: function () {
                alert('分享成功');
            }
        })
    });
</script>
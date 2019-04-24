


商品id:<font style="color:mediumvioletred">{{$res['goods_id']}}</font><br>
商品名称:<font style="color:mediumvioletred">{{$res['goods_name']}}</font><br>
商品描述:<font style="color:mediumvioletred">{{$res['goods_desc']}}</font><br>
浏览次数：<font style="color:mediumvioletred">{{$cache_view}}</font>次<hr>
{{--<h1>排行</h1>--}}
{{--@foreach($res1 as $key=>$val)--}}
{{--    商品id:{{$val->goods_id}}<br>--}}
{{--    商品名称:{{$val->goods_name}}<br>--}}
{{--    商品描述:{{$val->goods_desc}}<hr>--}}
{{--@endforeach--}}
<h1>记录</h1>
@foreach($res2 as $key=>$val)
    商品id:{{$val->goods_id}}<br>
    商品名称:{{$val->goods_name}}<br>
    商品描述:{{$val->goods_desc}}<hr>
@endforeach
<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class JssDkController extends Controller
{
   public function test(){
     //  echo '<pre>';print_r($_SERVER);echo '</pre>';
       /*
           REQUEST_SCHEME :http
           HTTP_HOST:local.1809kaoshi.com
           REQUEST_URI: /weixin/jssdk/test
       */
       /*签名*/
           $nonce=Str::random(10);
           $ticket=getticket();
           $time=time();
           $current_url=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
//                   echo 'nonceStr: '.$nonceStr;echo '</br>';
//                    echo 'ticket: '.$ticket;echo '</br>';
//                    echo '$timestamp: '.$timestamp;echo '</br>';
//                    echo '$current_url: '.$current_url;echo '</br>';die;
            $string1 = "jsapi_ticket=$ticket&noncestr=$nonce&timestamp=$time&url=$current_url";
            $signature=sha1($string1);
           $wx.config({
                    debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                    appId: env('WX_APPID'), // 必填，公众号的唯一标识
                    timestamp: $time, // 必填，生成签名的时间戳
                    nonceStr: $nonce, // 必填，生成签名的随机串
                    signature:$signature,// 必填，签名
                });
           $data=[
               'wx_config'=>$wx_config
           ];
           retrun view('weixin/jssdk',$data);

   }
}

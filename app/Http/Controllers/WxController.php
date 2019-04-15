<?php

namespace App\Http\Controllers;
use App\Weixinmodel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
class WxController extends Controller
{
    //第一次调用接口
    public function list(){
        echo $_GET['echostr'];
    }
    //点击关注
    public function WxEvent(){
        $content = file_get_contents("php://input");
        $time = date('Y-m-d H:i:s');
        is_dir('logs')or mkdir('logs',0777,true);
        $str = $time.$content."\n";
        file_put_contents("logs/wx_event.log",$str,FILE_APPEND);
        $data=simplexml_load_string($content);
        $openid=$data->FromUserName;
        print_r($openid);
        $wx_id=$data->ToUserName;
        $event=$data->Event;
        if($event=='subscribe'){
            //根据openid判断用户是否已存在
            $Weixin_model=new Weixinmodel();
            $local_user=$Weixin_model->where(['openid'=>$openid])->first();
            if($local_user){
                echo '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$wx_id.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. '欢迎回来 '. $local_user['nickname'] .']]></Content></xml>';
            }else{
                //获取用户信息
                $u=$this ->getUserInfo($openid);
                //用户信息入库
                $u_info=[
                    'openid'=>$u['openid'],
                    'nickname'=>$u['nickname'],
                    'sex'=>$u['sex'],
                    'headimgurl'=>$u['headimgurl'],
                ];
                $Weixin_model=new Weixinmodel();
                $res= $Weixin_model->insert($u_info);
                echo '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$wx_id.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. '欢迎关注 '. $u['nickname'] .']]></Content></xml>';
            }
        }
    }
    //获取access_token
    public function getaccesstoken(){
        $key='wx_assess_token';
        $token=Redis::get($key);
        if($token){
            echo '有';
        }else{
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('APPID').'&secret='.env('APPSECRET');
            $response=file_get_contents($url);
            $arr =json_decode($response,true);
            Redis::set($key,$arr['access_token']);
            Redis::expire($key,3600);
            $token=$arr['access_token'];
        }
        return $token;
    }
    //获取微信用户
    public function getUserInfo($openid){
        $url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->getaccesstoken()."&openid=".$openid."&lang=zh_CN";
        $data=file_get_contents($url);
        $u=json_decode($data,true);
        return $u;
    }

}

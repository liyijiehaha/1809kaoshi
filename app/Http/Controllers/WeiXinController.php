<?php

namespace App\Http\Controllers;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Weixinmodel;
use Illuminate\Support\Facades\DB;
use App\Wxgoodsmodel;
class WeiXinController extends Controller
{
    //第一次调用接口
    public function list(){
        echo $_GET['echostr'];
    }
    public function wxevent(){
        $content = file_get_contents("php://input");
        $time = date('Y-m-d H:i:s');
        is_dir('logs')or mkdir('logs',0777,true);
        $str = $time.$content."\n";
        file_put_contents("logs/wx_goods_event.log",$str,FILE_APPEND);
        $data=simplexml_load_string($content);
        $openid=$data->FromUserName;//用户openid
        $appid=$data->ToUserName;//公众号id
        $event=$data->Event;//事件
        $type=$data->MsgType;//消息类型
        $create_time=$data->CreateTime;//时间
        $text=$data->Content;//内容
        $client=new Client();
        if($event=='subscribe'){
            //根据openid判断用户是否已存在
            $Weixin_model=new Weixinmodel();
            $local_user=$Weixin_model->where(['openid'=>$openid])->first();
            if($local_user){
                echo '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$appid.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. '呦吼！欢迎小可爱回来  '. $local_user['nickname'] .']]></Content></xml>';
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
                echo '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$appid.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. '欢迎关注小杰娃！💗  '. $u['nickname'] .']]></Content></xml>';
            }
        }elseif($type=='text'){
            if($text=='最新商品'){
                $v=DB::table('shop_goods')->orderBy('create_time','desc')->first();
                echo '<xml>
                          <ToUserName><![CDATA['.$openid.']]></ToUserName>
                          <FromUserName><![CDATA['.$appid.']]></FromUserName>
                          <CreateTime>.time().</CreateTime>
                          <MsgType><![CDATA[news]]></MsgType>
                          <ArticleCount>1</ArticleCount>
                          <Articles>
                            <item>
                              <Title><![CDATA['.$v->goods_name.']]></Title>
                              <Description><![CDATA['.$v->goods_desc.']]></Description>
                              <PicUrl><![CDATA['.'http://1809liyijie.comcto.com/uploads/goodsImg/20190220\3a7b8dea4c6c14b2aa0990a2a2f0388e.jpg'.']]></PicUrl>
                              <Url><![CDATA['.'http://1809liyijie.comcto.com/weixin/detail'.']]></Url>
                            </item>
                          </Articles>
                        </xml>';
            }

        }
    }
    public function getUserInfo($openid){
        $access_token=getaccseetoken();
        $url='https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $data=file_get_contents($url);
        $u=json_decode($data,true);
        return $u;
    }
    public function detail(Request $request){
        $goods_id=36;
        $v=DB::table('shop_goods')->where(['goods_id'=>$goods_id])->first();
        return view('weixin/detail',$v);
    }
}

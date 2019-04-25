<?php

namespace App\Http\Controllers;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Weixinmodel;
use Illuminate\Support\Facades\DB;
use  Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
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
                              <Url><![CDATA['.'http://1809liyijie.comcto.com/weixin/detail/?goods_id='.$v->goods_id.']]></Url>
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
    public function detail(Request $request)
    {
        $goods_id=36;
        $res=DB::table('shop_goods')->where(['goods_id'=>$goods_id])->first();
        $cache_view=Redis::incr($goods_id);//浏览自增量
//        /*浏览量排序*/
//                    $redis_ss_view='redis_goods_view';//浏览量排行
//                    Redis::zAdd($redis_ss_view,$cache_view,$goods_id);//有序集合按浏览量排序
//                    $goods_id=Redis::Zrevrange ($redis_ss_view,0,10000,true);//倒序排行
//                    $res1=[];
//                    foreach ($goods_id as $k=>$v) {
//                        $where=[
//                            'goods_id'=>$k
//                        ];
//                        $res1[]=GoodsModel::where($where)->first();
//                    }
            /*浏览历史记录*/
            $redis_ss_history='redis_goods_history:'.Auth::id();//浏览量排行
            Redis::zAdd($redis_ss_history,time(),$goods_id);//有序集合按浏览量排序
            $goods=Redis::zRevRange($redis_ss_history,0,10000000000000,true);//倒序排行
            $res2=[];
            foreach($goods as $k=>$v) {
                $where=[
                    'goods_id'=>$k
                ];
                $res2[]=DB::table('shop_goods')->where($where)->first();
            }
            $data=[
                'res'=>$res,
                'cache_view'=>$cache_view
            ];
        return view('weixin/detail',$data,compact('res2'));
    }
    public function getJsConfig()
    {
        $nonce=Str::random(10);
        $ticket=getticket();
        $time=time();
        $current_url=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        var_dump($current_url);
        $string1 = "jsapi_ticket=$ticket&noncestr=$nonce&timestamp=$time&url=$current_url";
        var_dump($string1);
        $signature=sha1($string1);
        $wx_config=[
            // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
            'appId'=>env('WX_APPID'), // 必填，公众号的唯一标识
            'timestamp'=>$time, // 必填，生成签名的时间戳
            'nonceStr'=>$nonce, // 必填，生成签名的随机串
            'signature'=>$signature,// 必填，签名
        ];
        $data=[
            'wx_config'=>$wx_config
        ];
     return view('weixin/detail',$data);
    }
    //授权网页
    public function getu(){
        $code = $_GET['code'];
        //获取access_token
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('WX_APPID').'&secret='.env('WX_APPSECRET').'&code='.$code.'&grant_type=authorization_code';
        $response = json_decode(file_get_contents($url),true);
        $access_token=$response['access_token'];
        $openid=$response['openid'];
        //获取用户信息
        $url="https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
        $res = json_decode(file_get_contents($url),true);
        $arr=DB::table('p_sq_user')->where(['openid'=>$openid])->first();
        if($arr){
            echo    '呦吼！欢迎小可爱回来';
        }else{
            $info=[
                'nickname'=>$res['nickname'],
                'openid'=>$res['openid'],
                'sex'=>$res['sex'],
                'headimgurl'=>$res['headimgurl'],
            ];
            $res=DB::table('p_sq_user')->insert($info);
            echo    '呦吼！欢迎小可爱来授权';
        }
    }
}

<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/info', function () {
  phpinfo();
});
Route::get('/test/urlencode', function () {
   echo urlencode($_GET['url']);
});
//微信接口返回文件
Route::get('/weixin/list','WxController@list');
Route::post('/weixin/list','WxController@wxEvent');
/*获取access_token*/
Route::get('/weixin/getaccesstoken','WxController@getaccesstoken');
/*菜单*/
Route::get('/weixin/create_menu','WxController@create_menu');
/*群发*/
Route::get('weixin/send','WxController@send');
/*微信扫码支付*/
Route::get('weixin/pay/test','weixin\WxpayController@test');
Route::post('/weixin/pay/notify','weixin\WxpayController@notify');       //微信支付回调地址

Route::get('/weixin/jssdk/test','JssDkController@test');
Route::get('/weixin/jssdk/getImg', 'JssDkController@getImg');      //获取JSSDK上传的照片

////最新商品
//Route::get('/weixin/list','WeiXinController@list');
//Route::post('/weixin/list','WeiXinController@wxevent');
//Route::get('/weixin/detail','WeiXinController@detail');
//Route::get('/weixin/getJsConfig', 'WeiXinController@getJsConfig');      //jssdk测试


















//计划任务
Route::get('/weixin/index','Crontab\CrontabController@del_order');//删除过期订单
//网页授权
Route::get('/wxweb/u', 'WeiXinController@getu');
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

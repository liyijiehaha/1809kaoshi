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
//微信接口返回文件
Route::get('/weixin/list','WxController@list');
Route::post('/weixin/list','WxController@wxEvent');
/*获取access_token*/
Route::get('/weixin/getaccesstoken','WxController@getaccesstoken');
/*菜单*/
Route::get('/weixin/create_menu','WxController@create_menu');
/*群发*/
Route::get('weixin/send','WxController@send');





















//计划任务
Route::get('/weixin/index','Crontab\CrontabController@del_order');//删除过期订单
//网页授权
Route::get('/wxweb/u', 'WeiXinController@getu');
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

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
//微信接口返回文件
Route::get('/weixin/list','WxController@list');
Route::post('/weixin/list','WxController@wxEvent');
Route::get('/weixin/getaccesstoken','WxController@getaccesstoken');
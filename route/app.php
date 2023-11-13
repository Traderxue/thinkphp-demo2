<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use app\middleware\JwtMiddleware;
use think\facade\Route;

Route::get('think', function () {
    return 'hello,ThinkPHP6!';
});

Route::get('hello/:name', 'index/hello');

Route::post("/admin/register", "admin/register");

Route::post("/admin/login", "admin/login");

Route::post("/user/register", "user/register");

Route::post("/user/login","user/login");

Route::post("/upload","upload/index");


Route::group("/admin", function () {

    Route::post("/resetpwd", "admin/resetPwd");

    Route::get("/delete/:id", "admin/deleteById");

})->middleware(JwtMiddleware::class);


Route::group("/user", function () {

    Route::post("/resetpwd","user/resetPwd");

    Route::post("/freeze","user/freeze");

    Route::post("/balance","user/setBalance");

    Route::get("/getbyid/:id","user/getById");

    Route::get("/getpage","user/getPage");

    Route::get("/delete/:id","user/deleteById");

    Route::post("/edit","user/edit");

    Route::post("/transfer","user/transfer");

})->middleware(JwtMiddleware::class);

Route::group("/invite",function(){

    Route::get("/getu_id/:u_id","invite/getInviteCode");

    Route::post("/setcode","invite/setCode");

    Route::get("/getpage","invite/getPage");
});
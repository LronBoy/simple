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
use think\facade\Route;
use app\common\SystemResponse;

# 不需要登录授权接口
Route::group('openapi', function (){
	# 调试
	Route::get('xun_sou','OpenApi/xunSou');
});

# 404
Route::miss(function () {
	return app(SystemResponse::class)->json(404, '非法请求');
});
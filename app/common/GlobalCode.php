<?php
/***********************************************************
 * Description:  全局 code 特征
 * Copyright(C): Chengdu MeiDuo Network Technology Co.,Ltd.
 * Created by:   PhpStorm
 * Version:      v1.0.0
 * Function:     Include function
 *
 * @author:      Jeffry    w@phpuse.cn
 * @datetime:    2021/10/26  16:13
 * @others:      Use the PhpStorm
 *
 * history:      Modify record
 ***********************************************************/
namespace app\common;

trait GlobalCode
{
	protected array $code = [
		# 系统级 code 为4位
		403  => ['code' => 403, 'notice' => 'ERROR', 'msg' => '非法请求', 'explain' => '没有权限'],
		404  => ['code' => 404, 'notice' => 'ERROR', 'msg' => '非法请求', 'explain' => '页面不存在'],
		# 成功
		2000 => ['code' => 2000, 'notice' => 'SUCCESS', 'msg' => '成功', 'explain' => '成功'],
		# 通用错误
		2001 => ['code' => 2001, 'notice' => 'ERROR', 'msg' => '未知错误', 'explain' => '系统内部错误，不存在的错误码'],
		2002 => ['code' => 2002, 'notice' => 'ERROR', 'msg' => '请求错误', 'explain' => '参数校验错误'],
		# 请求成功，自定义提示
		2100 => ['code' => 2100, 'notice' => 'ERROR', 'msg' => '请求成功', 'explain' => '请求成功，自定义提示'],
		# 签名错误
		3001 => ['code' => 3001, 'notice' => 'ERROR', 'msg' => '签名错误', 'explain' => '请求head 未传入 sign 签名'],
		3002 => ['code' => 3002, 'notice' => 'ERROR', 'msg' => '签名错误', 'explain' => '参数 sign 错误'],
		3003 => ['code' => 3003, 'notice' => 'ERROR', 'msg' => '签名错误', 'explain' => '参数 chat_name 错误'],
		3004 => ['code' => 3004, 'notice' => 'ERROR', 'msg' => '签名错误', 'explain' => '参数 time 错误'],
		3005 => ['code' => 3005, 'notice' => 'ERROR', 'msg' => '签名错误', 'explain' => '参数 time 过期（签名生成后5分钟内有效）'],
		3006 => ['code' => 3006, 'notice' => 'ERROR', 'msg' => '签名错误', 'explain' => '非白名单请求！'],
		3007 => ['code' => 3007, 'notice' => 'ERROR', 'msg' => '签名错误', 'explain' => '获取的数据格式错误'],
		3008 => ['code' => 3008, 'notice' => 'ERROR', 'msg' => '未知错误', 'explain' => 'Rsa类未知错误'],
		# 鉴权
		4001 => ['code' => 4001, 'notice' => 'ERROR', 'msg' => '登录已过期', 'explain' => 'header头未传入token'],
		4002 => ['code' => 4002, 'notice' => 'ERROR', 'msg' => '登录已过期重新登录', 'explain' => 'token已过期'],
		# 第三方数据获取
		5000 => ['code' => 5000, 'notice' => 'ERROR', 'msg' => '请求失败', 'explain' => '第三方数据获取异常'],
		
		
		# 业务层 code 为7位 1、2、3位控制器，4、5位方法，6、7位错误码
		# /app/controller/OpenApi openApi
		1000101 => ['code' => 1000101, 'notice' => 'ERROR', 'msg' => '获取的数据格式错误', 'explain' => '获取的数据格式错误'],
	];
}
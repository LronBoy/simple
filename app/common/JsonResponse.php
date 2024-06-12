<?php
/***********************************************************
 * Description:  业务数据输出
 * Copyright(C): RuLi
 * Created by:   PhpStorm
 * Version:      v1.0.0
 * Function:     Include function
 *
 * @author:      Admin
 * @datetime:    2023/6/8  16:37
 * @others       Use the PhpStorm
 *
 * history:      Modify record
 ***********************************************************/

namespace app\common;
use think\facade\Log;
use think\response\Json;

trait JsonResponse
{
	/**
	 * 加载全局code特征
	 */
	use GlobalCode;
	
	
	/**
	 * 访问时间（毫秒级）
	 * @var float|null
	 */
	protected ?float $millisecond = null;
	
	/**
	 * description: 成功响应
	 *+----------------------------------------------------------------------
	 * @param array $data
	 * @param int $code
	 * @param bool $is_log
	 * @return Json
	 *+----------------------------------------------------------------------
	 * @history Modify record
	 * @author: Admin  2021/10/26 17:58
	 * @access: public
	 */
	public function success(array $data = [], int $code = 2000, bool $is_log = false): Json
	{
		$this->millisecond = millisecond();
		# 整理返回数据
		$content = [
			'code'      => $this->code[$code]['code'],
			'status'    => $this->code[$code]['notice'],
			'msg'       => $this->code[$code]['msg'],
			'data'      => $data,
			'timestamp'     => $this->millisecond,
			'business_id'   => getNullBusinessId(),
		];
		
		if($is_log){
			Log::write([
				'code'          => $code,
				'status'        => 'SUCCESS',
				'msg'           => '成功响应',
				'query'         => request()->param(),
				'query_url'     => request()->url(),
				'query_header'  => request()->header(),
				'data'          => $content,
				'timestamp'     => $this->millisecond,
				'business_id'   => getNullBusinessId(),
			]);
		}
		
		return json($content);
	}
	
	
	/**
	 * description: 失败响应
	 *+----------------------------------------------------------------------
	 * @param int $code         错误码
	 * @param string $message   错误信息
	 * @param boolean $status   是否原样输出
	 * @param mixed $data       错误数据
	 * @return Json
	 *+----------------------------------------------------------------------
	 * @history Modify record
	 * @author: Admin  2021/10/27 9:18
	 * @access: public
	 */
	public function error(int $code=2001, string $message='', bool $status=false, mixed $data=null): Json
	{
		$this->millisecond  = millisecond();
		$result_code        = $this->code[$code]['code'] ?? 2001 ;
		$result_status      = $this->code[$result_code]['notice'];
		
		$request = request()->param();
		$ip = request()->header('x-forwarded-for');
		$request['source_ip']   =  $ip ?? request()->ip();
		# 错误记录日志
		Log::write([
			'code'          => $code,
			'status'        => $result_status,
			'msg'           => $message ?: ($this->code[$code]['explain'] ?? '未知错误码'),
			'query'         => $request,
			'query_url'     => request()->url(),
			'query_header'  => request()->header(),
			'data'          => $data,
			'timestamp'     => $this->millisecond,
			'business_id'   => getNullBusinessId(),
		]);
		
		
		# 返回数据处理
		if(env('APP_DEBUG') || $status){
			$msg = $message ?: $this->code[$result_code]['explain'];
		}else{
			$msg = $this->code[$result_code]['msg'];
			$data = null;
		}
		
		# 错误码小于1000 系统级处理
		if($result_code < 1000){
			return app(SystemResponse::class)->json($result_code, $msg);
		}else{
			# 整理返回数据
			$content = [
				'code'      => $result_code,
				'status'    => $result_status,
				'msg'       => $msg,
				'data'      => $data,
				'timestamp' => $this->millisecond,
				'business_id'   => getNullBusinessId(),
			];
			return json($content);
		}
	}
	
	
}
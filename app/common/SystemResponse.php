<?php
/***********************************************************
 * Description: ${CARET} (SystemResponse.php for xunsou)
 * Copyright(C):
 * Created by:   PhpStorm
 * Version:      v1.0.0
 * Function:     Include function
 *
 * @author:      Aiwrr    aiwrrc@gmail.com
 * @datetime:    2024/6/12  15:39:50
 * @others:      Use the PhpStorm
 *
 * history:      Modify record
 ***********************************************************/
namespace app\common;

use think\Response;

class SystemResponse extends Response
{
	/**
	 * description: 系统级JSON响应
	 *+----------------------------------------------------------------------
	 * @param int $code
	 * @param string $message
	 * @param mixed|null $data
	 * @return $this
	 *+----------------------------------------------------------------------
	 * @author: Admin  2023-06-14 10:16:37
	 * @access: public
	 * history: Modify record
	 */
	public function json(int $code, string $message = '', mixed $data = null): static
	{
		return $this->contentType('application/json')
			->content(json_encode([
				'code' => $code,
				'msg'  => $message,
				'data' => $data
			], JSON_UNESCAPED_UNICODE));
	}
	
	
	/**
	 * description: 响应HTML
	 *+----------------------------------------------------------------------
	 * @param string $content
	 * @return $this
	 *+----------------------------------------------------------------------
	 * @author: Admin  2023-06-14 10:17:31
	 * @access: public
	 * history: Modify record
	 */
	public function html(string $content): static
	{
		return $this->contentType('text/html')
			->content($content);
	}
	
	
	/**
	 * description: 输出
	 *+----------------------------------------------------------------------
	 * @param mixed $data
	 * @return false|mixed|string
	 *+----------------------------------------------------------------------
	 * @author: Admin  2023-06-14 10:21:13
	 * @access: public
	 * history: Modify record
	 */
	public function output(mixed $data): mixed
	{
		if (is_array($data)) {
			return json_encode($data, JSON_UNESCAPED_UNICODE);
		} else {
			return $data;
		}
	}
}
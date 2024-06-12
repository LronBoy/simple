<?php
// 应用公共文件

use Carbon\Carbon;
use think\facade\Log;
use think\Request;


# PHP语言级公用自定义函数==================================================================================================

if (!function_exists('list_to_tree')) {
	/**
	 * description: 把返回的数据集转换成Tree
	 *+----------------------------------------------------------------------
	 * @param array     $list   要转换的数据集
	 * @param string    $pk     主键
	 * @param string    $pid    parent标记字段
	 * @param string    $child  子集名称
	 * @param int       $root   从第几层开始
	 * @return array
	 *+----------------------------------------------------------------------
	 * @history: Modify record
	 * @author : Jeffry  2023/11/21 9:42:25
	 * @noinspection PhpArrayAccessCanBeReplacedWithForeachValueInspection
	 */
	function list_to_tree(array $list, string $pk = 'id', string $pid = 'pid',
	                      string $child = 'children', int $root = 0): array
	{
		// 创建Tree
		$tree = [];
		if (is_array($list)) {            // 创建基于主键的数组引用
			$refer = [];
			foreach ($list as $key => $data) {
				$refer[$data[$pk]] =& $list[$key];
			}
			foreach ($list as $key => $data) {
				// 判断是否存在parent
				$parentId = $data[$pid];
				if ($root == $parentId) {
					$tree[] =& $list[$key];
				} else {
					if (isset($refer[$parentId])) {
						$parent =& $refer[$parentId];
						$parent[$child][] =& $list[$key];
					}
				}
			}
		}
		return $tree;
	}
}


if (!function_exists('get_child_list')) {
	/**
	 * description: 获取父节点下的所有数据
	 *+----------------------------------------------------------------------
	 * @param array     $list       要转换的数据集
	 * @param string    $pk         主键
	 * @param string    $pid        parent标记字段
	 * @param int       $root       从第几层开始
	 * @param array     $arr        递归返回值
	 * @param array     $except_ids 排除的节点
	 * @return array
	 *+----------------------------------------------------------------------
	 * @history: Modify record
	 * @author : Jeffry  2023/11/21 9:41:21
	 */
	function get_child_list(
		array $list, string $pk = 'id', string $pid = 'pid', int $root = 0,
		array &$arr = [], array &$except_ids = []): array
	{
		foreach ($list as $data) {
			// 判断是否存在parent
			$parentId = $data[$pid];
			// 排除
			in_array($parentId, $except_ids) && $except_ids[] = $data[$pk];
			
			if ($root == $parentId && !in_array($data[$pk], $except_ids)) {
				$arr[] = $data;
				get_child_list($list, $pk, $pid, $data[$pk], $arr, $except_ids);
			}
		}
		return $arr;
	}
}


if (!function_exists('get_parent_list')) {
	/**
	 * description: 获取子节点所有父级数据
	 *+----------------------------------------------------------------------
	 * @param array     $list   要转换的数据集
	 * @param int       $id     子节点ID
	 * @param string    $pk     主键
	 * @param string    $pid    parent标记字段
	 * @param array     $arr    递归返回值
	 * @return array
	 *+----------------------------------------------------------------------
	 * @history: Modify record
	 * @author : Jeffry  2023/11/21 9:41:06
	 */
	function get_parent_list(array $list, int $id, string $pk = 'id',
	                         string $pid = 'pid', array &$arr = []): array
	{
		if (is_array($list)) {
			foreach ($list as $value) {
				if ($value[$pk] == $id) {
					$arr[] = $value;
					get_parent_list($list, $value[$pid], $pk, $pid, $arr);
				}
			}
		}
		return $arr;
	}
}


if(!function_exists('isJson')){
	/**
	 * description: 验证是否为JSON字符串
	 *+----------------------------------------------------------------------
	 * @param $string
	 * @return bool
	 *+----------------------------------------------------------------------
	 * @author: Admin  2021-10-29 14:25:36
	 * @access: public
	 * history: Modify record
	 */
	function isJson($string): bool
	{
		json_decode($string);
		return (json_last_error() === JSON_ERROR_NONE);
	}
}


if (!function_exists('isPhone')) {
	/**
	 * description: 验证号码为手机号或者座机号码
	 *+----------------------------------------------------------------------
	 * @param $value
	 * @return bool
	 *+----------------------------------------------------------------------
	 * @author: Admin  2022-10-27 11:03:13
	 * @access: public
	 * history: Modify record
	 */
	function isPhone($value): bool
	{
		if (preg_match('/^((0\d{2,3}-\d{7,8})|(1[3-9][0-9]\d{8}))$/', (string)$value) !== 0) {
			return true;
		}
		return false;
	}
}


if (!function_exists('isMobile')) {
	/**
	 * description: 验证号码为手机号
	 *+----------------------------------------------------------------------
	 * @param $value
	 * @return bool
	 *+----------------------------------------------------------------------
	 * @history: Modify record
	 * @author : Jeffry  2023/11/21 9:40:37
	 */
	function isMobile($value): bool
	{
		if (preg_match('/^(1[3-9][0-9]\d{8})$/', (string)$value) !== 0) {
			return true;
		}
		return false;
	}
}


if(!function_exists('divArrSort')){
	/**
	 * description: 自定义数组排序
	 *+----------------------------------------------------------------------
	 * @param array     $list       需要排序的二维数组
	 * @param array     $sort_arr   自定义排序值（一维数组）
	 * @return array
	 *+----------------------------------------------------------------------
	 * @author: Admin  2022-10-27 10:14:12
	 * @access: public
	 * history: Modify record
	 */
	function divArrSort (array $list, array $sort_arr): array
	{
		# 根据权重自定义排序
		uasort($list, function ($a, $b) use($sort_arr){
			$a_key = array_search($a, $sort_arr);
			$b_key = array_search($b, $sort_arr);
			if($a_key < $b_key){
				return -1;
			}else if($a_key == $b_key){
				return 0;
			}else{
				return 1;
			}
		});
		
		return $list;
	}
}


if(!function_exists('millisecond')) {
	/**
	 * description: 获取毫秒级时间戳
	 *+----------------------------------------------------------------------
	 * @return int
	 *+----------------------------------------------------------------------
	 * @author: Admin  2021-10-29 14:25:56
	 * @access: public
	 * history: Modify record
	 */
	function millisecond(): int
	{
		[$t1, $t2] = explode(' ', microtime());
		return (int)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
	}
}


if(!function_exists('convert_url_query')){
	/**
	 * description: url参数返回数组
	 *+----------------------------------------------------------------------
	 * @param $query
	 * @return array
	 *+----------------------------------------------------------------------
	 * @author: Admin  2021-11-12 15:00:31
	 * @access: public
	 * history: Modify record
	 */
	function convert_url_query($query): array
	{
		$queryParts = explode('&', $query);
		$params = array();
		foreach ($queryParts as $param) {
			$item = explode('=', $param);
			$params[$item[0]] = $item[1] ?? '';
		}
		return $params;
	}
}


if(!function_exists('getNullBusinessId')){
	/**
	 * description: 当系统级错误时获取业务ID
	 *+----------------------------------------------------------------------
	 * @return string
	 *+----------------------------------------------------------------------
	 * @author: Admin  2021-10-29 17:58:51
	 * @access: public
	 * history: Modify record
	 */
	function getNullBusinessId(): string
	{
		$rand_num   = mt_rand(000001,999999);
		return strtoupper(sha1(base64_encode(millisecond().$rand_num)));
	}
}


if(!function_exists('sortDateTime')){
	/**
	 * description: 时间排序
	 *+----------------------------------------------------------------------
	 * @param array $date_arr
	 * @return array
	 *+----------------------------------------------------------------------
	 * @author: Admin  2022-10-27 10:14:12
	 * @access: public
	 * history: Modify record
	 */
	function sortDateTime (array $date_arr): array
	{
		# 根据权重自定义排序
		uasort($date_arr, function ($a, $b){
			$dateTimestamp1 = strtotime($a);
			$dateTimestamp2 = strtotime($b);
			return $dateTimestamp1 < $dateTimestamp2 ? -1 : 1;
		});
		return $date_arr;
	}
}


if (!function_exists('del_space_str')) {
	/**
	 * description: 处理空格的字符串
	 *+----------------------------------------------------------------------
	 * @param $str
	 * @return string
	 *+----------------------------------------------------------------------
	 * @history: Modify record
	 * @author : Jeffry  2023/11/21 9:40:24
	 */
	function del_space_str($str): string
	{
		$string = '';
		if($str){
			$string = str_replace(" ","",$str);
		}
		return $string;
	}
}


if (!function_exists('curl_request')) {
	/**
	 * description: curl请求
	 *+----------------------------------------------------------------------
	 * @param $url          string  提交的URL
	 * @param $post         array   提交方式，默认get
	 * @param $getHeader    int     获取请求头
	 * @param $httpsVerify  int     禁止https验证,默认0禁止
	 * @param $json         bool boolean是否json格式数据
	 * @param $header       array   设置请求头
	 * @return bool|string
	 *+----------------------------------------------------------------------
	 * @history: Modify record
	 * @author : Jeffry  2023/11/21 9:40:02
	 */
	function curl_request(string $url, array $header = [],array $post = [],
	                      int $getHeader = 0, int $httpsVerify = 0, bool $json = false): bool|string
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_USERAGENT,
			'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
		if ($post) {
			curl_setopt($curl, CURLOPT_POST, 1);
			if ($json) {
				$json_post = json_encode($post);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $json_post);//JSON类型字符串
				curl_setopt($curl, CURLOPT_HTTPHEADER, array(
						'Content-Type: application/json',
						'Content-Length: ' . strlen($json_post))
				);
			} else {
				curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
			}
		}
		if (!empty($header)) {
			curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		}
		curl_setopt($curl, CURLOPT_HEADER, $getHeader);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		if (!$httpsVerify) {
			//是否禁止https验证
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, '0');
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, '0');
		}
		$data = curl_exec($curl);
		if (curl_errno($curl)) {
			return curl_error($curl);
		}
		curl_close($curl);
		return $data;
		
	}
}


if (!function_exists('releaseLock')) {
	/**
	 * Get the Lua script to atomically release a lock.
	 *
	 * KEYS[1] - The name of the lock
	 * ARGV[1] - The owner key of the lock instance trying to release it
	 *
	 * @return string
	 */
	function releaseLock(): string
	{
		return <<<'LUA'
if redis.call("get",KEYS[1]) == ARGV[1] then
    return redis.call("del",KEYS[1])
else
    return 0
end
LUA;
	}
}


if (!function_exists('filterEmoji')) {
	/**
	 * description: emoji图标替换
	 *+----------------------------------------------------------------------
	 * @param $str
	 * @return array|string|string[]|null
	 *+----------------------------------------------------------------------
	 * @author: Admin  2022-12-28 16:32:00
	 * @access: public
	 * history: Modify record
	 */
	function filterEmoji($str): array|string|null
	{
		return preg_replace_callback( '/./u', function (array $match) {
			return strlen($match[0]) >= 4 ? '' : $match[0];
		}, $str);
	}
}


if (!function_exists('model_arr_to_blade')) {
	/**
	 * description: 模型中数组转模板数据
	 *+----------------------------------------------------------------------
	 * @param $arr
	 * @return array
	 *+----------------------------------------------------------------------
	 * @history: Modify record
	 * @author : Jeffry  2023/11/21 9:39:31
	 */
	function model_arr_to_blade($arr): array
	{
		return array_map(function ($key, $item) {
			return ['id' => $key, 'name' => $item];
		}, array_keys($arr), array_values($arr));
	}
}


if (!function_exists('format_time')) {
	/**
	 * description: 格式化时间
	 *+----------------------------------------------------------------------
	 * @param string $date_time
	 * @param string|null $now_time
	 * @return bool|string
	 *
	 *+----------------------------------------------------------------------
	 * @author: Admin  2023-06-27 10:08:01
	 * @access: public
	 * history: Modify record
	 */
	function format_time(string $date_time, string $now_time = null): bool|string
	{
		$now_time = $now_time ?? date('Y-m-d H:i:s');
		$date_time_arr  = explode(' ', $date_time);
		$now_time_arr   = explode(' ', $now_time);
		if($date_time_arr[0] == $now_time_arr[0]){
			return substr($date_time, 11, 5);
		}else{
			return substr($date_time, 5, 11);
		}
	}
}

if(!function_exists('get_fast_date_type')) {
	
	/**
	 * description: 获取快捷日期选项
	 *+----------------------------------------------------------------------
	 * @param int $start_year
	 * @param bool $is_all
	 * @return array
	 *+----------------------------------------------------------------------
	 * @history: Modify record
	 * @author : Jeffry  2024/1/31 15:38:39
	 */
	function get_fast_date_type(int $start_year = 2014, bool $is_all = true): array
	{
		$s_date = [
			0 => '今日',
			1 => '昨日',
			2 => '3日内',
			3 => '7日内',
			4 => '15日内',
			5 => '本月内',
			6 => '一个月内',
			7 => '三个月内',
			8 => '半年内',
		];
		$now_year = date('Y');
		$total_year = $now_year - 2014;
		for ($i = 0; $i <= $total_year; $i++) {
			$n = $total_year - ($i * 2);
			$s_date[] = $start_year + $i + $n . '年';
		}
		$_list = [];
		foreach ($s_date as $key => $item) {
			$_list[] = [
				'name' => $item,
				'value' => $key,
			];
		}
		$is_all && array_unshift($_list, ['name' => '全部', 'value' => -1]);
		return $_list;
	}
}


if (!function_exists('mobile_hidden')) {
	/**
	 * description: 将手机号码中间四位用*代替显示（最终显示如下 133****8888）
	 *+----------------------------------------------------------------------
	 * @param int|string $mobile 手机号码
	 * @return string
	 *+----------------------------------------------------------------------
	 * @history: Modify record
	 * @author : Jeffry  2023/11/21 10:07:55
	 */
	function mobile_hidden(int|string $mobile): string
	{
		if (preg_match("/([\x81-\xfe][\x40-\xfe])/", $mobile)) {
			$mobile = substr_replace(del_space_str($mobile), '****', 3, 6);
		} else {
			$mobile = substr_replace(del_space_str($mobile), '****', 3, 4);
		}
		return  $mobile;
	}
}


if(!function_exists('update_batch_sql')){
	/**
	 * description: 获取mysql原生批量更新语句
	 *+----------------------------------------------------------------------
	 * @param string $tableName
	 * @param array $multipleData
	[
	['where_key'=>1022016,'update_filed_one'=>231,'update_filed_two'=>342],
	['where_key'=>1022231,'update_filed_one'=>125,'update_filed_two'=>567],
	['where_key'=>1021233,'update_filed_one'=>456,'update_filed_two'=>120],
	]
	 * @return string
	 *+----------------------------------------------------------------------
	 * @author: Admin  2023-07-27 14:23:04
	 * @access: public
	 * history: Modify record
	 */
	function update_batch_sql(string $tableName = "", array $multipleData = []): string
	{
		if( $tableName && !empty($multipleData) ) {
			$updateColumn = array_keys($multipleData[0]);
			$referenceColumn = $updateColumn[0];
			$referenceColumnType = is_string($referenceColumn);
			unset($updateColumn[0]);
			$whereIn = "";
			
			$q = "UPDATE ".$tableName." SET ";
			
			if($referenceColumnType){
				foreach ( $updateColumn as $uColumn ) {
					$q .=  $uColumn." = CASE ";
					
					foreach( $multipleData as $data ) {
						$q .= "WHEN ".$referenceColumn." = '".$data[$referenceColumn]."' THEN ".(is_string($data[$uColumn]) ? "'".$data[$uColumn]."' " : $data[$uColumn]." ");
					}
					$q .= "ELSE ".$uColumn." END, ";
				}
			}else{
				foreach ( $updateColumn as $uColumn ) {
					$q .=  $uColumn." = CASE ";
					
					foreach( $multipleData as $data ) {
						$q .= "WHEN ".$referenceColumn." = ".$data[$referenceColumn]." THEN ".(is_string($data[$uColumn]) ? "'".$data[$uColumn]."' " : $data[$uColumn]." ");
					}
					$q .= "ELSE ".$uColumn." END, ";
				}
			}
			
			if($referenceColumnType){
				foreach( $multipleData as $data ) {
					$whereIn .= "'".$data[$referenceColumn]."', ";
				}
			}else{
				foreach( $multipleData as $data ) {
					$whereIn .= $data[$referenceColumn].", ";
				}
			}
			return rtrim($q, ", ")." WHERE ".$referenceColumn." IN (".  rtrim($whereIn, ', ').")";
		} else {
			return '';
		}
	}
}


if (!function_exists('getCurrentDateWeek')) {
	/**
	 * description: 获取当前是周几
	 *+----------------------------------------------------------------------
	 * @param $date
	 * @return string
	 *+----------------------------------------------------------------------
	 * @history: Modify record
	 * @author : Jeffry  2024/1/31 15:45:46
	 */
	function getCurrentDateWeek($date) :string
	{
		$date = $date ?: date('Y-m-d');
		# 获取当前周的第几天 周日是 0 周一到周六是 1 - 6
		$w = date('w', strtotime($date));
		if ($w == 0) {
			$week = "星期日";
		} elseif ($w == 1) {
			$week = "星期一";
		} elseif ($w == 2) {
			$week = "星期二";
		} elseif ($w == 3) {
			$week = "星期三";
		} elseif ($w == 4) {
			$week = "星期四";
		} elseif ($w == 5) {
			$week = "星期五";
		} elseif ($w == 6) {
			$week = "星期六";
		} else{
			$week = "未知";
		}
		return $week;
	}
}








# 框架级公用自定义函数=====================================================================================================

if(!function_exists('clear_File_temp')) {
	/**
	 * description: 删除上传临时文件
	 *+----------------------------------------------------------------------
	 * @param   Request $request
	 * @return  void
	 *
	 *+----------------------------------------------------------------------
	 * @author: Admin  2023-06-15 15:44:06
	 * @access: public
	 * history: Modify record
	 */
	function clear_File_temp(Request $request): void
	{
		$tmp = $request->file('file')->getPathname();
		if (file_exists($tmp)) {
			unlink($tmp);
		}
	}
}


if(!function_exists('writeLog')){
	/**
	 * description: 记录日志
	 *+----------------------------------------------------------------------
	 * @param mixed     $content    日志内容
	 * @param string    $msg        提示信息
	 * @param $business_id
	 * @return void
	 *+----------------------------------------------------------------------
	 * @author: Admin  2022-10-17 10:28:59
	 * @access: public
	 * history: Modify record
	 */
	function writeLog(mixed $content, string $msg, $business_id): void
	{
		Log::write('【'.$business_id.'】'.$msg.PHP_EOL.var_export($content, true) . PHP_EOL . PHP_EOL);
	}
}


if(!function_exists('now')){
	/**
	 * description: 获取当前时间
	 *+----------------------------------------------------------------------
	 * @param   bool    $flag       是否格式化
	 * @return  Carbon|string
	 *+----------------------------------------------------------------------
	 * @author: Admin  2023-06-13 13:48:40
	 * @access: public
	 * history: Modify record
	 */
	function now(bool $flag = false): Carbon|string
	{
		return $flag ? date('Y-m-d H:i:s') : Carbon::now();
	}
}



if (!function_exists('format_date_horizon')) {
	
	/**
	 * description: 格式化时间范围
	 *+----------------------------------------------------------------------
	 * @param null $date 选取时间范围字段“key”下标值
	 *                                  时间格式1：[
	 *                                      0 => '2018-01-01',
	 *                                      1 => '2018-02-02',
	 *                                  ]
	 *                                  时间格式2
	 *                                  '2018-01-01 ~ 2018-02-02'
	 *                                  '2018-01-01 - 2018-02-02'
	 *
	 * @param null $date_horizon 下拉选择时间范围字段“key”下标值
	 *                                  时间格式：0|1|2|3...
	 *                                  0'今日创建'|1'3日内创建'|2'7日内创建'|3'15日内创建'
	 *                                  4'一个月内创建'|5'三个月内创建'|6'半年内创建'
	 *                                  7'2016年创建'|8'2017年创建'|....
	 *+----------------------------------------------------------------------
	 * @return array|string[]
	 * @history:    Modify record
	 * @author: Admin  dt
	 * @access: public
	 */
	function format_date_horizon($date = null, $date_horizon = null, $start_year = 2014): array
	{
		$start = ' 00:00:00';
		$end = ' 23:59:59';
		if (!empty($date)) {
			if (is_array($date) && $date[0] != 'undefined') {
				$params['s_date'] = trim($date[0]);
				$params['e_date'] = trim($date[1]);
			} else if (!is_array($date) && str_contains($date, ' - ')) {
				$temp_date = explode(' - ', $date);
				isset($temp_date[0]) && $params['s_date'] = trim($temp_date[0]);
				isset($temp_date[1]) && $params['e_date'] = trim($temp_date[1]);
			} else if (!is_array($date) && str_contains($date, ' ~ ')) {
				$temp_date = explode(' ~ ', $date);
				isset($temp_date[0]) && $params['s_date'] = trim($temp_date[0]);
				isset($temp_date[1]) && $params['e_date'] = trim($temp_date[1]);
			} else if (!is_array($date) && str_contains($date, ',')) {
				$temp_date = explode(',', $date);
				isset($temp_date[0]) && $params['s_date'] = trim($temp_date[0]);
				isset($temp_date[1]) && $params['e_date'] = trim($temp_date[1]);
			}
		} else if (isset($date_horizon) && ctype_digit((string)$date_horizon)) {
			switch ($date_horizon) {
				case 0:
					$params['s_date'] = date('Y-m-d');
					$params['e_date'] = date('Y-m-d');
					break;
				case 1:
					$params['s_date'] = Carbon::parse('yesterday')->startOfDay()->toDateTimeString();
					$params['e_date'] = Carbon::parse('yesterday')->endOfDay()->toDateTimeString();
					break;
				case 2:
					$params['s_date'] = date('Y-m-d', strtotime('-3 days'));
					$params['e_date'] = date('Y-m-d');
					break;
				case 3:
					$params['s_date'] = date('Y-m-d', strtotime('-7 days'));
					$params['e_date'] = date('Y-m-d');
					break;
				case 4:
					$params['s_date'] = date('Y-m-d', strtotime('-15 days'));
					$params['e_date'] = date('Y-m-d');
					break;
				case 5:
					$params['s_date'] = Carbon::now()->startOfMonth()->toDateTimeString();
					$params['e_date'] = Carbon::now()->endOfMonth()->toDateTimeString();
					break;
				case 6:
					$params['s_date'] = date('Y-m-d', strtotime('-1 month'));
					$params['e_date'] = date('Y-m-d');
					break;
				case 7:
					$params['s_date'] = date('Y-m-d', strtotime('-3 month'));
					$params['e_date'] = date('Y-m-d');
					break;
				case 8:
					$params['s_date'] = date('Y-m-d', strtotime('-6 month'));
					$params['e_date'] = date('Y-m-d');
					break;
				
				default :
					$now_year = date('Y');
					$total_year = $now_year - $start_year;
					for ($i = -1; $i <= $total_year; $i++) {
						if ($date_horizon == $i + 9) {
							$params['s_date'] = $now_year - $i . '-01-01';
							$params['e_date'] = $now_year - $i . '-12-31';
							break;
						}
					}
					break;
			}
		}
		
		if (isset($params['s_date']) && isset($params['e_date'])) {
			$params['s_date'] = substr($params['s_date'] . $start, 0, 19);
			$params['e_date'] = substr($params['e_date'] . $end, 0, 19);
		} else {
			$params = ['s_date' => '', 'e_date' => ''];
		}
		
		return $params;
	}
}
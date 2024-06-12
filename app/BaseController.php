<?php
declare (strict_types = 1);

namespace app;

use app\common\JsonResponse;
use Exception;
use rsa;
use think\App;
use think\exception\ValidateException;
use think\Validate;

/**
 * 控制器基础类
 */
abstract class BaseController
{
	use JsonResponse;
	/**
	 * Request实例
	 * @var \think\Request
	 */
	protected \think\Request $request;
	
	/**
	 * 应用实例
	 * @var App
	 */
	protected App $app;
	
	/**
	 * 是否批量验证
	 * @var bool
	 */
	protected bool $batchValidate = false;
	
	/**
	 * 控制器中间件
	 * @var array
	 */
	protected array $middleware = [];
	
	/**
	 * 请求参数
	 * @var array|mixed|null
	 */
	protected mixed $params;
	
	/**
	 * 用户信息
	 * @var array
	 */
	protected mixed $userinfo;
	
	/**
	 * 构造方法
	 * @access public
	 * @param  App  $app  应用对象
	 * @noinspection PhpUndefinedFieldInspection
	 */
	public function __construct(App $app)
	{
		$this->app      = $app;
		$this->request  = $this->app->request;
		$this->params   = $this->app->request->param();
		$this->userinfo =  $this->app->request->userinfo;
		$this->millisecond  = millisecond();
		
		// 控制器初始化
		$this->initialize();
	}
	
	// 初始化
	protected function initialize()
	{}
	
	/**
	 * 验证数据
	 * @access protected
	 * @param  array        $data     数据
	 * @param  string|array $validate 验证器名或者验证规则数组
	 * @param  array        $message  提示信息
	 * @param  bool         $batch    是否批量验证
	 * @return array|string|true
	 * @throws ValidateException
	 */
	protected function validate(array $data, string|array $validate, array $message = [], bool $batch = false): bool|array|string
	{
		if (is_array($validate)) {
			$v = new Validate();
			$v->rule($validate);
		} else {
			if (strpos($validate, '.')) {
				// 支持场景
				[$validate, $scene] = explode('.', $validate);
			}
			$class = str_contains($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
			$v     = new $class();
			if (!empty($scene)) {
				$v->scene($scene);
			}
		}
		
		$v->message($message);
		
		// 是否批量验证
		if ($batch || $this->batchValidate) {
			$v->batch();
		}
		
		return $v->failException()->check($data);
	}
	
	
	/**
	 * description: 验证器
	 *+----------------------------------------------------------------------
	 * @param $type
	 * @return void
	 *+----------------------------------------------------------------------
	 * @throws Exception
	 * @author: Admin  2021-11-03 13:57:23
	 * @access: public
	 * history: Modify record
	 */
	protected function verifier($type): void
	{
		try {
			$code = match ($type) {
				'ip' => $this->verifierIp(),
				'sign' => $this->verifySign(),
				default => 2000,
			};
			$log_msg= $this->code[$code]['explain'];
		}catch (Exception $e){
			$code   = 2001;
			$log_msg= $e->getMessage().$e->getFile().$e->getLine();
		} finally {
			# 记录日志
			writeLog($this->params, $log_msg, getNullBusinessId());
		}
		
		if($code != 2000){
			header('HTTP/1.1 400 Bad Request');
			exit;
		}
		
	}
	
	
	/**
	 * description: 签名验证
	 *+----------------------------------------------------------------------
	 * @return int
	 * @throws Exception
	 *+----------------------------------------------------------------------
	 * @author: Admin  2021-11-03 13:57:32
	 * @access: public
	 * history: Modify record
	 */
	protected function verifySign(): int
	{
		# sign参数验证
		$sign = $this->request->header('sign');
		if(!$sign) {
			return 3001;
		}
		
		$rsa_obj = new rsa(env('RSA.OPENSSL_CNF_PATH'));
		$rsa_obj->init(env('RSA.MOOR_PUBLIC_KEY'), env('RSA.MOOR_PRIVATE_KEY'));
		
		# 解密
		$decrypt_str = $rsa_obj->decrypt($sign);
		if(!$decrypt_str){
			return 3002;
		}
		
		# 参数验证
		parse_str($decrypt_str, $decrypt_arr);
		
		if(!isset($decrypt_arr['name']) || !in_array($decrypt_arr['name'], explode(',', env('RSA.RSA_APP_NAME')))){
			return 3003;
		}
		
		if(!isset($decrypt_arr['time']) || !ctype_digit((string) $decrypt_arr['time'])){
			return 3004;
		}
		
		if(($decrypt_arr['time'] > $this->millisecond) || (($decrypt_arr['time']+(1000*60*env('RSA.SIGN_VALID_TIME'))) < $this->millisecond)){
			return 3005;
		}
		
		return 2000;
	}
	
	
	/**
	 * description: IP白名单验证
	 *+----------------------------------------------------------------------
	 * @return int
	 *+----------------------------------------------------------------------
	 * @author: Admin  2023-04-10 10:42:19
	 * @access: public
	 * history: Modify record
	 */
	protected function verifierIp(): int
	{
		return !in_array($this->params['source_ip'], explode(',', env('IP.ALLOW_IP'))) ? 3006 : 2000;
	}
}

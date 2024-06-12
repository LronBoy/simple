<?php
declare (strict_types = 1);

namespace app\controller;

use app\BaseController;
use think\App;
use think\Request;
use think\response\Json;

class OpenApi extends BaseController
{
	
	public function __construct(App $app)
	{
		parent::__construct($app);
	}
	
    /**
     * 显示资源列表
     */
    public function xunSou(): Json
    {
		return $this->success();
    }
}

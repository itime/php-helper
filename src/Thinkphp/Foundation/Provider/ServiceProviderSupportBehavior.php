<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Foundation\Provider;

use think\App;

/**
 * Class ServiceProviderSupportBehavior
 * 服务提供者 ThinkPHP6 过渡行为
 *
 * @package app\common\behavior
 */
class ServiceProviderSupportBehavior{
	
	/**
	 * @var \think\App
	 */
	private $app;
	
	/**
	 * ServiceBehavior constructor.
	 *
	 * @param \think\App $app
	 */
	public function __construct(App $app){
		$this->app = $app;
	}
	
	/**
	 * 执行操作
	 *
	 * @throws \think\Exception
	 */
	public function run(){
		$appPath = $this->app->getAppPath();
		$serviceFile = realpath($appPath.DIRECTORY_SEPARATOR."service.php");
		if(!$serviceFile){
			return;
		}
		
		/** @noinspection PhpIncludeInspection */
		$services = require_once $serviceFile;
		$instances = [];
		
		// register
		foreach($services as $service){
			$instances[$service] = $instance = new $service();
			if(method_exists($instance, "register")){
				call_user_func([$instance, "register"]);
			}
		}
		
		// boot
		foreach($instances as $instance){
			if(method_exists($instance, "boot")){
				$this->app->invokeMethod([$instance, "boot"]);
			}
		}
	}
}

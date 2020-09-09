<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Plugin;

use think\App;
use think\exception\HttpException;
use Xin\Plugin\AbstractPluginManager;
use Xin\Support\Arr;

class PluginManager extends AbstractPluginManager{
	
	/**
	 * @var \think\App
	 */
	protected $app;
	
	/**
	 * @var array
	 */
	protected $config;
	
	/**
	 * PluginManager constructor.
	 *
	 * @param \think\App $app
	 * @param array      $config
	 */
	public function __construct(App $app, array $config){
		parent::__construct($config);
		
		$this->app = $app;
	}
	
	/**
	 * @inheritDoc
	 */
	public function invoke($request, $plugin, $controller, $action){
		if(!$this->has($plugin)){
			throw new HttpException(404, "plugin {$plugin} not exist.");
		}
		
		$appName = $this->app->http->getName();
		
		$controllerLayer = 'controller';
		if($appName != $this->getDefaultAppName()){
			$controllerLayer = "{$appName}controller";
		}
		
		$class = $this->controllerClass($plugin, $controller, $controllerLayer);
		if(!class_exists($class)){
			throw new HttpException(404, "controller {$class} not exist.");
		}
		
		$request->setPlugin($plugin);
		$request->setController($controller);
		$request->setAction($action);
		
		return $this->app->invoke([$class, $action]);
	}
	
	/**
	 * 默认的应用名称
	 *
	 * @return string
	 */
	public function getDefaultAppName(){
		return Arr::get($this->config, 'default.app_name', 'admin');
	}
	
}

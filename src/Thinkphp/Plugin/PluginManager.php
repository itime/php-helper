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
	public function invoke($request, $pluginName, $controller, $action){
		if(!$this->has($pluginName)){
			throw new HttpException(404, "plugin {$pluginName} not exist.");
		}
		
		$appName = $this->app->http->getName();
		
		$controllerLayer = 'controller';
		if($appName != $this->getDefaultAppName()){
			$controllerLayer = "{$appName}controller";
		}
		
		$class = $this->controllerClass($pluginName, $controller, $controllerLayer);
		if(!class_exists($class)){
			throw new HttpException(404, "controller {$class} not exist.");
		}
		
		if($appName != "api"){
			$this->initView($appName, $pluginName);
		}
		
		$request->setPlugin($pluginName);
		$request->setController($controller);
		$request->setAction($action);
		
		return $this->app->invoke([$class, $action]);
	}
	
	/**
	 * 初始化视图
	 *
	 * @param string $appName
	 * @param string $pluginName
	 */
	protected function initView($appName, $pluginName){
		/** @var \think\View $view */
		$view = $this->app->make('view');
		
		//		/** @var \think\view\driver\Think $driver */
		//		$driver = $view->engine();
		//		$template = new Template();
		//		$ref = new \ReflectionProperty($driver, "template");
		//		$ref->setAccessible(true);
		//		$ref->setValue();
		
		$viewPath = $this->path($pluginName)."{$appName}view".DIRECTORY_SEPARATOR;
		$view->engine()->config([
			"view_dir_name" => "{$appName}view",
			"view_path"     => $viewPath,
		]);
		
		// layout view path
		$layoutPath = app_path('view')."layout.html";
		//		halt($layoutPath);
		$view->assign('__layout__', $layoutPath);
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

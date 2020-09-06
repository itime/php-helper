<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Plugin;

use think\App;
use think\exception\HttpException;
use think\helper\Str;
use think\Request;
use Xin\Support\Arr;

class PluginManager{
	
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
		$this->app = $app;
		$this->config = $config;
	}
	
	/**
	 * 插件是否存在
	 *
	 * @param string $plugin
	 * @return bool
	 */
	public function has($plugin){
		$path = $this->path($plugin);
		return is_dir($path);
	}
	
	/**
	 * 获取插件目录
	 *
	 * @param string $plugin
	 * @return string
	 */
	public function path($plugin){
		return root_path("plugin".DIRECTORY_SEPARATOR.$plugin);
	}
	
	/**
	 * 获取插件下的类路径
	 *
	 * @param string $classPath
	 * @return string
	 */
	public function classPath($classPath){
		return "\\plugin\\{$classPath}";
	}
	
	/**
	 * 获取插件下的控制器路径
	 *
	 * @param string $plugin
	 * @param string $controller
	 * @param string $layer
	 * @return string
	 */
	public function controllerPath($plugin, $controller, $layer = 'controller'){
		$controller = Str::studly($controller);
		return $this->classPath("{$plugin}\\{$layer}\\{$controller}Controller");
	}
	
	/**
	 * 调用插件操作
	 *
	 * @param \think\Request|\Xin\Thinkphp\Http\RequestOptimize $request
	 * @param string                                            $plugin
	 * @param string                                            $controller
	 * @param string                                            $action
	 * @return mixed
	 */
	public function invoke(Request $request, $plugin, $controller, $action){
		if(!$this->has($plugin)){
			throw new HttpException(404, "plugin {$plugin} not exist.");
		}
		
		$appName = $this->app->http->getName();
		
		$controllerLayer = 'controller';
		if($appName != $this->getDefaultAppName()){
			$controllerLayer = "{$appName}controller";
		}
		
		$class = $this->controllerPath($plugin, $controller, $controllerLayer);
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

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
use Xin\Contracts\Plugin\Factory as PluginFactory;
use Xin\Support\Arr;

class PluginManager implements PluginFactory{
	
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
	 * @inheritDoc
	 */
	public function has($plugin){
		return class_exists($this->pluginClass($plugin, "Plugin"));
	}
	
	/**
	 * @inheritDoc
	 */
	public function lists(){
		$it = new \FilesystemIterator($this->rootPath());
		
		$plugins = [];
		foreach($it as $file){
			if(!$file->isDir()){
				continue;
			}
			
			$name = $file->getFilename();
			if(!$this->has($name)){
				continue;
			}
			
			$plugins[$name] = $this->pluginClass($name, "Plugin");
		}
		
		return new PlugLazyCollection($this, $plugins);
	}
	
	/**
	 * @inheritDoc
	 */
	public function boot(){
		$plugins = $this->lists();
		foreach($plugins as $plugin){
			$pluginClass = $this->pluginClass($plugin, "Plugin");
			if(!class_exists($pluginClass)){
				continue;
			}
			
			$this->plugin($plugin)->boot();
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function plugin($plugin){
		$class = $this->pluginClass($plugin, "Plugin");
		if(!class_exists($class)){
			throw new PluginNotFoundException($plugin);
		}
		
		return new $class();
	}
	
	/**
	 * @inheritDoc
	 */
	public function pluginClass($plugin, $class){
		return "\\".$this->rootNamespace()."\\{$plugin}\\{$class}";
	}
	
	/**
	 * 获取插件下的控制器路径
	 *
	 * @param string $plugin
	 * @param string $controller
	 * @param string $layer
	 * @return string
	 */
	public function controllerClass($plugin, $controller, $layer = 'controller'){
		$controller = Str::studly($controller);
		return $this->pluginClass($plugin, "{$layer}\\{$controller}Controller");
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
	 * @inheritDoc
	 */
	public function rootPath($path = ''){
		return $this->config['path'].($path ? $path.DIRECTORY_SEPARATOR : $path);
	}
	
	/**
	 * @inheritDoc
	 */
	public function path($plugin){
		return $this->rootPath($plugin);
	}
	
	/**
	 * 默认命名空间
	 *
	 * @return string
	 */
	public function rootNamespace(){
		return Arr::get($this->config, 'namespace', 'plugin');
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

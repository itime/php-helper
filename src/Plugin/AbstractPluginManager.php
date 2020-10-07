<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Plugin;

use think\helper\Str;
use Xin\Contracts\Plugin\Factory as PluginFactory;
use Xin\Support\Arr;

abstract class AbstractPluginManager implements PluginFactory{
	
	/**
	 * @var array
	 */
	protected $config;
	
	/**
	 * AbstractPluginManager constructor.
	 *
	 * @param array $config
	 */
	public function __construct(array $config){
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
		$plugins = $this->lists()->lists();
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
	 * @inheritDoc
	 */
	public function controllerClass($plugin, $controller, $layer = 'controller'){
		$controller = Str::studly($controller);
		return $this->pluginClass($plugin, "{$layer}\\{$controller}Controller");
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
	 * @inheritDoc
	 */
	public function config($name, $default = null){
		return Arr::get($this->config, $name, $default);
	}
}

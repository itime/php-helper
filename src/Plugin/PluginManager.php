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

class PluginManager implements PluginFactory{
	
	/**
	 * @var array
	 */
	protected $config;
	
	/**
	 * @var array
	 */
	protected $pluginInfos = [];
	
	/**
	 * @var array
	 */
	protected $pluginBoots = [];
	
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
	public function rootPath($path = ''){
		return $this->config['path'].($path ? $path.DIRECTORY_SEPARATOR : $path);
	}
	
	/**
	 * @inheritDoc
	 */
	public function lists(){
		return new PlugLazyCollection($this);
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
	public function installPlugin($plugin){
		$pluginInfo = $this->pluginInfo($plugin);
		$this->plugin($plugin)->install($pluginInfo, $this);
	}
	
	/**
	 * @inheritDoc
	 */
	public function uninstallPlugin($plugin){
		$pluginInfo = $this->pluginInfo($plugin);
		$this->plugin($plugin)->uninstall($pluginInfo, $this);
	}
	
	/**
	 * @inheritDoc
	 * @throws \Xin\Contracts\Plugin\PluginNotFoundException
	 */
	public function pluginBoot(array $plugins = []){
		foreach($plugins as $plugin){
			if(isset($this->pluginBoots[$plugin])){
				continue;
			}
			
			$this->pluginBoots[$plugin] = true;
			$pluginInfo = $this->pluginInfo($plugin);
			$this->plugin($plugin)->boot($pluginInfo, $this);
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function plugin($plugin){
		if(isset($this->pluginBoots[$plugin])){
			return $this->pluginBoots[$plugin];
		}
		
		$class = $this->pluginClass($plugin, "Plugin");
		if(!class_exists($class)){
			throw new PluginNotFoundException($plugin);
		}
		
		return $this->pluginBoots[$plugin] = new $class();
	}
	
	/**
	 * @inheritDoc
	 */
	public function pluginInfo($plugin){
		if(isset($this->pluginInfos[$plugin])){
			return $this->pluginInfos[$plugin];
		}
		
		return $this->pluginInfos[$plugin] = new PluginInfo($plugin, $this);
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
	public function pluginPath($plugin){
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

<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Plugin;

use Xin\Contracts\Plugin\Plugin as PluginContract;

abstract class AbstractPlugin implements PluginContract{
	
	/**
	 * @var array
	 */
	protected $config = [];
	
	/**
	 * @inheritDoc
	 */
	public function getExtraConfig($name){
		return $this->loadConfig($name);
	}
	
	/**
	 * @inheritDoc
	 */
	public function install(){
	}
	
	/**
	 * @inheritDoc
	 */
	public function uninstall(){
	}
	
	/**
	 * @inheritDoc
	 */
	public function boot(){
	}
	
	/**
	 * @inheritDoc
	 */
	public function pluginPath($path = ''){
		return basename(__DIR__).($path ? $path.DIRECTORY_SEPARATOR : $path);
	}
	
	/**
	 * 加载配置信息
	 *
	 * @param string $name
	 * @return mixed
	 * @noinspection PhpIncludeInspection
	 */
	protected function loadConfig($name){
		if(!isset($this->config[$name])){
			$path = $this->pluginPath("config".DIRECTORY_SEPARATOR.$name."php");
			if(file_exists($path)){
				$this->config[$name] = require_once $path;
			}
		}
		
		return isset($this->config[$name]) ? $this->config[$name] : null;
	}
}

<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Filesystem;

use Xin\Support\Arr;

class FilesystemManager{
	
	/**
	 * @var array
	 */
	protected $config = [];
	
	/**
	 * FilesystemManager constructor.
	 *
	 * @param array $config
	 */
	public function __construct(array $config){
		$this->config = $config;
	}
	
	/**
	 * @param null|string $name
	 * @return \Xin\Filesystem\Filesystem
	 */
	public function disk(string $name = null){
		return $this->driver($name);
	}
	
	/**
	 * 获取驱动实例
	 *
	 * @param null|string $name
	 * @return mixed
	 */
	protected function driver(string $name = null){
		$name = $name ?: $this->getDefaultDriver();
		
		if(is_null($name)){
			throw new \InvalidArgumentException(sprintf(
				'Unable to resolve NULL driver for [%s].', static::class
			));
		}
		
		return $this->drivers[$name] = $this->getDriver($name);
	}
	
	/**
	 * 获取驱动实例
	 *
	 * @param string $name
	 * @return mixed
	 */
	protected function getDriver(string $name){
		return $this->drivers[$name] ?? $this->createDriver($name);
	}
	
	/**
	 * 获取缓存配置
	 *
	 * @access public
	 * @param null|string $name 名称
	 * @param mixed       $default 默认值
	 * @return mixed
	 */
	public function getConfig(string $name = null, $default = null){
		if(!is_null($name)){
			return Arr::get(
				$this->config,
				$name,
				$default
			);
		}
		
		return $this->config;
	}
	
	/**
	 * 获取磁盘配置
	 *
	 * @param string $disk
	 * @param null   $name
	 * @param null   $default
	 * @return array
	 */
	public function getDiskConfig($disk, $name = null, $default = null){
		if($config = $this->getConfig("disks.{$disk}")){
			return Arr::get($config, $name, $default);
		}
		
		throw new \InvalidArgumentException("Disk [$disk] not found.");
	}
	
	/**
	 * 默认驱动
	 *
	 * @return string|null
	 */
	public function getDefaultDriver(){
		return $this->getConfig('default');
	}
	
	/**
	 * 动态调用
	 *
	 * @param string $method
	 * @param array  $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters){
		return $this->driver()->$method(...$parameters);
	}
}

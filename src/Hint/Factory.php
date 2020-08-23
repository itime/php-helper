<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Hint;

use Psr\Container\ContainerInterface;
use Xin\Contracts\Hint\Factory as HintFactory;

class Factory implements HintFactory{
	
	/**
	 * @var \Psr\Container\ContainerInterface
	 */
	protected $container;
	
	/**
	 * @var string
	 */
	protected $default;
	
	/**
	 * 提示器
	 *
	 * @var array
	 */
	protected $hints = [];
	
	/**
	 * 创建器列表
	 *
	 * @var array
	 */
	private $customCreators = [];
	
	/**
	 * Factory constructor.
	 *
	 * @param \Psr\Container\ContainerInterface $container
	 */
	public function __construct(ContainerInterface $container){
		$this->container = $container;
	}
	
	/**
	 * 获取提示器
	 *
	 * @param string $name
	 * @return \Xin\Contracts\Hint\Hint
	 */
	public function hint($name = null){
		$name = $name ?: $this->getDefaultDriver();
		
		return $this->hints[$name] ?? $this->hints[$name] = $this->resolve($name);
	}
	
	/**
	 * 强制使用Api模式
	 *
	 * @return $this|\Xin\Hint\Factory
	 */
	public function shouldUseApi(){
		$this->setDefaultDriver('api');
		return $this;
	}
	
	/**
	 * 强制使用Web模式
	 *
	 * @return $this|\Xin\Hint\Factory
	 */
	public function shouldUseWeb(){
		$this->setDefaultDriver('web');
		return $this;
	}
	
	/**
	 * 解决给定的提示器
	 *
	 * @param string $name
	 * @return \Xin\Contracts\Hint\Hint
	 */
	protected function resolve($name){
		if(isset($this->customCreators[$name])){
			return $this->callCustomCreator($name);
		}
		
		$driverMethod = 'create'.ucfirst($name).'Driver';
		if(method_exists($this, $driverMethod)){
			return $this->{$driverMethod}();
		}
		
		throw new \InvalidArgumentException(
			"Hint driver [{$name}] is not defined."
		);
	}
	
	/**
	 * 调用自定义创建器
	 *
	 * @param string $name
	 * @return \Xin\Contracts\Hint\Hint
	 */
	protected function callCustomCreator($name){
		return $this->customCreators[$name]($this->container);
	}
	
	/**
	 * 自定义一个创建器
	 *
	 * @param string   $driver
	 * @param \Closure $callback
	 * @return $this
	 */
	public function extend($driver, \Closure $callback){
		$this->customCreators[$driver] = $callback;
		
		return $this;
	}
	
	/**
	 * 获取默认驱动
	 *
	 * @return string
	 */
	public function getDefaultDriver(){
		return $this->default;
	}
	
	/**
	 * 设置默认驱动
	 *
	 * @param string $name
	 */
	public function setDefaultDriver($name){
		$this->default = $name;
	}
	
	/**
	 * Dynamically call the default driver instance.
	 *
	 * @param string $name
	 * @param array  $arguments
	 * @return mixed
	 */
	public function __call($name, $arguments){
		return call_user_func_array([$this->hint(), $name], $arguments);
	}
	
}

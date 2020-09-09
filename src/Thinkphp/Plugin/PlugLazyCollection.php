<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Plugin;

use Xin\Contracts\Plugin\Factory as PluginFactory;
use Xin\Contracts\Plugin\PlugLazyCollection as PlugLazyCollectionContract;

class PlugLazyCollection implements \ArrayAccess, PlugLazyCollectionContract{
	
	/**
	 * @var PluginFactory
	 */
	protected $factory;
	
	/**
	 * @var array
	 */
	protected $plugins;
	
	/**
	 * PlugLazyCollection constructor.
	 *
	 * @param \Xin\Contracts\Plugin\Factory $factory
	 * @param array                         $plugins
	 */
	public function __construct(PluginFactory $factory, array $plugins){
		$this->factory = $factory;
		$this->plugins = $plugins;
	}
	
	/**
	 * 插件是否存在
	 *
	 * @param string $offset
	 * @return bool
	 */
	public function offsetExists($offset){
		return isset($this->plugins[$offset]);
	}
	
	/**
	 * 获取插件
	 *
	 * @param string $offset
	 * @return \Xin\Contracts\Plugin\Plugin
	 * @throws \Xin\Thinkphp\Plugin\PluginNotFoundException
	 */
	public function offsetGet($offset){
		return $this->resolve(
			$this->plugins[$offset]
		);
	}
	
	/**
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value){
		throw new \RuntimeException('not allow set.');
	}
	
	/**
	 * @param mixed $offset
	 */
	public function offsetUnset($offset){
		throw new \RuntimeException('not allow unset.');
	}
	
	/**
	 * @inheritDoc
	 */
	public function plugin($plugin){
		if(!isset($this->plugins[$plugin]) || empty($this->plugins[$plugin])){
			throw new PluginNotFoundException($plugin);
		}
		
		return $this->factory->plugin($plugin);
	}
	
	public function lists(){
		// TODO: Implement lists() method.
	}
}

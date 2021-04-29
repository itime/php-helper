<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Plugin;

use Xin\Contracts\Plugin\Factory as PluginFactory;
use Xin\Contracts\Plugin\PlugLazyCollection as PlugLazyCollectionContract;

class PlugLazyCollection implements \ArrayAccess, \Iterator, PlugLazyCollectionContract{

	/**
	 * @var PluginFactory
	 */
	protected $factory;

	/**
	 * @var array
	 */
	protected $plugins = [];

	/**
	 * @var \FilesystemIterator
	 */
	protected $fileIterator;

	/**
	 * @var \Xin\Contracts\Plugin\PluginInfo
	 */
	protected $current;

	/**
	 * @var int
	 */
	protected $key = 0;

	/**
	 * @var bool
	 */
	protected $isValid = true;

	/**
	 * PlugLazyCollection constructor.
	 *
	 * @param \Xin\Contracts\Plugin\Factory $factory
	 */
	public function __construct(PluginFactory $factory){
		$this->factory = $factory;
		$this->fileIterator = new \FilesystemIterator($factory->rootPath());
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
	 * @return \Xin\Contracts\Plugin\PluginInfo
	 * @throws \Xin\Contracts\Plugin\PluginNotFoundException
	 */
	public function offsetGet($offset){
		return $this->plugin($offset);
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

	/**
	 * @inheritDoc
	 */
	public function all(){
		while($plugin = $this->next()){
		}
		return $this->plugins;
	}

	/**
	 * @inheritDoc
	 */
	public function current(){
		return $this->current;
	}

	/**
	 * @inheritDoc
	 */
	public function next(){
		foreach($this->fileIterator as $file){
			if(!$file->isDir()){
				continue;
			}

			$name = $file->getFilename();
			if(!$this->factory->has($name)){
				continue;
			}

			$plugins[$name] = $this->factory->pluginClass($name, "Plugin");
		}

		return null;
	}

	/**
	 * @inheritDoc
	 */
	public function key(){
		return $this->key;
	}

	/**
	 * @inheritDoc
	 */
	public function valid(){
		return $this->isValid;
	}

	/**
	 * @inheritDoc
	 */
	public function rewind(){
		$this->plugins = [];
		reset($this->plugins);
	}
}

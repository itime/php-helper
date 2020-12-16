<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Menu;

use Xin\Support\Arr;
use Xin\Support\Manager;

/**
 * Class MenuManager
 * @method \Xin\Contracts\Menu\Writer driver($driver = null)
 * @method array|\iterable get($menu)
 * @method bool put($menu)
 * @method bool puts($menus)
 * @method bool forget($name)
 */
class MenuManager extends Manager{
	
	/**
	 * @var array
	 */
	protected $customGenerator = [];
	
	/**
	 * @var array
	 */
	protected $config = [];
	
	/**
	 * MenuManager constructor.
	 *
	 * @param mixed $app
	 * @param array $config
	 */
	public function __construct($app, array $config){
		parent::__construct($app);
		$this->config = $config;
	}
	
	/**
	 * @param string $name
	 * @return \Xin\Contracts\Menu\Writer
	 */
	public function menu($name = null){
		return $this->driver($name);
	}
	
	/**
	 * @param string $name
	 */
	public function shouldUse($name){
		$name = $name ?: $this->getDefaultDriver();
		
		$this->setDefaultDriver($name);
	}
	
	/**
	 * 设置默认的菜单器
	 *
	 * @param string $name
	 */
	public function setDefaultDriver($name){
		Arr::set($this->config, 'defaults.menu', $name);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getDefaultDriver(){
		return $this->getConfig('defaults.menu', 'admin');
	}
	
	/**
	 * @inheritDoc
	 */
	protected function resolveType($name){
		return $this->getMenuConfig($name, 'type', 'phpfile');
	}
	
	/**
	 * @inheritDoc
	 */
	protected function resolveConfig($name){
		return $this->getMenuConfig($name);
	}
	
	/**
	 * 获取菜单配置
	 *
	 * @param string $menu
	 * @param null   $name
	 * @param null   $default
	 * @return array
	 */
	public function getMenuConfig($menu, $name = null, $default = null){
		if($config = $this->getConfig("menus.{$menu}")){
			return Arr::get($config, $name, $default);
		}
		
		throw new \InvalidArgumentException("Menu [$menu] not found.");
	}
	
	/**
	 * 菜单配置是否存在
	 *
	 * @param string $name
	 * @return bool
	 */
	public function hasMenuConfig($name){
		return Arr::has($this->config, 'menus.'.$name);
	}
	
	/**
	 * 获取缓存配置
	 *
	 * @access public
	 * @param null|string $name 名称
	 * @param mixed       $default 默认值
	 * @return mixed
	 */
	public function getConfig($name = null, $default = null){
		if(is_null($name)){
			return $this->config;
		}
		
		return Arr::get($this->config, $name, $default);
	}
	
	/**
	 * 创建PHP文件菜单器
	 *
	 * @param array $config
	 * @return \Xin\Menu\PhpFile
	 */
	public function createPhpFileDriver(array $config){
		return new PhpFile($config);
	}
	
	/**
	 * @param mixed $user
	 * @param array $options
	 * @return array
	 */
	public function generate($user, array $options = []){
		$menus = $this->driver()->get($user);
		$generator = new Generator();
		return $generator->generate($menus, $options);
	}
	
	/**
	 * 扩展生成器
	 *
	 * @param string $name
	 * @param mixed  $callback
	 */
	public function generator($name, $callback){
		$this->customGenerator[$name] = $callback;
	}
	
	/**
	 * 调用生成器
	 *
	 * @param string $name
	 * @param array  $params
	 * @return false|mixed
	 */
	protected function callCustomGenerator($name, $params = []){
		return call_user_func_array(
			$this->customCreators[$name],
			$params
		);
	}
	
}

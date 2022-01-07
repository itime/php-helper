<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Menu;

use Xin\Contracts\Menu\Factory;
use Xin\Support\Arr;
use Xin\Support\Manager;

/**
 * Class MenuManager
 * @method \Xin\Contracts\Menu\Repository driver($driver = null)
 * @method array|\iterable all()
 * @method array|\iterable get($filter = null)
 * @method bool puts($menus, $app = null, $append = [])
 * @method bool forget($condition)
 */
class MenuManager extends Manager implements Factory {

	/**
	 * @var array
	 */
	protected $config = [];

	/**
	 * @var array
	 */
	protected $customGenerator = [];

	/**
	 * MenuManager constructor.
	 *
	 * @param mixed $app
	 * @param array $config
	 */
	public function __construct($app, array $config) {
		parent::__construct($app);

		$this->config = $config;

		$this->generator('default', Generator::class);
	}

	/**
	 * 获取菜单器实例
	 *
	 * @param string $name
	 * @return \Xin\Contracts\Menu\Repository
	 */
	public function menu($name = null) {
		return $this->driver($name);
	}

	/**
	 * @param string $name
	 */
	public function shouldUse($name) {
		$name = $name ?: $this->getDefaultDriver();

		$this->setDefaultDriver($name);
	}

	/**
	 * 设置默认的菜单器
	 *
	 * @param string $name
	 */
	public function setDefaultDriver($name) {
		Arr::set($this->config, 'defaults.menu', $name);
	}

	/**
	 * @inheritDoc
	 */
	public function getDefaultDriver() {
		return $this->getConfig('defaults.menu', 'admin');
	}

	/**
	 * @inheritDoc
	 */
	protected function resolveType($name) {
		return $this->getMenuConfig($name, 'type', 'phpfile');
	}

	/**
	 * @inheritDoc
	 */
	protected function resolveConfig($name) {
		return $this->getMenuConfig($name);
	}

	/**
	 * 获取菜单配置
	 *
	 * @param string $menu
	 * @param null   $name
	 * @param null   $default
	 * @return mixed
	 */
	public function getMenuConfig($menu, $name = null, $default = null) {
		if ($config = $this->getConfig("menus.{$menu}")) {
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
	public function hasMenuConfig($name) {
		return Arr::has($this->config, 'menus.' . $name);
	}

	/**
	 * 获取缓存配置
	 *
	 * @access public
	 * @param null|string $name 名称
	 * @param mixed       $default 默认值
	 * @return mixed
	 */
	public function getConfig($name = null, $default = null) {
		if (is_null($name)) {
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
	public function createPhpFileDriver(array $config) {
		return new PhpFile($config);
	}

	/**
	 * 生成菜单
	 *
	 * @param callable $filter
	 * @param array    $options
	 * @return array
	 */
	public function generate($filter, array $options = []) {
		$generator = $this->resolveGenerator();

		$menus = $this->driver()->get($filter);
		if (method_exists($menus, 'toArray')) {
			$menus = $menus->toArray();
		}

		return $generator->generate($menus, $options);
	}

	/**
	 * 扩展生成器
	 *
	 * @param string $name
	 * @param mixed  $callback
	 */
	public function generator($name, $callback) {
		$this->customGenerator[$name] = $callback;
	}

	/**
	 * 调用生成器
	 *
	 * @param string $name
	 * @return \Xin\Contracts\Menu\Generator
	 */
	protected function resolveGenerator($name = null) {
		$name = $name ?: $this->getDefaultDriver();
		$generatorType = $this->getMenuConfig($name, 'generator', 'default');

		if (!isset($this->customGenerator[$generatorType])) {
			throw new \RuntimeException("Menu Generator[{$generatorType}] not defined.");
		}

		$generator = $this->customGenerator[$generatorType];

		if (!is_object($generator)) {
			//if(is_callable($generator)){
			//	$this->customGenerator[$generator] = $generator();
			//}else{
			//	$this->customGenerator[$generator] = new $generator();
			//}
			$this->customGenerator[$generatorType] = $generator = new $generator();
		}

		return $generator;
	}

}

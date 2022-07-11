<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Menu;

use Xin\Contracts\Menu\Repository;
use Xin\Support\Arr;

abstract class Driver implements Repository
{

	/**
	 * @var $config
	 */
	protected $config;

	/**
	 * Driver constructor.
	 *
	 * @param $config
	 */
	public function __construct($config)
	{
		$this->config = $config;
	}

	/**
	 * 获取配置
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return array|\ArrayAccess|mixed
	 */
	public function config($key, $default = null)
	{
		if (empty($key)) {
			return $this->config;
		}

		return Arr::get($this->config, $key, $default);
	}

	/**
	 * 遍历树形数据
	 *
	 * @param callable $callback
	 * @param array $menus
	 * @param mixed $parent
	 */
	protected static function eachTree($callback, &$menus, &$parent = null)
	{
		foreach ($menus as &$menu) {
			call_user_func_array($callback, [&$menu, &$parent]);
			if (isset($menu['child'])) {
				self::eachTree($callback, $menu['child'], $menu);
			}
		}
		unset($menu);
	}

	/**
	 * 遍历删除菜单
	 *
	 * @param callable $callback
	 * @param array $menus
	 */
	public static function eachTreeFilter($callback, &$menus)
	{
		foreach ($menus as $key => &$menu) {
			if (call_user_func_array($callback, [$menu]) === true) {
				unset($menus[$key]);
			} elseif (isset($menu['child'])) {
				self::eachTreeFilter($callback, $menu['child']);
			}
		}
		unset($menu);
	}

}

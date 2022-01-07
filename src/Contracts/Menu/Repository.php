<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Menu;

interface Repository {

	/**
	 * 获取所有菜单
	 *
	 * @return mixed
	 */
	public function all();

	/**
	 * 获取菜单
	 *
	 * @param callable $filter
	 * @return mixed
	 */
	public function get($filter = null);

	/**
	 * 写入一组菜单
	 *
	 * @param array  $menus
	 * @param string $plugin
	 * @param array  $append
	 * @return bool
	 */
	public function puts($menus, $app = null, $append = []);

	/**
	 * 移除菜单
	 *
	 * @param mixed $condition
	 */
	public function forget($condition);

	/**
	 * 刷新菜单
	 *
	 * @param string $plugin
	 * @return bool
	 */
	public function refresh($plugin = null);

}

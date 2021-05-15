<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Contracts\Menu;

interface Factory{

	/**
	 * 使用菜单仓库
	 */
	public function shouldUse($name);

	/**
	 * 获取菜单仓库
	 *
	 * @param string $name
	 * @return \Xin\Contracts\Menu\Repository
	 */
	public function menu($name = null);
}

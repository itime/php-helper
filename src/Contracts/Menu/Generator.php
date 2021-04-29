<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Menu;

interface Generator{

	/**
	 * 生成菜单
	 *
	 * @param array $menus
	 * @param array $options
	 * @return array
	 */
	public function generate(array $menus, array $options = []);

}

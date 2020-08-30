<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Menu;

interface Menu{
	
	/**
	 * 生成菜单
	 *
	 * @param array $options
	 * @return array
	 */
	public function generate(array $options = []);
	
}

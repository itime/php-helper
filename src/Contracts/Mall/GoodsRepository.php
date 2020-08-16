<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Plugin;

interface GoodsRepository{
	
	/**
	 * get one goods on sale.
	 *
	 * @param $id
	 * @return mixed
	 */
	public function findOneById($id);
}

<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Mall;

use Xin\Contracts\Foundation\Repository;

interface GoodsRepository extends Repository{
	
	/**
	 * get one product by sku.
	 *
	 * @param $sku
	 * @return mixed
	 */
	public function findOneBySku($sku);
}

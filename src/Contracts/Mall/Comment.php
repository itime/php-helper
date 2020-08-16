<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Mall;

/**
 * Interface Comment
 */
interface Comment{
	
	/**
	 * get recommend comments by item id.
	 *
	 * @param $itemId
	 * @return mixed
	 */
	public function getRecommendByItem($itemId);
}

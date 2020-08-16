<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Discount;

/**
 * Interface AdjustmentContract.
 */
interface Adjustment{
	
	const ORDER_DISCOUNT_ADJUSTMENT = 'order_discount';
	
	const ORDER_ITEM_DISCOUNT_ADJUSTMENT = 'order_item_discount';
	
	/**
	 * create a adjustment.
	 *
	 * @param $type
	 * @param $label
	 * @param $amount
	 * @param $originId
	 * @param $originType
	 * @return mixed
	 */
	public function createNew($type, $label, $amount, $originId, $originType);
}

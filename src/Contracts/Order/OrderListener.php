<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Order;

interface OrderListener
{

	/**
	 * 订单被创建
	 * @param mixed $orderGoodsList
	 */
	public function onOrderCreated($orderGoodsList);

	/**
	 * 订单被删除
	 */
	public function onOrderDeleted();

	/**
	 * 订单状态被改变
	 */
	public function onOrderStatusChanged();

}

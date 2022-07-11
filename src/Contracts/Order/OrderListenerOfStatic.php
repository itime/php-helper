<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Order;
/**
 * @deprecated
 */
interface OrderListenerOfStatic
{

	/**
	 * 订单被创建
	 *
	 * @param \Xin\Contracts\Order\Orderable $order
	 * @param mixed $orderGoodsList
	 */
	public static function onOrderCreated(Orderable $order, $orderGoodsList);

	/**
	 * 订单被删除
	 *
	 * @param \Xin\Contracts\Order\Orderable $order
	 */
	public static function onOrderDeleted(Orderable $order);

	/**
	 * 订单状态被改变
	 *
	 * @param \Xin\Contracts\Order\Orderable $order
	 */
	public static function onOrderStatusChanged(Orderable $order);

}

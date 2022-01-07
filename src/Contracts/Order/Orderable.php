<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Order;

interface Orderable {

	/**
	 * 订单是否已取消
	 *
	 * @return bool
	 */
	public function isCancelled();

	/**
	 * 订单是否已关闭
	 *
	 * @return bool
	 */
	public function isClosed();

	/**
	 * 订单是正在进行中
	 *
	 * @return bool
	 */
	public function isPending();

	/**
	 * 订单是否已支付
	 *
	 * @return bool
	 */
	public function isPaySucceed();

	/**
	 * 订单是否已发货
	 *
	 * @return bool
	 */
	public function isDelivered();

	/**
	 * 订单是否已收货
	 *
	 * @return bool
	 */
	public function isReceived();

	/**
	 * 订单是否已评价
	 *
	 * @return bool
	 */
	public function isEvaluated();

	/**
	 * 订单是否已完成
	 *
	 * @return bool
	 */
	public function isCompleted();

	/**
	 * 设置订单取消
	 *
	 * @return bool
	 */
	public function setCancel();

	/**
	 * 设置订单已关闭
	 *
	 * @return bool
	 */
	public function setClose();

	/**
	 * 设置订单已支付
	 *
	 * @param string $payType
	 * @param string $paySn
	 * @return bool
	 */
	public function setPaid($payType, $paySn);

	/**
	 * 设置订单发货
	 *
	 * @param string $expressId
	 * @param string $expressNo
	 * @return bool
	 */
	public function setDelivery($expressId, $expressNo);

	/**
	 * 设置订单收货
	 *
	 * @return bool
	 */
	public function setReceipt();

	/**
	 * 设置订单评价
	 *
	 * @return bool
	 */
	public function setEvaluate();

	/**
	 * 核销订单
	 *
	 * @param int $verifierId
	 * @return bool
	 */
	public function verification($verifierId = 0);

}

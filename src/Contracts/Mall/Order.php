<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Mall;

interface Order{
	
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
}

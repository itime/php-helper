<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Bus\Order\Events;


class RefundChangeStatusEvent
{

	// 已取消
	public const CANCELED = 'canceled';

	// 商家已拒绝
	public const REFUSED = 'refused';

	// 买家已发货
	public const DELIVERED = 'delivered';

	// 卖家已收货
	public const RECEIVED = 'received';

	// 卖家已支付
	public const PAID = 'paid';

	// 交易已完成
	public const COMPLETED = 'completed';

	// 已关闭
	public const CLOSED = 'closed';

	/**
	 * @var mixed
	 */
	public $refund;

	/**
	 * @var string
	 */
	public $type;

	/**
	 * @param mixed $refund
	 * @param string $type
	 */
	public function __construct($refund, $type)
	{
		$this->refund = $refund;
		$this->type = $type;
	}

	/**
	 * 是否已取消变动
	 *
	 * @return bool
	 */
	public function isCanceled()
	{
		return $this->type === static::CANCELED;
	}

	/**
	 * 是否已支付变动
	 *
	 * @return bool
	 */
	public function isPaid()
	{
		return $this->type === static::CANCELED;
	}

	/**
	 * 是否后台已取消变动
	 *
	 * @return bool
	 */
	public function isRefused()
	{
		return $this->type === static::REFUSED;
	}

	/**
	 * 是否已发货变动
	 *
	 * @return bool
	 */
	public function isDelivered()
	{
		return $this->type === static::DELIVERED;
	}

	/**
	 * 是否已收货变动
	 *
	 * @return bool
	 */
	public function isReceived()
	{
		return $this->type === static::RECEIVED;
	}

	/**
	 * 是否已完成变动
	 *
	 * @return bool
	 */
	public function isCompleted()
	{
		return $this->type === static::COMPLETED;
	}

	/**
	 * 是否已关闭变动
	 *
	 * @return bool
	 */
	public function isClosed()
	{
		return $this->type === static::CLOSED;
	}

}

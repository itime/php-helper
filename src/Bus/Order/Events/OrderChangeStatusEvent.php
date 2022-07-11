<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Bus\Order\Events;

class OrderChangeStatusEvent
{

	// 已取消
	public const CANCELED = 'canceled';

	// 已支付
	public const PAID = 'paid';

	// 后台已取消
	public const ADMIN_CANCELED = 'admin_canceled';

	// 已核销
	public const VERIFICATION = 'verification';

	// 已发货
	public const DELIVERED = 'delivered';

	// 已收货
	public const RECEIVED = 'received';

	// 已评价
	public const EVALUATED = 'evaluated';

	// 已完成
	public const COMPLETED = 'completed';

	// 已关闭
	public const CLOSED = 'closed';

	/**
	 * @var mixed
	 */
	public $order;

	/**
	 * @var string
	 */
	public $type;

	/**
	 * @param mixed $order
	 * @param string $type
	 */
	public function __construct($order, $type)
	{
		$this->order = $order;
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
	public function isAdminCanceled()
	{
		return $this->type === static::ADMIN_CANCELED;
	}

	/**
	 * 是否已核销变动
	 *
	 * @return bool
	 */
	public function isVerification()
	{
		return $this->type === static::VERIFICATION;
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
	 * 是否已评价变动
	 *
	 * @return bool
	 */
	public function isEvaluated()
	{
		return $this->type === static::EVALUATED;
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

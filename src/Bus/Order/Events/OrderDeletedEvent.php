<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Bus\Order\Events;


class OrderDeletedEvent
{

	/**
	 * @var mixed
	 */
	public $order;

	/**
	 * @param mixed $order
	 */
	public function __construct($order)
	{
		$this->order = $order;
	}

}

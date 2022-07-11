<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Bus\Order\Enums;

use MyCLabs\Enum\Enum;

class RefundType extends Enum
{

	/**
	 * 仅退款
	 */
	public const REFUND = 0;

	/**
	 * 退款退货
	 */
	public const BARTER = 1;

	/**
	 * 获取枚举数据
	 *
	 * @return array
	 */
	public static function data()
	{
		return [
			self::REFUND => '仅退款',
			self::BARTER => '退款退货',
		];
	}

}

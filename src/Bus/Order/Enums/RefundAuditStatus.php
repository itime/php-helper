<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Bus\Order\Enums;

use MyCLabs\Enum\Enum;

class RefundAuditStatus extends Enum
{

	/**
	 * 进行中
	 */
	public const PENDING = 0;

	/**
	 * 已通过
	 */
	public const PASSED = 1;

	/**
	 * 商家已拒绝
	 */
	public const REFUSED = 2;

	/**
	 * 获取枚举数据
	 *
	 * @return array
	 */
	public static function data()
	{
		return [
			self::PENDING => '进行中',
			self::PASSED => '已通过',
			self::REFUSED => '已拒绝',
		];
	}

}

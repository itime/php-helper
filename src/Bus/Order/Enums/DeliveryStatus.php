<?php

namespace Xin\Bus\Order\Enums;

use MyCLabs\Enum\Enum;

/**
 * 订单发货状态枚举类
 */
class DeliveryStatus extends Enum
{

	// 待发货
	public const PENDING = 10;

	// 发货成功
	public const SUCCESS = 20;

	/**
	 * 获取枚举数据
	 *
	 * @return array
	 */
	public static function data()
	{
		return [
			self::PENDING => '待发货',
			self::SUCCESS => '已发货',
		];
	}

}

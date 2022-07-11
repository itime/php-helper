<?php

namespace Xin\Bus\Order\Enums;

use MyCLabs\Enum\Enum;

/**
 * 订单支付方式枚举类
 */
class PayType extends Enum
{

	// 余额支付
	public const BALANCE = 10;

	// 微信支付
	public const WECHAT = 20;

	// 支付宝
	public const ALIPAY = 30;

	/**
	 * 获取枚举数据
	 *
	 * @return array
	 */
	public static function data()
	{
		return [
			self::BALANCE => '余额支付',
			self::WECHAT => '微信支付',
			self::ALIPAY => '支付宝',
		];
	}

}

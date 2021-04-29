<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */
namespace Xin\Bus\Balance;

/**
 * 余额变动场景枚举类
 */
class SceneEnum{

	// 用户充值
	const RECHARGE = 10;

	// 用户消费
	const CONSUME = 20;

	// 管理员操作
	const ADMIN = 30;

	// 订单退款
	const REFUND = 40;

	/**
	 * 获取订单类型值
	 *
	 * @return array
	 */
	public static function data(){
		return [
			self::RECHARGE => [
				'name'     => '用户充值',
				'describe' => '用户充值：%s',
			],
			self::CONSUME  => [
				'name'     => '用户消费',
				'describe' => '用户消费：%s',
			],
			self::ADMIN    => [
				'name'     => '管理员操作',
				'describe' => '后台管理员 [%s] 操作',
			],
			self::REFUND   => [
				'name'     => '订单退款',
				'describe' => '订单退款：%s',
			],
		];
	}

}

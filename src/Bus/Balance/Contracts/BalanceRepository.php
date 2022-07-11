<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Bus\Balance\Contracts;

interface BalanceRepository
{

	/**
	 * 充值
	 *
	 * @param int $userId
	 * @param float $amount
	 * @param string $remark
	 * @param array $attributes
	 * @param array $logAttributes
	 * @return float
	 */
	public function recharge($userId, $amount, $remark = '', $attributes = [], $logAttributes = []);

	/**
	 * 消费
	 *
	 * @param int $userId
	 * @param float $amount
	 * @param string $remark
	 * @param array $attributes
	 * @return mixed
	 */
	public function consume($userId, $amount, $remark = '', $attributes = [], $logAttributes = []);

	/**
	 * 获取用户余额数据
	 *
	 * @param int $userId
	 * @return int|float
	 */
	public function value($userId);

}

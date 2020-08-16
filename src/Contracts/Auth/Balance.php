<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Auth;

interface Balance{
	
	/**
	 * 获取用户余额数据
	 *
	 * @param int $userId
	 * @return mixed
	 */
	public function getBalanceByUserId($userId);
	
	/**
	 * 获取用户余额数据
	 *
	 * @param int    $userId
	 * @param string $type
	 * @param int    $limit
	 * @return mixed
	 */
	public function getUserBalanceListByType($userId, $type = 'recharge', $limit = 15);
	
	/**
	 * 充值
	 *
	 * @param int    $userId
	 * @param float  $amount
	 * @param string $remark
	 * @param array  $attributes
	 * @return mixed
	 */
	public function recharge($userId, $amount, $remark = '', $attributes = []);
	
	/**
	 * 消费
	 *
	 * @param int    $userId
	 * @param float  $amount
	 * @param string $remark
	 * @param array  $attributes
	 * @return mixed
	 */
	public function consume($userId, $amount, $remark = '', $attributes = []);
}

<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Mall;

use Xin\Contracts\Foundation\Repository;

/**
 * Interface Order
 */
interface OrderRepository extends Repository{
	
	/**
	 * 根据订单编号获得订单数据.
	 *
	 * @param string $no
	 * @return mixed
	 */
	public function getOrderByNo($no);
	
	/**
	 * 根据订单状态获取订单数据
	 *
	 * @param mixed $input
	 * @param int   $userId
	 * @return mixed
	 */
	public function getOrderByStatus($input, $userId);
	
	/**
	 * 根据订单表达式获取订单数据
	 *
	 * @param mixed    $orderConditions
	 * @param mixed    $itemConditions
	 * @param int      $limit
	 * @param string[] $withs
	 * @return mixed
	 */
	public function getOrdersByConditions($orderConditions, $itemConditions, $limit = 15, $withs = ['items']);
	
	/**
	 * 根据规则获取订单数据
	 *
	 * @param mixed $andConditions
	 * @param mixed $orConditions
	 * @param int   $limit
	 * @return mixed
	 */
	public function getOrdersByCriteria($andConditions, $orConditions, $limit = 15);
	
	/**
	 * 根据状态和用户获取订单的数量.
	 *
	 * @param int $userId
	 * @param int $status
	 * @return int
	 */
	public function getOrderCountByUserAndStatus($userId, $status);
	
	/**
	 * 获取订单预创建信息
	 *
	 * @return mixed
	 */
	public function getPreCreateInfo();
}

<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Transaction;

interface Repository {

	/**
	 * 创建一个事务
	 *
	 * @param array $attributes
	 * @return \Xin\Contracts\Transaction\Transaction
	 */
	public function create($attributes = []);

	/**
	 * 事务是否存在
	 *
	 * @param int $id
	 * @return bool
	 */
	public function exist($id);

	/**
	 * 事务是否存在
	 *
	 * @param string $number
	 * @return bool
	 */
	public function existByNumber($number);

	/**
	 * 根据ID实例化事务
	 *
	 * @param int $id
	 * @return bool
	 */
	public function fromId($id);

	/**
	 * 根据编号实例化事务
	 *
	 * @param string $number
	 * @return bool
	 */
	public function fromNumber($number);

}

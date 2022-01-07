<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Transaction;

interface Transaction {

	/**
	 * 是否等待中
	 *
	 * @return bool
	 */
	public function isWaiting();

	/**
	 * 是否进行中
	 *
	 * @return bool
	 */
	public function isPending();

	/**
	 * 是否已完成
	 *
	 * @return bool
	 */
	public function isComplete();

	/**
	 * 设置为等待中
	 *
	 * @return bool
	 */
	public function setWaiting();

	/**
	 * 设置为进行中
	 *
	 * @return bool
	 */
	public function setPending();

	/**
	 * 设置为已完成
	 *
	 * @return bool
	 */
	public function setComplete();

	/**
	 * 是否错误
	 *
	 * @return bool
	 */
	public function isError();

	/**
	 * 获取错误
	 *
	 * @return bool
	 */
	public function getError();

	/**
	 * 设置错误
	 *
	 * @return bool
	 */
	public function setError($error);

	/**
	 * 获取编号
	 *
	 * @return bool
	 */
	public function getNumber();

	/**
	 * 获取ID
	 *
	 * @return bool
	 */
	public function getId();

}

<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Discount;

/**
 * Interface DiscountSubjectContract.
 */
interface Subject{

	/**
	 * get subject total amount.
	 *
	 * @return int
	 */
	public function getSubjectTotal();

	/**
	 * get subject count item.
	 *
	 * @return int
	 */
	public function getSubjectCount();

	/**
	 * get subject items.
	 *
	 * @return mixed
	 */
	public function getItems();

	/**
	 * get subject count.
	 *
	 * @return mixed
	 */
	public function countItems();

	/**
	 * @param $adjustment
	 * @return mixed
	 */
	public function addAdjustment($adjustment);

	/**
	 * get subject user.
	 *
	 * @return mixed
	 */
	public function getSubjectUser();

	/**
	 * get current total.
	 *
	 * @return mixed
	 */
	public function getCurrentTotal();

	/**
	 * get subject is paid.
	 *
	 * @return mixed
	 */
	public function isPaid();
}

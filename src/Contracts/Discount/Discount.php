<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Discount;

/**
 * Interface DiscountContract.
 */

/**
 * Interface DiscountContract.
 */
interface Discount{

	/**
	 * @return mixed
	 */
	public function hasRules();

	/**
	 * @return mixed
	 */
	public function isCouponBased();

	/**
	 * @return mixed
	 */
	public function getActions();

	/**
	 * @return mixed
	 */
	public function getRules();

	/**
	 * @return mixed
	 */
	public function setCouponUsed();

	/**
	 * @return mixed
	 */
	public function getStartsAt();

	/**
	 * @return mixed
	 */
	public function getEndsAt();

	/**
	 * @return mixed
	 */
	public function getUsed();

	/**
	 * @return mixed
	 */
	public function getUsageLimit();
}

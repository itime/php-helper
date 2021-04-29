<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Discount;

/**
 * Interface DiscountActionContract.
 */
interface Action{

	/**
	 * @param Subject  $subject
	 * @param array    $configuration
	 * @param Discount $discount
	 * @return mixed
	 */
	public function execute(Subject $subject, array $configuration, Discount $discount);

	/**
	 * @param Subject  $subject
	 * @param array    $configuration
	 * @param Discount $discount
	 * @return mixed
	 */
	public function calculate(Subject $subject, array $configuration, Discount $discount);
}

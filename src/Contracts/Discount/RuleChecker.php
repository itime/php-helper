<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Discount;

/**
 * Interface RuleCheckerContract.
 */
interface RuleChecker{

	/**
	 * @param Subject  $subject
	 * @param array    $configuration
	 * @param Discount $discount
	 * @return mixed
	 */
	public function isEligible(Subject $subject, array $configuration, Discount $discount);
}

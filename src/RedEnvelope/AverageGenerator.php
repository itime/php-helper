<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\RedEnvelope;

class AverageGenerator extends AbstractGenerator{
	
	/**
	 * @var array
	 */
	protected $config = [
		'num'    => 1,
		'amount' => 0.01,
	];
	
	/**
	 * @return array
	 */
	public function generate(){
		$num = $this->config['num'];
		$amount = $this->config['amount'];
		
		$avg = floatval(bcdiv($amount, $num, 2));
		if($avg < 0.01){
			throw new \LogicException("amount({$amount}) divided by num({$num}) shall not be less than 0.01");
		}
		
		//		if($avg <= 0){
		//			throw new \LogicException("The minimum amount cannot be 0");
		//		}
		
		return array_fill(0, $num, $avg);
	}
}

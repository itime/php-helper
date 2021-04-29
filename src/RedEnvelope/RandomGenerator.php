<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\RedEnvelope;

class RandomGenerator extends AbstractGenerator{

	/**
	 * @var array
	 */
	protected $config = [
		'num'    => 1,
		'amount' => 0.01,
		'ratio'  => 0.5,
	];

	/**
	 * @var int
	 */
	protected $surplusNum = 0;

	/**
	 * @var float
	 */
	protected $surplusAmount = 0.00;

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

		$this->surplusNum = $num;
		$this->surplusAmount = $amount;

		$result = [];
		for($i = 0; $i < $num; $i++){
			$result[] = $this->next();
		}

		return $result;
	}

	/**
	 * 获取下一个红包金额
	 *
	 * @return float
	 */
	protected function next(){
		if($this->surplusNum == 1){
			$value = $this->surplusAmount;
			$this->reset();
			return $value;
		}

		$ratio = $this->config['ratio'];

		$avgValue = floatval(bcdiv($this->surplusAmount, $this->surplusNum--, 2));
		$driftValue = floatval(bcmul($avgValue, $ratio, 2));
		$maxDriftValue = floatval(bcadd($avgValue, $driftValue, 2));
		$minDriftValue = floatval(bcsub($avgValue, $driftValue, 2));

		if($minDriftValue < 0.01){
			$minDriftValue = 0.01;
		}

		$value = $this->randFloat($minDriftValue, $maxDriftValue);
		$this->surplusAmount = floatval(bcsub($this->surplusAmount, $value, 2));

		return $value;
	}

	/**
	 * 重置数据
	 */
	protected function reset(){
		$this->surplusNum = 0;
		$this->surplusAmount = 0.00;
	}
}

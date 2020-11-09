<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\RedEnvelope;

class CommonlyGenerator extends AbstractGenerator{
	
	/**
	 * @var array
	 */
	protected $config = [
		'min_amount'   => 0.01,
		'max_amount'   => 1,
		'ratio_amount' => 0.5,
		'ratio'        => 0.5,
		
		'skip_ratio_weight' => 10,
	];
	
	/**
	 * @return string
	 */
	public function generate(){
		$ratio = $this->config['ratio'];
		$minValue = $this->config['min_amount'];
		$maxValue = $this->config['max_amount'];
		$avgValue = $this->config['ratio_amount'];
		$skipRatioWeight = $this->config['skip_ratio_weight'];
		
		$isSkipRatioWeight = rand(0, $skipRatioWeight) == intval($skipRatioWeight / 2);
		if($isSkipRatioWeight){
			return $this->randFloat($minValue, $maxValue);
		}
		
		$driftValue = floatval(bcmul($avgValue, $ratio, 2));
		$maxDriftValue = floatval(bcadd($avgValue, $driftValue, 2));
		$minDriftValue = floatval(bcsub($avgValue, $driftValue, 2));
		
		if($minDriftValue < $minValue){
			$minDriftValue = $minValue;
		}
		
		return $this->randFloat($minDriftValue, $maxDriftValue);
	}
	
}

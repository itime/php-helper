<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\RedEnvelope;

use Xin\Contracts\RedEnvelope\Generator as GeneratorContract;

abstract class AbstractGenerator implements GeneratorContract{
	
	/**
	 * @var array
	 */
	protected $config = [];
	
	/**
	 * AbstractGenerator constructor.
	 *
	 * @param array $config
	 */
	public function __construct(array $config){
		$this->config = array_merge($this->config, $config);
	}
	
	/**
	 * 浮点数随机数
	 *
	 * @param float $min
	 * @param float $max
	 * @return float
	 */
	protected function randFloat($min, $max){
		$value = rand($min * 100, $max * 100);
		$value = bcdiv($value, 100, 2);
		return floatval($value);
	}
}

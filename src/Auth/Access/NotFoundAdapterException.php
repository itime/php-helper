<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Auth;

class NotFoundAdapterException extends \Exception{

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var array
	 */
	private $adapters;

	/**
	 * NotFoundAdapterException constructor.
	 *
	 * @param string $name
	 * @param array  $adapters
	 */
	public function __construct($name, $adapters){
		parent::__construct("验证规则[{$name}]适配器未找到！");

		$this->name = $name;
		$this->adapters = $adapters;
	}
}

<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Bus\Balance;

use Xin\Support\Str;

trait Balanceable{

	/**
	 * @var \Xin\Contracts\Bus\Balance\BalanceRepository
	 */
	private $balancers = [];

	/**
	 * 获取余额处理器实例
	 *
	 * @return \Xin\Contracts\Bus\Balance\BalanceRepository
	 */
	public function balance($bag = 'default'){
		if(isset($this->balancers[$bag])){
			return $this->balancers[$bag];
		}

		$method = 'make'.Str::camel($bag).'Balance';
		if(!method_exists($this, $method)){
			throw new \RuntimeException("{$bag} balance not defined.");
		}

		return $this->balancers[$bag] = $this->$method();
	}

	/**
	 * 返回默认余额处理器
	 *
	 * @return \Xin\Contracts\Bus\Balance\BalanceRepository
	 */
	protected function makeDefaultBalance(){
		return new Balance([
			'model' => static::class,
			'field' => 'balance',
			'log'   => [
				'type'  => 'table',
				'table' => $this->name."_balance_log",
			],
		]);
	}

	/**
	 * 充值
	 *
	 * @param float  $amount
	 * @param string $remark
	 * @param array  $attributes
	 * @return mixed
	 */
	public function recharge($amount, $remark = '', $attributes = [], $bag = 'default'){
		return $this->balance($bag)->recharge($this->getOrigin('id'), $amount, $remark, $attributes);
	}

	/**
	 * 消费
	 *
	 * @param float  $amount
	 * @param string $remark
	 * @param array  $attributes
	 * @return mixed
	 */
	public function consume($amount, $remark = '', $attributes = [], $bag = 'default'){
		return $this->balance($bag)->consume($this->getOrigin('id'), $amount, $remark, $attributes);
	}

	/**
	 * 获取当前用户余额
	 *
	 * @return float
	 */
	public function getBalance($bag = 'default'){
		return $this->balance($bag)->value(
			$this->getOrigin('id')
		);
	}
}

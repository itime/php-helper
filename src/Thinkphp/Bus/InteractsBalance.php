<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Bus;

/**
 * Class InteractsBalance
 *
 * @mixin \think\Model
 */
trait InteractsBalance{
	
	/**
	 * @return \Xin\Contracts\Bus\BalanceRepository
	 */
	public function balance(){
		return new Balance(
			$this->name, [
				'balance_log' => [
					'type'  => 'database',
					'table' => $this->name."_balance_log",
				],
			]
		);
	}
	
	/**
	 * 充值
	 *
	 * @param float  $amount
	 * @param string $remark
	 * @param array  $attributes
	 * @return mixed
	 */
	public function recharge($amount, $remark = '', $attributes = []){
		return $this->balance()->recharge(
			$this->getData('id'),
			$amount, $remark, $attributes
		);
	}
	
	/**
	 * 消费
	 *
	 * @param float  $amount
	 * @param string $remark
	 * @param array  $attributes
	 * @return mixed
	 */
	public function consume($amount, $remark = '', $attributes = []){
		return $this->balance()->consume(
			$this->getData('id'),
			$amount, $remark, $attributes
		);
	}
}

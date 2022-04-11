<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Bus\Balance;

use Xin\Support\Str;

trait HasBalanceRepository
{

	/**
	 * @var BalanceRepository[]
	 */
	private static $balancerRepositories = [];

	/**
	 * 获取余额处理器实例
	 *
	 * @return \Xin\Contracts\Bus\Balance\BalanceRepository
	 */
	public static function balance($bag = 'default')
	{
		if (isset(static::$balancerRepositories[$bag])) {
			return static::$balancerRepositories[$bag];
		}

		$method = 'make' . Str::camel($bag) . 'Balance';
		if (!method_exists(static::class, $method)) {
			throw new \RuntimeException("{$bag} balance not defined.");
		}

		return static::$balancerRepositories[$bag] = call_user_func([static::class, $method]);
	}

	/**
	 * 返回默认余额处理器
	 *
	 * @return \Xin\Contracts\Bus\Balance\BalanceRepository
	 */
	protected static function makeDefaultBalance()
	{
		return new BalanceRepository('balance');
	}

	/**
	 * 创建余额仓库管理器
	 * @param array $config
	 * @return BalanceRepository
	 */
	protected static function makeBalance(array $config)
	{
		return new BalanceRepository(array_replace_recursive([
			'model' => static::class,
			'log' => [
				'type' => 'table',
				'table' => (new static)->getName() . "_balance_log",
			],
		], $config));
	}

	/**
	 * 充值
	 *
	 * @param float $amount
	 * @param string $remark
	 * @param array $attributes
	 * @return mixed
	 */
	public function recharge($amount, $remark = '', $attributes = [], $bag = 'default')
	{
		return static::balance($bag)->recharge($this->getOrigin('id'), $amount, $remark, $attributes);
	}

	/**
	 * 消费
	 *
	 * @param float $amount
	 * @param string $remark
	 * @param array $attributes
	 * @return mixed
	 */
	public function consume($amount, $remark = '', $attributes = [], $bag = 'default')
	{
		return static::balance($bag)->consume($this->getOrigin('id'), $amount, $remark, $attributes);
	}

	/**
	 * 获取当前用户余额
	 *
	 * @return float
	 */
	public function getBalance($bag = 'default')
	{
		return static::balance($bag)->value(
			$this->getOrigin('id')
		);
	}

}

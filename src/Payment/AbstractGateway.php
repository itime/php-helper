<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Payment;

use Xin\Contracts\Payment\Gateway as GatewayContract;
use Yansongda\Pay\Contracts\GatewayApplicationInterface;
use Yansongda\Pay\Exceptions\BusinessException;
use Yansongda\Pay\Exceptions\GatewayException;
use Yansongda\Pay\Exceptions\InvalidConfigException;
use Yansongda\Pay\Exceptions\InvalidGatewayException;
use Yansongda\Pay\Exceptions\InvalidSignException;

abstract class AbstractGateway implements GatewayContract
{

	/**
	 * @var GatewayManager
	 */
	protected $payManager;

	/**
	 * @var GatewayApplicationInterface
	 */
	protected $gateway;

	/**
	 * 微信支付器
	 *
	 * @param GatewayManager $payManager
	 * @param GatewayApplicationInterface $gateway
	 */
	public function __construct(GatewayManager $payManager, GatewayApplicationInterface $gateway)
	{
		$this->payManager = $payManager;
		$this->gateway = $gateway;
	}

	/**
	 * 安全调用
	 * @param callable $callback
	 * @return \Yansongda\Supports\Collection
	 */
	protected function call(callable $callback)
	{
		try {
			return $callback();
		} catch (BusinessException $e) {
			throw new \LogicException($e->getMessage(), $e->getCode(), $e);
		} catch (GatewayException $e) {
			throw new \LogicException($e->getMessage(), $e->getCode(), $e);
		} catch (InvalidConfigException $e) {
			throw new \LogicException($e->getMessage(), $e->getCode(), $e);
		} catch (InvalidGatewayException $e) {
			throw new \LogicException($e->getMessage(), $e->getCode(), $e);
		} catch (InvalidSignException $e) {
			throw new \LogicException("签名错误，请检查支付配置", $e->getCode(), $e);
		}
	}

}

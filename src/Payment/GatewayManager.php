<?php

namespace Xin\Payment;

use Xin\Contracts\Payment\GatewayFactory as GatewayFactoryContract;
use Xin\Capsule\WithConfig;
use Xin\Capsule\WithContainer;
use Xin\Contracts\Payment\Factory as PaymentFactory;
use Xin\Contracts\Payment\PayOrderProvider;
use Xin\Saas\App\HasAppId;
use Xin\Support\Str;

class GatewayManager implements GatewayFactoryContract
{
	use WithConfig, WithContainer, HasAppId;

	/**
	 * @var array
	 */
	protected $config = [];

	/**
	 * @var array
	 */
	protected $gateways = [];

	/**
	 * @var PayOrderProvider
	 */
	protected $payOrderProvider;

	/**
	 * @var PaymentFactory
	 */
	protected $paymentFactory;

	/**
	 * @param PaymentFactory $paymentFactory
	 * @param array $config
	 */
	public function __construct(PaymentFactory $paymentFactory, array $config = [])
	{
		$this->paymentFactory = $paymentFactory;
		$this->config = $config;
	}

	/**
	 * 小程序支付
	 * @param array $paymentInfo
	 * @param string $type
	 * @return array
	 */
	public function miniapp(array $paymentInfo, $type = null)
	{
		$payOrder = $this->makePayOrder($paymentInfo, $type);

		$paymentInfo = $this->preparePaymentInfo($paymentInfo, $payOrder);

		$gateway = $this->pay($type)->miniapp($paymentInfo);

		return [
			'pay_order' => $payOrder,
			'gateway' => $gateway,
		];
	}

	/**
	 * App 支付
	 * @param array $paymentInfo
	 * @param string $type
	 * @return array
	 */
	public function app(array $paymentInfo, $type = null)
	{
		$payOrder = $this->makePayOrder($paymentInfo, $type);

		$paymentInfo = $this->preparePaymentInfo($paymentInfo, $payOrder);

		$gateway = $this->pay($type)->app($paymentInfo);

		return [
			'pay_order' => $payOrder,
			'gateway' => $gateway,
		];
	}

	/**
	 * @inerhitDoc
	 */
	public function pay($type = null)
	{
		$type = $type ?: $this->getType();

		if (!isset($this->gateways[$type])) {
			$method = "create" . Str::studly($type) . "Gateway";
			if (method_exists($this, $method)) {
				$gateway = call_user_func([$this, $method]);
			} else {
				throw new \RuntimeException("{$type} pay service not defined.");
			}

			$this->gateways[$type] = $gateway;
		}

		return $this->gateways[$type];
	}

	/**
	 * @return WechatGateway
	 */
	protected function createWechatGateway()
	{
		if (!$this->paymentFactory->hasWechat()) {
			throw new \LogicException("微信支付未配置！");
		}

		return new WechatGateway(
			$this, $this->paymentFactory->wechat()
		);
	}

	/**
	 * @param string $transactionId
	 * @param string $type
	 * @return string
	 */
	public function transactionIdToOutTradeNo($transactionId, $type = null)
	{
		$type = $type ?: $this->getType();

		return $this->payOrderProvider()->transactionIdToOutTradeNo($type, $transactionId);
	}

	/**
	 * @return $this
	 */
	public function useWechatPay()
	{
		$this->setConfig('type', 'wechat');

		return $this;
	}

	/**
	 * @return $this
	 */
	public function useAlipay()
	{
		$this->setConfig('type', 'alipay');

		return $this;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->getConfig('type', 'wechat');
	}

	/**
	 * @return PayOrderProvider
	 */
	protected function payOrderProvider()
	{
		if (!$this->payOrderProvider) {
			$this->payOrderProvider = new PayOrderProviderProvider();
		}

		return $this->payOrderProvider;
	}

	/**
	 * 预处理支付信息
	 * @param array $paymentInfo
	 * @param mixed $payOrder
	 * @return array
	 */
	protected function preparePaymentInfo(array $paymentInfo, $payOrder)
	{
		$paymentInfo['out_trade_no'] = $payOrder['pay_no'];

		unset($paymentInfo['user_data']);

		return $paymentInfo;
	}

	/**
	 * 创建支付单号
	 *
	 * @param array $paymentInfo
	 * @param $type
	 * @return \plugins\order\model\PayLog
	 */
	public function makePayOrder(array $paymentInfo, $type)
	{
		$type = $type ?: $this->getType();

		$payNo = Str::makeOrderSn();

		$data = [
			'app_id' => $this->getAppId(),
			'type' => $type,
			'pay_no' => $payNo,
			'out_trade_no' => $paymentInfo['out_trade_no'],
			'amount' => $paymentInfo['out_trade_no'] ?? '',
			'body' => $paymentInfo['body'],
			'detail' => $paymentInfo,
			'return_url' => $paymentInfo['return_url'] ?? '',
			'notify_url' => $paymentInfo['notify_url'] ?? '',
			'user_id' => $paymentInfo['user_id'] ?? 0,
			'voucher' => '',
			'mch_info' => '',
			'status' => 0,
			'pay_time' => 0,
		];

		if (isset($paymentInfo['total_fee'])) {
			$data['amount'] = bcdiv($paymentInfo['total_fee'], 100, 2);
		}

		return $this->payOrderProvider->createPayOrder($data);
	}
}
<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Payment;

/**
 * @property \Yansongda\Pay\Gateways\Wechat $gateway
 */
class WechatGateway extends AbstractGateway
{
	/**
	 * 小程序支付
	 *
	 * @param array $paymentInfo
	 * @return \Yansongda\Supports\Collection
	 */
	public function miniapp(array $paymentInfo)
	{
		$paymentInfo = $this->preparePaymentInfo($paymentInfo);

		return $this->call(function () use ($paymentInfo) {
			return $this->gateway->miniapp($paymentInfo);
		});
	}

	/**
	 * APP 支付
	 * @param array $paymentInfo
	 * @return \Yansongda\Supports\Collection
	 */
	public function app(array $paymentInfo)
	{
		$paymentInfo = $this->preparePaymentInfo($paymentInfo);

		return $this->call(function () use ($paymentInfo) {
			return $this->gateway->app($paymentInfo);
		});
	}

	/**
	 * 预处理数据
	 * @param array $paymentInfo
	 * @return array
	 */
	protected function preparePaymentInfo(array $paymentInfo)
	{
		if (isset($paymentInfo['amount'])) {
			$paymentInfo['total_fee'] = (int)$paymentInfo['amount'] * 100;
			unset($paymentInfo['amount']);
		}

		return $paymentInfo;
	}
}

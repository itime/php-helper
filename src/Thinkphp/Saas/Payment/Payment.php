<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas\Payment;

use Xin\Contracts\Saas\Payment\Repository;
use Xin\Foundation\Payment\Payment as BasePayment;
use Xin\Foundation\Payment\PaymentNotConfigureException;
use Yansongda\Pay\Pay;

class Payment extends BasePayment implements Repository {

	/**
	 * @inheritDoc
	 */
	public function wechatOfId($id, array $options) {
		if ($id == 0) {
			return $this->wechat($options);
		}

		$config = $this->resolveConfig(['id' => $id], 'wechat');

		return $this->newWechatInstance($config, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function wechatOfAppId($appId, array $options = []) {
		if ($appId == 0) {
			return $this->wechat($options);
		}

		$config = $this->resolveConfig(['app_id' => $appId], 'wechat');

		return $this->newWechatInstance($config, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function alipayOfId($id, array $options = []) {
		if ($id == 0) {
			return $this->alipay($options);
		}

		$config = $this->resolveConfig(['id' => $id], 'alipay');

		return $this->newAlipayInstance($config, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function alipayOfAppId($appId, array $options = []) {
		if ($appId == 0) {
			return $this->alipay($options);
		}

		$config = $this->resolveConfig(['app_id' => $appId], 'alipay');

		return $this->newAlipayInstance($config, $options);
	}

	/**
	 * 实例化微信支付
	 *
	 * @param mixed $config
	 * @param array $options
	 * @return \Yansongda\Pay\Gateways\Wechat
	 */
	protected function newWechatInstance($config, array $options) {
		$instance = Pay::wechat($config);

		return $this->initApplication($instance, $options);
	}

	/**
	 * 实例化支付宝支付
	 *
	 * @param mixed $config
	 * @param array $options
	 * @return \Yansongda\Pay\Gateways\Alipay
	 */
	protected function newAlipayInstance($config, array $options) {
		$instance = Pay::alipay($config);

		return $this->initApplication($instance, $options);
	}

	/**
	 * 解析配置信息
	 *
	 * @param mixed  $query
	 * @param string $type
	 * @return array
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	protected function resolveConfig($query, $type) {
		$payment = DatabasePayment::where($query)->find();

		if (empty($payment)) {
			throw new PaymentNotConfigureException("未配置支付信息！");
		}

		return $payment->toArray();
	}

}

<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Thinkphp\Saas\Payment;

use Xin\Contracts\Saas\Payment\ConfigProvider as ConfigProviderContract;
use Xin\Payment\Exceptions\PaymentNotConfigureException;

class ConfigProvider implements ConfigProviderContract {

	/**
	 * @inheritDoc
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public function retrieveById($id, $type) {
		return $this->getConfig(['id' => $id], 'alipay');
	}

	/**
	 * @inheritDoc
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\DbException
	 * @throws \think\db\exception\ModelNotFoundException
	 */
	public function retrieveByAppId($appId, $type, $name = null) {
		return $this->getConfig(['app_id' => $appId], $type);
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
	protected function getConfig($query, $type) {
		$payment = DatabasePayment::where($query)->find();

		if (empty($payment)) {
			throw new PaymentNotConfigureException("未配置支付信息！");
		}

		return $payment->toArray();
	}

}

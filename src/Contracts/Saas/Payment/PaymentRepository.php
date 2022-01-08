<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Saas\Payment;

use Xin\Contracts\Foundation\Payment;

interface PaymentRepository extends Payment {

	/**
	 * 根据id获取微信支付的实例
	 *
	 * @param int   $id
	 * @param array $options
	 * @return \Yansongda\Pay\Gateways\Wechat
	 */
	public function wechatOfId($id, array $options);

	/**
	 * 根据应用id获取微信支付的实例
	 *
	 * @param int   $appId
	 * @param array $options
	 * @return \Yansongda\Pay\Gateways\Wechat
	 */
	public function wechatOfAppId($appId, array $options = []);

	/**
	 * 根据应用id获取支付宝支付的实例
	 *
	 * @param int   $id
	 * @param array $options
	 * @return \Yansongda\Pay\Gateways\Alipay
	 */
	public function alipayOfId($id, array $options = []);

	/**
	 * 根据应用id获取支付宝支付的实例
	 *
	 * @param int   $appId
	 * @param array $options
	 * @return \Yansongda\Pay\Gateways\Alipay
	 */
	public function alipayOfAppId($appId, array $options = []);

}

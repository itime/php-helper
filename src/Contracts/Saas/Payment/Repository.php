<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Saas\Payment;

use Xin\Contracts\Payment\Factory;

interface Repository extends Factory {

	/**
	 * 锁定 appId
	 * @param int $appId
	 * @return void
	 */
	public function shouldUseOfAppId($appId);

	/**
	 * 锁定 wechat id
	 * @param int $id
	 * @return void
	 */
	public function shouldUseOfWechatId($id);

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
	 * @param int    $appId
	 * @param string $name
	 * @param array  $options
	 * @return \Yansongda\Pay\Gateways\Wechat
	 */
	public function wechatOfAppId($appId, $name = null, array $options = []);

	/**
	 * 锁定 alipay id
	 * @param int $id
	 * @return void
	 */
	public function shouldUseOfAlipayId($id);

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
	 * @param int    $appId
	 * @param string $name
	 * @param array  $options
	 * @return \Yansongda\Pay\Gateways\Alipay
	 */
	public function alipayOfAppId($appId, $name = null, array $options = []);

}

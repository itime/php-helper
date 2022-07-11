<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Payment;

interface Factory
{

	/**
	 * 微信支付
	 *
	 * @param null $name
	 * @param array $options
	 * @return \Yansongda\Pay\Gateways\Wechat
	 */
	public function wechat($name = null, array $options = []);

	/**
	 * 是否配置微信支付
	 *
	 * @return bool
	 */
	public function hasWechat($name = null);

	/**
	 * 支付宝支付
	 *
	 * @param null $name
	 * @param array $options
	 * @return \Yansongda\Pay\Gateways\Alipay
	 */
	public function alipay($name = null, array $options = []);

	/**
	 * 是否配置支付宝支付
	 *
	 * @return bool
	 */
	public function hasAlipay($name = null);

	/**
	 * 获取配置数据
	 *
	 * @param string $key
	 * @return array
	 */
	public function getConfig($key = null, $default = null);

}

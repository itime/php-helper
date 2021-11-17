<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace Xin\Contracts\Foundation;

interface Payment{

	/**
	 * 微信支付
	 *
	 * @param array $options
	 * @return \Yansongda\Pay\Gateways\Wechat
	 */
	public function wechat(array $options = []);

	/**
	 * 是否配置微信支付
	 *
	 * @return bool
	 */
	public function hasWechat();

	/**
	 * 支付宝支付
	 *
	 * @param array $options
	 * @return \Yansongda\Pay\Gateways\Alipay
	 */
	public function alipay(array $options = []);

	/**
	 * 是否配置支付宝支付
	 *
	 * @return bool
	 */
	public function hasAlipay();
}
